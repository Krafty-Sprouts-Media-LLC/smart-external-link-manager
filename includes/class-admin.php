<?php
/**
 * Filename: class-admin.php
 * Author: Krafty Sprouts Media, LLC
 * Created: 18/08/2025
 * Version: 2.2.3
 * Last Modified: 24/11/2025
 * Description: Admin functionality for Smart External Link Manager
 *
 * @package SELM
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin functionality for Smart External Link Manager
 *
 * @since 1.0.0
 */
class SELM_Admin {

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
		// Settings handling
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_selm_save_settings', array( $this, 'handle_settings_save' ) );
		add_action( 'admin_post_selm_reset_settings', array( $this, 'handle_settings_reset' ) );
		
		// Admin notices
		add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
		
		// Admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			'selm_settings',
			'selm_settings',
			array( $this, 'sanitize_settings' )
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized data
	 * @since 1.0.0
	 */
	public function sanitize_settings( $input ) {
		return $this->core->sanitize_settings( $input );
	}

	/**
	 * Handle settings save
	 *
	 * @since 1.0.0
	 */
	public function handle_settings_save() {
		// Check nonce
		$nonce = isset( $_POST['_wpnonce'] ) ? wp_unslash( $_POST['_wpnonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'selm_settings' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'smart-external-link-manager' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'smart-external-link-manager' ) );
		}

		// Sanitize and save settings
		$settings = $this->core->sanitize_settings( $_POST );
		$result = $this->core->update_options( $settings );

		if ( $result ) {
			$message = 'settings_saved';
		} else {
			$message = 'settings_error';
		}

		// Redirect with message
		$redirect_url = add_query_arg(
			array(
				'page' => 'smart-external-link-manager-settings',
				'message' => $message,
			),
			admin_url( 'options-general.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Handle settings reset
	 *
	 * @since 1.0.0
	 */
	public function handle_settings_reset() {
		// Check nonce
		$nonce = isset( $_POST['_wpnonce'] ) ? wp_unslash( $_POST['_wpnonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'selm_reset' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'smart-external-link-manager' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'smart-external-link-manager' ) );
		}

		// Reset settings
		$result = $this->core->reset_settings();

		if ( $result ) {
			$message = 'settings_reset';
		} else {
			$message = 'reset_error';
		}

		// Redirect with message
		$redirect_url = add_query_arg(
			array(
				'page' => 'smart-external-link-manager-settings',
				'message' => $message,
			),
			admin_url( 'options-general.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Show admin notices
	 *
	 * @since 1.0.0
	 */
	public function show_admin_notices() {
		// Show settings messages
		if ( ! isset( $_GET['page'] ) || 'smart-external-link-manager-settings' !== sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}

		// Verify nonce for message display
		if ( ! isset( $_GET['message'] ) ) {
			return;
		}

		$message = sanitize_text_field( wp_unslash( $_GET['message'] ) );

		switch ( $message ) {
			case 'settings_saved':
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Smart External Link Manager settings saved successfully.', 'smart-external-link-manager' ) . '</p></div>';
				break;
			case 'settings_error':
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Error saving Smart External Link Manager settings.', 'smart-external-link-manager' ) . '</p></div>';
				break;
			case 'settings_reset':
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Smart External Link Manager settings reset to defaults.', 'smart-external-link-manager' ) . '</p></div>';
				break;
			case 'reset_error':
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Error resetting Smart External Link Manager settings.', 'smart-external-link-manager' ) . '</p></div>';
				break;
		}
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook_suffix Current admin page.
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {
		if ( 'settings_page_smart-external-link-manager-settings' !== $hook_suffix ) {
			return;
		}

		// Enqueue admin styles
		wp_enqueue_style(
			'selm-admin-css',
			SELM_ASSETS_URL . 'css/admin.css',
			array(),
			SELM_VERSION
		);
	}
	
	/**
	 * Get SVG icon HTML
	 *
	 * @param string $icon_name Icon name (without .svg extension).
	 * @param string $class Additional CSS classes.
	 * @return string SVG icon HTML
	 * @since 2.2.0
	 */
	private function get_svg_icon( $icon_name, $class = '' ) {
		$icon_path = SELM_ASSETS_URL . 'images/' . $icon_name . '.svg';
		$icon_file = SELM_PLUGIN_DIR . 'assets/images/' . $icon_name . '.svg';
		
		if ( ! file_exists( $icon_file ) ) {
			return '';
		}
		
		$svg_content = file_get_contents( $icon_file );
		if ( $svg_content ) {
			// Add class to SVG element
			$svg_content = str_replace( '<svg', '<svg class="selm-icon ' . esc_attr( $class ) . '"', $svg_content );
			return $svg_content;
		}
		
		return '';
	}

	/**
	 * Render settings page
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		$options = $this->core->get_options();
		$post_types = $this->core->get_available_post_types();
		$icon_types = $this->core->get_available_icon_types();
		$icon_positions = $this->core->get_available_icon_positions();
		?>
		<div class="wrap selm-wrap">
			<div class="selm-header">
				<h1 class="selm-title">
					<?php echo wp_kses_post( $this->get_svg_icon( 'icon-plugin' ) ); ?>
					<?php esc_html_e( 'Smart External Link Manager', 'smart-external-link-manager' ); ?>
				</h1>
				<div class="selm-status-badge <?php echo esc_attr( $options['enabled'] ? 'selm-status-active' : 'selm-status-inactive' ); ?>">
					<?php echo esc_html( $options['enabled'] ? __( 'Active', 'smart-external-link-manager' ) : __( 'Inactive', 'smart-external-link-manager' ) ); ?>
				</div>
			</div>
			
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="selm-settings-form">
				<?php wp_nonce_field( 'selm_settings' ); ?>
				<input type="hidden" name="action" value="selm_save_settings">

				<div class="selm-container">
					<!-- Navigation -->
					<div class="selm-nav">
						<a class="selm-nav-item active" data-target="general">
							<?php echo wp_kses_post( $this->get_svg_icon( 'icon-general' ) ); ?>
							<?php esc_html_e( 'General', 'smart-external-link-manager' ); ?>
						</a>
						<a class="selm-nav-item" data-target="behavior">
							<?php echo wp_kses_post( $this->get_svg_icon( 'icon-behavior' ) ); ?>
							<?php esc_html_e( 'Link Behavior', 'smart-external-link-manager' ); ?>
						</a>
						<a class="selm-nav-item" data-target="icons">
							<?php echo wp_kses_post( $this->get_svg_icon( 'icon-icons' ) ); ?>
							<?php esc_html_e( 'Icons', 'smart-external-link-manager' ); ?>
						</a>
						<a class="selm-nav-item" data-target="exclusions">
							<?php echo wp_kses_post( $this->get_svg_icon( 'icon-exclusions' ) ); ?>
							<?php esc_html_e( 'Exclusions', 'smart-external-link-manager' ); ?>
						</a>
						<a class="selm-nav-item" data-target="advanced">
							<?php echo wp_kses_post( $this->get_svg_icon( 'icon-advanced' ) ); ?>
							<?php esc_html_e( 'Advanced', 'smart-external-link-manager' ); ?>
						</a>
					</div>

					<!-- Content -->
					<div class="selm-content">
						<!-- General Settings -->
						<div class="selm-card active" id="general">
							<div class="selm-card-header">
								<h2 class="selm-card-title"><?php esc_html_e( 'General Settings', 'smart-external-link-manager' ); ?></h2>
								<p class="selm-card-description"><?php esc_html_e( 'Basic configuration for the plugin.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="enabled"><?php esc_html_e( 'Enable Plugin', 'smart-external-link-manager' ); ?></label>
								<label class="selm-toggle">
									<input type="checkbox" name="enabled" id="enabled" value="1" <?php checked( $options['enabled'] ); ?>>
									<span class="selm-slider"></span>
								</label>
								<p class="selm-description"><?php esc_html_e( 'Enable or disable all external link processing.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="processing_mode"><?php esc_html_e( 'Processing Mode', 'smart-external-link-manager' ); ?></label>
								<select name="processing_mode" id="processing_mode" class="regular-text">
									<option value="php" <?php selected( $options['processing_mode'] ?? 'php', 'php' ); ?>>
										<?php esc_html_e( 'Server-Side (PHP) - Recommended for SEO', 'smart-external-link-manager' ); ?>
									</option>
									<option value="js" <?php selected( $options['processing_mode'] ?? 'php', 'js' ); ?>>
										<?php esc_html_e( 'Client-Side (JavaScript) - Recommended for Caching', 'smart-external-link-manager' ); ?>
									</option>
								</select>
								<p class="selm-description"><?php esc_html_e( 'Choose PHP for better SEO (processed before page load) or JavaScript for better compatibility with caching plugins.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label"><?php esc_html_e( 'Apply to Post Types', 'smart-external-link-manager' ); ?></label>
								<div class="selm-checkbox-group">
									<?php foreach ( $post_types as $post_type ) : ?>
										<label class="selm-checkbox-item">
											<input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $options['post_types'], true ) ); ?>>
											<?php echo esc_html( $post_type->labels->name ); ?>
										</label>
									<?php endforeach; ?>
								</div>
								<p class="selm-description"><?php esc_html_e( 'Select which content types should have external links processed.', 'smart-external-link-manager' ); ?></p>
							</div>
						</div>

						<!-- Link Behavior -->
						<div class="selm-card" id="behavior">
							<div class="selm-card-header">
								<h2 class="selm-card-title"><?php esc_html_e( 'Link Behavior', 'smart-external-link-manager' ); ?></h2>
								<p class="selm-card-description"><?php esc_html_e( 'Control how external links behave when clicked.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="open_new_tab"><?php esc_html_e( 'Open in New Tab', 'smart-external-link-manager' ); ?></label>
								<label class="selm-toggle">
									<input type="checkbox" name="open_new_tab" id="open_new_tab" value="1" <?php checked( $options['open_new_tab'] ); ?>>
									<span class="selm-slider"></span>
								</label>
								<p class="selm-description"><?php esc_html_e( 'Automatically add target="_blank" to external links.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="add_nofollow"><?php esc_html_e( 'Add "nofollow"', 'smart-external-link-manager' ); ?></label>
								<label class="selm-toggle">
									<input type="checkbox" name="add_nofollow" id="add_nofollow" value="1" <?php checked( $options['add_nofollow'] ); ?>>
									<span class="selm-slider"></span>
								</label>
								<p class="selm-description"><?php esc_html_e( 'Tell search engines not to follow external links (rel="nofollow").', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="add_noopener"><?php esc_html_e( 'Add "noopener"', 'smart-external-link-manager' ); ?></label>
								<label class="selm-toggle">
									<input type="checkbox" name="add_noopener" id="add_noopener" value="1" <?php checked( $options['add_noopener'] ); ?>>
									<span class="selm-slider"></span>
								</label>
								<p class="selm-description"><?php esc_html_e( 'Improve security by preventing new tabs from accessing the original window (rel="noopener").', 'smart-external-link-manager' ); ?></p>
							</div>
						</div>

						<!-- Icon Settings -->
						<div class="selm-card" id="icons">
							<div class="selm-card-header">
								<h2 class="selm-card-title"><?php esc_html_e( 'Icon Settings', 'smart-external-link-manager' ); ?></h2>
								<p class="selm-card-description"><?php esc_html_e( 'Customize the appearance of external link indicators.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="add_icon"><?php esc_html_e( 'Enable Icons', 'smart-external-link-manager' ); ?></label>
								<label class="selm-toggle">
									<input type="checkbox" name="add_icon" id="add_icon" value="1" <?php checked( $options['add_icon'] ); ?>>
									<span class="selm-slider"></span>
								</label>
								<p class="selm-description"><?php esc_html_e( 'Display an icon next to external links.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="icon_type"><?php esc_html_e( 'Icon Type', 'smart-external-link-manager' ); ?></label>
								<select name="icon_type" id="icon_type" class="regular-text">
									<?php foreach ( $icon_types as $type_key => $type_label ) : ?>
										<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $options['icon_type'] ?? 'svg', $type_key ); ?>>
											<?php echo esc_html( $type_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if ( isset( $options['icon_type'] ) && 'dashicon' === $options['icon_type'] ) : ?>
									<div class="notice notice-warning inline" style="margin-top: 10px; padding: 10px;">
										<p><strong><?php esc_html_e( 'Deprecation Notice:', 'smart-external-link-manager' ); ?></strong> <?php esc_html_e( 'Dashicons support is deprecated and will be removed in a future version. Please switch to SVG icons for better performance and compatibility.', 'smart-external-link-manager' ); ?></p>
									</div>
								<?php endif; ?>
							</div>

							<div class="selm-form-group" id="selm-svg-file-group" style="<?php echo ( isset( $options['icon_type'] ) && 'svg' === $options['icon_type'] ) ? '' : 'display: none;'; ?>">
								<label class="selm-label" for="icon_svg_file"><?php esc_html_e( 'SVG Icon File', 'smart-external-link-manager' ); ?></label>
								<select name="icon_svg_file" id="icon_svg_file" class="regular-text">
									<?php
									$svg_files = $this->core->get_available_svg_icons();
									$current_svg = $options['icon_svg_file'] ?? 'icon-external';
									foreach ( $svg_files as $file_key => $file_label ) :
										?>
										<option value="<?php echo esc_attr( $file_key ); ?>" <?php selected( $current_svg, $file_key ); ?>>
											<?php echo esc_html( $file_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<p class="selm-description"><?php esc_html_e( 'Select which SVG icon to display next to external links. Icons are located in the plugin\'s assets/images folder.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group" id="selm-icon-class-group" style="<?php echo ( isset( $options['icon_type'] ) && 'svg' === $options['icon_type'] ) ? 'display: none;' : ''; ?>">
								<label class="selm-label" for="icon_class"><?php esc_html_e( 'Icon Class', 'smart-external-link-manager' ); ?></label>
								<input type="text" name="icon_class" id="icon_class" value="<?php echo esc_attr( $options['icon_class'] ?? 'selm-external-icon-svg' ); ?>" class="regular-text">
								<p class="selm-description">
									<span id="selm-icon-class-desc-svg" style="<?php echo ( isset( $options['icon_type'] ) && 'svg' === $options['icon_type'] ) ? '' : 'display: none;'; ?>">
										<?php esc_html_e( 'CSS class for SVG icon styling (e.g. selm-external-icon-svg).', 'smart-external-link-manager' ); ?>
									</span>
									<span id="selm-icon-class-desc-fontawesome" style="<?php echo ( isset( $options['icon_type'] ) && 'fontawesome' === $options['icon_type'] ) ? '' : 'display: none;'; ?>">
										<?php esc_html_e( 'CSS class for Font Awesome icon (e.g. fa fa-external-link).', 'smart-external-link-manager' ); ?>
									</span>
									<span id="selm-icon-class-desc-dashicon" style="<?php echo ( isset( $options['icon_type'] ) && 'dashicon' === $options['icon_type'] ) ? '' : 'display: none;'; ?>">
										<?php esc_html_e( 'CSS class for Dashicons icon (e.g. dashicons-external).', 'smart-external-link-manager' ); ?>
										<strong><?php esc_html_e( ' (Deprecated)', 'smart-external-link-manager' ); ?></strong>
									</span>
								</p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="icon_position"><?php esc_html_e( 'Icon Position', 'smart-external-link-manager' ); ?></label>
								<select name="icon_position" id="icon_position" class="regular-text">
									<?php foreach ( $icon_positions as $pos_key => $pos_label ) : ?>
										<option value="<?php echo esc_attr( $pos_key ); ?>" <?php selected( $options['icon_position'], $pos_key ); ?>>
											<?php echo esc_html( $pos_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="selm-form-group" id="selm-custom-icon-group" style="<?php echo ( isset( $options['icon_type'] ) && 'custom' === $options['icon_type'] ) ? '' : 'display: none;'; ?>">
								<label class="selm-label" for="custom_icon"><?php esc_html_e( 'Custom Icon HTML', 'smart-external-link-manager' ); ?></label>
								<textarea name="custom_icon" id="custom_icon" rows="3" class="large-text code"><?php echo esc_textarea( $options['custom_icon'] ?? '' ); ?></textarea>
								<p class="selm-description"><?php esc_html_e( 'Enter raw HTML or SVG for your custom icon (requires "Custom Icon" type selected above).', 'smart-external-link-manager' ); ?></p>
							</div>
						</div>

						<!-- Exclusions -->
						<div class="selm-card" id="exclusions">
							<div class="selm-card-header">
								<h2 class="selm-card-title"><?php esc_html_e( 'Exclusions', 'smart-external-link-manager' ); ?></h2>
								<p class="selm-card-description"><?php esc_html_e( 'Define which links should be ignored by the plugin.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="exclude_domains"><?php esc_html_e( 'Excluded Domains', 'smart-external-link-manager' ); ?></label>
								<textarea name="exclude_domains" id="exclude_domains" rows="5" class="large-text code"><?php echo esc_textarea( implode( "\n", $options['exclude_domains'] ) ); ?></textarea>
								<p class="selm-description"><?php esc_html_e( 'Enter one domain per line (e.g. google.com). Subdomains are automatically handled.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="exclude_classes"><?php esc_html_e( 'Excluded CSS Classes', 'smart-external-link-manager' ); ?></label>
								<textarea name="exclude_classes" id="exclude_classes" rows="3" class="large-text code"><?php echo esc_textarea( implode( "\n", $options['exclude_classes'] ) ); ?></textarea>
								<p class="selm-description"><?php esc_html_e( 'Links with these classes will be ignored. Enter one class per line.', 'smart-external-link-manager' ); ?></p>
							</div>
						</div>

						<!-- Advanced -->
						<div class="selm-card" id="advanced">
							<div class="selm-card-header">
								<h2 class="selm-card-title"><?php esc_html_e( 'Advanced Settings', 'smart-external-link-manager' ); ?></h2>
								<p class="selm-card-description"><?php esc_html_e( 'Developer tools and custom styling.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="custom_css"><?php esc_html_e( 'Custom CSS', 'smart-external-link-manager' ); ?></label>
								<textarea name="custom_css" id="custom_css" rows="6" class="large-text code"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
								<p class="selm-description"><?php esc_html_e( 'Add custom CSS to style your external links. The class .selm-external-link is added to all processed links.', 'smart-external-link-manager' ); ?></p>
							</div>

							<div class="selm-form-group">
								<label class="selm-label" for="debug_mode"><?php esc_html_e( 'Debug Mode', 'smart-external-link-manager' ); ?></label>
								<label class="selm-toggle">
									<input type="checkbox" name="debug_mode" id="debug_mode" value="1" <?php checked( $options['debug_mode'] ); ?>>
									<span class="selm-slider"></span>
								</label>
								<p class="selm-description"><?php esc_html_e( 'Enable logging for troubleshooting (requires WP_DEBUG).', 'smart-external-link-manager' ); ?></p>
							</div>
						</div>
					</div>
				</div>
				
				<div class="selm-actions">
					<?php submit_button( __( 'Save Changes', 'smart-external-link-manager' ), 'primary', 'submit', false ); ?>
				</div>
			</form>
			
			<div style="margin-top: 20px; text-align: right;">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php esc_attr_e( 'Are you sure you want to reset all settings to defaults?', 'smart-external-link-manager' ); ?>')" style="display: inline-block;">
					<?php wp_nonce_field( 'selm_reset' ); ?>
					<input type="hidden" name="action" value="selm_reset_settings">
					<button type="submit" class="button-link selm-reset-btn"><?php esc_html_e( 'Reset to Defaults', 'smart-external-link-manager' ); ?></button>
				</form>
			</div>
			
			<!-- Branding Footer -->
			<div class="selm-branding">
				<div class="selm-branding-content">
					<p class="selm-branding-text">
						<?php esc_html_e( 'Developed by', 'smart-external-link-manager' ); ?>
						<a href="https://kraftysprouts.com" target="_blank" rel="noopener noreferrer" class="selm-branding-link">
							<strong>Krafty Sprouts Media, LLC</strong>
						</a>
					</p>
				</div>
			</div>
		</div>
		
		<script>
		jQuery(document).ready(function($) {
			// Tab switching
			$('.selm-nav-item').on('click', function(e) {
				e.preventDefault();
				
				// Remove active class from all
				$('.selm-nav-item').removeClass('active');
				$('.selm-card').removeClass('active');
				
				// Add active class to clicked
				$(this).addClass('active');
				
				// Show target card
				var target = $(this).data('target');
				$('#' + target).addClass('active');
			});
			
			// Icon type change handler
			function updateIconFields() {
				var iconType = $('#icon_type').val();
				
				// Show/hide SVG file selector
				if (iconType === 'svg') {
					$('#selm-svg-file-group').show();
					$('#selm-icon-class-group').hide();
				} else {
					$('#selm-svg-file-group').hide();
					$('#selm-icon-class-group').show();
				}
				
				// Show/hide custom icon field
				if (iconType === 'custom') {
					$('#selm-custom-icon-group').show();
				} else {
					$('#selm-custom-icon-group').hide();
				}
				
				// Update icon class description
				$('#selm-icon-class-desc-svg, #selm-icon-class-desc-fontawesome, #selm-icon-class-desc-dashicon').hide();
				if (iconType === 'svg') {
					$('#selm-icon-class-desc-svg').show();
				} else if (iconType === 'fontawesome') {
					$('#selm-icon-class-desc-fontawesome').show();
				} else if (iconType === 'dashicon') {
					$('#selm-icon-class-desc-dashicon').show();
				}
			}
			
			// Initialize on page load
			updateIconFields();
			
			// Update on change
			$('#icon_type').on('change', updateIconFields);
		});
		</script>
		<?php
	}
}