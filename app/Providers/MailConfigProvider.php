<?php

namespace App\Providers;

use App\Models\Mailcredential;
use Illuminate\Support\ServiceProvider;
use Config;

class MailConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //get email view data in provider class

        $mailConfig = Mailcredential::where('status','=',1)->first();
        if(isset($mailConfig)){
            $data = [
                'driver' => $mailConfig->mail_mailer,
                'host' => $mailConfig->mail_host,
                'port' => $mailConfig->mail_port,
                'encryption' => $mailConfig->mail_encryption,
                'username' => $mailConfig->mail_username,
                'password' => $mailConfig->mail_password,
                'from'  => ['address' => $mailConfig->mail_from_address,'name' => $mailConfig->mail_from_name],
            ];  

            Config::set('mail',$data);
        }

    }
}
