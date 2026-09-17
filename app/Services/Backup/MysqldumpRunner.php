<?php

namespace App\Services\Backup;

use App\Services\Backup\Contracts\DumpRunner;
use App\Services\Backup\Exceptions\BackupDumpException;
use Symfony\Component\Process\Process;

/**
 * Shells out to mysqldump and pipes its output straight into gzip and then
 * to disk, entirely at the OS level - the dump content never passes through
 * PHP memory. Credentials are passed via a 0600 defaults-extra-file instead
 * of --password=... so they never appear in `ps`/process listings.
 */
class MysqldumpRunner implements DumpRunner
{
    public function dump(string $destinationAbsolutePath): void
    {
        $binary = (string) config('backup.mysqldump_binary');
        $gzip = (string) config('backup.gzip_binary');

        if (!$this->binaryExists($binary)) {
            throw new BackupDumpException(
                "mysqldump binary not found or not executable (configured: \"{$binary}\"). ".
                'Install it on the server or set BACKUP_MYSQLDUMP_PATH in .env to its full path.'
            );
        }

        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (!is_array($connection) || ($connection['driver'] ?? null) !== 'mysql') {
            throw new BackupDumpException('Database backup currently supports the mysql/mariadb driver only.');
        }

        $cnfPath = tempnam(sys_get_temp_dir(), 'dbbkcnf');
        if ($cnfPath === false) {
            throw new BackupDumpException('Could not create a temporary credentials file.');
        }

        $cnfBody = "[client]\n"
            .'user='.($connection['username'] ?? '')."\n"
            .'password='.($connection['password'] ?? '')."\n"
            .'host='.($connection['host'] ?? '127.0.0.1')."\n"
            .'port='.($connection['port'] ?? '3306')."\n";

        file_put_contents($cnfPath, $cnfBody);
        chmod($cnfPath, 0600);

        try {
            $command = sprintf(
                '%s --defaults-extra-file=%s --single-transaction --quick --no-tablespaces --routines --triggers --events %s | %s > %s',
                escapeshellarg($binary),
                escapeshellarg($cnfPath),
                escapeshellarg($connection['database'] ?? ''),
                escapeshellarg($gzip),
                escapeshellarg($destinationAbsolutePath)
            );

            $process = Process::fromShellCommandline($command);
            $process->setTimeout((int) config('backup.process_timeout', 1800));
            $process->run();

            if (!$process->isSuccessful()) {
                throw new BackupDumpException(
                    $this->sanitizeError($process->getErrorOutput()) ?: 'mysqldump exited with a non-zero status.'
                );
            }

            clearstatcache(true, $destinationAbsolutePath);
            if (!is_file($destinationAbsolutePath) || filesize($destinationAbsolutePath) === 0) {
                throw new BackupDumpException('Dump process reported success but produced an empty file.');
            }
        } finally {
            @unlink($cnfPath);
        }
    }

    protected function binaryExists(string $binary): bool
    {
        if (str_contains($binary, '/')) {
            return is_executable($binary);
        }

        $which = Process::fromShellCommandline('command -v '.escapeshellarg($binary));
        $which->run();

        return $which->isSuccessful() && trim($which->getOutput()) !== '';
    }

    /**
     * Defense in depth: even though credentials never reach argv or stdout,
     * strip anything that looks like a password/path leak before it is
     * stored in backup_histories.error_message or logged.
     */
    protected function sanitizeError(string $error): string
    {
        $error = preg_replace('/password=\S+/i', 'password=***', $error) ?? $error;
        $error = preg_replace('/--defaults-extra-file=\S+/i', '--defaults-extra-file=***', $error) ?? $error;

        return trim(mb_substr($error, 0, 2000));
    }
}
