=== Smart External Link Manager ===
Contributors: kraftysprouts
Tags: external links, seo, links, nofollow, security
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically detects and manages external links with icon insertion, link behavior customization, and comprehensive link processing.

== Description ==

Smart External Link Manager is a powerful WordPress plugin that automatically detects and manages external links throughout your website. It provides comprehensive control over how external links behave, appear, and function, improving both user experience and SEO.

= Key Features =

* **Automatic External Link Detection**: Intelligently identifies external links with domain comparison and www. normalization
* **Link Behavior Customization**: 
  * Open links in new tab (target="_blank")
  * Add nofollow attribute for SEO
  * Add noopener attribute for security
  * Custom CSS class assignment
* **Icon Integration**: 
  * Dashicons support (WordPress native icons)
  * Font Awesome compatibility
  * Custom icon support (HTML/SVG)
  * Flexible positioning (before/after link text)
* **Domain Management**: 
  * Exclude specific domains from processing
  * Wildcard subdomain exclusion
  * CSS class-based link exclusion
* **Post Type Control**: Enable/disable processing for specific post types
* **Custom Styling**: Add custom CSS for external links
* **Admin Interface**: User-friendly settings page in WordPress admin

= How It Works =

Once activated and configured, the plugin automatically processes external links in:
* Post content
* Page content
* Post excerpts
* Widget text

External links are automatically modified based on your settings without any additional code or shortcodes needed.

= Excluding Links =

You can exclude links in two ways:

1. **CSS Class Exclusion**: Add the class `no-external` to any link you want to exclude
2. **Domain Exclusion**: Add domains to the exclusion list in the plugin settings

= Customization =

External links automatically receive the class `selm-external-link` for custom styling. Icons receive the class `selm-external-icon`.

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Smart External Link Manager"
4. Click Install Now
5. Activate the plugin

= Manual Installation =

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New
4. Click Upload Plugin
5. Choose the zip file and click Install Now
6. Activate the plugin

= Via FTP =

1. Extract the plugin zip file
2. Upload the `smart-external-link-manager` folder to `/wp-content/plugins/`
3. Log in to your WordPress admin panel
4. Navigate to Plugins
5. Find "Smart External Link Manager" and click Activate

== Frequently Asked Questions ==

= Does this plugin work with page builders? =

Yes, the plugin processes links in content regardless of how they were added, including page builders like Elementor, Beaver Builder, etc.

= Will this slow down my site? =

No, the plugin uses efficient server-side processing and only runs on content that's being displayed. Performance impact is minimal.

= Can I exclude specific links? =

Yes, you can exclude links by adding the `no-external` CSS class or by adding domains to the exclusion list.

= Does it work with caching plugins? =

Yes, the plugin processes links server-side before content is cached, so it works with all caching plugins.

= What post types are supported? =

All public post types are supported. You can enable or disable processing for specific post types in the plugin settings.

= Can I customize the icon appearance? =

Yes, you can customize icons through CSS using the `.selm-external-icon` class, or use custom HTML/SVG icons.

== Screenshots ==

1. Plugin settings page with all configuration options
2. External link with icon displayed in content
3. Domain exclusion settings
4. Icon customization options

== Changelog ==

= 2.2.3 =
* Fixed WordPress.org Plugin Check compliance issues
* Removed deprecated load_plugin_textdomain() function
* Fixed all output escaping issues
* Replaced wp_redirect() with wp_safe_redirect() for security
* Fixed nonce verification and sanitization
* Updated tested up to WordPress 6.8

= 2.2.2 =
* Added developer branding in admin footer
* Updated settings menu title for clarity

= 2.2.1 =
* Added SVG icon support as default icon type
* Multiple SVG icon styles available
* Dynamic icon settings interface
* Deprecated Dashicons support

= 2.2.0 =
* Completely redesigned admin interface
* Modern card-based layout with tabbed navigation
* Custom SVG icons for admin interface
* Improved form controls and user experience

= 2.1.0 =
* Added client-side (JavaScript) processing mode
* Performance optimizations with object cache
* Batch processing for dynamic content
* Cache-friendly JavaScript handler

= 1.0.0 =
* Initial release
* Automatic external link detection
* Link behavior customization (target="_blank", nofollow, noopener)
* Icon integration (SVG, Font Awesome, Custom, Dashicons)
* Domain exclusion system
* CSS class-based exclusion
* Post type control
* Custom CSS support
* Admin settings interface

== Upgrade Notice ==

= 2.2.3 =
WordPress.org compliance update. All security and coding standard issues resolved.

= 2.2.0 =
Major admin interface redesign with improved user experience and modern design.

= 2.1.0 =
Added JavaScript processing mode for cache compatibility. Choose between PHP or JS processing.

== Support ==

For support, feature requests, or bug reports, please visit:
* Plugin URI: https://kraftysprouts.com/smart-external-link-manager
* Author URI: https://kraftysprouts.com

== Credits ==

Developed by Krafty Sprouts Media, LLC

