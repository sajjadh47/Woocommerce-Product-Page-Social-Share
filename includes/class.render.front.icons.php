<?php

/**
 * Add Social Media Buttons To Front End If Available
 */
if ( ! class_exists( 'SSFWC_SHARING_BUTTONS' ) )
{
	class SSFWC_SHARING_BUTTONS
	{
		public $plugin_options;

		public $social_media_icons_bg;

		public $ssfwc_position = 55;

	    public function __construct()
	    {
	    	$this->plugin_options = get_option( SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME );

	    	// check if plugin is enabled to show buttons
	    	if ( isset( $this->plugin_options['ssfwc_show_hide_field'] ) && $this->plugin_options['ssfwc_show_hide_field'] !== 'on' ) return;

	    	// load script
	        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

	        // add async tag to the script
	        add_filter( 'script_loader_tag', [ $this, 'add_async_tag_to_script' ], 10, 3 );

	        // in case file was deleted to avoid fatal error
	        if ( file_exists( SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR . '/assets/data/social_media_icons_bg.json' ) )
	        {
	        	$this->social_media_icons_bg = json_decode( file_get_contents( SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR . '/assets/data/social_media_icons_bg.json' ) );
	        }

	        // check if buttons position is selected
	        if ( isset( $this->plugin_options['ssfwc_buttons_position_field'] ) )
	        {
		        switch ( $this->plugin_options['ssfwc_buttons_position_field'] )
		        {
					case 'ssfwc_position_default':
						
						$this->ssfwc_position = 55;
					break;
					
					case 'ssfwc_position_apt':
						
						$this->ssfwc_position = 8;
					break;
					
					case 'ssfwc_position_bpt':
					
						$this->ssfwc_position = 3;
					break;
					
					case 'ssfwc_position_asd':
						
						$this->ssfwc_position = 25;
					break;
					
					case 'ssfwc_position_aatcb':
						
						$this->ssfwc_position = 35;
					break;
					
					case 'ssfwc_position_bti':
						
						$this->ssfwc_position = 5;
					break;

					case 'ssfwc_position_api':
						
						$this->ssfwc_position = 10;
					break;

					default :

						$this->ssfwc_position = 55;
					break;
				}
			}

			if ( $this->ssfwc_position == 5 )
			{
				add_action( 'woocommerce_after_single_product_summary', [ $this, 'render_social_buttons' ], $this->ssfwc_position );
			}
			else if( $this->ssfwc_position == 10 )
			{	
				add_action( 'woocommerce_product_thumbnails', [ $this, 'render_social_buttons' ], $this->ssfwc_position );
			}
			else
			{
				add_action( 'woocommerce_single_product_summary', [ $this, 'render_social_buttons' ], $this->ssfwc_position );
			}

			add_shortcode( 'ssfwc_social_sharing_buttons', [ $this, 'ssfwc_social_sharing_buttons' ] );
	    }

	    public function enqueue_scripts()
	    {
			wp_enqueue_script( 'ssfwc-addtoany-script', '//static.addtoany.com/menu/page.js', true );

		    wp_enqueue_script( 'ssfwc-public-script', plugins_url( 'assets/js/public.js', SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ), [ 'jquery' ], true );
		    
		    wp_enqueue_style( 'ssfwc-public-stylesheet', plugins_url( 'assets/css/public.css', SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ), true );
		    
		    wp_enqueue_style( 'ssfwc-bootstrap-stylesheet', plugins_url( 'assets/css/vendor/bootstrap/bootstrap.min.css', SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL ), true );
		}

		/**
		 * Add async tag to load addtoadny script asynchronous
		 */
		public function add_async_tag_to_script( $tag, $handle, $src )
		{
		    if ( 'ssfwc_addtoany_script' != $handle ) return $tag;

		    return str_replace( '<script', '<script async', $tag );
		}

		public function ssfwc_social_sharing_buttons()
		{	
			ob_start();

				$this->render_social_buttons();

				$html = ob_get_contents();

			ob_end_clean();

			return $html;
		}

		public function render_social_buttons()
		{
			$allowed_html = wp_kses_allowed_html( 'post' );
			
			$html  = '<div class="ssfwc_social_share_buttons not_before_tab row row-cols-1 a2a_kit a2a_kit_size_32 a2a_default_style">';

			$social_services = explode( ",", $this->plugin_options['ssfwc_buttons_list_field'] );

			foreach ( $social_services as $value )
			{	
				if ( ! empty( $value ) )
				{
					if ( isset( $this->plugin_options['ssfwc_buttons_icontext_field'] ) && $this->plugin_options['ssfwc_buttons_icontext_field'] == 'text_icons' )
					{	
						$text = ucwords( str_replace( "_", " ", $value ) );
						
						$color = $this->social_media_icons_bg->$value;
						
						$bg   = "style='$color;line-height: 32px!important;color: white!important;padding: 2px 5px!important;white-space: nowrap;margin: 2px;'";
						
						$class = 'text_only';
					}
					else
					{	
						$text = '';
						
						$bg   = '';
						
						$class = 'icons_only';
					}

					if ( isset( $this->plugin_options['ssfwc_buttons_style_field']) && $this->plugin_options['ssfwc_buttons_style_field'] == 'square' && $this->plugin_options['ssfwc_buttons_icontext_field'] == 'icons_only' )
					{	
						echo '<style type="text/css">.a2a_svg, .a2a_count { border-radius: 0 !important; }</style>';
					}
					elseif( isset( $this->plugin_options['ssfwc_buttons_style_field']) && $this->plugin_options['ssfwc_buttons_style_field'] == 'circle'  && $this->plugin_options['ssfwc_buttons_icontext_field'] == 'icons_only' )
					{	
						echo '<style type="text/css">.a2a_svg, .a2a_count { border-radius: 100% !important; }</style>';
					}

					$html .= "<a $bg class='a2a_button_$value $class col'>" . $text . "</a>";
				}
			}
			
			$html .= '</div>';
			
			echo wp_kses_post( $html );
		}
	}

	new SSFWC_SHARING_BUTTONS();
}
