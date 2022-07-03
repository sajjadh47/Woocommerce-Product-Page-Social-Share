<?php

/**
 * Register Plugin Settings & Render Settings Page
 */
class WppssPluginSettings
{
	public $PluginSettingsFields;

	public $section_name = 'wpss_main_settings_section';

	public $section_page = 'wpss_settings_section';

	public $settings_group_name = 'wpss_settings_group';

	public $buttons_positions;

	public $plugin_options;

	public $buttons_icon_style;
    
    public function __construct()
    {
    	$this->PluginSettingsFields = array(
			
			'wpss_show_hide_field' => array(
				'title' => __( 'Show / Hide Sharing Buttons', 'woo-product-page-social-share' ),
				'callback' =>'render_enable_sharing_field'
			),

			'wpss_buttons_style_field' => array(
				'title' => __( 'Buttons Style', 'woo-product-page-social-share' ),
				'callback' =>'render_buttons_style_field'
			),
			
			'wpss_buttons_position_field' => array(
				'title' => __( 'Buttons Visible Position', 'woo-product-page-social-share' ),
				'callback' =>'render_buttons_position_field'
			),

			'wpss_buttons_list_field' => array(
				'title' => __( 'Social Buttons To Add', 'woo-product-page-social-share' ),
				'callback' =>'render_buttons_list_field'
			),

			'wpss_buttons_icontext_field' => array(
				'title' => __( 'Icons Style', 'woo-product-page-social-share' ),
				'callback' =>'render_buttons_icontext_field'
			)
		);

		$this->buttons_positions = array(
			
			'wpss_position_default' => __( 'Default', 'woo-product-page-social-share' ),
			'wpss_position_api' 	=> __( 'After Product Image', 'woo-product-page-social-share' ),
			'wpss_position_apt' 	=> __( 'After Product Title', 'woo-product-page-social-share' ),
			'wpss_position_bpt' 	=> __( 'Before Product Title', 'woo-product-page-social-share' ),
			'wpss_position_asd' 	=> __( 'After Short Description', 'woo-product-page-social-share' ),
			'wpss_position_aatcb' 	=> __( 'After Add To Cart Button', 'woo-product-page-social-share' ),
			'wpss_position_bti' 	=> __( 'Before Tab Information', 'woo-product-page-social-share' )
		);

		$this->buttons_icon_style = array(

			'icons_only' => __( 'Icons Only', 'woo-product-page-social-share' ),
			'text_icons' => __( 'Text With Icons', 'woo-product-page-social-share' ),
		);

		$this->plugin_options = get_option( WPPSS_PLUGIN_OPTION_NAME );

		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    // ------------------------------------------------------------
	// Add A New Menu Page To The Dashboard For The Plugin Settings
	// ------------------------------------------------------------
    public function add_menu_page()
    {		
		add_menu_page( __( 'Woo Social Sharing', 'woo-product-page-social-share' ), __( 'Social Sharing', 'woo-product-page-social-share' ), 'manage_options', 'woo-social-sharing', array( $this, 'render_social_sharing' ), 'dashicons-share' );
	}

	// ---------------------------------------------------------
	// Render Plugin Settings Form
	// ---------------------------------------------------------
	public function render_social_sharing()
	{
		$icon = "url( " . plugins_url( "assets/icon/icon.png", WPPSS_PLUGIN_URL ) . " )"; ?>

		<div class="wrap wpss_social_share_buttons_settings wpss_social_share_buttons">
		    
		    <h2 style="background-image: <?php echo $icon; ?>;background-repeat:  no-repeat;background-position: left 12px;background-size: 25px;padding-left: 30px;">
		    	<?php _e( 'Woocommerce Product Social Sharing', 'woo-product-page-social-share' ); ?>
			</h2>

		    <form action="options.php" method="post"><?php

				settings_fields( $this->settings_group_name );

				do_settings_sections( $this->section_page );

				submit_button( __( 'Save Changes', 'woo-product-page-social-share' ) ); ?>

		    </form>
		
		</div>

	<?php }

	// ---------------------------------------------------------
	// Styles & Scripts For Backend Settings Page [/wp-admin/admin.php?page=woo-social-sharing]
	// ---------------------------------------------------------
	public function enqueue_scripts()
	{
		$cssFiles = array( 'style.css', 'bootstrap.css', 'icons.26.svg.css', 'select2.min.css' );

		$jsFiles = array( 'select2.min.js', 'popper.min.js', 'bootstrap.min.js', 'app.js' );

		if ( get_current_screen()->id == 'toplevel_page_woo-social-sharing' ) {

			foreach ( $cssFiles as $id => $fileName ) {
				
				wp_enqueue_style( 'wppss-stylesheet-' . $id, plugins_url( 'assets/css/' . $fileName, WPPSS_PLUGIN_URL ), true );
			}

			foreach ( $jsFiles as $id => $fileName ) {
				
				wp_enqueue_script( 'wppss-script-' . $id, plugins_url( 'assets/js/' . $fileName, WPPSS_PLUGIN_URL ), true );

				if ( $fileName == 'bootstrap.min.js' ) {
					
					wp_enqueue_script( 'wppss-script-' . $id, plugins_url( 'assets/js/' . $fileName, WPPSS_PLUGIN_URL ), array( 'jquery' ), true );
				}
			}
		}
	}

	// ---------------------------------------------------------
	// Register Plugin Settings Options Via Settins API
	// ---------------------------------------------------------
	public function register_settings()
	{
		// add_settings_section( $id, $title, $callback, $page )
		add_settings_section( $this->section_name, '', '__return_empty_string', $this->section_page );

		foreach ( $this->PluginSettingsFields as $fieldID => $PluginSettingsField ) {

			// add_settings_field( $id, $title, $callback, $page, $section, $args )
			add_settings_field(
				$fieldID,
				$PluginSettingsField['title'],
				array( $this, $PluginSettingsField['callback'] ),
				$this->section_page,
				$this->section_name
			);
		}

		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( $this->settings_group_name, WPPSS_PLUGIN_OPTION_NAME, array( $this, 'validate_input' ) ) ;
	}

	// ---------------------------------------------------------
	// Validate User Submitted Settings Input (using trim() see @http://php.net/trim)
	// --------------------------------------------------------
	public function validate_input( $arr_input )
	{
		$options = $this->plugin_options;
		
		$options['wpss_show_hide_field'] = trim( $arr_input['wpss_show_hide_field'] );
		
		$options['wpss_buttons_style_field'] = trim( $arr_input['wpss_buttons_style_field'] );
		
		$options['wpss_buttons_position_field'] = trim( $arr_input['wpss_buttons_position_field'] );
		
		$options['wpss_buttons_list_field'] = trim( $arr_input['wpss_buttons_list_field'] );
		
		$options['wpss_buttons_icontext_field'] = trim( $arr_input['wpss_buttons_icontext_field'] );
		
		return $options;
	}

	public function render_enable_sharing_field()
	{ ?>
	    <label class="switch">
	    	
	    	<input type="checkbox" name="<?= WPPSS_PLUGIN_OPTION_NAME; ?>[wpss_show_hide_field]" <?php checked( 'on', $this->plugin_options['wpss_show_hide_field'], true ); ?> />
			
			<span class="slider"></span>
		
		</label>

	<?php }

	public function render_buttons_style_field()
	{ ?>

		<div class="buttons_style_preview">

			<label for="rounded_radio">
				<input type="radio" id="rounded_radio" name="<?= WPPSS_PLUGIN_OPTION_NAME; ?>[wpss_buttons_style_field]" value="rounded" <?php echo $this->plugin_options['wpss_buttons_style_field'] == 'rounded' ? 'checked' : "" ?>>
				<img data-toggle="tooltip" data-placement="top" title="Rounded" src="<?php echo plugins_url( '/assets/icon/rounded.png', WPPSS_PLUGIN_URL ); ?>">
			</label>

			<label for="square_radio">
				<input type="radio" id="square_radio" name="<?= WPPSS_PLUGIN_OPTION_NAME; ?>[wpss_buttons_style_field]" value="square" <?php echo $this->plugin_options['wpss_buttons_style_field'] == 'square' ? 'checked' : "" ?>>
				<img data-toggle="tooltip" data-placement="top" title="Squared" src="<?php echo plugins_url( '/assets/icon/square.png', WPPSS_PLUGIN_URL ); ?>">
			</label>

			<label for="circle_radio">
				<input type="radio" id="circle_radio" name="<?= WPPSS_PLUGIN_OPTION_NAME; ?>[wpss_buttons_style_field]" value="circle" <?php echo $this->plugin_options['wpss_buttons_style_field'] == 'circle' ? 'checked' : "" ?>>
				<img data-toggle="tooltip" data-placement="top" title="Circled" src="<?php echo plugins_url( '/assets/icon/circle.png', WPPSS_PLUGIN_URL ); ?>">
			</label>

		</div>

	<?php }

	public function render_buttons_position_field()
	{ ?>    
	    <select name="<?= WPPSS_PLUGIN_OPTION_NAME; ?>[wpss_buttons_position_field]" id="wpss_buttons_position">

		<?php
			
			foreach ( $this->buttons_positions as $key => $value ) {
				
				echo "<option value=$key";
				
				if( isset( $this->plugin_options['wpss_buttons_position_field'] ) && $key == $this->plugin_options['wpss_buttons_position_field'] ){
				
					echo " selected";
				}
				
				echo ">$value</option>";
			}
		?>

	    </select>

	<?php

	}

	// incomplete
	public function render_buttons_list_field()
	{ ?>
		<input type="hidden" name="<?= WPPSS_PLUGIN_OPTION_NAME; ?>[wpss_buttons_list_field]" id="wpss_buttons_list_field_values" value="<?php echo $this->plugin_options['wpss_buttons_list_field']; ?>">
		
		<ul id="selected">
			
			<section class="selected_container">
				
			<?php
			
				$selected_services = explode( ',', $this->plugin_options['wpss_buttons_list_field'] );

				$templates = json_decode( file_get_contents( WPPSS_PLUGIN_PATH . '/assets/data/social_media_list.json' ) );

				foreach ( $selected_services as $value ) {
					
					if ( ! empty( $value ) && isset( $templates->$value ) ) echo $templates->$value;
				}

			?>
			
			</section>
			
			<button id="social_service_toggle_btn" class="button" data-toggle="modal" data-target="#myModal"><?php _e( 'Add Social Service +', 'woo-product-page-social-share' ); ?></button>
		</ul>

		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel">

			<div class="modal-dialog" role="document">

				<div class="modal-content">

					<div class="modal-header">

						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><?php _e( 'Save Changes', 'woo-product-page-social-share' ); ?></span></button>

						<h4 class="modal-title" id="myModalLabel"><?php _e( 'Select Social Services', 'woo-product-page-social-share' ); ?></h4>

					</div>

					<div class="modal-body">

						<div id="social_service_modal">

							<ul id="selectable" class="control_list">

								<?php foreach ( $templates as $id => $html ) : ?>

									<?php echo $html; ?>

								<?php endforeach; ?>

							</ul>

						</div>

					</div>

				</div>

			</div>

		</div>

	<?php }

	public function render_buttons_icontext_field()
	{ ?>
		<select name="wpss_register_settings_fields[wpss_buttons_icontext_field]" id="wpss_buttons_icontext_field">
			
			<?php

				foreach ( $this->buttons_icon_style as $key => $value ) {
					
					echo "<option value=$key";
					
					if( isset( $this->plugin_options['wpss_buttons_icontext_field'] ) && $key == $this->plugin_options['wpss_buttons_icontext_field'] ){
					
						echo " selected";
					}
					
					echo ">$value</option>";
				}
			?>

		</select>
<?php }

}

new WppssPluginSettings;
