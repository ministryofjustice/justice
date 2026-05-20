# Justice GovUK – WordPress Theme

A WordPress theme designed to display public-facing, procedural information for the **Ministry of Justice, UK**.

- **Version:** 10.1.2
- **Author:** Ministry of Justice | Justice Digital
- **Theme URI:** https://www.justice.gov.uk/
- **Repository:** https://github.com/ministryofjustice/justice
- **License:** MIT

---

## Requirements

| Requirement | Minimum Version |
|---|---|
| WordPress | 6.0 |
| PHP | 8.0 |
| Node.js | (see `.npmrc`) |

---

## Installation

1. Download or clone the repository into your WordPress themes directory:
   ```
   wp-content/themes/justice/
   ```
2. Install Node dependencies:
   ```bash
   npm install
   ```
3. Build assets (see [Asset Compilation](#asset-compilation) below).
4. Activate the theme from the WordPress admin panel under **Appearance → Themes**.

---

## Asset Compilation

This theme uses [Laravel Mix](https://laravel-mix.com/) with WordPress block support via `@tinypixelco/laravel-mix-wp-blocks`.

### Available commands

| Command | Description |
|---|---|
| `npm run dev` | One-time development build |
| `npm run watch` | Watch for changes and rebuild |
| `npm run hot` | Hot module replacement |
| `npm run staging` | Production build (alias) |
| `npm run production` | Minified production build with versioning |

Compiled assets are output to the `dist/` directory.

---

## Theme Structure

```
justice/
├── dist/                   # Compiled assets (generated)
├── inc/                    # PHP includes and feature modules
│   ├── acf/                # Advanced Custom Fields configuration
│   ├── content-quality/    # Content quality tooling
│   ├── documents/          # Document handling
│   ├── post-meta/          # Custom post meta
│   └── ...
├── src/                    # Source files
│   ├── components/         # SCSS and JS for individual UI components
│   ├── img/                # Images and favicon assets
│   ├── js/                 # JavaScript source files
│   ├── sass/               # SCSS source files
│   └── archived/           # Legacy CSS and JS (reference only)
├── template-parts/         # Reusable template partials
├── error-pages/            # Custom error page templates
├── acf-json/               # ACF field group JSON (local sync)
├── functions.php           # Theme bootstrap
├── style.css               # Theme header
├── header.php              # Site header
├── footer.php              # Site footer
├── index.php               # Main template
├── page.php                # Page template
├── page_home.php           # Homepage template
├── search.php              # Search results template
├── 404.php                 # 404 error template
├── sidebar.php             # Default sidebar
├── sidebar-right.php       # Right sidebar
├── layout-one-sidebar.php  # One-sidebar page layout
├── layout-two-sidebar.php  # Two-sidebar page layout
└── webpack.mix.js          # Laravel Mix build config
```

---

## Features

- **Page-centric content model** — the WordPress Posts post type is disabled; all content is managed as Pages.
- **Custom Block Editor components** — bespoke Gutenberg blocks including a sidebar block, search bar block, inline list block, navigation blocks, and more.
- **Dynamic navigation** — header and footer menu registration with a dynamic menu system and secondary navigation.
- **Breadcrumbs** — automatically generated breadcrumb trails for page hierarchies and search results.
- **Advanced Custom Fields (ACF)** — ACF integration with local JSON field group sync.
- **Amazon S3 / Offload Media support** — built-in asset URL rewriting for WP Offload Media, with optional MinIO compatibility.
- **Admin branding** — custom MoJ branding applied to the WordPress admin.
- **Nginx cache integration** — cache-purging support for Nginx-based hosting.
- **Security hardening** — custom security measures applied via `inc/security.php`.
- **Sitemap support** — custom sitemap generation.
- **Redirect management** — centralised redirect handling.
- **Search** — enhanced search functionality with parent-page filtering.
- **Filterable inline scripts** — extended `WP_Scripts` class for fine-grained control over localised script output.
- **WP-CLI commands** — custom WP-CLI commands available when running in CLI context.
- **Content quality tooling** — tooling to help maintain editorial standards.
- **Debug mode** — additional debug output when `WP_ENV=development`.

---

## Environment Variables

The theme responds to the following environment variables (managed via [Roots WPConfig](https://roots.io/bedrock/)):

| Variable | Description |
|---|---|
| `WP_ENV` | Set to `development` to enable debug mode |
| `WP_OFFLOAD_MEDIA_PRESET` | Set to `minio` to enable MinIO-compatible S3 asset handling |

---

## Block Editor Components

Custom block components are located in `src/components/` and compiled via `src/js/block-editor.js`. Components include:

- **Sidebar Block** – page sidebar with MoJ branding
- **Search Bar Block** – custom search input for the block editor
- **Navigation (Main & Secondary)** – site navigation blocks
- **Inline List Block** – horizontal list component
- **To The Top** – back-to-top button
- **Core Rich Text extensions** – anchor links and underline formatting
- **Simple Definition List Blocks** – accessible definition list support
- **Image with Text** – combined image and text layout
- **File Download** – file download link component

---

## Menus

Register the following menus in **Appearance → Menus**:

| Menu Location | Description |
|---|---|
| `header-menu` | Primary site navigation (header) |
| `footer-menu` | Footer navigation links |

---

## Contributing

Issues and pull requests should be submitted via the GitHub repository:
https://github.com/ministryofjustice/justice/issues

---

## License

This theme is released under the [MIT License](https://github.com/ministryofjustice/justice-gov-uk/blob/main/LICENSE).
