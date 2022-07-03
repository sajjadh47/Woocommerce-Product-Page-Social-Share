<?php

/**
 * Add Social Media Buttons To Front End If Available
 */
class WppssSharingButtons
{
	public $plugin_options;

	public $social_media_icons_bg;

	public $wpss_position;

    public function __construct()
    {
    	$this->plugin_options = get_option( WPPSS_PLUGIN_OPTION_NAME );

    	if ( $this->plugin_options['wpss_show_hide_field'] !== 'on' ) return;

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_filter( 'script_loader_tag', array( $this, 'add_async_tag_to_script' ), 10, 3 );

        $this->social_media_icons_bg = json_decode( file_get_contents( WPPSS_PLUGIN_PATH . '/assets/data/social_media_icons_bg.json' ) );

        switch ( $this->plugin_options['wpss_buttons_position_field'] )
        {
			case 'wpss_position_default':
				
				$this->wppss_position = 55;
			break;
			
			case 'wpss_position_apt':
				
				$this->wppss_position = 8;
			break;
			
			case 'wpss_position_bpt':
			
				$this->wppss_position = 3;
			break;
			
			case 'wpss_position_asd':
				
				$this->wppss_position = 25;
			break;
			
			case 'wpss_position_aatcb':
				
				$this->wppss_position = 35;
			break;
			
			case 'wpss_position_bti':
				
				$this->wppss_position = 5;
			break;

			case 'wpss_position_api':
				
				$this->wppss_position = 10;
			break;

			default :

				$this->wppss_position = 55;
			break;
		}

		if ( $this->wppss_position == 5 )
		{
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'render_social_buttons' ), $this->wppss_position );
		
		}else if( $this->wppss_position == 10 ){
			
			add_action( 'woocommerce_product_thumbnails', array( $this, 'render_social_buttons' ), $this->wppss_position );
		
		}else{
			
			add_action( 'woocommerce_single_product_summary', array( $this, 'render_social_buttons' ), $this->wppss_position );
		}

		add_shortcode( 'wppss_social_sharing_buttons', array( $this, 'wppss_social_sharing_buttons' ) );
    }

    public function enqueue_scripts()
    {
		wp_enqueue_script( 'wppss-addtoany-script', '//static.addtoany.com/menu/page.js', true );

	    wp_enqueue_script( 'wppss-frontend-script', plugins_url( 'assets/js/script.js', WPPSS_PLUGIN_URL ), array( 'jquery' ), true );
	    
	    wp_enqueue_style( 'wppss-frontend-stylesheet', plugins_url( 'assets/css/front_style.css', WPPSS_PLUGIN_URL ), true );
	    
	    wp_enqueue_style( 'wppss-bootstrap-stylesheet', plugins_url( 'assets/css/bootstrap.css', WPPSS_PLUGIN_URL ), true );
	}

	// ---------------------------------------------------------
	// Add async tag to load addtoadny script asynchronous
	// ---------------------------------------------------------
	public function add_async_tag_to_script( $tag, $handle, $src )
	{
	    if ( 'wpss_addtoany_script' != $handle ) return $tag;

	    return str_replace( '<script', '<script async', $tag );
	}

	public function wppss_social_sharing_buttons()
	{	
		ob_start();

			$this->render_social_buttons();

			$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}

	public function render_social_buttons()
	{
		$html  = '<div class="wpss_social_share_buttons not_before_tab row a2a_kit a2a_kit_size_32 a2a_default_style">';

		$social_services = explode( ",", $this->plugin_options['wpss_buttons_list_field'] );

		foreach ( $social_services as $value ) {
			
			if ( ! empty( $value ) ) {

				if ( isset( $this->plugin_options['wpss_buttons_icontext_field'] ) && $this->plugin_options['wpss_buttons_icontext_field'] == 'text_icons' ) {
					
					$text = ucwords( str_replace( "_", " ", $value ) );
					
					$color = $this->social_media_icons_bg->$value;
					
					$bg   = "style='$color;line-height: 32px!important;color: white!important;padding: 2px 5px!important;white-space: nowrap;'";
					
					$class = 'text_only';
				
				}else{
					
					$text = '';
					
					$bg   = '';
					
					$class = 'icons_only';
				}

				if ( isset( $this->plugin_options['wpss_buttons_style_field']) && $this->plugin_options['wpss_buttons_style_field'] == 'square' && $this->plugin_options['wpss_buttons_icontext_field'] == 'icons_only' ) {
					
					echo '<style type="text/css">.a2a_svg, .a2a_count { border-radius: 0 !important; }</style>';
				
				}elseif( isset( $this->plugin_options['wpss_buttons_style_field']) && $this->plugin_options['wpss_buttons_style_field'] == 'circle'  && $this->plugin_options['wpss_buttons_icontext_field'] == 'icons_only' ){
					
					echo '<style type="text/css">.a2a_svg, .a2a_count { border-radius: 100% !important; }</style>';
				}

				$html .= "<a $bg class='a2a_button_$value $class col-xs-6 col-md-6 col-lg-6'>".$text."</a>";
			}
		}
		
		$html .= '</div>';
		
		echo $html;
	}
}

new WppssSharingButtons();
