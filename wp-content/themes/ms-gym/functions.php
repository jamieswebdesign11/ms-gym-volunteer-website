<?php
/**
 * @package WordPress
 * @subpackage 
 */
 
add_theme_support( 'post-thumbnails' );
require_once('include/wp_bootstrap_navwalker.php');
add_action( 'after_setup_theme', 'register_my_menu' );
function register_my_menu() { register_nav_menus( array( 'main_menu' => 'Main Menu', 'footer_menu' => 'Footer Menu', 'mobile_menu' => 'Mobile Menu', 'mobile_dropdown_menu' => 'Mobile Dropdown Menu', 'sitemap' => 'Sitemap Menu', ) );}



function theme_styles() {
	
	global $version;
	$version = mt_rand();
	
	wp_enqueue_style( 'my-bootstrap-extension', get_template_directory_uri() . '/assets/css/bootstrap.css', array(), $version); 
	wp_enqueue_style( 'font_awesome', get_template_directory_uri() . '/assets/fontawesome/all.css' ); 
	wp_enqueue_style( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/css/swiper.min.css' );
	wp_enqueue_style( 'slick-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css' );
	wp_enqueue_style( 'slick-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css' );
	wp_enqueue_style( 'lightgallery', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/css/lightgallery-bundle.min.css' );
	wp_enqueue_style( 'lightgallery_lg-video', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/css/lg-video.css' );
	wp_enqueue_style( 'photo_box' , get_template_directory_uri() . '/assets/css/photobox.css', array(), $version); 
	wp_enqueue_style( 'owl-carousel' , get_template_directory_uri() . '/assets/css/owl.carousel.min.css', array(), $version); 
	wp_enqueue_style( 'owl-carousel-theme' , get_template_directory_uri() . '/assets/css/owl.theme.default.min.css', array(), $version); 
	wp_enqueue_style( 'base' , get_template_directory_uri() . '/assets/css/base.css', array(), $version); 
	wp_enqueue_style( 'flex' , get_template_directory_uri() . '/assets/css/flex.css', array(), $version); 
	wp_enqueue_style( 'main_css', get_template_directory_uri() . '/style.css', array(), $version); 
}
add_action( 'wp_enqueue_scripts', 'theme_styles');

/*
*
* Default Remove Jquery from Wordpress
*
*/
function theme_js_remove_base_jquery() {

	global $wp_scripts;
	//Remove Default Jquery
	wp_deregister_script( 'jquery' );	
	
}
add_action( 'wp_enqueue_scripts', 'theme_js_remove_base_jquery');

/*
*
* We Will Be Adding Jquery BEFORE Gravity Forms Runs It's Inline Jquery
*
*/
function inject_jquery_above_gravity_form( $content = '' ) 
{
	// keep track of jquery so it's not loaded twice!
	global $jquery_already_injected;
	
	if ( !isset($jquery_already_injected) ) {
		
		$jquery_already_injected = true;

		// End inline script
		$content .= "</script>\n";

		// Inject jQuery
		$content .= "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>\n";		

		// Start inline script again
		$content .= "<script>";
	}

	return $content;
}
add_filter( 'gform_cdata_open', 'inject_jquery_above_gravity_form' );



/**
 * Enqueue jQuery in footer unless it's already been injected above Gravity Form.
 * In this case, enqueue a fake version to trigger dependent scripts, and then remove this fake version.
 */

function enqueue_jquery()
{
	global $jquery_already_injected;

	// jQuery has not been loaded
	if ( !isset($jquery_already_injected) ) {
		
		wp_enqueue_script( 'our_jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', '', '', true);
		wp_enqueue_script( 'bootstrap_js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.0/js/bootstrap.min.js', '', '', true);
		wp_enqueue_script( 'matchHeight', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.0/jquery.matchHeight-min.js', '', '', true);
		wp_enqueue_script( 'slick-slider', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js', '', '', true);
		wp_enqueue_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/js/swiper.jquery.min.js', '', '', true);
		wp_enqueue_script( 'lightgallery', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/lightgallery.min.js', '', '', true);
		wp_enqueue_script( 'lightgallery_lg-video', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/plugins/video/lg-video.min.js', '', '', true);
        wp_enqueue_script( 'visible', get_template_directory_uri() . '/assets/js/visible.js', '', '', true);
		wp_enqueue_script( 'photobox', get_template_directory_uri() . '/assets/js/jquery.photobox.js', '', '', true);
		wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', '', '', true);
		wp_enqueue_script( 'my_custom_js', get_template_directory_uri() . '/assets/js/scripts.js', '', '', true);
	
	}
	// jQuery has already been loaded above a Gravity Form
	else {
		// Enqueue fake script to trigger dependencies
		wp_enqueue_script( 'jquery', '//fake-jquery-script.js', [], null );
		
		wp_enqueue_script( 'our_jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', '', '', true);
		wp_enqueue_script( 'bootstrap_js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.0/js/bootstrap.min.js', '', '', true);
		wp_enqueue_script( 'matchHeight', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.0/jquery.matchHeight-min.js', '', '', true);
		wp_enqueue_script( 'slick-slider', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js', '', '', true);
		wp_enqueue_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/js/swiper.jquery.min.js', '', '', true);
		wp_enqueue_script( 'lightgallery', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/lightgallery.min.js', '', '', true);
		wp_enqueue_script( 'lightgallery_lg-video', 'https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/plugins/video/lg-video.min.js', '', '', true);
        wp_enqueue_script( 'visible', get_template_directory_uri() . '/assets/js/visible.js', '', '', true);
		wp_enqueue_script( 'photobox', get_template_directory_uri() . '/assets/js/jquery.photobox.js', '', '', true);
		wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', '', '', true);
		wp_enqueue_script( 'my_custom_js', get_template_directory_uri() . '/assets/js/scripts.js', '', '', true);
	
		
		// Remove this fake script's HTML before it's actually injected into the DOM
		function gc_remove_fake_jquery_script( $tag ) {
			$tag = ( strpos($tag, 'fake-jquery-script') !== false ) ? '' : $tag;
			return $tag;
		}
		add_filter( 'script_loader_tag', 'gc_remove_fake_jquery_script' );
	}

}
add_action('wp_footer', 'enqueue_jquery', 9);





function theme_options_register( $wp_customize ) {
	class Text_Editor_Custom_Control extends WP_Customize_Control { 
		public $type = 'textarea'; /** ** Render the content on the theme customizer page */ 
		public function render_content() { 
		?> 
		<label><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php $settings = array( 'media_buttons' => false, 'quicktags' => true ); $this->filter_editor_setting_link(); wp_editor($this->value(), $this->id, $settings ); ?> 
		</label>
		<?php do_action('admin_footer'); do_action('admin_print_footer_scripts'); } 
		private function filter_editor_setting_link() { 
			add_filter( 'the_editor', function( $output ) { 
				return preg_replace( '/<textarea/', '<textarea ' . $this->get_link(), $output, 1 ); 
			}); 
		} 
	} 
	function editor_customizer_script() {
		wp_enqueue_script( 'wp-editor-customizer', get_template_directory_uri() . '/js/customizer-panel.js', array( 'jquery' ), rand(), true ); 
	}
	add_action( 'customize_controls_enqueue_scripts', 'editor_customizer_script' ); 	 
	
	
	$wp_customize->add_panel( 'theme_options', array(
		'priority' => 40,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Theme Options', 'textdomain' ),
		'description' => __( 'Theme Options Panel', 'theme_customizer' ),

    ));
	
	$wp_customize->add_section( 'theme_option_section_page_settings', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Page Settings', 'textdomain' ),
		'description' => '',
		'panel' => 'theme_options',
	));
	
	
	//Creating 404 page select field list
	$page_list = array(); $args = array('post_type' => 'page'); $themePages = get_posts($args); 
	foreach($themePages as $themePage) {
		$page_list[$themePage->post_title] = $themePage->post_title;
		$removeHome = array_search('Home',$page_list);
		unset($page_list[$removeHome]);
	}
	
	$wp_customize->add_setting( 'google_ga_code' , array(
		'default'   => '',
		'transport' => 'refresh',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'google_ga_code_control', array(
        'label'      => __( 'Google GA Code'),
        'section'    => 'theme_option_section_page_settings',
		'settings'   => 'google_ga_code',
        'type'       => 'textarea',
	)));
	
	$wp_customize->add_setting( 'gt_manager' , array(
		'default'   => '',
		'transport' => 'refresh',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'gt_manager_control', array(
        'label'      => __( 'Google Tag Manager Code'),
        'section'    => 'theme_option_section_page_settings',
		'settings'   => 'gt_manager',
        'type'       => 'textarea',
	)));
}
add_action( 'customize_register', 'theme_options_register' );




add_filter('gform_init_scripts_footer', 'init_scripts');
function init_scripts() {
    return true;
}

function attachmentpages_noindex() {
if(is_attachment()) {
echo '<meta name="robots" content="noindex" />';
}
}
add_action('wp_head', 'attachmentpages_noindex');

function wpdocs_excerpt_more( $more ) {
    return sprintf( '<a class="read-more btn" href="%1$s">%2$s</a>',
        get_permalink( get_the_ID() ),
        __( 'Read More', 'textdomain' )
    );
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );

//Get Versioned Filepath for CSS/JS Cache
function get_versioned_filepath($filename){
	$url = get_stylesheet_directory_uri() . $filename;
	$file = get_stylesheet_directory() .'/'. $filename;
	if ( file_exists($file)) {
		return $url . '?ver=' . date("Ymd-His", filemtime($file)) .'-'. filesize($file);
	}
	clearstatcache();
}

//String to SEO Freindly URL
setlocale(LC_ALL, 'en_US.UTF8');
function seo_friendly_url($str, $replace=array(), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("~[^a-zA-Z0-9/_|+ -]~", '', $clean);
	$clean = preg_replace("~[/_|+ -]+~", $delimiter, $clean);
	$clean = strtolower(trim($clean, '-'));
	return $clean;
}

//For Blog
function twentytwelve_widgets_init() {
    register_sidebar( array(
        'name' => 'The Blog',
        'id' => 'sidebar-the-blog',
        'before_widget' => '<div class="widget">',
        'after_widget' => '<div class="clear"></div></div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ) );
}
add_action( 'widgets_init', 'twentytwelve_widgets_init' );


function getTextExcerpt($text){
	global $post;
	if($text != ''){
		$text = strip_shortcodes($text);
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]&gt;', ']]&gt;', $text);
		$excerpt_length = 35; // 35 words
		$excerpt_more = '...';
		$text = wp_trim_words($text, $excerpt_length, $excerpt_more);
	}
	return $text;
}

function pagination_bar() {
    global $the_query;
 
    $total_pages = $the_query->max_num_pages;
 
    if ($total_pages > 1){
        $current_page = max(1, get_query_var('paged'));
 
        echo paginate_links(array(
            'base' => get_pagenum_link(1) . '%_%',
            'format' => '/page/%#%',
            'current' => $current_page,
            'total' => $total_pages,
        ));
    }
}


if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
}