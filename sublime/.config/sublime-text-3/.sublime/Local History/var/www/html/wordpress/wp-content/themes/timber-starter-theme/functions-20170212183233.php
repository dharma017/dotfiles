<?php
remove_action('template_redirect', 'redirect_canonical');
header('Access-Control-Allow-Origin: *');
header('X-Frame-Options: GOFORIT');
// header( 'X-Frame-Options: SAMEORIGIN' );

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		parent::__construct();
	}

	function register_post_types() {
		//this is where you can register custom post types
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function add_to_context( $context ) {
		$context['foo'] = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		// $context['menu'] = new TimberMenu();
		$context['header_menu'] = new TimberMenu(66);
        $context['top_menu'] = new TimberMenu(65);
        $context['bottom_menu'] = new TimberMenu(67);
        $context['weather'] = $this->getWeather();
        $context['local_time'] = $this->getCurrentTime();

		$context['slider_resorts'] = Timber::get_posts(array(
			'post_type'     => 'resort',
			'post_status'   => 'publish',
			'posts_per_page' => -1,
			'orderby'       => 'date',
			'order'         => 'DESC'
		));

		$context['site'] = $this;
		$context['options'] = get_fields('options');
		$context['social_media'] = get_option('social_media_option_name');
		$context['speak_to_agent'] = get_option('speak_to_agent_option_name');
		$context['get_search_query'] = get_search_query();
		$context['get_current_url'] = TimberHelper::get_current_url();
		return $context;
	}

	function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own fuctions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}

    /**
     * https://openweathermap.org/current
         api.openweathermap.org/data/2.5/weather?id=2172797
     */
	public function getWeather()
    {
       $jsonurl = "http://api.openweathermap.org/data/2.5/weather?id=1282027&appid=dd0b18bf0b1efe4b109b21ca4959da3c";
       $json = file_get_contents($jsonurl);

       $weather = json_decode($json);
       $kelvin = $weather->main->temp;
       $celcius = $kelvin - 273.15;
       return $celcius."°C";

    }

    public function getCurrentTime()
    {   
        date_default_timezone_set("Indian/Maldives");
        return date("H:i:s");
    }


}

new StarterSite();

/**
 * define ajax url to use in js file
 */
add_action('wp_head','pluginname_ajaxurl');
function pluginname_ajaxurl() {
?>
<script type="text/javascript">
    var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
	var pa_vars = {};
    pa_vars.pa_nonce = '<?php echo wp_create_nonce( 'pa_nonce' ); ?>';
</script>
<?php
}

require_once locate_template('assets/lib/common-helper.php');
require_once locate_template('assets/lib/custom-routes.php');

require_once locate_template('assets/lib/breadcrumbs.php');
require_once locate_template('assets/lib/countries.php');
require_once locate_template('assets/lib/autopopulate-cf.php');

require_once locate_template('assets/lib/resort-search.php');
require_once locate_template('assets/lib/special-offer-search.php');
require_once locate_template('assets/lib/resort-package-search.php');

function getStarRating($ID)
{
	$star_rating =  get_field('star_rating',$ID)->slug;
	$star_rating_cnt = str_replace('-star', '', $star_rating);

	$html = '';
	for($i=0; $i < $star_rating_cnt; $i++) { 
        // $html .= '<i class="fa fa-star" style="color:gold;"></i>';
		$html .= '<i class="fa fa-star"></i>';
	}

	/*if ($star_rating_cnt) {
		$html .= 'Star Deluxe';
	}else{
		$html .= 'Star';
	}*/
	
	return $html;
}

if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
function my_jquery_enqueue() {
   wp_deregister_script('jquery');
   // wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js", false, null);
   wp_register_script('jquery', get_template_directory_uri() . '/assets/js/jquery/jquery.min.js', false, null);
   wp_enqueue_script('jquery');
}

/*function my_enqueue($hook) {
    wp_enqueue_script( 'my_custom_script', get_template_directory_uri() . '/static/admin.js' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );*/

/** Dynamic List for Contact Form 7 **/
/** Usage: [select name term:taxonomy_name] **/
function dynamic_select_list($tag, $unused){ 
    $options = (array)$tag['options'];

    foreach ($options as $option) 
        if (preg_match('%^term:([-0-9a-zA-Z_]+)$%', $option, $matches)) 
            $term = $matches[1];

    //check if post_type is set
    if(!isset($term))
        return $tag;

    $taxonomy = get_terms($term, array('hide_empty' => 0));

    if (!$taxonomy)  
        return $tag;

    $tag['raw_values'][] = 'Meal Plan';  
    $tag['values'][] = '0';  
    $tag['labels'][] = 'Select Meal Plan';  

    foreach ($taxonomy as $cat) {  
        $tag['raw_values'][] = $cat->name;  
        $tag['values'][] = $cat->name;  
        $tag['labels'][] = $cat->name;
    }

    return $tag; 
}
add_filter( 'wpcf7_form_tag', 'dynamic_select_list', 10, 2);

/**
 * [postdropdown jobPosition post_type="job-opening"]
 */
// wpcf7_add_shortcode('postdropdown', 'createbox', true);
// function createbox(){
//   global $post;
//   extract( shortcode_atts( array( 'post_type' => 'room',), $atts ) );
//   $args = array('post_type' => $post_type );
//   $myposts = get_posts( $args );
//   $output = "<select name='postType' id='postType' onchange='document.getElementById(\"postType\").value=this.value;'><option></option>";
//   foreach ( $myposts as $post ) : setup_postdata($post);
//     $title = get_the_title();
//     $output .= "<option value='$title'> $title </option>";

//   endforeach;
//   $output .= "</select>";
//   return $output;
// }

//Adding select element dynamically populated with custom posts to Contact Form 7 WP plugin
//In CF7 form use [customselect roomdropdown] format to insert the select element, in the email body - [roomdropdown]
wpcf7_add_shortcode('customselect', 'createdynamicselect', true);
function createdynamicselect(){
	//Settings
	$selectname = 'coursesdropdown';
	$is_multiple = false;

	global $post;
	$courses = get_posts( array('numberposts'=>-1,'post_type'=>'room', 'orderby'=>'menu_order') );
	$output = "\r\n<select name='".$selectname.( $is_multiple ? "[]" : "" )."' id='".$selectname."' ".( $is_multiple ? "multiple" : "" ).">\r\n";
	$output .= "<option value='0'>Select Room</option>\r\n";
	foreach ( $courses as $post ) : setup_postdata($post);
	    $coursename = get_the_title();
	    $output .= "<option value='".$coursename."'>".$coursename."</option>\r\n";
	endforeach;
	$output .= "</select>";
	$output = '<span class="wpcf7-form-control-wrap '.$selectname.'">'.$output.'</span>';
	return $output;
}

function cf7_add_post_id(){
 
    return $_GET['id'];
}
 
add_shortcode('CF7_ADD_POST_ID', 'cf7_add_post_id');

class SocialMediaSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action('admin_head', array( $this, 'social_media_custom_css' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Social Media Settings', 
            'manage_options', 
            'social-media-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'social_media_option_name' );
        ?>
        <div class="wrap">
            <h2>Social Media Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'social_media_option_group' );   
                do_settings_sections( 'social-media-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Add Custom Admin css
     */
    public function social_media_custom_css(){
        echo '<style>
                .l-social-media {
                  width: 50%;
                  max-width: 100%;
                } 
              </style>';
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'social_media_option_group', // Option group
            'social_media_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Social Media Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'social-media-setting-admin' // Page
        );  

        add_settings_field(
            'facebook', // ID
            'Facebook', // Title 
            array( $this, 'facebook_callback' ), // Callback
            'social-media-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'twitter', 
            'Twitter', 
            array( $this, 'twitter_callback' ), 
            'social-media-setting-admin', 
            'setting_section_id'
        );

        add_settings_field(
            'tumblr', 
            'Tumblr', 
            array( $this, 'tumblr_callback' ), 
            'social-media-setting-admin', 
            'setting_section_id'
        );  

        add_settings_field(
            'instagram', 
            'Instagram', 
            array( $this, 'instagram_callback' ), 
            'social-media-setting-admin', 
            'setting_section_id'
        );        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['facebook'] ) )
            // $new_input['id_number'] = absint( $input['id_number'] );
            $new_input['facebook'] = sanitize_text_field( $input['facebook'] );

        if( isset( $input['twitter'] ) )
            $new_input['twitter'] = sanitize_text_field( $input['twitter'] );

        if( isset( $input['tumblr'] ) )
            $new_input['tumblr'] = sanitize_text_field( $input['tumblr'] );

        if( isset( $input['instagram'] ) )
            $new_input['instagram'] = sanitize_text_field( $input['instagram'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function facebook_callback()
    {
        printf(
            '<input type="text" id="facebook" class="l-social-media" name="social_media_option_name[facebook]" value="%s" /><p>Put URL like https://www.facebook.com/username</p>',
            isset( $this->options['facebook'] ) ? esc_attr( $this->options['facebook']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function twitter_callback()
    {
        printf(
            '<input type="text" id="twitter" class="l-social-media" name="social_media_option_name[twitter]" value="%s" /><p>Put URL like https://www.twitter.com/username</p>',
            isset( $this->options['twitter'] ) ? esc_attr( $this->options['twitter']) : ''
        );
    }

     /** 
     * Get the settings option array and print one of its values
     */
    public function tumblr_callback()
    {
        printf(
            '<input type="text" id="tumblr" class="l-social-media" name="social_media_option_name[tumblr]" value="%s" /><p>Put URL like https://www.tumblr.com/username</p>',
            isset( $this->options['tumblr'] ) ? esc_attr( $this->options['tumblr']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function instagram_callback()
    {
        printf(
            '<input type="text" id="instagram" class="l-social-media" name="social_media_option_name[instagram]" value="%s" /><p>Put URL like https://www.instagram.com/username</p>',
            isset( $this->options['instagram'] ) ? esc_attr( $this->options['instagram']) : ''
        );
    }
}

if( is_admin() )
    $social_media_settings_page = new SocialMediaSettingsPage();

class SpeaktoAgentSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action('admin_head', array( $this, 'speak_to_agent_custom_css' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Speak To Agent Settings', 
            'manage_options', 
            'speak-to-agent-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'speak_to_agent_option_name' );
        ?>
        <div class="wrap">
            <h2>Speak To Agent Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'speak_to_agent_option_group' );   
                do_settings_sections( 'speak-to-agent-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Add Custom Admin css
     */
    public function speak_to_agent_custom_css(){
        echo '<style>
                .l-social-media {
                  width: 50%;
                  max-width: 100%;
                } 
              </style>';
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'speak_to_agent_option_group', // Option group
            'speak_to_agent_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Speak To Agent Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'speak-to-agent-setting-admin' // Page
        );  

        add_settings_field(
            'landline', // ID
            'Landline', // Title 
            array( $this, 'landline_callback' ), // Callback
            'speak-to-agent-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'cell', // ID
            'Cell', // Title 
            array( $this, 'cell_callback' ), // Callback
            'speak-to-agent-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'viber', // ID
            'Viber', // Title 
            array( $this, 'viber_callback' ), // Callback
            'speak-to-agent-setting-admin', // Page
            'setting_section_id' // Section           
        );  

        add_settings_field(
            'whatsapp', // ID
            'Whatsapp', // Title 
            array( $this, 'whatsapp_callback' ), // Callback
            'speak-to-agent-setting-admin', // Page
            'setting_section_id' // Section           
        ); 

        add_settings_field(
            'wechat', // ID
            'Wechat', // Title 
            array( $this, 'wechat_callback' ), // Callback
            'speak-to-agent-setting-admin', // Page
            'setting_section_id' // Section           
        );       
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['landline'] ) )
            // $new_input['id_number'] = absint( $input['id_number'] );
            $new_input['landline'] = sanitize_text_field( $input['landline'] );

        if( isset( $input['cell'] ) )
            $new_input['cell'] = sanitize_text_field( $input['cell'] );

        if( isset( $input['viber'] ) )
            $new_input['viber'] = sanitize_text_field( $input['viber'] );

        if( isset( $input['whatsapp'] ) )
            $new_input['whatsapp'] = sanitize_text_field( $input['whatsapp'] );

        if( isset( $input['wechat'] ) )
            $new_input['wechat'] = sanitize_text_field( $input['wechat'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function landline_callback()
    {
        printf(
            '<input type="text" id="landline" class="l-social-media" name="speak_to_agent_option_name[landline]" value="%s" /><p>Put like +9779825472549</p>',
            isset( $this->options['landline'] ) ? esc_attr( $this->options['landline']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function cell_callback()
    {
        printf(
            '<input type="text" id="cell" class="l-social-media" name="speak_to_agent_option_name[cell]" value="%s" /><p>Put like +9779825472549</p>',
            isset( $this->options['cell'] ) ? esc_attr( $this->options['cell']) : ''
        );
    }

     /** 
     * Get the settings option array and print one of its values
     */
    public function viber_callback()
    {
        printf(
            '<input type="text" id="viber" class="l-social-media" name="speak_to_agent_option_name[viber]" value="%s" /><p>Put like +9779825472549</p>',
            isset( $this->options['viber'] ) ? esc_attr( $this->options['viber']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function whatsapp_callback()
    {
        printf(
            '<input type="text" id="whatsapp" class="l-social-media" name="speak_to_agent_option_name[whatsapp]" value="%s" /><p>Put like +9779825472549</p>',
            isset( $this->options['whatsapp'] ) ? esc_attr( $this->options['whatsapp']) : ''
        );
    }

     /** 
     * Get the settings option array and print one of its values
     */
    public function wechat_callback()
    {
        printf(
            '<input type="text" id="wechat" class="l-social-media" name="speak_to_agent_option_name[wechat]" value="%s" /><p>Put like +9779825472549</p>',
            isset( $this->options['wechat'] ) ? esc_attr( $this->options['wechat']) : ''
        );
    }
}

if( is_admin() )
    $speak_to_agent_settings_page = new SpeaktoAgentSettingsPage();

function my_acf_google_map_api( $api ){
	
	$api['key'] = 'AIzaSyAIacEF59leRVV68mpsQKcD306L78EzyPQ';
	
	return $api;
	
}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');


function get_resort_related_objects( $post_id ) {

  // Get resort_type var
  $posts_array = get_posts(
      array(
        'posts_per_page' => -1,
        'post_type'     => 'room',
        'post_status'   => 'publish',
        'meta_key' => 'resort_cf', //name of custom field
        'meta_value' => $post_id, //name of custom field
        'orderby'       => 'date',
        'order'         => 'DESC'
      )
  );
  // dd($posts_array);

  // Simplify array to look like: resort_type => resorts
  foreach ($posts_array as $key => $value) {
    $room_data[$value->ID] = $value->post_title;
  }

  $term_list = wp_get_post_terms($post_id, 'meal_plan', array("fields" => "all"));
  foreach ($term_list as $key => $value) {
    $meal_plan_data[$value->term_id] = $value->name;
  }

  $response_data->room = $room_data;
  $response_data->meal_plan = $meal_plan_data;

  return $response_data;

}
