<?php

defined('ABSPATH') || exit;

if (class_exists('PPVersionNotices\Module\TopNotice\Module')) {
    // Remove the 'revisionary' Upgrade to Pro notice
    add_filter(\PPVersionNotices\Module\TopNotice\Module::SETTINGS_FILTER, function ($settings) {
        if (isset($settings['revisionary']['message']) && str_contains($settings['revisionary']['message'], 'Upgrade to Pro')) {
            unset($settings['revisionary']);
        }

        return $settings;
    }, 99);
}

// Only allow pages for PublishPress Revisions (formerly Revisionary) plugin
add_filter('revisionary_enabled_post_types', fn () => ['page' => 1]);
add_filter('revisionary_archive_post_types', fn () => ['page' => 1]);

// Add the `rich-text` class to the Cookie Compliance user-facing settings page.
add_filter('cookie_compliance_settings_page_class', function($class_name) {
    return $class_name . ' rich-text';
});
