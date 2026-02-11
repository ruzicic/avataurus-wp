# Avataurus for WordPress

Replace default WordPress avatars with unique, deterministic face avatars.

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/avataurus)](https://wordpress.org/plugins/avataurus/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/avataurus)](https://wordpress.org/plugins/avataurus/)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)](https://www.gnu.org/licenses/gpl-2.0.html)

## How It Works

Each user's email address deterministically produces a unique avatar with distinct eyes, mouth, and background color. Same email, same face, every time.

Powered by [Avataurus](https://avataurus.com) — an open source deterministic avatar generator.

## Install

1. Upload the `avataurus` folder to `/wp-content/plugins/`
2. Activate the plugin
3. Go to **Settings > Discussion > Default Avatar**
4. Select **Avataurus (Face)** or **Avataurus (Initial)**

## Features

- **Zero config** — activate and go
- **Respects Gravatars** — real Gravatar profile pictures are preserved
- **Deterministic** — same email = same avatar, always
- **Fast** — served from Cloudflare's edge (200+ locations)
- **Privacy-friendly** — no tracking, no cookies, no data collection
- **Clean uninstall** — removes all traces when deactivated

## Filters

```php
// Change the variant per context
add_filter( 'avataurus_variant', function( $variant, $id_or_email ) {
    // Use initials in admin, faces on frontend
    return is_admin() ? 'initial' : 'face';
}, 10, 2 );

// Modify the seed
add_filter( 'avataurus_seed', function( $seed, $id_or_email ) {
    return $seed;
}, 10, 2 );

// Modify the full avatar URL
add_filter( 'avataurus_avatar_url', function( $url, $id_or_email, $args ) {
    return $url;
}, 10, 3 );
```

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) — as required for WordPress.org plugins.
