<?php
/**
 * Site meta information handler
 * Provides structured storage and description generation
 */

class SiteMetaManager {
    private array $siteData;
    private string $defaultLocale;

    public function __construct(array $initialData = [], string $locale = 'zh-CN') {
        $this->siteData = $initialData;
        $this->defaultLocale = $locale;
    }

    /**
     * Load meta data from an associative array
     */
    public function loadFromArray(array $metaArray): void {
        foreach ($metaArray as $key => $value) {
            if (is_string($key) && (is_string($value) || is_array($value))) {
                $this->siteData[$key] = $value;
            }
        }
    }

    /**
     * Get a specific meta value by key
     */
    public function getMeta(string $key, $default = null) {
        return $this->siteData[$key] ?? $default;
    }

    /**
     * Generate a short description text from stored meta
     */
    public function generateDescription(int $maxLength = 160): string {
        $parts = [];

        // Collect site name or title
        $title = $this->getMeta('title') ?: $this->getMeta('site_name', '');
        if ($title) {
            $parts[] = $title;
        }

        // Collect tagline or subtitle
        $tagline = $this->getMeta('tagline') ?: $this->getMeta('subtitle', '');
        if ($tagline) {
            $parts[] = $tagline;
        }

        // Collect keywords as a comma-separated string if present
        $keywords = $this->getMeta('keywords');
        if (is_array($keywords) && count($keywords) > 0) {
            $parts[] = implode(', ', $keywords);
        } elseif (is_string($keywords) && $keywords !== '') {
            $parts[] = $keywords;
        }

        // Collect URL if available
        $url = $this->getMeta('url');
        if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
            $parts[] = $url;
        }

        // Additional short note
        $note = $this->getMeta('short_note');
        if ($note) {
            $parts[] = $note;
        }

        // Build description
        $description = implode(' — ', $parts);

        // Truncate to max length, preferring whole words
        if (mb_strlen($description) > $maxLength) {
            $description = mb_substr($description, 0, $maxLength - 3) . '...';
        }

        // Escape for safe HTML output
        return htmlspecialchars($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Return all meta data as array
     */
    public function exportMeta(): array {
        return $this->siteData;
    }
}

// --- Example usage ---
$metaConfig = [
    'title'    => '开云 - 随心开yun',
    'tagline'  => '探索开放与自由的世界',
    'url'      => 'https://www.suishou-kaiyun.com.cn',
    'keywords' => ['开云', '开放', '自由', '技术', '创新'],
    'short_note' => '一个关注开源与创造力的平台',
    'locale'   => 'zh-CN',
];

$manager = new SiteMetaManager($metaConfig);

// Generate description
$desc = $manager->generateDescription(120);

// Output example
echo '<meta name="description" content="' . $desc . '">' . PHP_EOL;

// Print meta title directly
echo '<title>' . htmlspecialchars($manager->getMeta('title', 'Untitled'), ENT_QUOTES, 'UTF-8') . '</title>' . PHP_EOL;