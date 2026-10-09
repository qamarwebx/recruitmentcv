<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Website / branding image uploads (logos, favicon, branch images, WhatsApp
 * icon) in both apps: JPG, PNG, GIF, WEBP and SVG. The type always comes
 * from the CONTENT - never the file name, the browser's declared type or a
 * data-URI label:
 *
 * - raster: the bytes must be a real image (finfo + getimagesizefromstring)
 *   of an allowed type; stored unchanged, as before;
 * - SVG: parsed as XML and rebuilt from a whitelist (sanitizeSvg()) -
 *   scripts, event handlers, foreignObject, animations and external
 *   references removed, entity declarations refused - and only the cleaned
 *   markup is stored. The images are served from the websites' own origins, so an SVG
 *   opened directly must not be able to run anything there.
 *
 * Callers keep their existing folder and naming (only the extension follows
 * the real type) - see store(). Twin file in both apps.
 */
final class ImageUpload
{
    public const RASTER = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];

    public const SVG = 'image/svg+xml';

    /** For <input type="file" accept="...">. */
    public const ACCEPT = 'image/png,image/jpeg,image/gif,image/webp,image/svg+xml,.svg';

    public const MAX_BYTES = 2048 * 1024;

    public const ERROR_TYPE = 'Only JPG, PNG, GIF, WEBP or SVG images are allowed.';

    public const ERROR_SIZE = 'Image must not be larger than 2MB.';

    public const ERROR_SVG = 'This SVG file can\'t be used (it is not a valid or safe SVG image).';

    private const SVG_NS = 'http://www.w3.org/2000/svg';

    private const XLINK_NS = 'http://www.w3.org/1999/xlink';

    /** Elements kept in an SVG (everything else - script, foreignObject, animate, set, a, iframe ... - is removed). */
    private const SVG_ELEMENTS = [
        'svg', 'g', 'defs', 'symbol', 'use', 'title', 'desc', 'style', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'textpath', 'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'pattern', 'marker', 'image', 'switch',
        'filter', 'feblend', 'fecolormatrix', 'fecomponenttransfer', 'fecomposite', 'feconvolvematrix', 'fediffuselighting', 'fedisplacementmap',
        'fedistantlight', 'fedropshadow', 'feflood', 'fefunca', 'fefuncb', 'fefuncg', 'fefuncr', 'fegaussianblur', 'femerge', 'femergenode',
        'femorphology', 'feoffset', 'fepointlight', 'fespecularlighting', 'fespotlight', 'fetile', 'feturbulence',
    ];

    /**
     * An uploaded file: ['ok' => bool, 'error' => ?string, 'mime' => ?string, 'extension' => ?string, 'content' => ?string].
     */
    public static function fromFile(?UploadedFile $file, int $maxBytes = self::MAX_BYTES): array
    {
        if (!$file || !$file->isValid()) {
            return self::fail(self::ERROR_TYPE);
        }
        if ((int) $file->getSize() > $maxBytes) {
            return self::fail(self::ERROR_SIZE);
        }

        return self::inspect((string) file_get_contents($file->getRealPath()), $maxBytes);
    }

    /** A "data:<type>;base64,<data>" value (browser FileReader); the declared type is ignored - the content decides. */
    public static function fromDataUri(?string $value, int $maxBytes = self::MAX_BYTES): array
    {
        if (!is_string($value) || !preg_match('#^data:[a-z0-9.+/-]*(?:;[a-z0-9=.+-]+)*;base64,([A-Za-z0-9+/=\s]+)$#i', $value, $m)) {
            return self::fail('Invalid image data.');
        }
        if (strlen($m[1]) > (int) ceil($maxBytes * 4 / 3) + 8) {
            return self::fail(self::ERROR_SIZE);
        }
        $bytes = base64_decode(preg_replace('/\s+/', '', $m[1]), true);
        if ($bytes === false || $bytes === '') {
            return self::fail('Invalid image data.');
        }

        return self::inspect($bytes, $maxBytes);
    }

    /** Raw bytes -> the same result shape as fromFile(). */
    public static function inspect(string $bytes, int $maxBytes = self::MAX_BYTES): array
    {
        if ($bytes === '') {
            return self::fail(self::ERROR_TYPE);
        }
        if (strlen($bytes) > $maxBytes) {
            return self::fail(self::ERROR_SIZE);
        }

        if (self::looksLikeSvg($bytes)) {
            $clean = self::sanitizeSvg($bytes);

            return $clean === null ? self::fail(self::ERROR_SVG) : ['ok' => true, 'error' => null, 'mime' => self::SVG, 'extension' => 'svg', 'content' => $clean];
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes) ?: '';
        if (!isset(self::RASTER[$mime]) || @getimagesizefromstring($bytes) === false) {
            return self::fail(self::ERROR_TYPE);
        }

        return ['ok' => true, 'error' => null, 'mime' => $mime, 'extension' => self::RASTER[$mime], 'content' => $bytes];
    }

    /**
     * Validation rule (multipart uploads): fails with the reason unless the
     * file is an allowed image by its content. Use with 'file' and the
     * field's existing size rule.
     */
    public static function rule(int $maxBytes = self::MAX_BYTES): \Closure
    {
        return function ($attribute, $value, $fail) use ($maxBytes) {
            $result = self::fromFile($value instanceof UploadedFile ? $value : null, $maxBytes);
            if (!$result['ok']) {
                $fail($result['error']);
            }
        };
    }

    /**
     * Writes an inspected image as $directory/$filename (a generated name:
     * letters, digits, ".", "_", "-" only, ending in an image extension).
     */
    public static function store(array $image, string $directory, string $filename): void
    {
        if (empty($image['ok']) || !preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.(jpg|png|gif|webp|svg)$/', $filename)) {
            throw new \InvalidArgumentException('Refusing to store this image.');
        }
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        if (file_put_contents(rtrim($directory, '/') . '/' . $filename, $image['content'], LOCK_EX) === false) {
            throw new \RuntimeException('The image could not be saved.');
        }
    }

    public static function isSvg(?string $filename): bool
    {
        return is_string($filename) && str_ends_with(strtolower($filename), '.svg');
    }

    // ---- SVG ---------------------------------------------------------------

    private static function looksLikeSvg(string $bytes): bool
    {
        $head = strtolower(substr(ltrim($bytes, "\xEF\xBB\xBF \t\r\n"), 0, 4096));

        return str_starts_with($head, '<?xml') || str_starts_with($head, '<svg') || str_starts_with($head, '<!--') || str_starts_with($head, '<!doctype')
            ? str_contains($head, '<svg') || str_contains($head, '<!doctype')
            : false;
    }

    /**
     * Rebuilds an SVG from a whitelist: only the elements in SVG_ELEMENTS (in
     * the SVG namespace) survive; event handlers, foreign-namespace
     * attributes, xml:base and any reference other than a local "#id" (or an
     * embedded raster data: image on <image>) are dropped; CSS keeps no
     * external url()/@import/expression. DOCTYPE / entities (XXE, entity
     * expansion) are refused. Null = not a usable SVG.
     */
    public static function sanitizeSvg(string $svg): ?string
    {
        // Entities (XXE, entity expansion) are refused; a plain DOCTYPE line
        // (common in editor exports, no internal subset) is just dropped.
        if (preg_match('/<!ENTITY/i', $svg) || preg_match('/<!DOCTYPE[^>\[]*\[/i', $svg)) {
            return null;
        }
        $svg = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svg);

        $previous = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $loaded = $doc->loadXML($svg, LIBXML_NONET | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $loaded ? $doc->documentElement : null;
        if (!$root || strtolower($root->localName) !== 'svg' || !in_array($root->namespaceURI, [self::SVG_NS, null], true)) {
            return null;
        }

        self::cleanNode($root);

        // Processing instructions / comments outside the root (e.g. xml-stylesheet).
        foreach (iterator_to_array($doc->childNodes) as $node) {
            if ($node !== $root) {
                $doc->removeChild($node);
            }
        }
        // Namespace declarations other than SVG / xlink are unused now (their elements and attributes are gone).
        foreach (iterator_to_array((new \DOMXPath($doc))->query('namespace::*', $root)) as $ns) {
            if (!in_array($ns->prefix, ['', 'xml', 'xlink'], true) && $ns->nodeValue !== self::SVG_NS) {
                $root->removeAttributeNS($ns->nodeValue, $ns->prefix);
            }
        }
        if (!$root->hasAttribute('xmlns')) {
            $root->setAttribute('xmlns', self::SVG_NS);
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $doc->saveXML($root);
    }

    private static function cleanNode(\DOMElement $element): void
    {
        foreach (iterator_to_array($element->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $name = strtolower($child->localName);
                if (!in_array($child->namespaceURI, [self::SVG_NS, null], true) || !in_array($name, self::SVG_ELEMENTS, true)) {
                    $element->removeChild($child);
                    continue;
                }
                if ($name === 'style') {
                    $css = self::cleanCss($child->textContent);
                    while ($child->firstChild) {
                        $child->removeChild($child->firstChild);
                    }
                    $child->appendChild($child->ownerDocument->createTextNode($css));
                    self::cleanAttributes($child);
                    continue;
                }
                self::cleanAttributes($child);
                self::cleanNode($child);
            } elseif ($child instanceof \DOMProcessingInstruction || $child instanceof \DOMComment) {
                $element->removeChild($child);
            }
        }
        self::cleanAttributes($element);
    }

    private static function cleanAttributes(\DOMElement $element): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->localName);
            $ns = $attribute->namespaceURI;
            $value = (string) $attribute->value;
            $compact = strtolower(preg_replace('/[\x00-\x20]+/', '', $value));

            $isHref = $name === 'href' && ($ns === null || $ns === self::XLINK_NS);
            $keep = ($ns === null || $isHref || ($ns === 'http://www.w3.org/XML/1998/namespace' && $name === 'space'))
                && !str_starts_with($name, 'on')
                && $name !== 'base';

            if ($keep && $isHref) {
                $keep = str_starts_with($compact, '#')
                    || (strtolower($element->localName) === 'image' && preg_match('#^data:image/(png|jpe?g|gif|webp);base64,#', $compact));
            } elseif ($keep) {
                $keep = !preg_match('/javascript:|vbscript:|data:|expression\(|@import|-moz-binding|behavior:/', $compact)
                    && !preg_match('/url\((?!["\']?#)/', $compact);
            }

            if (!$keep) {
                $element->removeAttributeNode($attribute);
            }
        }
    }

    /** CSS of a <style> element: no imports, scripts or external url() references. */
    private static function cleanCss(string $css): string
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css);
        $css = preg_replace('/@import[^;]*;?/i', '', $css);
        $css = preg_replace('/url\(\s*(?!["\']?#)[^)]*\)/i', 'none', $css);
        // (Text is escaped when the SVG is written, so it can't break out of <style>.)
        return preg_replace('/expression\s*\(|javascript\s*:|vbscript\s*:|-moz-binding|behavior\s*:/i', '', $css);
    }

    private static function fail(string $error): array
    {
        return ['ok' => false, 'error' => $error, 'mime' => null, 'extension' => null, 'content' => null];
    }
}
