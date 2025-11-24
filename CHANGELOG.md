# Changelog

All notable changes to Smart External Link Manager will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.3] - 24/11/2025

### Fixed
- **WordPress.org Plugin Check Compliance**: Fixed all issues flagged by Plugin Check tool
  - Removed `.gitignore` file (hidden files not permitted for WordPress.org)
  - Removed invalid "Network" header from plugin file
  - Removed "Domain Path" header (WordPress.org handles translations automatically)
  - Removed deprecated `load_plugin_textdomain()` function call
  - Fixed all output escaping issues in admin class (escaped all `__()` and `$this` outputs)
  - Replaced `wp_redirect()` with `wp_safe_redirect()` for security
  - Fixed nonce verification and sanitization (added `wp_unslash()` before sanitization)
  - Updated `readme.txt`: Tested up to WordPress 6.8, fixed stable tag to 2.2.0
  - Limited tags to 5 in `readme.txt` (removed excess tags)

### Security
- **Improved Security**: Enhanced nonce handling and input sanitization
  - All nonce values now properly unslashed before verification
  - All GET/POST inputs properly sanitized and unslashed
  - All admin outputs properly escaped

## [2.2.2] - 2025-11-24

### Added
- **Developer Branding**: Added Krafty Sprouts Media, LLC branding in admin footer
  - Professional footer section with company attribution
  - Clickable company name linking to kraftysprouts.com
  - Clean, minimal design that matches admin interface

### Changed
- **Settings Menu Title**: Updated menu item title for clarity
  - Changed from "External Links" to "Smart External Link Manager"
  - Now shows full plugin name in Settings menu for better identification
  - Improved user experience with clearer menu labeling

## [2.2.1] - 2025-11-24

### Added
- **SVG Icon Support**: SVG icons now the default icon type for external links
  - SVG icon file selector in admin settings with multiple icon options
  - Three external link icon styles: Box with Arrow (default), Arrow in Box, Simple Arrow
  - New `icon_svg_file` option for selecting SVG icons
  - `get_svg_icon_html()` helper method for SVG icon generation
  - `get_available_svg_icons()` method for managing external link icon options
- **Dynamic Icon Settings**: Context-aware icon settings interface
  - Fields show/hide based on selected icon type
  - SVG file selector appears when SVG is selected
  - Icon class field shows for Font Awesome and Dashicons
  - Custom icon textarea shows for custom icon type
  - Context-specific help text for each icon type
- **Separated Plugin and External Link Icons**: Clear distinction between plugin branding and external link indicators
  - `icon-plugin.svg` - Main plugin icon (used in admin header and branding)
  - External link icons - Separate icons specifically for marking external links on frontend
  - Updated admin header to use plugin icon instead of external link icon

### Changed
- **Default Icon Type**: Changed from Dashicons to SVG icons
  - New installations default to SVG icon type
  - Default icon class changed to `selm-external-icon-svg`
  - Improved performance and compatibility with SVG icons
- **Icon Type Options**: Reordered icon types with SVG as primary option
  - SVG Icon (new default)
  - Font Awesome
  - Custom Icon
  - Dashicons (moved to last position, marked as deprecated)
- **JavaScript Handler**: Updated to support SVG icons
  - Added `selm_get_svg_icon()` function for SVG icon generation
  - Updated default config to use SVG instead of Dashicons
  - SVG icon support in client-side processing mode
- **Icon Organization**: Separated plugin identity icons from functional external link icons
  - Plugin icon (`icon-plugin.svg`) used for branding and admin interface
  - External link icons (`icon-external*.svg`) used for marking external links on frontend
  - Clear documentation of icon purposes in README

### Deprecated
- **Dashicons Support**: Dashicons icon type is now deprecated
  - Deprecation notice displayed when Dashicons is selected
  - Warning message recommends switching to SVG icons
  - Dashicons will be removed in a future version
  - Dashicons CSS only enqueued when explicitly selected

## [2.2.0] - 2025-11-24

### Added
- **New Admin Interface**: Completely redesigned admin settings page for better user experience
  - Modern card-based layout
  - Tabbed navigation for easy access to sections
  - Improved form controls with toggle switches
  - Responsive design for mobile compatibility
- **Admin Styles**: Dedicated CSS for the settings page
- **SVG Icon System**: Replaced all dashicons with custom SVG icons
  - Custom SVG icons for all admin interface elements
  - Plugin icon SVG files (256x256 and 128x128) for WordPress.org submission
  - Inline SVG loading for optimal performance
  - `get_svg_icon()` helper method for easy icon management

### Changed
- **Settings Organization**: Grouped settings into logical sections (General, Behavior, Icons, Exclusions, Advanced)
- **Input Handling**: Improved handling of newline-separated text areas for exclusions
- **Icon System**: Replaced WordPress dashicons with custom SVG icons throughout admin interface
  - Header icon now uses custom SVG
  - All navigation menu icons converted to SVG
  - Removed dashicons CSS dependency from admin interface
  - Improved icon styling and color transitions

## [2.1.0] - 2025-11-24

### Added
- **Client-Side Processing Mode**: New JavaScript-based processing option for cache-friendly link management
  - Choose between server-side (PHP) or client-side (JavaScript) processing modes
  - JavaScript mode works with cached HTML pages
  - Automatic processing of dynamically loaded content via MutationObserver
  - Batch processing (50 links per frame) to prevent UI blocking
  - WeakSet implementation for memory-efficient link tracking
- **Performance Optimizations**: Ported from KSM-ELM
  - Object cache integration for script data (1-hour cache duration)
  - Static property caching for request-level performance
  - Minimal hook usage with early conditional checks
  - Cache group management on activation/deactivation
- **JavaScript Handler**: Complete vanilla JavaScript implementation
  - No jQuery dependency for better performance
  - Debounced MutationObserver for dynamic content
  - Supports all SELM features (icons, exclusions, domain filtering)
  - Automatic cleanup on page unload
- **Processing Mode Setting**: Admin option to switch between PHP and JS modes
  - Server-Side (PHP): Processes links before page output, better for SEO
  - Client-Side (JavaScript): Cache-friendly, processes after page load
  - Helpful description explaining the difference between modes

### Changed
- **Cache Management**: Enhanced cache clearing on plugin activation/deactivation/uninstall
  - Object cache group flushing (ported from KSM-ELM)
  - Proper cache key generation with domain hashing
  - Graceful fallback if object cache unavailable

### Technical Improvements
- **Script Data Caching**: New `get_script_data()` method with object cache support
- **Conditional Hook Registration**: PHP processing hooks only register when PHP mode is enabled
- **JavaScript Localization**: Enhanced script data passing with full configuration support
- **Memory Efficiency**: WeakSet usage prevents memory leaks in JavaScript processing

## [1.0.0] - 2025-08-18

### Added
- **Initial Release**: Complete external link management for WordPress
- **Automatic Link Detection**: Smart identification of external links
  - Intelligent domain comparison with www. normalization
  - Automatic exclusion of internal links and subdomains
  - Real-time link processing in content, excerpts, and widgets
- **Link Behavior Customization**: Comprehensive link attribute management
  - Open in new tab (target="_blank") option
  - Automatic nofollow attribute addition
  - Automatic noopener attribute for security
  - Custom CSS class assignment (selm-external-link)
- **Icon Integration**: Visual external link indicators
  - Dashicons support with built-in WordPress icons
  - Font Awesome compatibility for modern icons
  - Custom icon support with HTML/SVG
  - Flexible icon positioning (before/after link text)
  - Configurable icon classes and styling
- **Domain Management**: Advanced exclusion system
  - Exclude specific domains from processing
  - Wildcard subdomain exclusion support
  - CSS class-based link exclusion
  - Flexible domain matching algorithms
- **Content Processing**: Universal content compatibility
  - Post content processing
  - Excerpt processing
  - Widget text processing
  - Custom content filter support

### Settings & Configuration
- **General Settings**:
  - Enable/disable external link processing
  - Post type selection
  - Debug mode for troubleshooting
- **Link Behavior**:
  - New tab opening control
  - Nofollow attribute management
  - Noopener security attribute
  - Custom link attributes
- **Icon Settings**:
  - Icon type selection (Dashicons, Font Awesome, Custom)
  - Icon position control (before/after text)
  - Custom icon class configuration
  - Icon display toggle
- **Exclusion Settings**:
  - Domain exclusion list
  - CSS class exclusion list
  - Advanced filtering options
- **Styling Options**:
  - Custom CSS input
  - Icon styling control
  - Link appearance customization

### Technical Features
- **Performance Optimized**: Efficient link processing
  - Regex-based link detection
  - Minimal DOM manipulation
  - Conditional script loading
  - Smart caching strategies
- **WordPress Integration**: Seamless WordPress compatibility
  - Native WordPress hooks and filters
  - Post type compatibility
  - Widget integration
  - Theme compatibility
- **Security**: Built with WordPress security standards
  - Nonce verification
  - Capability checks
  - Data sanitization
  - XSS protection
  - Secure link attributes (noopener)
- **Extensibility**: Developer-friendly architecture
  - Action and filter hooks
  - Object-oriented design
  - Modular components
  - Clean API structure

### Admin Interface
- **Settings Page**: Complete configuration under Settings → External Links
- **Post Type Selection**: WordPress native post type picker
- **Icon Preview**: Visual icon selection and preview
- **Quick Actions**:
  - Reset to defaults
  - Save settings
- **Status Indicators**: Real-time plugin status display
- **Validation**: Form validation and error handling

### Link Processing
- **Smart Detection**: Advanced URL analysis
  - Protocol-aware detection (http/https)
  - Subdomain handling
  - Port number support
  - Query parameter preservation
- **Attribute Management**: Comprehensive link enhancement
  - Target attribute injection
  - Rel attribute management
  - Class attribute addition
  - Custom attribute support
- **Icon Insertion**: Flexible icon placement
  - Before/after text positioning
  - Multiple icon format support
  - CSS-based styling
  - Responsive design compatibility

### Content Compatibility
- **Universal Processing**: Works with all content types
  - Post content (the_content)
  - Post excerpts (the_excerpt)
  - Widget text content
  - Custom content areas
- **Filter Integration**: WordPress filter system
  - Priority-based processing
  - Multiple filter support
  - Custom filter hooks
  - Theme compatibility

### Icon System
- **Dashicons Support**: WordPress native icons
  - Built-in external link icons
  - Consistent WordPress styling
  - No additional dependencies
  - Automatic enqueuing
- **Font Awesome Support**: Modern icon library
  - Extensive icon selection
  - Multiple icon styles (solid, regular, brands)
  - Custom CSS class support
  - Version compatibility
- **Custom Icons**: Flexible icon options
  - HTML/SVG support
  - Image-based icons
  - Custom styling
  - Brand-specific icons

### Domain Exclusion
- **Flexible Matching**: Advanced domain filtering
  - Exact domain matching
  - Subdomain exclusion
  - Wildcard support
  - Case-insensitive matching
- **Management Interface**: Easy domain management
  - Add/remove domains
  - Bulk domain import
  - Domain validation
  - Preview excluded links

### CSS Integration
- **Automatic Styling**: Built-in CSS classes
  - .selm-external-link for all external links
  - .selm-external-icon for icons
  - .selm-custom-icon for custom icons
  - Responsive design support
- **Custom Styling**: Advanced customization
  - Custom CSS input field
  - Theme integration
  - Style inheritance
  - Mobile optimization

### Technical Details
- **WordPress Compatibility**: 5.0+
- **PHP Compatibility**: 7.4+
- **Architecture**: Object-oriented with clean separation of concerns
- **Standards**: WordPress coding standards compliant
- **Translation Ready**: Full internationalization support
- **Performance**: Optimized for speed and efficiency

### Error Handling
- **Graceful Degradation**: Robust error management
  - Invalid URL handling
  - Missing icon fallbacks
  - Network error recovery
  - Debug logging system
- **Validation**: Comprehensive input validation
  - URL format validation
  - Domain validation
  - CSS class validation
  - Icon format validation

---

**Initial release of Smart External Link Manager - External Link Processing Pro**

Smart External Link Manager provides comprehensive external link management with automatic detection, icon insertion, behavior customization, and advanced filtering. Built for modern WordPress sites with performance, security, and user experience in mind.