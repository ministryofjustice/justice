<?php

namespace MOJ\Justice;

use DOMElement;

defined('ABSPATH') || exit;

class ContentLinks
{
    // Only use the extensions that we expect otherwise use the standard link component
    const array ALLOWED_EXTENSIONS = [
        'doc',
        'docx',
        'pdf',
        'ppt',
        'pptx',
        'xls',
        'xlsx',
        'zip',
    ];


    public static function isExternal(string $url): bool
    {
        // Use regex to check if the URL is absolute, does it start with 'http', 'https' or '//'
        $is_absolute_url = preg_match('/^(https?:\/\/|\/\/)/', $url);

        if (!$is_absolute_url) {
            // If it's not an absolute URL, we assume it's internal
            return false;
        }

        // Get the hostname of the URL
        $url_host = parse_url($url, PHP_URL_HOST);
        $home_host = parse_url(get_site_url(), PHP_URL_HOST);

        return ($url_host !== $home_host);
    }

    /**
     * Get the link parameters from a DOMElement.
     *
     * This is a wrapper around getLinkParams to extract the parameters from a DOMElement.
     *
     * @param DOMElement $node The DOMElement to extract parameters from.
     * @return array|null The link parameters, or null if the href is not set.
     */
    public static function getLinkParamsFromNode(\DOMElement $node): array|null
    {
        return self::getLinkParams(
            $node->getAttribute('href'),
            $node->nodeValue,
            $node->getAttribute('id'),
            $node->getAttribute('target')
        );
    }


    /**
     * Get the link parameters based on the URL, label, ID, and target.
     *
     * This method determines if the link is a file download or a standard link,
     * and returns the appropriate parameters for rendering.
     *
     * @param string|null $url The URL of the link.
     */
    public static function getLinkParams(
        string|null $url,
        string|null $label = null,
        string|null  $id = null,
        string|null $target = null
    ): array|null {
        $format = pathinfo($url, PATHINFO_EXTENSION);
        $external = self::isExternal($url);
        $url = self::prefixWithSitePath($url);

        if (in_array($format, self::ALLOWED_EXTENSIONS) && !$external) {
            // We are dealing with an internal download file
            return self::getFileDownloadParams($url, $label, $id);
        }

        return self::getStandardLinkParams($url, $label, $id, $target);
    }


    /**
     * Get the parameters for a standard link.
     *
     * This method determines the parameters for a standard link based on the URL, label, ID, and target.
     * It checks if the link is external, whether it should open in a new tab,
     * and whether the label already contains text indicating it opens in a new tab or window.
     *
     * @param string|null $url The URL of the link.
     * @param string|null $label The label for the link.
     * @param string|null $id The ID of the link.
     * @param string|null $target The target attribute of the link.
     * @return array The parameters for the standard link.
     */
    public static function getStandardLinkParams($url, $label = null, $id = null, $target = null): array
    {
        // Determine properties based upon the URL and label
        $external = self::isExternal($url);
        // If the URL is external, we assume it should open in a new tab
        $newTab = $external || $target === '_blank';
        // If the label is not provided, we use the filename as the label
        if (!$label) {
            $label = pathinfo($url, PATHINFO_FILENAME);
        }
        // TODO - act on this variable
        // If the label already has new tab/window then don't repeat it
        $manualNewTabText = (str_contains($label, 'new tab') || str_contains($label, 'new window'));

        return [
            // Pass the ID, unmodified.
            'id' => $id,
            'external' => $external,
            'label' => $label,
            'url' => $url,
            'new_tab' => $newTab,
            'manual_new_tab_text' => $manualNewTabText,
        ];
    }


    /**
     * Get the parameters for a file download link.
     *
     * This method determines the parameters for a file download link based on the URL, label, and ID.
     * It extracts the file format, calculates the file size, and retrieves the document ID.
     * If the label is not provided, it uses the filename from the URL.
     *
     * @param string $url The URL of the file.
     * @param string|null $label The label for the file download link.
     * @param string|null $id The ID of the file download link.
     * @return array The parameters for the file download link.
     */
    public static function getFileDownloadParams($url, $label = null, $id = null): array
    {
        $format = pathinfo($url, PATHINFO_EXTENSION);

        $label = $label ? trim($label) : null;

        // Get the document ID from the link
        $post_id = Documents::getDocumentIdByUrl($url);

        // If the label is empty, try to get it from the post title
        if (empty($label) && $post_id) {
            $label = get_the_title($post_id);
        }

        // If the label is still empty, use the filename from the URL
        if (empty($label)) {
            $label = pathinfo($url, PATHINFO_FILENAME);
        }

        return [
            // Pass the ID, unmodified.
            'id' => $id,
            'format' => strtoupper($format),
            'filesize' => Documents::getFormattedFilesize($post_id),
            'label' => $label,
            'url' => $url,
            'language' => null,
        ];
    }

    /**
     * Prefix root-relative URLs with the current site's multisite path.
     *
     * Handles content authored on prod multisite using absolute paths (e.g. "/page"),
     * then migrated to an environment where the site lives under a path prefix
     * (e.g. "/justice/page"). On subdomain or mapped-domain sites the site path
     * is "/", so the URL is returned unchanged.
     *
     * No-op for: empty/null URLs, external URLs, URLs not starting with "/",
     * non-multisite, root-hosted sites, and URLs already prefixed with the site path.
     *
     * @param string|null $url The URL of the link.
     * @return string|null The URL, prefixed with the site path if applicable.
     */
    public static function prefixWithSitePath($url)
    {
        // Do nothing, if:
        // - we have no URL,
        // - or we have an external URL
        // - or we don't have an absolute path
        // - or we are not on multisite
        if (!$url || self::isExternal($url) || !str_starts_with($url, '/') || !is_multisite()) {
            return $url;
        }

        global $current_blog;
        $site_path = rtrim($current_blog->path ?? '/', '/'); // "" or "/justice"

        // Do nothing, if we are on a site without a path prefix (e.g. subdomain or main site).
        if ($site_path === '') {
            return $url;
        }

        // Do nothing, if the URL is already prefixed with the site path.
        // The site path must be followed by a path boundary: end-of-string, "/", "?", or "#".
        // This correctly treats "/justice", "/justice/", "/justice?x=1" and "/justice#a" as
        // already-prefixed, while still prefixing e.g. "/justice-news" (different path segment).
        if (str_starts_with($url, $site_path)
            && preg_match('#^(/|\?|\#|$)#', substr($url, strlen($site_path)))
        ) {
            return $url;
        }

        // Here, we are on a multi-site install, with a path-style site. 
        // Append the site's path to the link's absolute path.
        return $site_path . $url;
    }
}
