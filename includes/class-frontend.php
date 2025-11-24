<?php
/**
 * Filename: class-frontend.php
 * Author: Krafty Sprouts Media, LLC
 * Created: 18/08/2025
 * Version: 2.2.3
 * Last Modified: 24/11/2025
 * Description: Frontend functionality for Smart External Link Manager
 *
 * @package SELM
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend functionality for Smart External Link Manager
 *
 * @since 1.0.0
 */
class SELM_Frontend {

	/**
	 * Core instance
	 *
	 * @var SELM_Core
	 * @since 1.0.0
	 */
	private $core;

	/**
	 * Constructor
	 *
	 * @param SELM_Core $core Core instance.
	 * @since 1.0.0
	 */
	public function __construct( $core ) {
		$this->core = $core;
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		if ( ! $this->core->is_enabled() ) {
			return;
		}

		// Frontend hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		
		// Only process server-side if PHP mode is enabled
		if ( ! $this->core->is_js_mode() ) {
			add_filter( 'the_content', array( $this, 'process_external_links' ), 20 );
			add_filter( 'the_excerpt', array( $this, 'process_external_links' ), 20 );
			add_filter( 'widget_text', array( $this, 'process_external_links' ), 20 );
		}
	}

	/**
	 * Enqueue frontend scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_scripts() {
		if ( ! $this->should_load_on_page() ) {
			return;
		}

		// Add inline CSS for external links
		$css = $this->generate_external_link_css();
		wp_add_inline_style( 'wp-block-library', $css );

		// Add custom CSS if provided
		$custom_css = $this->core->get_option( 'custom_css', '' );
		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( 'wp-block-library', $custom_css );
		}

		// Enqueue dashicons only if dashicon type is selected (deprecated)
		if ( $this->core->get_option( 'add_icon', true ) && 'dashicon' === $this->core->get_option( 'icon_type', 'svg' ) ) {
			wp_enqueue_style( 'dashicons' );
		}
		
		// Enqueue JavaScript handler if JS mode is enabled
		if ( $this->core->is_js_mode() ) {
			$this->enqueue_javascript_handler();
		}
	}
	
	/**
	 * Enqueue JavaScript handler for client-side processing
	 * Ported from KSM-ELM for cache-friendly processing
	 *
	 * @since 2.1.0
	 */
	private function enqueue_javascript_handler() {
		// Skip if not on frontend or if doing AJAX
		if ( is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
			return;
		}
		
		// Get script data (with caching)
		$script_data = $this->core->get_script_data();
		
		// Enqueue the external link handler script
		wp_enqueue_script(
			'selm-handler',
			SELM_ASSETS_URL . 'js/selm-handler.js',
			array(), // No dependencies for better performance
			SELM_VERSION,
			true // Load in footer
		);
		
		// Pass data to JavaScript
		wp_localize_script(
			'selm-handler',
			'selm_data',
			$script_data
		);
	}

	/**
	 * Process external links in content
	 *
	 * @param string $content Content to process.
	 * @return string Processed content
	 * @since 1.0.0
	 */
	public function process_external_links( $content ) {
		if ( ! $this->should_load_on_page() ) {
			return $content;
		}

		// Process all links in content
		$content = preg_replace_callback(
			'/<a\s+([^>]*?)href=["\']([^"\'>]+)["\']([^>]*?)>(.*?)<\/a>/i',
			array( $this, 'process_link_callback' ),
			$content
		);

		return $content;
	}

	/**
	 * Process individual link callback
	 *
	 * @param array $matches Regex matches.
	 * @return string Processed link HTML
	 * @since 1.0.0
	 */
	public function process_link_callback( $matches ) {
		$before_href = $matches[1];
		$url = $matches[2];
		$after_href = $matches[3];
		$link_text = $matches[4];

		// Reconstruct original link
		$original_link = '<a ' . $before_href . 'href="' . $url . '"' . $after_href . '>' . $link_text . '</a>';

		// Process the link
		return $this->core->process_external_link( $original_link, $url );
	}

	/**
	 * Generate CSS for external links
	 *
	 * @return string CSS
	 * @since 1.0.0
	 */
	private function generate_external_link_css() {
		$css = "
			.selm-external-link {
				position: relative;
			}
			
			.selm-external-icon {
				display: inline-block;
				vertical-align: middle;
				margin: 0 2px;
				font-size: 0.9em;
				line-height: 1;
			}
			
			.selm-external-icon.dashicons {
				width: 1em;
				height: 1em;
				font-size: 1em;
			}
			
			.selm-custom-icon {
				display: inline-block;
				vertical-align: middle;
			}
		";

		// Add icon type specific styles
		$icon_type = $this->core->get_option( 'icon_type', 'svg' );
		switch ( $icon_type ) {
			case 'fontawesome':
				$css .= "
					.selm-external-icon.fa,
					.selm-external-icon.fas,
					.selm-external-icon.far,
					.selm-external-icon.fab {
						font-size: 0.9em;
					}
				";
				break;
		}

		return $css;
	}

	/**
	 * Check if we should load on current page
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	private function should_load_on_page() {
		// Don't load in admin
		if ( is_admin() ) {
			return false;
		}

		// Check if plugin is enabled
		if ( ! $this->core->is_enabled() ) {
			return false;
		}

		// Check post type
		if ( is_singular() ) {
			$post_type = get_post_type();
			return $this->core->is_post_type_enabled( $post_type );
		}

		// Load on archive pages if any post type is enabled
		if ( is_home() || is_archive() || is_search() ) {
			$enabled_post_types = $this->core->get_option( 'post_types', array( 'post', 'page' ) );
			return ! empty( $enabled_post_types );
		}

		// Load on other pages (like widgets)
		return true;
	}

	/**
	 * Get external link statistics
	 *
	 * @param string $content Content to analyze.
	 * @return array Statistics
	 * @since 1.0.0
	 */
	public function get_external_link_stats( $content ) {
		$stats = array(
			'total_links' => 0,
			'external_links' => 0,
			'internal_links' => 0,
			'processed_links' => 0,
		);

		// Find all links
		preg_match_all( '/<a\s+[^>]*href=["\']([^"\'>]+)["\'][^>]*>/i', $content, $matches );
		$stats['total_links'] = count( $matches[0] );

		foreach ( $matches[1] as $url ) {
			if ( $this->core->is_external_url( $url ) ) {
				$stats['external_links']++;
				if ( ! $this->core->is_domain_excluded( $url ) ) {
					$stats['processed_links']++;
				}
			} else {
				$stats['internal_links']++;
			}
		}

		return $stats;
	}

	/**
	 * Extract all external domains from content
	 *
	 * @param string $content Content to analyze.
	 * @return array External domains
	 * @since 1.0.0
	 */
	public function extract_external_domains( $content ) {
		$domains = array();

		// Find all links
		preg_match_all( '/<a\s+[^>]*href=["\']([^"\'>]+)["\'][^>]*>/i', $content, $matches );

		foreach ( $matches[1] as $url ) {
			if ( $this->core->is_external_url( $url ) ) {
				$parsed_url = wp_parse_url( $url );
				if ( ! empty( $parsed_url['host'] ) ) {
					$domain = preg_replace( '/^www\./', '', $parsed_url['host'] );
					if ( ! in_array( $domain, $domains, true ) ) {
						$domains[] = $domain;
					}
				}
			}
		}

		return $domains;
	}
}