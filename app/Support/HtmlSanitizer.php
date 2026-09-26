<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Minimal allow-list HTML sanitizer for Partner-submitted Website Config
 * rich text - specifically the Privacy Policy / Terms of Service "body"
 * fields, the only Website Config values ever rendered unescaped
 * ({!! !!}) on the public site. Scoped exactly to what the Quill toolbar
 * in website-config-partial.blade.php can actually produce (see its
 * `legalToolbar` config) - nothing more. No existing sanitizer/purifier
 * package was in composer.json to reuse, and PHP's own DOMDocument is
 * always available, so this avoids adding a new dependency for something
 * this narrowly scoped.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = ['h2', 'h3', 'p', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote', 'ol', 'ul', 'li', 'a', 'br'];

    private const ALIGNABLE_TAGS = ['p', 'h2', 'h3', 'li', 'blockquote'];

    /**
     * Drops disallowed tags but keeps their text/children (a partner
     * pasting from Word/Google Docs shouldn't lose their words over an
     * unsupported wrapper tag); script/style/iframe/etc. are removed
     * ENTIRELY, content included, since those exist only to execute or
     * embed something. Every attribute is stripped except a validated
     * href on <a> and a validated text-align on the block-level tags the
     * "align" toolbar button can apply to (Quill's align format is
     * configured to emit inline style, not a ql-align-* class, precisely
     * so this survives sanitization and still renders without needing
     * Quill's own CSS on the public pages).
     */
    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $root = $dom->documentElement;

        if (!$root) {
            return '';
        }

        static::cleanChildren($dom, $root);

        $output = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }

    private static function cleanChildren(DOMDocument $dom, DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if (!$child instanceof DOMElement) {
                $node->removeChild($child);
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'img', 'svg', 'link', 'meta'], true)) {
                $node->removeChild($child);
                continue;
            }

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                static::cleanChildren($dom, $child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            static::sanitizeAttributes($child, $tag);
            static::cleanChildren($dom, $child);
        }
    }

    private static function sanitizeAttributes(DOMElement $el, string $tag): void
    {
        $href = $el->getAttribute('href');
        $style = $el->getAttribute('style');

        foreach (iterator_to_array($el->attributes) as $attr) {
            $el->removeAttribute($attr->name);
        }

        if ($tag === 'a') {
            $safeHref = static::sanitizeHref($href);
            if ($safeHref !== null) {
                $el->setAttribute('href', $safeHref);
            }
        }

        if (in_array($tag, self::ALIGNABLE_TAGS, true) && $style !== '' && preg_match('/text-align\s*:\s*(left|right|center|justify)/i', $style, $m)) {
            $el->setAttribute('style', 'text-align: ' . strtolower($m[1]) . ';');
        }
    }

    /**
     * Allows relative paths, fragments, http(s), mailto and tel; blocks
     * javascript:/data:/vbscript: (checked with control characters and
     * whitespace stripped first, so "java\tscript:" can't sneak past a
     * naive prefix check).
     */
    private static function sanitizeHref(string $href): ?string
    {
        $href = trim($href);

        if ($href === '') {
            return null;
        }

        $normalized = strtolower(preg_replace('/[\x00-\x1F\s]+/', '', $href));

        foreach (['javascript:', 'data:', 'vbscript:'] as $unsafeScheme) {
            if (str_starts_with($normalized, $unsafeScheme)) {
                return null;
            }
        }

        return $href;
    }
}
