<?php

namespace App\Support;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allow-list sanitiser for admin-authored rich text (CMS pages).
 *
 * Keeps the formatting the Filament RichEditor produces (headings, paragraphs,
 * lists, links, basic inline formatting) and removes everything that could run
 * code in a visitor's browser: script/style/iframe/object/embed/svg/form
 * elements, every on* handler, style attributes and javascript:/data: URLs.
 * Unknown elements are unwrapped (their text survives), dangerous ones are
 * dropped with their content. Applied on save (Page::saving) and again on
 * render (page.blade.php), so legacy rows are covered too.
 */
class HtmlSanitizer
{
    /** @var array<string, array<int, string>> element => allowed attributes */
    private const ALLOWED = [
        'h2' => [], 'h3' => [], 'h4' => [],
        'p' => [], 'br' => [], 'hr' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [], 'sub' => [], 'sup' => [],
        'ul' => [], 'ol' => [], 'li' => [],
        'blockquote' => [],
        'a' => ['href', 'title', 'target', 'rel'],
    ];

    /** Elements removed together with their content. */
    private const DROP = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'svg', 'math',
        'form', 'input', 'textarea', 'select', 'button', 'link', 'meta', 'base', 'template', 'noscript', 'canvas', 'audio', 'video',
    ];

    public static function clean(?string $html): string
    {
        $html = (string) $html;

        if (trim($html) === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(
            '<?xml encoding="utf-8"?><div id="asv-sanitizer-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementById('asv-sanitizer-root');
        if (! $root) {
            return '';
        }

        static::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMComment) {
                $node->removeChild($child);

                continue;
            }

            if (! $child instanceof DOMElement) {
                continue; // text nodes are safe – they are escaped on output
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);

                continue;
            }

            if (! array_key_exists($tag, self::ALLOWED)) {
                // Unknown / disallowed wrapper (div, span, font, table…): keep the text, drop the element.
                static::walk($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                $name = strtolower($attribute->name);

                if (! in_array($name, self::ALLOWED[$tag], true)) {
                    $child->removeAttribute($attribute->name);

                    continue;
                }

                if ($name === 'href' && ! static::safeUrl($attribute->value)) {
                    $child->removeAttribute('href');
                }

                if ($name === 'target' && $attribute->value !== '_blank') {
                    $child->removeAttribute('target');
                }
            }

            if ($tag === 'a') {
                $child->removeAttribute('rel');
                if ($child->getAttribute('target') === '_blank') {
                    $child->setAttribute('rel', 'noopener');
                }
            }

            static::walk($child);
        }
    }

    /** Only http(s), mailto, tel, same-document anchors and site-relative paths may be linked. */
    public static function safeUrl(string $url): bool
    {
        $url = trim(preg_replace('/[\x00-\x20\x7f]+/', '', $url) ?? '');

        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '#') || (str_starts_with($url, '/') && ! str_starts_with($url, '//'))) {
            return true;
        }

        return (bool) preg_match('~^(https?:|mailto:|tel:)~i', $url);
    }
}
