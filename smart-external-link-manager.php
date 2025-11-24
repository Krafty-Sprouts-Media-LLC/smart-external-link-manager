<?php
/**
 * Plugin Name: Smart External Link Manager
 * Plugin URI: https://kraftysprouts.com/smart-external-link-manager
 * Description: Advanced external link management with automatic detection, icon insertion, link behavior customization, and comprehensive link processing.
 * Version: 2.2.3
 * Author: Krafty Sprouts Media, LLC
 * Author URI: https://kraftysprouts.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-external-link-manager
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 *
 * @package SELM
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'SELM_VERSION', '2.2.3' );
define( 'SELM_PLUGIN_FILE', __FILE__ );
define( 'SELM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SELM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SELM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SELM_INCLUDES_DIR', SELM_PLUGIN_DIR . 'includes/' );
define( 'SELM_ASSETS_URL', SELM_PLUGIN_URL . 'assets/' );

/**
 * Main plugin class
 *
 * @since 1.0.0
 */
class SELM_Smart_External_Link_Manager {

	/**
	 * Plugin instance
	 *
	 * @var SELM_Smart_External_Link_Manager
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Core instance
	 *
	 * @var SELM_Core
	 * @since 1.0.0
	 */
	public $core;

	/**
	 * Admin instance
	 *
	 * @var SELM_Admin
	 * @since 1.0.0
	 */
	public $admin;

	/**
	 * Frontend instance
	 *
	 * @var SELM_Frontend
	 * @since 1.0.0
	 */
	public $frontend;

	/**
	 * Get plugin instance
	 *
	 * @return SELM_Smart_External_Link_Manager
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize plugin
	 *
	 * @since 1.0.0
	 */
	private function init() {
		// Load autoloader
		$this->load_autoloader();

		// Register activation/deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( 'SELM_Smart_External_Link_Manager', 'uninstall' ) );

		// Initialize on plugins_loaded
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * Load autoloader
	 *
	 * @since 1.0.0
	 */
	private function load_autoloader() {
		require_once SELM_INCLUDES_DIR . 'class-core.php';
		require_once SELM_INCLUDES_DIR . 'class-admin.php';
		require_once SELM_INCLUDES_DIR . 'class-frontend.php';
	}

	/**
	 * Initialize after plugins loaded
	 *
	 * @since 1.0.0
	 */
	public function plugins_loaded() {
		// Initialize core components
		$this->init_components();
	}

	/**
	 * Initialize core components
	 *
	 * @since 1.0.0
	 */
	private function init_components() {
		// Initialize core
		$this->core = new SELM_Core();
		$this->frontend = new SELM_Frontend( $this->core );

		// Initialize admin interface if in admin
		if ( is_admin() ) {
			$this->admin = new SELM_Admin( $this->core );
		}
	}

	/**
	 * Plugin activation
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		// Set default options
		$this->set_default_options();
		
		// Clear any existing cache (ported from KSM-ELM)
		if ( function_exists( 'wp_cache_flush_group' ) ) {
			wp_cache_flush_group( 'selm' );
		}
	}

	/**
	 * Plugin deactivation
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		// Clear any cached data
		delete_transient( 'selm_cache' );
		
		// Clear object cache (ported from KSM-ELM)
		if ( function_exists( 'wp_cache_flush_group' ) ) {
			wp_cache_flush_group( 'selm' );
		}
	}

	/**
	 * Plugin uninstall
	 *
	 * @since 1.0.0
	 */
	public static function uninstall() {
		// Remove all options
		delete_option( 'selm_settings' );

		// Remove transients
		delete_transient( 'selm_cache' );
		
		// Clear object cache (ported from KSM-ELM)
		if ( function_exists( 'wp_cache_flush_group' ) ) {
			wp_cache_flush_group( 'selm' );
		}
	}

	/**
	 * Set default options
	 *
	 * @since 1.0.0
	 */
	private function set_default_options() {
		$default_options = array(
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

		add_option( 'selm_settings', $default_options );
	}
}

/**
 * Get plugin instance
 *
 * @return SELM_Smart_External_Link_Manager
 * @since 1.0.0
 */
function selm_smart_external_link_manager() {
	return SELM_Smart_External_Link_Manager::get_instance();
}

// Initialize plugin
selm_smart_external_link_manager();

// Add admin menu
if ( is_admin() ) {
	add_action( 'admin_menu', 'selm_admin_menu' );
}

/**
 * Add admin menu
 *
 * @since 1.0.0
 */
function selm_admin_menu() {
	add_options_page(
		__( 'Smart External Link Manager Settings', 'smart-external-link-manager' ),
		__( 'Smart External Link Manager', 'smart-external-link-manager' ),
		'manage_options',
		'smart-external-link-manager-settings',
		array( selm_smart_external_link_manager()->admin, 'render_settings_page' )
	);
}