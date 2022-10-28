<?php

/**
 * Register Plugin Settings & Render Settings Page
 */
if ( ! class_exists( 'SSFWC_PLUGIN_SETTINGS' ) )
{
	class SSFWC_PLUGIN_SETTINGS
	{
		public $PluginSettingsFields;

		public $section_name = 'ssfwc_main_settings_section';

		public $section_page = 'ssfwc_settings_section';

		public $settings_group_name = 'ssfwc_settings_group';

		public $buttons_positions;

		public $plugin_options;

		public $buttons_icon_style;
	    
	    public function __construct()
	    {
	    	$this->PluginSettingsFields = [
				'ssfwc_show_hide_field' => [
					'title' => __( 'Show / Hide Sharing Buttons', 'social-sharer-for-woo' ),
					'callback' =>'render_enable_sharing_field'
				],
				'ssfwc_buttons_style_field' => [
					'title' => __( 'Buttons Style', 'social-sharer-for-woo' ),
					'callback' =>'render_buttons_style_field'
				],
				'ssfwc_buttons_position_field' => [
					'title' => __( 'Buttons Visible Position', 'social-sharer-for-woo' ),
					'callback' =>'render_buttons_position_field'
				],
				'ssfwc_buttons_list_field' => [
					'title' => __( 'Social Buttons To Add', 'social-sharer-for-woo' ),
					'callback' =>'render_buttons_list_field'
				],
				'ssfwc_buttons_icontext_field' => [
					'title' => __( 'Icons Style', 'social-sharer-for-woo' ),
					'callback' =>'render_buttons_icontext_field'
				]
			];

			$this->buttons_positions = [
				'ssfwc_position_default' => __( 'Default', 'social-sharer-for-woo' ),
				'ssfwc_position_api' 	 => __( 'After Product Image', 'social-sharer-for-woo' ),
				'ssfwc_position_apt' 	 => __( 'After Product Title', 'social-sharer-for-woo' ),
				'ssfwc_position_bpt' 	 => __( 'Before Product Title', 'social-sharer-for-woo' ),
				'ssfwc_position_asd' 	 => __( 'After Short Description', 'social-sharer-for-woo' ),
				'ssfwc_position_aatcb' 	 => __( 'After Add To Cart Button', 'social-sharer-for-woo' ),
				'ssfwc_position_bti' 	 => __( 'Before Tab Information', 'social-sharer-for-woo' )
			];

			$this->buttons_icon_style = [
				'icons_only' => __( 'Icons Only', 'social-sharer-for-woo' ),
				'text_icons' => __( 'Text With Icons', 'social-sharer-for-woo' ),
			];

			$this->plugin_options = get_option( SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME );

			add_action( 'admin_menu', [ $this, 'add_menu_page' ] );

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

			add_action( 'admin_init', [ $this, 'register_settings' ] );
	    }

		/**
		 * Add A New Menu Page To The Dashboard For The Plugin Settings
		 */
	    public function add_menu_page()
	    {		
			add_menu_page( __( 'Social Sharer For Woo', 'social-sharer-for-woo' ), __( 'Social Sharing', 'social-sharer-for-woo' ), 'manage_options', 'social-sharer-for-woo', array( $this, 'render_social_sharing' ), 'dashicons-share' );
		}

		/**
		 * Render Plugin Settings Form
		 */
		public function render_social_sharing()
		{
			$icon = "url( " . plugins_url( "assets/icon/icon.png", SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ) . " )"; ?>

			<div class="wrap ssfw_social_share_buttons_settings ssfw_social_share_buttons">
			    
			    <h2 style="background-image: <?php echo $icon; ?>;background-repeat:  no-repeat;background-position: left 12px;background-size: 25px;padding-left: 30px;">
			    	<?php echo esc_html( __( 'Woocommerce Product Social Sharing Settings', 'social-sharer-for-woo' ) ); ?>
				</h2>

			    <form action="options.php" method="post"><?php

					settings_fields( $this->settings_group_name );

					do_settings_sections( $this->section_page );

					submit_button( __( 'Save Changes', 'social-sharer-for-woo' ) ); ?>

			    </form>
			
			</div><?php
		}

		/**
		 * Styles & Scripts For Backend Settings Page [/wp-admin/admin.php?page=social-sharer-for-woo]
		 */
		public function enqueue_scripts()
		{
			$cssFiles = [ 'admin.css', 'vendor/bootstrap/bootstrap.min.css', 'icons.26.svg.css', 'vendor/select2/select2.min.css' ];

			$jsFiles = [ 'vendor/select2/select2.min.js', 'vendor/popper/popper.min.js', 'vendor/bootstrap/bootstrap.min.js', 'admin.js' ];

			if ( get_current_screen()->id == 'toplevel_page_social-sharer-for-woo' )
			{
				foreach ( $cssFiles as $id => $fileName )
				{	
					wp_enqueue_style( 'ssfw-stylesheet-' . $id, plugins_url( 'assets/css/' . $fileName, SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ), true );
				}

				foreach ( $jsFiles as $id => $fileName )
				{	
					wp_enqueue_script( 'ssfw-script-' . $id, plugins_url( 'assets/js/' . $fileName, SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ), true );

					if ( $fileName == 'vendor/bootstrap/bootstrap.min.js' )
					{	
						wp_enqueue_script( 'ssfw-script-' . $id, plugins_url( 'assets/js/' . $fileName, SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ), [ 'jquery' ], true );
					}
				}
			}
		}

		/**
		 * Register Plugin Settings Options Via Settins API
		 */
		public function register_settings()
		{
			// add_settings_section( $id, $title, $callback, $page )
			add_settings_section( $this->section_name, '', '__return_empty_string', $this->section_page );

			foreach ( $this->PluginSettingsFields as $fieldID => $PluginSettingsField )
			{
				// add_settings_field( $id, $title, $callback, $page, $section, $args )
				add_settings_field(
					$fieldID,
					$PluginSettingsField['title'],
					[ $this, $PluginSettingsField['callback'] ],
					$this->section_page,
					$this->section_name
				);
			}

			// register_setting( $option_group, $option_name, $sanitize_callback )
			register_setting( $this->settings_group_name, SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME, [ $this, 'validate_input' ] ) ;
		}

		// ---------------------------------------------------------
		// Validate User Submitted Settings Input (using trim() see @http://php.net/trim)
		// --------------------------------------------------------
		public function validate_input( $arr_input )
		{
			$options = $this->plugin_options;
			
			$options['ssfwc_show_hide_field'] = trim( $arr_input['ssfwc_show_hide_field'] );
			
			$options['ssfwc_buttons_style_field'] = trim( $arr_input['ssfwc_buttons_style_field'] );
			
			$options['ssfwc_buttons_position_field'] = trim( $arr_input['ssfwc_buttons_position_field'] );
			
			$options['ssfwc_buttons_list_field'] = trim( $arr_input['ssfwc_buttons_list_field'] );
			
			$options['ssfwc_buttons_icontext_field'] = trim( $arr_input['ssfwc_buttons_icontext_field'] );
			
			return $options;
		}

		public function render_enable_sharing_field()
		{ ?>
		    <label class="switch">
		    	
		    	<input type="checkbox" name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_show_hide_field]" <?php echo isset( $this->plugin_options['ssfwc_show_hide_field'] ) ? checked( 'on', $this->plugin_options['ssfwc_show_hide_field'], false ) : ''; ?> />
				
				<span class="slider"></span>
			
			</label><?php
		}

		public function render_buttons_style_field()
		{ ?>

			<div class="buttons_style_preview">

				<label for="rounded_radio">
					<input type="radio" id="rounded_radio" name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_buttons_style_field]" value="rounded" <?php echo isset( $this->plugin_options['ssfwc_buttons_style_field'] ) && $this->plugin_options['ssfwc_buttons_style_field'] == 'rounded' ? 'checked' : "" ?>>
					<img title="Rounded" src="<?php echo plugins_url( '/assets/icon/rounded.png', SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ); ?>">
				</label>

				<label for="square_radio">
					<input type="radio" id="square_radio" name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_buttons_style_field]" value="square" <?php echo isset( $this->plugin_options['ssfwc_buttons_style_field'] ) && $this->plugin_options['ssfwc_buttons_style_field'] == 'square' ? 'checked' : "" ?>>
					<img title="Squared" src="<?php echo plugins_url( '/assets/icon/square.png', SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ); ?>">
				</label>

				<label for="circle_radio">
					<input type="radio" id="circle_radio" name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_buttons_style_field]" value="circle" <?php echo isset( $this->plugin_options['ssfwc_buttons_style_field'] ) && $this->plugin_options['ssfwc_buttons_style_field'] == 'circle' ? 'checked' : "" ?>>
					<img title="Circled" src="<?php echo plugins_url( '/assets/icon/circle.png', SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ); ?>">
				</label>

			</div><?php
		}

		public function render_buttons_position_field()
		{ ?>    
		    <select name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_buttons_position_field]" id="ssfwc_buttons_position">

			<?php
				
				foreach ( $this->buttons_positions as $key => $value )
				{	
					echo "<option value=$key";
					
						if( isset( $this->plugin_options['ssfwc_buttons_position_field'] ) && $key == $this->plugin_options['ssfwc_buttons_position_field'] )
						{
							echo " selected";
						}
					
					echo ">" . esc_html( $value ) . "</option>";
				}
			?>

		    </select><?php
		}

		// incomplete
		public function render_buttons_list_field()
		{ ?>
			<input type="hidden" name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_buttons_list_field]" id="ssfwc_buttons_list_field_values" value="<?php echo isset( $this->plugin_options['ssfwc_buttons_list_field'] ) ? $this->plugin_options['ssfwc_buttons_list_field'] : ''; ?>">
			
			<ul id="selected">
				
				<section class="selected_container">
					
				<?php

					$allowed_html = array(
					    'li' => array(
					        'data-id'  => array(),
					    ),
					    'span' => array(
					        'class'  => array(),
					        'style'  => array(),
					    ),
					);
					
					if ( isset( $this->plugin_options['ssfwc_buttons_list_field'] ) )
					{	
						$selected_services = explode( ',', $this->plugin_options['ssfwc_buttons_list_field'] );

						$templates = json_decode( file_get_contents( SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR . '/assets/data/social_media_list.json' ) );

						foreach ( $selected_services as $value )
						{
							if ( ! empty( $value ) && isset( $templates->$value ) ) echo wp_kses( $templates->$value, $allowed_html );
						}
					}
				?>
				
				</section>
				
				<button id="social_service_toggle_btn" class="button" data-bs-toggle="modal" data-bs-target="#SSFWC_MODAL"><?php _e( 'Add Social Service +', 'social-sharer-for-woo' ); ?></button>
			</ul>

			<!-- Modal -->	
			<div class="modal fade ssfwc_social_share_buttons" id="SSFWC_MODAL" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="SSFWC_MODALLabel">

				<div class="modal-dialog modal-xl" role="document">

					<div class="modal-content">

						<div class="modal-header">
							
							<h4 class="modal-title" id="SSFWC_MODALLabel"><?php _e( 'Select Social Services', 'social-sharer-for-woo' ); ?></h4>

							<button type="button" class="btn btn-outline-danger close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;right: 170px;"><span aria-hidden="true"><?php _e( 'Cancel', 'social-sharer-for-woo' ); ?></span></button>
							
							<button type="button" class="btn btn-outline-success close save" data-bs-dismiss="modal" aria-label="Close" style="margin-right: 10px;"><span aria-hidden="true"><?php _e( 'Save Changes', 'social-sharer-for-woo' ); ?></span></button>

						</div>

						<div class="modal-body">

							<div id="social_service_modal">

								<ul id="selectable" class="control_list">

									<?php if( isset( $templates ) ) : foreach ( $templates as $id => $html ) : ?>

										<?php echo wp_kses( $html, $allowed_html ); ?>

									<?php endforeach; endif; ?>

								</ul>

							</div>

						</div>

					</div>

				</div>

			</div><?php
		}

		public function render_buttons_icontext_field()
		{ ?>
			<select name="<?php echo SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME; ?>[ssfwc_buttons_icontext_field]" id="ssfwc_buttons_icontext_field">
				
				<?php

					foreach ( $this->buttons_icon_style as $key => $value )
					{	
						echo "<option value=$key";
						
							if( isset( $this->plugin_options['ssfwc_buttons_icontext_field'] ) && $key == $this->plugin_options['ssfwc_buttons_icontext_field'] )
							{
								echo " selected";
							}
						
						echo ">" . esc_html( $value ) . "</option>";
					}
				?>

			</select><?php
		}
	}
	
	new SSFWC_PLUGIN_SETTINGS;
}
