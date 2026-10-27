<?php
/**
 * Allowlist cleaner for rich text from the admin editor (Quill).
 * Keeps basic formatting and safe links; drops everything else, including
 * scripts, event handlers and inline styles. Text is always kept and escaped.
 */
class HtmlSanitizer
{
    // Tag => attributes allowed on it
    const ALLOWED = [
        'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'h1' => [], 'h2' => [], 'h3' => [], 'blockquote' => [],
        'a' => ['href'],
    ];

    // Removed together with their contents
    const DROP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'template', 'noscript', 'svg', 'math'];

    public static function clean($html)
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        // The meta tag makes libxml read the input as UTF-8
        $doc->loadHTML('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            return '';
        }

        self::cleanChildren($body);

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return trim($out);
    }

    /** True when the HTML has no visible text (e.g. Quill's empty "<p><br></p>"). */
    public static function isBlank($html)
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8')) === '';
    }

    private static function cleanChildren(DOMNode $parent)
    {
        // Copy first: the list changes as nodes are removed or unwrapped
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node instanceof DOMText) {
                continue;
            }
            if (!$node instanceof DOMElement) {
                $parent->removeChild($node); // comments, processing instructions
                continue;
            }

            $tag = strtolower($node->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $parent->removeChild($node);
                continue;
            }

            self::cleanChildren($node);

            if (!isset(self::ALLOWED[$tag])) {
                // Unknown tag (div, span, img, ...): keep its text, drop the tag
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            foreach (iterator_to_array($node->attributes) as $attr) {
                if (!in_array(strtolower($attr->name), self::ALLOWED[$tag], true)) {
                    $node->removeAttribute($attr->name);
                }
            }

            if ($tag === 'a') {
                $href = trim($node->getAttribute('href'));
                if (!preg_match('#^(https?://|mailto:)#i', $href)) {
                    $node->removeAttribute('href');
                } else {
                    $node->setAttribute('rel', 'noopener noreferrer nofollow');
                    $node->setAttribute('target', '_blank');
                }
            }
        }
    }
}
?>
