# Plugin Icons

This folder contains SVG icons used throughout the Smart External Link Manager plugin.

## External Link Icons (for marking external links on frontend)

- `icon-external.svg` - Box with arrow (default external link icon)
- `icon-external-arrow.svg` - Arrow in box (alternative external link icon)
- `icon-external-simple.svg` - Simple arrow (minimal external link icon)

## Admin Interface Icons

- `icon-plugin.svg` - Main plugin icon (used in admin header)
- `icon-general.svg` - General settings icon
- `icon-behavior.svg` - Link behavior settings icon
- `icon-icons.svg` - Icon settings icon
- `icon-exclusions.svg` - Exclusions settings icon
- `icon-advanced.svg` - Advanced settings icon

## Plugin Icons (for WordPress.org)

- `icon-256x256.svg` - Plugin icon source (256x256) - Source file for WordPress.org submission
- `icon-128x128.svg` - Plugin icon source (128x128) - Source file for WordPress.org submission

**WordPress.org Submission Requirements:**
- These SVG files are source files for creating the plugin icon
- Convert to PNG format: `icon-256x256.png` and `icon-128x128.png`
- PNG files must be placed in the **plugin root directory** (not in assets/images)
- WordPress.org requires exact filenames: `icon-256x256.png` and `icon-128x128.png`
- The design should represent the plugin (can be based on `icon-plugin.svg` or a custom design)
- File naming is correct - no need to rename these source files

## Converting SVG to PNG for WordPress.org

WordPress.org requires PNG format for plugin icons. To convert:

1. Use an online converter or image editor
2. Convert `icon-256x256.svg` to `icon-256x256.png` (256x256 pixels)
3. Place the PNG file in the plugin root directory
4. WordPress.org will automatically use it as the plugin icon

## Usage

Icons are loaded via the `get_svg_icon()` method in the Admin class, which:
- Reads the SVG file content
- Adds appropriate CSS classes
- Returns inline SVG HTML for optimal performance

