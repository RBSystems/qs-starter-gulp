<?php
/*********************
** Helper functions
**********************/

//ACF Google API
function get_google_api_key(){
    if( function_exists( 'get_field' ) ){
        $google_api_key = get_field('google_api_key','option');

        return $google_api_key;
    }

}
function google_api_acf_init() {
	acf_update_setting('google_api_key', get_google_api_key());
}

/** GLOBAL header scripts **/

function qs_add_header_scripts(){
    if( function_exists( 'get_field' ) ){
        $header_scripts = get_field('qs_header_scripts','option');
        $page_header_scripts = get_field('page_header_scripts');

        if( $header_scripts ) {
            echo $header_scripts;
        }
        if( $page_header_scripts ){
            echo $page_header_scripts;
        }
    }
}
/** Global Footer scripts **/

function qs_add_footer_scripts(){
    if( function_exists( 'get_field' ) ){
        $footer_scripts = get_field('qs_footer_scripts','option');
        $page_footer_scripts = get_field('page_footer_scripts');

        if( $footer_scripts ) {
            echo $footer_scripts;
        }
        if( $page_footer_scripts ){
            echo $page_footer_scripts;
        }
    }
}
function qstheme_textdomain(){
    load_theme_textdomain('qstheme', THEME . '/languages');
}

if ( ! function_exists( 'add_body_class' ) ){
    function add_body_class( $classes ) {        
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_winIE, $is_edge;
        if( $is_lynx ) $classes[] = 'lynx';
        elseif( $is_gecko ) $classes[] = 'firefox-gecko';
        elseif( $is_opera ) $classes[] = 'opera';
        elseif( $is_NS4 ) $classes[] = 'ns4';
        elseif( $is_safari ) $classes[] = 'safari';
        elseif( $is_chrome ) $classes[] = 'chrome';
	elseif( $is_edge ) $classes[] = 'ms-edge';
        elseif( $is_winIE ) $classes[] = 'ms-winIE';	    
        elseif( $is_IE ) {
            $classes[] = 'ie';
            if( preg_match( '/MSIE ( [0-11]+ )( [a-zA-Z0-9.]+ )/', $_SERVER['HTTP_USER_AGENT'], $browser_version ) )
            $classes[] = 'ie' . $browser_version[1];
        } else $classes[] = 'unknown';
        if( $is_iphone ) $classes[] = 'iphone';

        if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
                 $classes[] = 'osx';
        } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
                 $classes[] = 'linux';
        } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
                 $classes[] = 'windows';
        }
        if (defined('LANG')){
            $classes[] = 'lang-'.LANG;
        }
        if (defined('ENV')){
            $classes[] = 'env-'.ENV;
        }
		if ( class_exists( 'WooCommerce' ) ) {
			$classes[] = 'woo-is-on';
		}
		if( ! is_user_logged_in() ){
			$classes[] = 'logged-out';
		}		
	 
        return $classes;
    }

}

function register_theme_menus() {
    register_nav_menus(array(
        'header-menu' => __('Header Menu', 'qstheme'), // Main Navigation
    ));
}

//Header menu
function header_menu() {
	wp_nav_menu(
		array(
			'theme_location'  => 'header-menu',
			'menu_class'      => 'header_menu_class',
			'container'       => ''
		)
	);
}

// get the the role object
$role_object = get_role( 'editor' );

// add $cap capability to this role object
$role_object->add_cap( 'edit_theme_options' );

// HTML Email
function qsemail_set_content_type(){
	return "text/html";
}

// Fix url uppercase urls
function isPartUppercase() {
    $url = $_SERVER['REQUEST_URI'];
    if(!strpos($_SERVER['REQUEST_URI'], '%')){
        return;
    }

    if(preg_match("/[A-Z]/", $url)) {
        $_SERVER['REQUEST_URI'] = strtolower($url);
    }
    return false;
}
isPartUppercase();

// remove visual composer shortcode from content
// for example on search results or custom page template
function remove_vc_shortcodes_from_content( $excerpt ) {
	$excerpt = preg_replace('/\[\/?vc_.*?\]/', '', $excerpt);
	echo $excerpt;
}
// Locate function definition
function get_function_location( $function_name ){
   $reflFunc = new ReflectionFunction( $function_name );
   print $reflFunc->getFileName() . ':' . $reflFunc->getStartLine();
}

/** Display hooks list **/
//add_action( 'wp_footer', 'qs_list_hooks_filters' );
function qs_list_hooks_filters(){

   if( ! defined('WP_DEBUG') || ! WP_DEBUG || ! current_user_can('administrator')){
       return;
   }
   global $wp_filter;

   $comment_filters = array ();
   $h1  = '<h1>Current Filters list:</h1>';
   $out = '';
   $toc = '<ul>';

   foreach ( $wp_filter as $key => $val ) {
       if ( FALSE !== strpos( $key, 'comment' ) )
       {
           $comment_filters[$key][] = var_export( $val, TRUE );
       }
   }

   foreach ( $comment_filters as $name => $arr_vals ) {
       $out .= "<h2 id=$name>$name</h2><pre>" . implode( "\n\n", $arr_vals ) . '</pre>';
       $toc .= "<li><a href='#$name'>$name</a></li>";
   }

   print "<pre style='direction:ltr; text-align:left;'>$h1$toc</ul>$out</pre>";
}

/**
 * [convert_gregorian_date_to_jewesh]
 * @param  [type] $date [date with 3 part format]
 * @return [type]       [description]
 */
function convert_gregorian_date_to_jewesh( $date ) {
    if( $date ) {
        $date_exploded          = explode( '.', $date);
        $jewish_date_convertion = gregoriantojd( $date_exploded[1], $date_exploded[0], $date_exploded[2] );
        $jewish_date            = jdtojewish($jewish_date_convertion, true, CAL_JEWISH_ADD_GERESHAYIM);
        $jewish_date_utf        = iconv( 'WINDOWS-1255', 'UTF-8', $jewish_date );

        return $jewish_date_utf;
    }
}  
/**
 * print_svg - Print SVG 
 * @param  string $path svg url
 * @return string svg image
 */
function print_svg($path){
	try{
		return file_get_contents($path);
	} catch (\Exception $e) {
		return '';
	}
}

/**
 * this function add validation to cf7 submiting form to all "tel" tags
 * @param  object $result
 * @param  object $tag
 */
function custom_tel_confirmation_validation_filter( $result, $tag ) {
    $tel = isset( $_POST[$tag->name] ) ? trim( $_POST[$tag->name] ) : '';
    $re = '/^[\+]?[(]?[0-9]{2,3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/';

    if (!preg_match($re, $tel, $matches) || strlen( $tel ) > 10) {
        $result->invalidate($tag, "Please enter a valid phone number" );
    }

    return $result;
}
/**
 * disable rest api by create error fo every request - invoke by hook - rest_authentication_errors
 * @param  mixed (object|null) WP_Error $result on error
 * @return object WP_Error $result 
 */
function disabled_rest_api( $result ) {
	return new WP_Error( 'rest_is_disable', 'REST API is disabled.', array( 'status' => 401 ) );
}
/**
 * disable rest api by before init rest - invoke by hook - rest_api_init
 */
function disabled_rest_api_init() {
	die( 'REST API is disabled.' );
}
