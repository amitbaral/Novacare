<?php
if( ! defined( 'ABSPATH' ) )  { die(); }

global $avia_config;

if( ! is_array( $avia_config ) )
{
	$avia_config = array();
}

/**
 * With WP 5.8 block editor was introduced to widget page. This is not supported by Enfold.
 * Based on https://wordpress.org/plugins/classic-widgets/ we disable this feature.
 *
 * For users who need to use it we updated our widgets but preview is not supported properly.
 *
 * ACTIVATING THIS FEATURE IS NOT SUPPORTED and in trial beta !!
 * =============================================================
 *
 * @since 4.9			started to update widgets to support Block Widget editor - but this is only in trial BETA and preview is not supported properly !!
 */
if( ! current_theme_supports( 'avia_enable_widgets_block_editor' ) )
{
	// Disables the block editor from managing widgets in the Gutenberg plugin.
	add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );

	// Disables the block editor from managing widgets.
	add_filter( 'use_widgets_block_editor', '__return_false' );
}

/*
 * if you run a child theme and don't want to load the default functions.php file
 * set the global var below in you childthemes function.php to true:
 *
 * example: global $avia_config; $avia_config['use_child_theme_functions_only'] = true;
 * The default functions.php file will then no longer be loaded. You need to make sure then
 * to include framework and functions that you want to use by yourself.
 *
 * This is only recommended for advanced users
 */
if( isset( $avia_config['use_child_theme_functions_only'] ) )
{
	return;
}

/**
 * Disable loading of non active plugin support
 */
$checkfile = trailingslashit( pathinfo( __FILE__, PATHINFO_DIRNAME ) ) . 'config-include.php';
if( file_exists( $checkfile ) )
{
	require_once $checkfile;
}

/*
 * create a global var which stores the ids of all posts which are displayed on the current page. It will help us to filter duplicate posts
 */
$avia_config['posts_on_current_page'] = array();


/*
 * wpml multi site config file
 * needs to be loaded before the framework
 */
if( ! current_theme_supports( 'avia_exclude_wpml' ) )
{
	require_once( 'config-wpml/config.php' );
}

/**
 * layerslider plugin - needs to be loaded before framework because we need to add data to the options array
 *
 * To be backwards compatible we still support  add_theme_support('deactivate_layerslider');
 * This will override the option setting "activation" of the bundled plugin !!
 *
 * @since 4.2.1
 */
require_once( 'config-layerslider/config.php' );


/*
 * These are the available color sets in your backend.
 * If more sets are added users will be able to create additional color schemes for certain areas
 *
 * The array key has to be the class name, the value is only used as tab heading on the styling page
 */
$color_sets = array(
				'header_color'      => 'Logo Area',
				'main_color'        => 'Main Content',
				'alternate_color'   => 'Alternate Content',
				'footer_color'      => 'Footer',
				'socket_color'      => 'Socket'
			);

/**
 * @since 4.8.6.3
 * @param array $color_sets
 * @return array
 */
$avia_config['color_sets'] = apply_filters( 'avf_color_sets', $color_sets );

/*
 * Define a set of colors to be used by ALB color selection popup in the palette below the iris select window
 * Can be filtered before rendering to backend
 *
 * @since 4.8.3
 */
$avia_config['default_alb_color_palette'] = array( '#000000', '#ffffff', '#B02B2C', '#edae44', '#eeee22', '#83a846', '#7bb0e7', '#745f7e', '#5f8789', '#d65799', '#4ecac2' );

/*
 * add support for responsive mega menus
 */
add_theme_support( 'avia_mega_menu' );



/*
 * add support for improved backend styling
 */
add_theme_support( 'avia_improved_backend_style' );

/**
 * add support for toggles in option page instead of checkboxes
 *
 * @since 4.6.3
 */
add_theme_support( 'avia_option_pages_toggles' );


/*
 * deactivates the default mega menu and allows us to pass individual menu walkers when calling a menu
 */
add_filter( 'avia_mega_menu_walker', '__return_false' );


/*
 * adds support for the new avia sidebar manager
 */
add_theme_support( 'avia_sidebar_manager' );


/*
 * Filters for post formats etc
 */
//add_theme_support( 'avia_queryfilter' );


/*
 * Register theme text domain
 */
if( ! function_exists( 'avia_lang_setup' ) )
{
	add_action( 'after_setup_theme', 'avia_lang_setup' );

	function avia_lang_setup()
	{
		$lang = apply_filters( 'ava_theme_textdomain_path', get_template_directory()  . '/lang' );
		load_theme_textdomain( 'avia_framework', $lang );
	}

	avia_lang_setup();
}


/*
 * function that changes the icon of the  theme update tab
 */
if( ! function_exists( 'avia_theme_update_filter' ) )
{
	function avia_theme_update_filter( $data )
	{
		if( current_theme_supports( 'avia_improved_backend_style' ) )
		{
			$data['icon'] = 'new/arrow-repeat-two-7@3x.png';
		}
		return $data;
	}

	add_filter( 'avf_update_theme_tab', 'avia_theme_update_filter', 30, 1 );
}

/**
 * Needed by framework options page already - not only in frontend
 */
require_once 'includes/helper-privacy.php'; 					// holds privacy managment shortcodes and functions

##################################################################
# AVIA FRAMEWORK by Kriesi

# this include calls a file that automatically includes all
# the files within the folder framework and therefore makes
# all functions and classes available for later use

require_once 'framework/avia_framework.php';

##################################################################

/**
 * Init object so we can hook and add user defined image sizes
 */
$resp_images = Av_Responsive_Images();

/*
 * Register additional image thumbnail sizes
 * Those thumbnails are generated on image upload!
 *
 * If the size of an array was changed after an image was uploaded you either need to re-upload the image
 * or use the thumbnail regeneration plugin: http://wordpress.org/extend/plugins/regenerate-thumbnails/
 */

$avia_config['imgSize']['widget'] 			 	= array('width'=>36,  'height'=>36);						// small preview pics eg sidebar news
$avia_config['imgSize']['square'] 		 	    = array('width'=>180, 'height'=>180);		                 // small image for blogs
$avia_config['imgSize']['featured'] 		 	= array('width'=>1500, 'height'=>430 );						// images for fullsize pages and fullsize slider
$avia_config['imgSize']['featured_large'] 		= array('width'=>1500, 'height'=>630 );						// images for fullsize pages and fullsize slider
$avia_config['imgSize']['extra_large'] 		 	= array('width'=>1500, 'height'=>1500 , 'crop' => false);	// images for fullscrren slider
$avia_config['imgSize']['portfolio'] 		 	= array('width'=>495, 'height'=>400 );						// images for portfolio entries (2,3 column)
$avia_config['imgSize']['portfolio_small'] 		= array('width'=>260, 'height'=>185 );						// images for portfolio 4 columns
$avia_config['imgSize']['gallery'] 		 		= array('width'=>845, 'height'=>684 );						// images for portfolio entries (2,3 column)
$avia_config['imgSize']['magazine'] 		 	= array('width'=>710, 'height'=>375 );						// images for magazines
$avia_config['imgSize']['masonry'] 		 		= array('width'=>705, 'height'=>705 , 'crop' => false);		// images for fullscreen masonry
$avia_config['imgSize']['entry_with_sidebar'] 	= array('width'=>845, 'height'=>321);		            	// big images for blog and page entries
$avia_config['imgSize']['entry_without_sidebar']= array('width'=>1210, 'height'=>423 );						// images for fullsize pages and fullsize slider

/**
 * @used_by							av_responsive_images				10
 * @param array $avia_config['imgSize']
 * @return array
 */
$avia_config['imgSize'] = apply_filters( 'avf_modify_thumb_size', $avia_config['imgSize'] );


$avia_config['selectableImgSize'] = array(
			'square' 				=> __( 'Square', 'avia_framework' ),
			'featured'  			=> __( 'Featured Thin', 'avia_framework' ),
			'featured_large'  		=> __( 'Featured Large', 'avia_framework' ),
			'portfolio' 			=> __( 'Portfolio', 'avia_framework' ),
			'gallery' 				=> __( 'Gallery', 'avia_framework'),
			'entry_with_sidebar' 	=> __( 'Entry with Sidebar', 'avia_framework' ),
			'entry_without_sidebar'	=> __( 'Entry without Sidebar', 'avia_framework' ),
			'extra_large' 			=> __( 'Fullscreen Sections/Sliders', 'avia_framework' )
		);

/**
 * @since 4.5.7.2
 * @param array $avia_config['selectableImgSize']
 * @param array $avia_config['imgSize']
 * @return array
 */
$avia_config['selectableImgSize'] = apply_filters( 'avf_modify_selectable_image_sizes', $avia_config['selectableImgSize'], $avia_config['imgSize'] );


$avia_config['readableImgSize'] = $avia_config['selectableImgSize'];

$avia_config['readableImgSize']['widget'] =				__( 'Widget', 'avia_framework' );
$avia_config['readableImgSize']['portfolio_small'] =	__( 'Portfolio small', 'avia_framework' );
$avia_config['readableImgSize']['magazine'] =			__( 'Magazine', 'avia_framework' );
$avia_config['readableImgSize']['masonry'] =			__( 'Masonry', 'avia_framework' );

/**
 * @used_by						av_responsive_images				10
 * @since 4.5.7.2
 * @param array $avia_config['readableImgSize']
 * @param array $avia_config['imgSize']
 * @return array
 */
$avia_config['readableImgSize'] = apply_filters( 'avf_modify_readable_image_sizes', $avia_config['readableImgSize'], $avia_config['imgSize'] );


/**
 * Get options and reinit responsive image object
 */
$resp_img_config = array(
		'default_jpeg_quality'	=> 100,						//	ensure best image quality - use filter avf_responsive_images_defaults to change
		'theme_images'			=> $avia_config['imgSize'],
		'readableImgSizes'		=> $avia_config['readableImgSize'],
		'no_lazy_loading_ids'	=> array()					//	add is's of images for permanently disable lazy loading attribute
	);

$resp_images->reinit( $resp_img_config );



avia_backend_add_thumbnail_size( $avia_config );

if ( ! isset( $content_width ) )
{
	$content_width = $avia_config['imgSize']['featured']['width'];
}



/*
 * register the layout classes
 *
 */

$avia_config['layout']['fullsize'] 		= array('content' => 'av-content-full alpha', 'sidebar' => 'hidden', 	  	  'meta' => '','entry' => '');
$avia_config['layout']['sidebar_left'] 	= array('content' => 'av-content-small', 	  'sidebar' => 'alpha' ,'meta' => 'alpha', 'entry' => '');
$avia_config['layout']['sidebar_right'] = array('content' => 'av-content-small alpha','sidebar' => 'alpha', 'meta' => 'alpha', 'entry' => 'alpha');





/*
 * These are some of the font icons used in the theme, defined by the entypo icon font. the font files are included by the new aviaBuilder
 * common icons are stored here for easy retrieval
 */

 $avia_config['font_icons'] = apply_filters( 'avf_default_icons', array(

    //post formats +  types
    'standard' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue836'),
    'link'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue822'),
    'image'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80f'),
    'audio'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue801'),
    'quote'   		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue833'),
    'gallery'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80e'),
    'video'   		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80d'),
    'portfolio'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue849'),
    'product'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue859'),

    //social
    'behance' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue915'),
	'dribbble' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fe'),
	'facebook' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f3'),
	'flickr' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8ed'),
	'linkedin' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fc', 'display_name' => 'LinkedIn' ),
	'instagram' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'uf16d'),
	'pinterest' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f8'),
	'whatsapp'		=> array( 'font' =>'entypo-fontello', 'icon' => 'uf232', 'display_name' => 'WhatsApp' ),
	'skype' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue90d'),
	'tumblr' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fa'),
	'twitter' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f1'),
	'vimeo' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8ef'),
	'rss' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue853'),
	'yelp'			=> array( 'font' =>'entypo-fontello', 'icon' => 'uf1e9'),
	'youtube'		=> array( 'font' =>'entypo-fontello', 'icon' => 'uf16a'),
	'xing'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue923'),
	'soundcloud'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue913'),
	'five_100_px'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue91d', 'display_name' => '500px' ),
	'vk'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue926'),
	'reddit'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue927'),
	'digg'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue928'),
	'delicious'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue929'),
	'mail' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue805'),

	//woocomemrce
	'cart' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue859'),
	'details'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue84b'),

	//bbpress
	'supersticky'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue808'),
	'sticky'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue809'),
	'one_voice'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue83b'),
	'multi_voice'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue83c'),
	'closed'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue824'),
	'sticky_closed' => array( 'font' =>'entypo-fontello', 'icon' => 'ue808\ue824'),
	'supersticky_closed' => array( 'font' =>'entypo-fontello', 'icon' => 'ue809\ue824'),

	//navigation, slider & controls
	'play' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue897'),
	'pause'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue899'),
	'next'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue879'),
    'prev'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue878'),
    'next_big'  	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue87d'),
    'prev_big'  	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue87c'),
	'close'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue814'),
	'reload'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue891'),
	'mobile_menu'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8a5'),

	//image hover overlays
    'ov_external'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue832'),
    'ov_image'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue869'),
    'ov_video'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue897'),

	//misc
    'search'  		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue803'),
    'info'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue81e'),
	'clipboard' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8d1'),
	'scrolltop' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue876'),
	'scrolldown' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue877'),
	'bitcoin' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue92a'),

	'locked_option'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue824' ),

));




/*
 * a small array that contains admin notices that can, for example, be called after an update
 * just set the db option avia_admin_notice to contain the key of the notice you want to display
 * eg: update_option('avia_admin_notice', 'performance_update');
 *
 * classes: error, warning, success, info
 * msg: whatever floats your boat :D
 */

$avia_config['admin_notices'] = array(

	//default update success
	'update_success' 		=> array('class'=>'success', 'msg' => __('Enfold update was successful! ','avia_framework')),

	//update to version 4.3 - performance update. display notice and link to blog post
	'performance_update' 	=> array('class'=>'info', 	 'msg' => "<strong>Attention:</strong> The last Enfold update added a lot of performance options. Make sure to read more about it <a href='https://kriesi.at/archives/enfold-4-3-performance-update' target='_blank' rel='noopener noreferrer'>here</a><br><br>If you are running a caching plugin please make sure to reset your cached files, since the CSS and JS file structure of the theme changed heavily"
	),

	//update to version 4.4 - gdpr update. display notice and link to blog post
	'gdpr_update' 	=> array('class'=>'info', 	 'msg' => "<strong>Attention:</strong> Enfold was updated for GDPR compliance. Make sure to read more about it <a href='https://kriesi.at/archives/enfold-4-4-and-the-gdpr-general-data-protection-regulation' target='_blank' rel='noopener noreferrer'>here</a>"
	),

	'gdpr_update_2'	=> array(
						'class'	=> 'info',
						'msg'	=> '<strong>Attention:</strong> Enfold GDPR compliance has been extended to support &quot;Must Opt In&quot;. Options and shortcodes have been extended. Please read the description on the theme options tab carefully and check the extended documentation <a href="https://kriesi.at/documentation/enfold/privacy-cookies/#implementation-of-data-security-in-enfold" target="_blank" rel="noopener noreferrer">here</a>.'
			)


	//more to come...
);





add_theme_support( 'automatic-feed-links' );

##################################################################
# Frontend Stuff necessary for the theme:
##################################################################



/*
 * Register frontend javascripts:
 */
if( ! function_exists( 'avia_register_frontend_scripts' ) )
{
	if( ! is_admin() )
	{
		add_action( 'wp_enqueue_scripts', 'avia_register_frontend_scripts' );
	}

	function avia_register_frontend_scripts()
	{
		global $avia_config;

		$vn = avia_get_theme_version();
		$options = avia_get_option();

		$template_url = get_template_directory_uri();
		$child_theme_url = get_stylesheet_directory_uri();

		//register js
		wp_enqueue_script( 'avia-compat', $template_url . '/js/avia-compat.js', array(), $vn, false ); //needs to be loaded at the top to prevent bugs
		wp_enqueue_script( 'avia-default', $template_url . '/js/avia.js', array( 'jquery' ), $vn, true );
		wp_enqueue_script( 'avia-hamburger-menu', $template_url . '/js/avia-snippet-hamburger-menu.js', array( 'jquery', 'avia-default' ), $vn, true );
		wp_enqueue_script( 'avia-shortcodes', $template_url . '/js/shortcodes.js', array( 'jquery', 'avia-default' ), $vn, true );
		wp_enqueue_script( 'avia-parallax', $template_url . '/js/parallax.js', array( 'jquery', 'avia-default' ), $vn, true );
		wp_enqueue_script( 'avia-parallax-support', $template_url . '/js/avia-snippet-parallax.js', array( 'jquery', 'avia-default' ), $vn, true );

		wp_enqueue_script( 'jquery' );


		//register styles
		wp_register_style( 'avia-style',  $child_theme_url . '/style.css', array(), $vn, 'all' ); //only include in childthemes. has no purpose in main theme
		wp_register_style( 'avia-custom',  $template_url . '/css/custom.css', array(), $vn, 'all' );

		wp_enqueue_style( 'avia-grid', $template_url . '/css/grid.css', array(), $vn, 'all' );
		wp_enqueue_style( 'avia-base', $template_url . '/css/base.css', array('avia-grid'), $vn, 'all' );
		wp_enqueue_style( 'avia-layout', $template_url . '/css/layout.css', array('avia-base'), $vn, 'all' );
		wp_enqueue_style( 'avia-scs', $template_url . '/css/shortcodes.css', array('avia-layout'), $vn, 'all' );


		/************************************************************************
		Conditional style and script calling, based on theme options or other conditions
		*************************************************************************/

		//lightbox inclusion
		$condition = ! empty( $avia_config['use_standard_lightbox'] ) && ( 'disabled' != $avia_config['use_standard_lightbox'] );
		avia_enqueue_style_conditionally( $condition, 'avia-popup-css', $template_url . '/js/aviapopup/magnific-popup.css', array( 'avia-layout' ), $vn, 'screen' );
		avia_enqueue_style_conditionally( $condition, 'avia-lightbox', $template_url . '/css/avia-snippet-lightbox.css', array( 'avia-layout' ), $vn, 'screen' );
		avia_enqueue_script_conditionally( $condition, 'avia-popup-js', $template_url . '/js/aviapopup/jquery.magnific-popup.min.js', array( 'jquery' ), $vn, true );
		avia_enqueue_script_conditionally( $condition, 'avia-lightbox-activation', $template_url . '/js/avia-snippet-lightbox.js', array( 'avia-default' ), $vn, true );


		//mega menu inclusion (only necessary with sub menu items)
		$condition = ( avia_get_submenu_count('avia') > 0 );
		avia_enqueue_script_conditionally( $condition, 'avia-megamenu', $template_url . '/js/avia-snippet-megamenu.js', array( 'avia-default' ), $vn, true );


		//sidebar menu inclusion (only necessary when header position is set to be a sidebar)
		$condition = (isset($options['header_position']) && $options['header_position'] != 'header_top');
		avia_enqueue_script_conditionally( $condition , 'avia-sidebarmenu', $template_url.'/js/avia-snippet-sidebarmenu.js', array('avia-default'), $vn, true);


		//sticky header with header size calculator
		$condition  = (isset($options['header_position']) && $options['header_position'] == 'header_top');
		$condition2 = (isset($options['header_sticky']) && $options['header_sticky'] == 'header_sticky') && $condition;
		avia_enqueue_script_conditionally( $condition2 , 'avia-sticky-header', $template_url.'/js/avia-snippet-sticky-header.js', array('avia-default'), $vn, true);

		//	footer - curtain behaviour
		$condition = ( isset( $options['color-body_style'] ) && $options['color-body_style'] == 'stretched' );
		avia_enqueue_script_conditionally( $condition, 'avia-footer-effects', $template_url . '/js/avia-snippet-footer-effects.js', array( 'avia-default' ), $vn, true );

		//site preloader
		$condition = (isset($options['preloader']) && $options['preloader'] == 'preloader');
		avia_enqueue_script_conditionally( $condition , 'avia-siteloader-js', $template_url.'/js/avia-snippet-site-preloader.js', array('avia-default'), $vn, true, false);
		avia_enqueue_style_conditionally(  $condition , 'avia-siteloader', $template_url.'/css/avia-snippet-site-preloader.css', array('avia-layout'), $vn, 'screen', false);


		//load widget assets only if we got active widgets
		$condition = (avia_get_active_widget_count() > 0);
        avia_enqueue_script_conditionally( $condition , 'avia-widget-js' , $template_url.'/js/avia-snippet-widget.js', array('avia-default'), $vn, true);
		avia_enqueue_style_conditionally(  $condition , 'avia-widget-css', $template_url.'/css/avia-snippet-widget.css', array('avia-layout'), $vn, 'screen');


		//load mediaelement js
		$opt_mediaelement = isset( $options['disable_mediaelement'] ) ? $options['disable_mediaelement'] : '';

		$condition = true;
		if( 'force_mediaelement' != $opt_mediaelement )
		{
			$condition  = ( $opt_mediaelement != 'disable_mediaelement' ) && av_video_assets_required();
		}

		/**
		 * Allow to force loading of WP media element for 3rd party plugins. Nedded for wp_enqueue_media() to load properly.
		 *
		 * @since 4.1.2
		 * @param boolean $condition
		 * @param array $options
		 * @return boolean
		 */
		$condition = apply_filters( 'avf_enqueue_wp_mediaelement', $condition, $options );

		$condition2 = ( version_compare( get_bloginfo( 'version' ), '4.9', '>=' ) ) && $condition;
		avia_enqueue_script_conditionally( $condition , 'wp-mediaelement');
		avia_enqueue_style_conditionally( $condition2 , 'wp-mediaelement'); //With WP 4.9 we need to load the stylesheet seperately


		//comment reply script
		global $post;
		$condition = ! ( isset( $options['disable_blog'] ) && $options['disable_blog'] == 'disable_blog' ) && $post && comments_open();
		$condition = ( is_singular() && get_option( 'thread_comments' ) ) && $condition;
		avia_enqueue_script_conditionally( $condition , 'comment-reply' );



		//rtl inclusion
		avia_enqueue_style_conditionally( is_rtl(), 'avia-rtl', $template_url . '/css/rtl.css', array(), $vn, 'all' );


		//disable jquery migrate if no plugins are active (enfold does not need it) or if user asked for it in optimization options
		$condition = avia_count_active_plugins() == 0 || ( isset( $options['disable_jq_migrate'] ) && $options['disable_jq_migrate'] != 'disable_jq_migrate' );
		if( ! $condition )
		{
			avia_disable_query_migrate();
		}

		//move jquery to footer if no unkown plugins are active
		if( av_count_untested_plugins() == 0 || ( isset( $options['jquery_in_footer'] ) && $options['jquery_in_footer'] == 'jquery_in_footer' ) )
		{
			av_move_jquery_into_footer();
		}

		/************************************************************************
		Inclusion of the dynamic stylesheet
		*************************************************************************/


        global $avia;

		$safe_name = avia_backend_safe_string( $avia->base_data['prefix'] );
		$safe_name = apply_filters( 'avf_dynamic_stylesheet_filename', $safe_name );

        if( get_option( 'avia_stylesheet_exists' . $safe_name) == 'true' )
        {
            $avia_upload_dir = wp_upload_dir();

			/**
			 * Change the default dynamic upload url
			 *
			 * @since 4.4
			 */
			$avia_dyn_upload_path = apply_filters( 'avf_dyn_stylesheet_dir_url',  $avia_upload_dir['baseurl'] . '/dynamic_avia' );
			$avia_dyn_upload_path = trailingslashit( $avia_dyn_upload_path );

            if( is_ssl() )
			{
				$avia_dyn_upload_path = str_replace( 'http://', 'https://', $avia_dyn_upload_path );
			}

			/**
			 * Change the default dynamic stylesheet name
			 *
			 * @since 4.4
			 */
			$avia_dyn_stylesheet_url = apply_filters( 'avf_dyn_stylesheet_file_url', $avia_dyn_upload_path . $safe_name . '.css' );

			$version_number = get_option( 'avia_stylesheet_dynamic_version' . $safe_name );
			if( empty( $version_number ) )
			{
				$version_number = $vn;
			}

            wp_enqueue_style( 'avia-dynamic', $avia_dyn_stylesheet_url, array(), $version_number, 'all' );
        }

		wp_enqueue_style( 'avia-custom' );

		if( $child_theme_url != $template_url )
		{
			wp_enqueue_style( 'avia-style' );
		}
	}
}


if( ! function_exists( 'avia_remove_default_video_styling' ) )
{
	if( ! is_admin() )
	{
		add_action( 'wp_footer', 'avia_remove_default_video_styling', 1 );
	}

	function avia_remove_default_video_styling()
	{
		/**
		 * remove default style for videos
		 *
		 * With WP 4.9 we need to load the stylesheet seperately - therefore we must not remove it
		 */
		if( version_compare( get_bloginfo( 'version' ), '4.9', '<' ) )
		{
			wp_dequeue_style( 'mediaelement' );
		}

		// wp_dequeue_script( 'wp-mediaelement' );
		// wp_dequeue_style( 'wp-mediaelement' );
	}
}


/*
 * Activate native wordpress navigation menu and register a menu location
 */
if( ! function_exists( 'avia_nav_menus' ) )
{
	function avia_nav_menus()
	{
		global $avia_config, $wp_customize;

		add_theme_support( 'nav_menus' );

		foreach( $avia_config['nav_menus'] as $key => $value )
		{
			//wp-admin\customize.php does not support html code in the menu description - thus we need to strip it
			$name = ( ! empty( $value['plain'] ) && ! empty( $wp_customize ) ) ? $value['plain'] : $value['html'];
			register_nav_menu( $key, THEMENAME . ' ' . $name );
		}
	}

	$avia_config['nav_menus'] = array(
							'avia'	=> array(
										'html'	=> __( 'Main Menu', 'avia_framework' )
										),
							'avia2'	=> array(
										'html'	=> __( 'Secondary Menu', 'avia_framework' ) . ' <br/><small>(' . __( 'Will be displayed if you selected a header layout that supports a submenu', 'avia_framework' ) . ' <a target="_blank" href="' . admin_url( '?page=avia#goto_header' ) . '">' . __( 'here', 'avia_framework' ) . '</a>)</small>',
										'plain'	=> __( 'Secondary Menu - will be displayed if you selected a header layout that supports a submenu', 'avia_framework')
										),
							'avia3'	=> array(
										'html'	=> __( 'Footer Menu <br/><small>(no dropdowns)</small>', 'avia_framework' ),
										'plain'	=> __( 'Footer Menu (no dropdowns)', 'avia_framework' )
										)
									);

	avia_nav_menus(); //call the function immediately to activate
}

/*
 *  load some frontend functions in folder include:
 */
require_once( 'includes/admin/register-portfolio.php' );		// register custom post types for portfolio entries
require_once( 'includes/admin/register-widget-area.php' );		// register sidebar widgets for the sidebar and footer
require_once( 'includes/loop-comments.php' );					// necessary to display the comments properly
require_once( 'includes/helper-template-logic.php' ); 			// holds the template logic so the theme knows which templates to use
require_once( 'includes/helper-social-media.php' );			// holds some helper functions necessary for twitter and facebook buttons
require_once( 'includes/helper-post-format.php' ); 			// holds actions and filter necessary for post formats
require_once( 'includes/helper-markup.php' ); 					// holds the markup logic (schema.org and html5)
require_once( 'includes/helper-assets.php' ); 					// holds asset managment functions
require_once( 'includes/class-avia-custom-pages.php' ); 		// holds management functions for custom pages like 404, maintenance, footer page
require_once( 'includes/class-responsive-typo.php' );			// management for responsive typos in theme options page

if( current_theme_supports( 'avia_conditionals_for_mega_menu' ) )
{
	require_once( 'includes/helper-conditional-megamenu.php' );  // holds the walker for the responsive mega menu
}

require_once( 'includes/helper-responsive-megamenu.php' ); 	// holds the walker for the responsive mega menu

//adds the plugin initalization scripts that add styles and functions
require_once( 'config-gutenberg/class-avia-gutenberg.php' );	//	gutenberg - might be necessary to move when part of WP core

if( ! current_theme_supports( 'avia_exclude_bbPress' ) )
{
	require_once( 'config-bbpress/config.php' );				//compatibility with  bbpress forum plugin
}

require_once( 'config-templatebuilder/config.php' );				//templatebuilder plugin

if( ! current_theme_supports( 'avia_exclude_GFForms' ) )
{
	require_once( 'config-gravityforms/config.php' );			//compatibility with gravityforms plugin
}

if( ! current_theme_supports( 'avia_exclude_pojo_accessibility' ) )
{
	require_once( 'config-pojo-accessibility/class-avia-pojo-accessibility.php' );	//compatibility with "One Click Accessibility" plugin
}

if( ! current_theme_supports( 'avia_exclude_wp_accessibility' ) )
{
	require_once( 'config-wp-accessibility/class-avia-wp-accessibility.php' );		//compatibility with "WP Accessibility" plugin
}

if( ! current_theme_supports( 'avia_exclude_WooCommerce' ) )
{
	require_once( 'config-woocommerce/woo-loader.php' );			//compatibility with woocommerce plugin
}

if( ! current_theme_supports( 'avia_exclude_wpSEO' ) )
{
	require_once( 'config-wordpress-seo/config.php' );				//compatibility with Yoast WordPress SEO plugin
}

if( ! current_theme_supports( 'avia_exclude_rank_math' ) )
{
	require_once( 'config-rank-math/config.php' );				//compatibility with Rank Math SEO plugin
}

if( ! current_theme_supports( 'avia_exclude_menu_exchange' ) )
{
	require_once( 'config-menu-exchange/config.php' );				//compatibility with Zen Menu Logic and Themify_Conditional_Menus plugin
}

if( ! current_theme_supports( 'avia_exclude_relevanssi' ) )
{
	require_once( 'config-relevanssi/class-avia-relevanssi.php' );	//compatibility with relevanssi plugin
}

if( ! current_theme_supports( 'deactivate_tribe_events_calendar' ) )
{
	require_once( 'config-events-calendar/config.php' );			//compatibility with the Events Calendar plugin
}

if( ! current_theme_supports( 'avia_exclude_instagram_feed' ) )
{
	require_once( 'config-instagram-feed/class-avia-instagram-feed.php' );		//compatibility with Smash Balloon Instagram Feed plugin
}

if( ! current_theme_supports( 'avia_exclude_leaflet_map' ) )
{
	require_once( 'config-leaflet-maps/class-avia-leaflet-maps.php' );		//compatibility with Leflet Maps plugin
}

// if(is_admin())
require_once( 'includes/admin/helper-compat-update.php');			// include helper functions for new versions



/*
 *  dynamic styles for front and backend
 */
if( ! function_exists( 'avia_custom_styles' ) )
{
	function avia_custom_styles()
	{
		require_once( 'includes/admin/register-dynamic-styles.php' );	// register the styles for dynamic frontend styling
		avia_prepare_dynamic_styles();
	}

	add_action( 'init', 'avia_custom_styles', 20 );
	add_action( 'admin_init', 'avia_custom_styles', 20 );
}

/*
 *  add post format options
 */
add_theme_support( 'post-formats', array( 'link', 'quote', 'gallery', 'video', 'image', 'audio' ) );



/*
 *  Remove the default shortcode function, we got new ones that are better ;)
 */
//add_theme_support( 'avia-disable-default-shortcodes', true );


/*
 * compat mode for easier theme switching from one avia framework theme to another
 */
add_theme_support( 'avia_post_meta_compat' );


/*
 * make sure that enfold widgets dont use the old slideshow parameter in widgets, but default post thumbnails
 */
add_theme_support( 'force-post-thumbnails-in-widget' );


/*
 * display page titles via wordpress default output
 *
 * @since 3.6
 */
function av_theme_slug_setup()
{
   add_theme_support( 'title-tag' );
}

add_action( 'after_setup_theme', 'av_theme_slug_setup' );

/*title fallback (up to WP 4.1)*/
if ( ! function_exists( '_wp_render_title_tag' ) )
{
    function av_theme_slug_render_title()
    {
	    echo '<title>' . avia_set_title_tag() . '</title>';
	}
	add_action( 'wp_head', 'av_theme_slug_render_title' );
}

if( ! function_exists( 'avia_add_admin_body_class' ) )
{
	/**
	 * Add a class to identify that enfold is used
	 *
	 * @since 4.7.4.1
	 * @param string $classes
	 * @return string
	 */
	function avia_add_admin_body_class( $classes )
	{
		$classes .= ' enfold-active';

		return $classes;
	}
	add_filter( 'admin_body_class', 'avia_add_admin_body_class', 10, 1 );
}

if( ! function_exists( 'avia_disable_seo_analysis_delay_cb' ) )
{
    if ( current_theme_supports( 'avia_disable_seo_analysis_delay' ) ) 
    {   
        add_filter( 'admin_body_class', 'avia_disable_seo_analysis_delay_cb', 10, 1 );
    }
   
	/**
	 * Add a unique name to the body class attribute,
	 * which can be checked to adjust the analysis tool delay.
	 *
	 * @since 5.0
	 * @return array
	 */
	function avia_disable_seo_analysis_delay_cb($classes) {
        $classes .= ' avia-seo-analysis-no-delay';
		return $classes;
	}
}


/*
 *  register custom functions that are not related to the framework but necessary for the theme to run
 */
require_once( 'functions-enfold.php' );

/**
 * Novacare SEO Suburb Landing Pages Integration
 */
if ( file_exists( get_template_directory() . '/includes/suburb-data.php' ) ) {
    require_once get_template_directory() . '/includes/suburb-data.php';
}

// Shortcode to display the 20 Suburbs Interlinking Matrix on any page/post
function novacare_suburbs_grid_shortcode( $atts ) {
    if ( ! function_exists( 'novacare_get_suburbs_data' ) ) {
        return '';
    }
    $all_suburbs = novacare_get_suburbs_data();
    
    ob_start();
    ?>
    <div class="nc-suburbs-grid-wrapper" style="margin: 40px 0;">
        <h3 style="font-size: 1.5rem; color: #3B0764; font-weight: 700; margin-bottom: 20px;">Surrounding Sutherland Shire Suburbs We Serve</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            <?php foreach ( $all_suburbs as $key => $sub_item ) : 
                $link = home_url( '/' . $sub_item['slug'] . '/' );
                ?>
                <a href="<?php echo esc_url( $link ); ?>" style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.2s ease;">
                    <strong style="display: block; color: #3B0764; font-size: 0.98rem;"><?php echo esc_html( $sub_item['name'] ); ?></strong>
                    <span style="font-size: 0.8rem; color: #6B7280;">Postcode <?php echo esc_html( $sub_item['postcode'] ); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'novacare_suburbs_grid', 'novacare_suburbs_grid_shortcode' );

/**
 * Dynamic Suburb Page Template Auto-Loader
 * Automatically routes all 20 suburb URLs to template-suburb.php without requiring manual WP database pages.
 */
function novacare_suburb_template_loader( $template ) {
    if ( ! function_exists( 'novacare_get_suburbs_data' ) || is_admin() ) {
        return $template;
    }
    
    $request_uri = trim( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
    $segments = explode( '/', $request_uri );
    $current_slug = end( $segments );
    
    if ( empty( $current_slug ) ) {
        return $template;
    }

    $all_suburbs = novacare_get_suburbs_data();
    foreach ( $all_suburbs as $key => $suburb_info ) {
        if ( $suburb_info['slug'] === $current_slug || $key === $current_slug ) {
            $suburb_template = get_template_directory() . '/template-suburb.php';
            if ( file_exists( $suburb_template ) ) {
                global $wp_query;
                if ( is_object( $wp_query ) ) {
                    $wp_query->is_404 = false;
                    $wp_query->is_page = true;
                    $wp_query->is_singular = true;
                    status_header( 200 );
                }
                return $suburb_template;
            }
        }
    }
    return $template;
}
add_filter( 'template_include', 'novacare_suburb_template_loader', 99 );

/**
 * Enqueue Inter Google Font Globally
 */
function novacare_enqueue_inter_font() {
    wp_enqueue_style( 
        'novacare-google-font-inter', 
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', 
        array(), 
        null 
    );
}
add_action( 'wp_enqueue_scripts', 'novacare_enqueue_inter_font', 5 );

/**
 * ============================================================================
 * NOVA CARE AUSTRALIA - SEO & GENERATIVE ENGINE OPTIMIZATION (GEO) SUITE
 * ============================================================================
 */

/**
 * 1. Allow AI Crawlers & Link Sitemaps in robots.txt
 */
if ( ! function_exists( 'novacare_ai_robots_txt' ) ) {
    function novacare_ai_robots_txt( $output, $public ) {
        $ai_bots = "
# Generative Engine Optimization (GEO) - AI Scraper Access
User-agent: GPTBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: Bytespider
Allow: /

# Sitemap Declarations
Sitemap: " . esc_url( home_url( '/sitemap_index.xml' ) ) . "
Sitemap: " . esc_url( home_url( '/llms.txt' ) ) . "
";
        return $output . $ai_bots;
    }
    add_filter( 'robots_txt', 'novacare_ai_robots_txt', 99, 2 );
}

/**
 * 2. Dynamic /llms.txt Endpoint Generator for AI Engines
 */
if ( ! function_exists( 'novacare_add_llms_rewrite_rule' ) ) {
    function novacare_add_llms_rewrite_rule() {
        add_rewrite_rule( '^llms\.txt$', 'index.php?novacare_llms=1', 'top' );
    }
    add_action( 'init', 'novacare_add_llms_rewrite_rule' );
}

if ( ! function_exists( 'novacare_add_llms_query_var' ) ) {
    function novacare_add_llms_query_var( $vars ) {
        $vars[] = 'novacare_llms';
        return $vars;
    }
    add_filter( 'query_vars', 'novacare_add_llms_query_var' );
}

if ( ! function_exists( 'novacare_render_llms_txt' ) ) {
    function novacare_render_llms_txt() {
        if ( get_query_var( 'novacare_llms' ) ) {
            header( 'Content-Type: text/plain; charset=utf-8' );
            echo "# Nova Care Australia\n\n";
            echo "> Premier NDIS Disability Support & Home Care Package Provider in Sydney & Sutherland Shire.\n\n";
            echo "## Organization Summary\n";
            echo "Nova Care Australia provides compassionate, individualised disability support services under the National Disability Insurance Scheme (NDIS) and Home Care Packages for seniors across Sydney and the Sutherland Shire region.\n\n";
            echo "## Core Services\n";
            echo "- NDIS Supported Independent Living (SIL)\n";
            echo "- Individualised In-Home Care & Support\n";
            echo "- Personal Care & Community Nursing\n";
            echo "- Home Care Packages (Level 1, 2, 3 & 4)\n";
            echo "- Respite Care & Emergency Support\n";
            echo "- Social & Community Participation\n";
            echo "- Transport & Mobility Assistance\n\n";
            echo "## Primary Service Locations (Sutherland Shire & St George Area)\n";
            if ( function_exists( 'novacare_get_suburbs_data' ) ) {
                $suburbs = novacare_get_suburbs_data();
                foreach ( $suburbs as $key => $sub ) {
                    echo "- " . esc_html( $sub['name'] ) . " (Postcode " . esc_html( $sub['postcode'] ) . "): " . esc_url( home_url( '/' . $sub['slug'] . '/' ) ) . "\n";
                }
            } else {
                echo "- Sutherland, Cronulla, Caringbah, Miranda, Engadine, Menai, Gymea, Jannali, Sylvania, Kirrawee\n";
            }
            echo "\n## Official Contact & Verification\n";
            echo "- Phone: 0426 142 162\n";
            echo "- Main Website: " . esc_url( home_url( '/' ) ) . "\n";
            echo "- Contact Page: " . esc_url( home_url( '/contact/' ) ) . "\n";
            echo "- NDIS Registered Provider Services: Yes\n";
            exit;
        }
    }
    add_action( 'template_redirect', 'novacare_render_llms_txt' );
}

/**
 * Auto-flush rewrite rules once if llms.txt isn't registered yet
 */
if ( ! function_exists( 'novacare_flush_llms_rewrite' ) ) {
    function novacare_flush_llms_rewrite() {
        if ( ! get_option( 'novacare_llms_rewrite_flushed' ) ) {
            novacare_add_llms_rewrite_rule();
            flush_rewrite_rules();
            update_option( 'novacare_llms_rewrite_flushed', 1 );
        }
    }
    add_action( 'init', 'novacare_flush_llms_rewrite', 99 );
}

/**
 * 3. Unified Schema.org JSON-LD @graph Entity Architecture
 */
if ( ! function_exists( 'novacare_inject_global_schema_graph' ) ) {
    function novacare_inject_global_schema_graph() {
        global $post;

        $org_id = home_url( '/#organization' );
        $site_id = home_url( '/#website' );

        $graph = array(
            array(
                '@type'         => array( 'MedicalBusiness', 'LocalBusiness' ),
                '@id'           => $org_id,
                'name'          => 'Nova Care Australia',
                'url'           => home_url( '/' ),
                'telephone'     => '0426 142 162',
                'priceRange'    => '$$',
                'description'   => 'Premier NDIS disability support services and Home Care Package provider based in Sans Souci, serving St George, Bayside, and Sutherland Shire.',
                'image'         => get_stylesheet_directory_uri() . '/images/logo.png',
                'hasMap'        => 'https://www.google.com/maps/place/Nova+Care+Australia/@-33.9912899,151.1323251,17z/data=!3m1!4b1!4m6!3m5!1s0x6b12b16cf958ecdd:0x6b7776e0bb17e7e5!8m2!3d-33.9912899!4d151.1323251',
                'sameAs'        => array(
                    'https://www.google.com/maps/place/Nova+Care+Australia/@-33.9912899,151.1323251,17z/data=!3m1!4b1!4m6!3m5!1s0x6b12b16cf958ecdd:0x6b7776e0bb17e7e5!8m2!3d-33.9912899!4d151.1323251'
                ),
                'geo'           => array(
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => -33.9912899,
                    'longitude' => 151.1323251
                ),
                'address'       => array(
                    '@type'           => 'PostalAddress',
                    'addressLocality' => 'Sans Souci',
                    'addressRegion'   => 'NSW',
                    'postalCode'      => '2219',
                    'addressCountry'  => 'AU'
                ),
                'areaServed'    => array(
                    array( '@type' => 'AdministrativeArea', 'name' => 'Sans Souci' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Dolls Point' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Sandringham' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Ramsgate' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Kogarah' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Blakehurst' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Taren Point' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Sylvania' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'Sutherland Shire' ),
                    array( '@type' => 'AdministrativeArea', 'name' => 'St George Area' ),
                    array( '@type' => 'State', 'name' => 'New South Wales' )
                )
            ),
            array(
                '@type'     => 'WebSite',
                '@id'       => $site_id,
                'url'       => home_url( '/' ),
                'name'      => 'Nova Care Australia',
                'publisher' => array( '@id' => $org_id )
            )
        );


        // Add BreadcrumbList Schema if not home page
        if ( ! is_front_page() ) {
            $breadcrumb_items = array(
                array(
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => home_url( '/' )
                )
            );

            if ( is_singular() && is_object( $post ) ) {
                $breadcrumb_items[] = array(
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => get_the_title( $post ),
                    'item'     => get_permalink( $post )
                );
            }

            $graph[] = array(
                '@type'           => 'BreadcrumbList',
                '@id'             => home_url( $_SERVER['REQUEST_URI'] ?? '/' ) . '#breadcrumb',
                'itemListElement' => $breadcrumb_items
            );
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@graph'   => $graph
        );

        echo "\n<!-- Nova Care Unified Schema.org Graph -->\n";
        echo '<script type="application/ld+json">' . json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n\n";
    }
    add_action( 'wp_head', 'novacare_inject_global_schema_graph', 2 );
}

/**
 * 4. Performance Optimizations (Preconnect & Resource Hints for Core Web Vitals)
 */
if ( ! function_exists( 'novacare_resource_hints' ) ) {
    function novacare_resource_hints( $urls, $relation_type ) {
        if ( 'preconnect' === $relation_type ) {
            $urls[] = array(
                'href' => 'https://fonts.googleapis.com',
                'crossorigin' => true,
            );
            $urls[] = array(
                'href' => 'https://fonts.gstatic.com',
                'crossorigin' => true,
            );
        }
        return $urls;
    }
    add_filter( 'wp_resource_hints', 'novacare_resource_hints', 10, 2 );
}

/**
 * 5. OpenGraph, Twitter Cards, Canonical & GEO Meta Tags (Social & AI Search Optimization)
 */
if ( ! function_exists( 'novacare_inject_seo_meta_tags' ) ) {
    function novacare_inject_seo_meta_tags() {
        global $post;

        // Skip if Yoast or RankMath already handles OG meta tags
        if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
            return;
        }

        $site_name   = 'Nova Care Australia';
        $title       = is_front_page() ? 'Nova Care Australia | NDIS & Aged Care Sans Souci' : wp_get_document_title();
        $description = is_singular() && is_object( $post ) && ! empty( $post->post_excerpt ) 
            ? esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) 
            : 'Nova Care Australia provides premier NDIS disability support, personal care, and Home Care Packages in Sans Souci, St George, and Sutherland Shire.';
        $url         = is_singular() ? get_permalink( $post ) : home_url( $_SERVER['REQUEST_URI'] ?? '/' );
        $image       = get_stylesheet_directory_uri() . '/images/logo.png';

        if ( is_singular() && is_object( $post ) && has_post_thumbnail( $post->ID ) ) {
            $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
            if ( ! empty( $thumbnail_src[0] ) ) {
                $image = $thumbnail_src[0];
            }
        }

        echo "\n<!-- Nova Care OpenGraph, Meta Description & GEO Tags -->\n";
        echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
        echo '<meta property="og:locale" content="en_AU" />' . "\n";
        echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
        echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
        
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";

        // Local GEO Meta Tags for AI Search Engines (Perplexity, Gemini, ChatGPT)
        echo '<meta name="geo.region" content="AU-NSW" />' . "\n";
        echo '<meta name="geo.placename" content="Sans Souci, St George & Sutherland Shire, NSW, Australia" />' . "\n";
        echo '<meta name="geo.position" content="-33.9912899;151.1323251" />' . "\n";
        echo '<meta name="ICBM" content="-33.9912899, 151.1323251" />' . "\n";

        // Canonical Tag Fallback
        echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n\n";
    }
    add_action( 'wp_head', 'novacare_inject_seo_meta_tags', 1 );
}

/**
 * 6. Clean Up WordPress Head Bloat for Faster Crawling
 */
if ( ! function_exists( 'novacare_cleanup_head_bloat' ) ) {
    function novacare_cleanup_head_bloat() {
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    }
    add_action( 'init', 'novacare_cleanup_head_bloat' );
}

/**
 * 7. Guaranteed H1 Heading & Title/Description Hierarchy Optimizer
 */
// A. Optimize Document Title Tag (Optimal 50 chars for Google SERP)
if ( ! function_exists( 'novacare_seo_document_title' ) ) {
    function novacare_seo_document_title( $title ) {
        if ( is_front_page() || is_home() ) {
            return 'Nova Care Australia | NDIS & Aged Care Sans Souci';
        }
        return $title;
    }
    add_filter( 'pre_get_document_title', 'novacare_seo_document_title', 99 );
}


// B. Guarantee at least one H1 heading on every page (Fix 0 H1 tags issue)
if ( ! function_exists( 'novacare_guarantee_h1_heading' ) ) {
    function novacare_guarantee_h1_heading( $content ) {
        if ( is_admin() || ( function_exists( 'is_main_query' ) && ! is_main_query() ) ) {
            return $content;
        }

        // Check if content already contains an <h1 tag
        if ( false === stripos( $content, '<h1' ) ) {
            $page_title = is_front_page() 
                ? 'Nova Care Australia - NDIS Disability Support & In-Home Aged Care Services' 
                : get_the_title();
            
            $h1_tag = '<h1 class="av-seo-h1-fallback screen-reader-text" style="position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0;">' . esc_html( $page_title ) . '</h1>';
            
            $content = $h1_tag . $content;
        }

        return $content;
    }
    add_filter( 'the_content', 'novacare_guarantee_h1_heading', 1 );
}



