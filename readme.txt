=== Avataurus ===
Contributors: ruzicic
Tags: avatar, gravatar, profile picture, identicon, user avatar
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replace default WordPress avatars with unique, deterministic face avatars. Every user gets a distinct, colorful face.

== Description ==

Avataurus replaces the default WordPress avatar (Gravatar mystery person, blank, etc.) with unique, colorful face avatars generated from each user's email address.

**How it works:** Each email address deterministically produces a unique combination of eye shape, mouth expression, and background color. Same email = same face, every time. No randomness, no external accounts needed.

**Two variants:**

* **Face** — Eyes + mouth expressions on colored backgrounds
* **Initial** — Eyes + first letter of the user's name in monospace font

**Key features:**

* Zero configuration — activate and go
* Works with comments, author pages, admin panels, and anywhere WordPress uses avatars
* Respects existing Gravatars — if a user has a real Gravatar, it's used instead
* Deterministic — same email always produces the same avatar
* Fast — avatars are served from Cloudflare's edge network (200+ locations)
* Lightweight — no JavaScript, no database changes, no bloat
* Privacy-friendly — no tracking, no cookies, no data collection

**Powered by [Avataurus](https://avataurus.com)** — an open source deterministic avatar generator.

== Installation ==

1. Upload the `avataurus` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu
3. Go to Settings → Discussion → Default Avatar
4. Select "Avataurus (Face)" or "Avataurus (Initial)"

That's it. All users without a Gravatar will now get a unique Avataurus face.

== Frequently Asked Questions ==

= Do I need an API key? =

No. Avataurus is free and open source. No API key, no account, no sign-up.

= Will this replace my existing Gravatars? =

No. If a user has a real Gravatar profile picture, it will be used. Avataurus only replaces the default fallback avatar (mystery person, blank, etc.).

= Is this GDPR compliant? =

Avataurus generates avatars based on a hash of the email address. The actual email is never sent to the service. No cookies, no tracking, no data storage.

= Can I use both variants on the same site? =

The default variant is set globally in Settings → Discussion. You can use the `avataurus_variant` filter to change it per context.

= How do I customize the avatar? =

Use the `avataurus_avatar_url` filter to modify the URL, or the `avataurus_seed` filter to change the seed used for generation.

== Screenshots ==

1. Avataurus face variant in comments
2. Avataurus initial variant in comments
3. Settings page — select your preferred variant

== Changelog ==

= 1.0.0 =
* Initial release
* Face and Initial avatar variants
* Gravatar fallback detection with caching
* Settings integration in Discussion page

== Upgrade Notice ==

= 1.0.0 =
Initial release. Activate and select Avataurus as your default avatar in Settings → Discussion.
