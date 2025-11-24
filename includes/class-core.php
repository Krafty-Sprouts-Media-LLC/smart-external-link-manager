<?php
/**
 * Filename: class-core.php
 * Author: Krafty Sprouts Media, LLC
 * Created: 18/08/2025
 * Version: 2.2.3
 * Last Modified: 24/11/2025
 * Description: Core functionality for Smart External Link Manager
 *
 * @package SELM
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core functionality for Smart External Link Manager
 *
 * @since 1.0.0
 */
class SELM_Core {

	/**
	 * Plugin options
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $options;

	/**
	 * Script data cache
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private static $script_data = null;
	
	/**
	 * Processing mode: 'php' for server-side, 'js' for client-side
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $processing_mode = 'php';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_options();
	}

	/**
	 * Load plugin options
	 *
	 * @since 1.0.0
	 */
	private function load_options() {
		$default_options = $this->get_default_options();
		$this->options = get_option( 'selm_settings', $default_options );
		$this->options = wp_parse_args( $this->options, $default_options );
	}

	/**
	 * Get default options
	 *
	 * @return array Default options
	 * @since 1.0.0
	 */
	public function get_default_options() {
		return array(
			'enabled' => true,
			'processing_mode' => 'php', // 'php' for server-side, 'js' for client-side
			'post_types' => array( 'post', 'page' ),
			'add_icon' => true,
			'icon_type' => 'svg', // 'svg', 'fontawesome', 'custom', 'dashicon' (deprecated)
			'icon_position' => 'after', // 'before', 'after'
			'icon_class' => 'selm-external-icon-svg',
			'icon_svg_file' => 'icon-external', // SVG file name without extension
			'custom_icon' => '',
			'open_new_tab' => true,
			'add_nofollow' => true,
			'add_noopener' => true,
			'exclude_domains' => array(),
			'exclude_classes' => array( 'no-external' ),
			'custom_css' => '',
			'debug_mode' => false,
		);
	}

	/**
	 * Check if plugin is enabled
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_enabled() {
		return (bool) $this->get_option( 'enabled', true );
	}

	/**
	 * Get plugin option
	 *
	 * @param string $key Option key.
	 * @param mixed  $default Default value.
	 * @return mixed Option value
	 * @since 1.0.0
	 */
	public function get_option( $key, $default = null ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : $default;
	}

	/**
	 * Get all options
	 *
	 * @return array All options
	 * @since 1.0.0
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Update options
	 *
	 * @param array $options New options.
	 * @return bool Success status
	 * @since 1.0.0
	 */
	public function update_options( $options ) {
		$this->options = $options;
		return update_option( 'selm_settings', $options );
	}

	/**
	 * Check if post type is enabled
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_post_type_enabled( $post_type ) {
		$enabled_post_types = $this->get_option( 'post_types', array( 'post', 'page' ) );
		return in_array( $post_type, $enabled_post_types, true );
	}

	/**
	 * Get available post types
	 *
	 * @return array Post types
	 * @since 1.0.0
	 */
	public function get_available_post_types() {
		return get_post_types( array( 'public' => true ), 'objects' );
	}

	/**
	 * Get available icon types
	 *
	 * @return array Icon types
	 * @since 1.0.0
	 */
	public function get_available_icon_types() {
		return array(
			'svg' => __( 'SVG Icon', 'smart-external-link-manager' ),
			'fontawesome' => __( 'Font Awesome', 'smart-external-link-manager' ),
			'custom' => __( 'Custom Icon', 'smart-external-link-manager' ),
			'dashicon' => __( 'Dashicons (Deprecated)', 'smart-external-link-manager' ),
		);
	}

	/**
	 * Get available icon positions
	 *
	 * @return array Icon positions
	 * @since 1.0.0
	 */
	public function get_available_icon_positions() {
		return array(
			'before' => __( 'Before Link Text', 'smart-external-link-manager' ),
			'after' => __( 'After Link Text', 'smart-external-link-manager' ),
		);
	}
	
	/**
	 * Get available SVG icon files for external links
	 *
	 * @return array SVG icon files
	 * @since 2.2.1
	 */
	public function get_available_svg_icons() {
		return array(
			'icon-external' => __( 'Box with Arrow (Default)', 'smart-external-link-manager' ),
			'icon-external-arrow' => __( 'Arrow in Box', 'smart-external-link-manager' ),
			'icon-external-simple' => __( 'Simple Arrow', 'smart-external-link-manager' ),
		);
	}

	/**
	 * Check if URL is external
	 *
	 * @param string $url URL to check.
	 * @return bool True if external, false otherwise
	 * @since 1.0.0
	 */
	public function is_external_url( $url ) {
		// Parse the URL
		$parsed_url = wp_parse_url( $url );
		if ( ! $parsed_url || empty( $parsed_url['host'] ) ) {
			return false;
		}

		// Get current site domain
		$site_url = wp_parse_url( home_url() );
		$site_host = $site_url['host'] ?? '';

		// Remove www. for comparison
		$url_host = preg_replace( '/^www\./', '', $parsed_url['host'] );
		$site_host = preg_replace( '/^www\./', '', $site_host );

		// Check if hosts are different
		return $url_host !== $site_host;
	}

	/**
	 * Check if domain is excluded
	 *
	 * @param string $url URL to check.
	 * @return bool True if excluded, false otherwise
	 * @since 1.0.0
	 */
	public function is_domain_excluded( $url ) {
		$excluded_domains = $this->get_option( 'exclude_domains', array() );
		if ( empty( $excluded_domains ) ) {
			return false;
		}

		$parsed_url = wp_parse_url( $url );
		if ( ! $parsed_url || empty( $parsed_url['host'] ) ) {
			return false;
		}

		$url_host = preg_replace( '/^www\./', '', $parsed_url['host'] );

		foreach ( $excluded_domains as $domain ) {
			$domain = trim( $domain );
			$domain = preg_replace( '/^www\./', '', $domain );
			if ( $url_host === $domain || strpos( $url_host, '.' . $domain ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if link has excluded class
	 *
	 * @param string $link_html Link HTML.
	 * @return bool True if excluded, false otherwise
	 * @since 1.0.0
	 */
	public function has_excluded_class( $link_html ) {
		$excluded_classes = $this->get_option( 'exclude_classes', array( 'no-external' ) );
		if ( empty( $excluded_classes ) ) {
			return false;
		}

		foreach ( $excluded_classes as $class ) {
			$class = trim( $class );
			if ( strpos( $link_html, 'class="' . $class . '"' ) !== false ||
				 strpos( $link_html, "class='{$class}'" ) !== false ||
				 strpos( $link_html, "class='{$class} " ) !== false ||
				 strpos( $link_html, "class=" . $class . " " ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate icon HTML
	 *
	 * @return string Icon HTML
	 * @since 1.0.0
	 */
	public function generate_icon_html() {
		if ( ! $this->get_option( 'add_icon', true ) ) {
			return '';
		}

		$icon_type = $this->get_option( 'icon_type', 'svg' );
		$icon_class = $this->get_option( 'icon_class', 'selm-external-icon-svg' );
		$custom_icon = $this->get_option( 'custom_icon', '' );
		$icon_svg_file = $this->get_option( 'icon_svg_file', 'icon-external' );

		switch ( $icon_type ) {
			case 'svg':
				return $this->get_svg_icon_html( $icon_svg_file, $icon_class );
			case 'fontawesome':
				return '<i class="' . esc_attr( $icon_class ) . ' selm-external-icon"></i>';
			case 'custom':
				if ( ! empty( $custom_icon ) ) {
					return '<span class="selm-external-icon selm-custom-icon">' . $custom_icon . '</span>';
				}
				return '';
			case 'dashicon':
				// Deprecated: Dashicons support
				return '<span class="dashicons ' . esc_attr( $icon_class ) . ' selm-external-icon"></span>';
			default:
				return $this->get_svg_icon_html( $icon_svg_file, $icon_class );
		}
	}
	
	/**
	 * Get SVG icon HTML
	 *
	 * @param string $icon_name Icon file name without extension.
	 * @param string $class CSS class.
	 * @return string SVG icon HTML
	 * @since 2.2.0
	 */
	private function get_svg_icon_html( $icon_name, $class = '' ) {
		$icon_file = SELM_PLUGIN_DIR . 'assets/images/' . sanitize_file_name( $icon_name ) . '.svg';
		
		if ( ! file_exists( $icon_file ) ) {
			// Fallback to default external icon
			$icon_file = SELM_PLUGIN_DIR . 'assets/images/icon-external.svg';
			if ( ! file_exists( $icon_file ) ) {
				return '';
			}
		}
		
		$svg_content = file_get_contents( $icon_file );
		if ( $svg_content ) {
			// Add class to SVG element
			$class_attr = 'selm-external-icon ' . esc_attr( $class );
			$svg_content = str_replace( '<svg', '<svg class="' . $class_attr . '"', $svg_content );
			return $svg_content;
		}
		
		return '';
	}

	/**
	 * Process external link
	 *
	 * @param string $link_html Original link HTML.
	 * @param string $url Link URL.
	 * @return string Processed link HTML
	 * @since 1.0.0
	 */
	public function process_external_link( $link_html, $url ) {
		// Check if we should process this link
		if ( ! $this->is_external_url( $url ) ||
			 $this->is_domain_excluded( $url ) ||
			 $this->has_excluded_class( $link_html ) ) {
			return $link_html;
		}

		// Add target="_blank" if enabled
		if ( $this->get_option( 'open_new_tab', true ) && strpos( $link_html, 'target=' ) === false ) {
			$link_html = str_replace( '<a ', '<a target="_blank" ', $link_html );
		}

		// Add rel attributes
		$rel_attributes = array();
		if ( $this->get_option( 'add_nofollow', true ) ) {
			$rel_attributes[] = 'nofollow';
		}
		if ( $this->get_option( 'add_noopener', true ) ) {
			$rel_attributes[] = 'noopener';
		}

		if ( ! empty( $rel_attributes ) ) {
			$rel_value = implode( ' ', $rel_attributes );
			if ( strpos( $link_html, 'rel=' ) !== false ) {
				// Add to existing rel attribute
				$link_html = preg_replace( '/rel=["\']([^"\']*)["\']/i', 'rel="$1 ' . $rel_value . '"', $link_html );
			} else {
				// Add new rel attribute
				$link_html = str_replace( '<a ', '<a rel="' . $rel_value . '" ', $link_html );
			}
		}

		// Add external link class
		if ( strpos( $link_html, 'class=' ) !== false ) {
			$link_html = preg_replace( '/class=["\']([^"\']*)["\']/i', 'class="$1 selm-external-link"', $link_html );
		} else {
			$link_html = str_replace( '<a ', '<a class="selm-external-link" ', $link_html );
		}

		// Add icon if enabled
		$icon_html = $this->generate_icon_html();
		if ( ! empty( $icon_html ) ) {
			$icon_position = $this->get_option( 'icon_position', 'after' );
			if ( 'before' === $icon_position ) {
				$link_html = preg_replace( '/(<a[^>]*>)/', '$1' . $icon_html . ' ', $link_html );
			} else {
				$link_html = preg_replace( '/(<\/a>)/', ' ' . $icon_html . '$1', $link_html );
			}
		}

		return $link_html;
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized data
	 * @since 1.0.0
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Boolean options
		$boolean_options = array(
			'enabled',
			'add_icon',
			'open_new_tab',
			'add_nofollow',
			'add_noopener',
			'debug_mode',
		);

		foreach ( $boolean_options as $option ) {
			$sanitized[ $option ] = isset( $input[ $option ] ) ? (bool) $input[ $option ] : false;
		}

		// Text options
		$sanitized['processing_mode'] = isset( $input['processing_mode'] ) && in_array( $input['processing_mode'], array( 'php', 'js' ), true ) ? sanitize_text_field( $input['processing_mode'] ) : 'php';
		$sanitized['icon_type'] = isset( $input['icon_type'] ) ? sanitize_text_field( $input['icon_type'] ) : 'svg';
		$sanitized['icon_position'] = isset( $input['icon_position'] ) ? sanitize_text_field( $input['icon_position'] ) : 'after';
		$sanitized['icon_class'] = isset( $input['icon_class'] ) ? sanitize_text_field( $input['icon_class'] ) : 'selm-external-icon-svg';
		$sanitized['icon_svg_file'] = isset( $input['icon_svg_file'] ) ? sanitize_file_name( $input['icon_svg_file'] ) : 'icon-external';
		$sanitized['custom_icon'] = isset( $input['custom_icon'] ) ? wp_kses_post( $input['custom_icon'] ) : '';
		$sanitized['custom_css'] = isset( $input['custom_css'] ) ? wp_strip_all_tags( $input['custom_css'] ) : '';

		// Array options
		$sanitized['post_types'] = isset( $input['post_types'] ) && is_array( $input['post_types'] ) ? array_map( 'sanitize_text_field', $input['post_types'] ) : array( 'post', 'page' );
		
		// Helper to sanitize array or newline-separated string
		$sanitize_array_input = function( $input_val ) {
			if ( is_array( $input_val ) ) {
				$items = $input_val;
			} else {
				$items = explode( "\n", (string) $input_val );
			}
			return array_filter( array_map( 'trim', array_map( 'sanitize_text_field', $items ) ) );
		};

		$sanitized['exclude_domains'] = isset( $input['exclude_domains'] ) ? $sanitize_array_input( $input['exclude_domains'] ) : array();
		$sanitized['exclude_classes'] = isset( $input['exclude_classes'] ) ? $sanitize_array_input( $input['exclude_classes'] ) : array( 'no-external' );

		return $sanitized;
	}

	/**
	 * Reset settings to defaults
	 *
	 * @return bool Success status
	 * @since 1.0.0
	 */
	public function reset_settings() {
		$default_options = $this->get_default_options();
		$this->options = $default_options;
		return update_option( 'selm_settings', $default_options );
	}

	/**
	 * Debug log
	 *
	 * @param string $message Log message.
	 * @since 1.0.0
	 */
	public function debug_log( $message ) {
		if ( $this->get_option( 'debug_mode', false ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[Smart External Link Manager] ' . $message );
		}
	}
	
	/**
	 * Get processing mode
	 *
	 * @return string Processing mode ('php' or 'js')
	 * @since 2.1.0
	 */
	public function get_processing_mode() {
		return $this->get_option( 'processing_mode', 'php' );
	}
	
	/**
	 * Check if using JavaScript processing mode
	 *
	 * @return bool
	 * @since 2.1.0
	 */
	public function is_js_mode() {
		return $this->get_processing_mode() === 'js';
	}
	
	/**
	 * Get script data with caching consideration
	 * Ported from KSM-ELM for performance optimization
	 *
	 * @return array Script data for JavaScript
	 * @since 2.1.0
	 */
	public function get_script_data() {
		// Use cached data if available
		if ( self::$script_data !== null ) {
			return self::$script_data;
		}
		
		// Try to get from object cache first (if available)
		$cache_key = 'selm_script_data_' . md5( home_url() );
		$cached_data = wp_cache_get( $cache_key, 'selm' );
		
		if ( $cached_data !== false ) {
			self::$script_data = $cached_data;
			return $cached_data;
		}
		
		// Generate data
		$home_url = home_url();
		$parsed_url = wp_parse_url( $home_url );
		
		$data = array(
			'site_host' => $parsed_url['host'] ?? '',
			'site_scheme' => $parsed_url['scheme'] ?? 'https',
		);
		
		// Add configuration if JS mode
		if ( $this->is_js_mode() ) {
			$data['config'] = array(
				'addIcon' => $this->get_option( 'add_icon', true ),
				'iconType' => $this->get_option( 'icon_type', 'svg' ),
				'iconClass' => $this->get_option( 'icon_class', 'selm-external-icon-svg' ),
				'iconSvgFile' => $this->get_option( 'icon_svg_file', 'icon-external' ),
				'iconPosition' => $this->get_option( 'icon_position', 'after' ),
				'customIcon' => $this->get_option( 'custom_icon', '' ),
				'addNofollow' => $this->get_option( 'add_nofollow', true ),
				'addNoopener' => $this->get_option( 'add_noopener', true ),
				'excludeClasses' => $this->get_option( 'exclude_classes', array( 'no-external' ) ),
				'excludeDomains' => $this->get_option( 'exclude_domains', array() ),
			);
		}
		
		// Cache for 1 hour (3600 seconds)
		wp_cache_set( $cache_key, $data, 'selm', 3600 );
		self::$script_data = $data;
		
		return $data;
	}
	
	/**
	 * Clear script data cache
	 *
	 * @since 2.1.0
	 */
	public function clear_script_data_cache() {
		self::$script_data = null;
		$cache_key = 'selm_script_data_' . md5( home_url() );
		wp_cache_delete( $cache_key, 'selm' );
	}
}