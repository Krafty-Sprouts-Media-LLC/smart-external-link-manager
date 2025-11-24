# Smart External Link Manager

**Version:** 1.0.0  
**Author:** Krafty Sprouts Media, LLC  
**Requires at least:** WordPress 5.0  
**Tested up to:** WordPress 6.4  
**Requires PHP:** 7.4 or higher  
**License:** GPL v2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

## Description

Smart External Link Manager is a powerful WordPress plugin that automatically detects and manages external links throughout your website. It provides comprehensive control over how external links behave, appear, and function, improving both user experience and SEO.

### Key Features

- **Automatic External Link Detection**: Intelligently identifies external links with domain comparison and www. normalization
- **Link Behavior Customization**: 
  - Open links in new tab (target="_blank")
  - Add nofollow attribute for SEO
  - Add noopener attribute for security
  - Custom CSS class assignment
- **Icon Integration**: 
  - Dashicons support (WordPress native icons)
  - Font Awesome compatibility
  - Custom icon support (HTML/SVG)
  - Flexible positioning (before/after link text)
- **Domain Management**: 
  - Exclude specific domains from processing
  - Wildcard subdomain exclusion
  - CSS class-based link exclusion
- **Post Type Control**: Enable/disable processing for specific post types
- **Custom Styling**: Add custom CSS for external links
- **Admin Interface**: User-friendly settings page in WordPress admin

## Installation

### Manual Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin**
5. Choose the zip file and click **Install Now**
6. Activate the plugin

### Via FTP

1. Extract the plugin zip file
2. Upload the `smart-external-link-manager` folder to `/wp-content/plugins/`
3. Log in to your WordPress admin panel
4. Navigate to **Plugins**
5. Find "Smart External Link Manager" and click **Activate**

## Configuration

After activation, configure the plugin:

1. Go to **Settings > External Links** in your WordPress admin
2. Configure the following options:

### General Settings
- **Enable External Link Manager**: Toggle the plugin on/off
- **Post Types**: Select which post types should process external links

### Link Behavior
- **Open in new tab**: Automatically add `target="_blank"` to external links
- **Add nofollow attribute**: Add `rel="nofollow"` for SEO purposes
- **Add noopener attribute**: Add `rel="noopener"` for security

### Icon Settings
- **Add External Link Icon**: Enable/disable icon display
- **Icon Type**: Choose between Dashicons, Font Awesome, or Custom
- **Icon Class**: Specify the CSS class for the icon
- **Icon Position**: Place icon before or after link text

### Domain Exclusions
- Add domains to exclude from external link processing
- Use wildcards for subdomain exclusion

### Custom CSS
- Add custom styles for external links and icons

## Usage

Once configured, the plugin automatically processes external links in:
- Post content
- Page content
- Post excerpts
- Widget text

External links will be automatically modified based on your settings without any additional code or shortcodes needed.

## Excluding Links

### Method 1: CSS Class Exclusion
Add the class `no-external` to any link you want to exclude:
```html
<a href="https://example.com" class="no-external">Excluded Link</a>
```

### Method 2: Domain Exclusion
Add domains to the exclusion list in the plugin settings.

## Customization

### Custom CSS Classes
External links automatically receive the class `selm-external-link` for custom styling:
```css
.selm-external-link {
    color: #0066cc;
    text-decoration: underline;
}
```

### Icon Styling
Icons receive the class `selm-external-icon`:
```css
.selm-external-icon {
    margin-left: 5px;
    opacity: 0.7;
}
```

## File Structure

```
smart-external-link-manager/
├── assets/
│   ├── css/          # CSS files
│   ├── js/           # JavaScript files
│   └── images/       # Image files
├── includes/
│   ├── class-admin.php      # Admin interface
│   ├── class-core.php        # Core functionality
│   └── class-frontend.php    # Frontend processing
├── languages/        # Translation files
├── CHANGELOG.md      # Version history
├── README.md         # This file
└── smart-external-link-manager.php  # Main plugin file
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher (or MariaDB equivalent)

## Frequently Asked Questions

### Does this plugin work with page builders?
Yes, the plugin processes links in content regardless of how they were added, including page builders like Elementor, Beaver Builder, etc.

### Will this slow down my site?
No, the plugin uses efficient server-side processing and only runs on content that's being displayed. Performance impact is minimal.

### Can I exclude specific links?
Yes, you can exclude links by adding the `no-external` CSS class or by adding domains to the exclusion list.

### Does it work with caching plugins?
Yes, the plugin processes links server-side before content is cached, so it works with all caching plugins.

## Support

For support, feature requests, or bug reports, please visit:
- **Plugin URI**: https://kraftysprouts.com/smart-external-link-manager
- **Author URI**: https://kraftysprouts.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes.

## Credits

Developed by **Krafty Sprouts Media, LLC**

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Krafty Sprouts Media, LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

