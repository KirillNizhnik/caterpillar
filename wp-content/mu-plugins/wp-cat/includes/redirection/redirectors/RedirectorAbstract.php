<?php

abstract class RedirectorAbstract
{
    protected array $parts;
    protected array $unnecessaryParts = [
        'equipment',
        'new-equipment',
        'used-equipment',
        'machinery',
        'new',
        'used'
    ];

    public function __construct(array $parts)
    {
        $this->parts = $this->stripParts($parts);
    }

    abstract public function redirect(): void;

    public function getMostSuitableUrl(string $taxonomy, string $postType): ?string
    {
        if (count($this->parts) === 0) {
            return null;
        }
        $lastPartIndex = count($this->parts) - 1;
        $terms = $this->getAvailableTerms($this->parts, $taxonomy);

        if (isset($terms[$lastPartIndex])) {
            return get_term_link($terms[$lastPartIndex], $taxonomy);
        }
        $postUrl = $this->getAvailablePostUrl($this->parts[$lastPartIndex], $postType);
        if ($postUrl !== null) {
            return $postUrl;
        }
        if (!empty($terms)) {
            return get_term_link($terms[count($terms) - 1], $taxonomy);
        }
        return null;
    }

    protected function getAvailableTerms(array $parts, string $taxonomy): array
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'slug' => $parts,
            'fields' => 'ids',
            'hide_empty'=> 0,
        ]);
        return is_array($terms) ? array_values($terms) : [];
    }

    protected function getAvailablePostUrl(string $slug, string $postType): ?string
    {
        $post = get_posts([
            'post_type' => $postType,
            'numberposts' => 1,
            'name' => $slug,
            'fields' => 'ids'
        ]);
        if (isset($post[0])) {
            return get_permalink($post[0]);
        }
        return null;
    }

    protected function makeRedirect($location, $status = 301): void
    {
        wp_redirect($location, $status, 'WP_CAT');
        exit();
    }

    protected function stripParts($parts): array
    {
        return array_values(array_diff($parts, $this->unnecessaryParts));
    }
}