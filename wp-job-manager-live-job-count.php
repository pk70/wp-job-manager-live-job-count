<?php
/**
 * Plugin Name: WP Job Manager - Live Job Count
 * Plugin URI:  https://github.com/pk70/wp-job-manager-live-job-count
 * Description: With the help of this plugin admin user can count and display total job, total registered company and total job seeker, this pluging will load data without page refresh
 * Author:      Moinul
 * Author URI:  http://discovernanosoft.com
 * Contributor: Cal Evans
 * Version:     1.1.1
 * Requires at least: 4.0
 * Tested up to: 4.6
 * Text Domain: job_manager_live_job_count
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'wjmljc_register_css_js' )){

/* Register css and js file */

function wjmljc_register_css_js(){
    wp_enqueue_script('jquery');

    wp_register_style('wjmljc_jobcss', plugins_url('css/style.css', __FILE__));

    wp_enqueue_style('wjmljc_jobcss');

   
    wp_register_script('wjmljc_ajax_script', plugins_url('js/job_count_ajax.js', __FILE__),array('jquery'));
    wp_enqueue_script('wjmljc_ajax_script');
    wp_localize_script('wjmljc_ajax_script', 'ajaxobject',
    array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'refresh_time' => get_option('wjmljc_ajax_timer')
    )
    );
  }

  add_action('wp_enqueue_style', 'wjmljc_register_css_js');
  add_action('wp_enqueue_scripts', 'wjmljc_register_css_js');
}else{
    echo "wjmljc_register_css_js function already exist";
}

/* Count all seeker from wpdb */

function wjmljc_count_all_job_seeker(){

 global $wpdb;
 $numpost = $wpdb->get_var("SELECT count(DISTINCT wp_users.ID) FROM  $wpdb->users INNER JOIN  $wpdb->usermeta 
ON $wpdb->users.ID =  $wpdb->usermeta.user_id AND $wpdb->users.user_status=0 WHERE  $wpdb->usermeta.meta_key = 'wp_capabilities'
AND $wpdb->usermeta.meta_value LIKE '%candidate%'"); 
 return $numpost; 

}

/* Count all company from wpdb */

function wjmljc_count_all_company(){

 global $wpdb;
 $sql = "select count(*) 
           from {$wpdb->posts} 
          where post_type='gd_place' 
                and post_status='publish';";
  $count = $wpdb->get_var($sql);
 return $count; 

}


function wjmljc_count_all_jobs() {
     global $wpdb; 
     $sql = "SELECT COUNT(*) 
               FROM $wpdb->posts
              WHERE post_status = 'publish' 
                    AND post_type = 'job_listing'";
     $numpost = $wpdb->get_var($sql); 
   return $numpost;
}


/* Ajax call */
function wjmljc_action_ajax(){
    wjmljc_register_css_js();
     $numpost = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts
     WHERE post_status = 'publish' AND post_type = 'job_listing'"); 
       $array =['count_job'     => wjmljc_count_all_jobs(),
                'count_company' => wjmljc_count_all_company(),
                'count_seeker'  => wjmljc_count_all_job_seeker()];
       echo json_encode($array);
    
    die();
  
}

/*
 * Returns just the number of jobs in the system.
 */
function wjmljc_just_jobs() {
    //echo ;
    return wjmljc_count_all_jobs();
}

/*
 * Returns just the number of companies in the system.
 */
function wjmljc_just_company() {
    return wjmljc_count_all_company();
}

/*
 * Returns just the number of seekers in the system.
 */

function wjmljc_just_seeker() {
    return wjmljc_count_all_job_seeker();
}


/* Get and include template files. */
 
function wjmljc_template_load(){

   $template=load_template(dirname( __FILE__ ) .'/job-count-template.php');
   return $template;
}
add_action('wp_ajax_nopriv_wjmljc_action_ajax', 'wjmljc_action_ajax');
add_action('wp_ajax_wjmljc_action_ajax', 'wjmljc_action_ajax');
add_shortcode('livecount_job','wjmljc_template_load');
add_shortcode('livecount_just_jobs','wjmljc_just_jobs');
add_shortcode('livecount_just_company','wjmljc_just_company');
add_shortcode('livecount_just_seeker','wjmljc_just_seeker');

add_action ('wp_head' , 'wjmljc_count_all_job_seeker');

/* Create wjmljc settings menu */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wjmljc_settings_action_link' );

/* Setting page action link */

function wjmljc_settings_action_link( $links ) {
   echo $plugin = plugin_basename(__FILE__);
  
  $settings_link = sprintf( '<a href="options-general.php?page=%s">%s</a>', $plugin, __('Settings') );
  array_unshift( $links, $settings_link );
  
  return $links;
}
add_action('admin_menu', 'wjmljc_create_menu');

/* Create amin menu */

function wjmljc_create_menu() {

  //create new top-level menu
  add_options_page('Job Count Settings', 'Live Job Settings', 'administrator', __FILE__, 'wjmljc_template_loads_page' , plugins_url('assets/images/icon.png', __FILE__) );

  //call register settings function
  add_action( 'admin_init', 'wjmljc_register_settings');
}

/* register plugin setting page */

function wjmljc_register_settings() {
  //register our settings
  register_setting( 'job-count-live-plugin-settings-group', 'wjmljc_show_job' );
  register_setting( 'job-count-live-plugin-settings-group', 'wjmljc_show_company' );
  register_setting( 'job-count-live-plugin-settings-group', 'wjmljc_show_seeker' );
  register_setting( 'job-count-live-plugin-settings-group', 'wjmljc_ajax_timer' );
}

/* create setting page with field*/

function wjmljc_template_loads_page() {
?>
<div class="wrap">
<h1>Live Job Count</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'job-count-live-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'job-count-live-plugin-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Show all published Jobs</th>
        <td><input type="checkbox" name="wjmljc_show_job" id="wjmljc_show_job" value="1" 
                        <?php echo (get_option('wjmljc_show_job') == 1) ? 'checked="checked"' : ''; ?> /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Show all registered company</th>
        <td><input type="checkbox" name="wjmljc_show_company" value="1"
           <?php echo (get_option('wjmljc_show_company') == 1) ? 'checked="checked"' : ''; ?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Show all job seeker</th>
        <td><input type="checkbox" name="wjmljc_show_seeker" value="1" 
         <?php echo (get_option('wjmljc_show_seeker') == 1) ? 'checked="checked"' : ''; ?> /></td>
        </tr>

         <tr valign="top">
        <th scope="row">Auto refresh time</th>
        <td><input type="text" placeholder="enter millisecond" name="wjmljc_ajax_timer" value=" <?php echo get_option('wjmljc_ajax_timer');?>"/>
          <p>please input Millisecond (1 Second= 1000 Millisecond)</p></td>
       
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } 
/* install wjmljc plugin*/

     function wjmljc_install() {
           update_option('wjmljc_show_company',1);
            update_option('wjmljc_show_seeker',1);
             update_option('wjmljc_show_job',1);
             update_option('wjmljc_ajax_timer',50000);
     }
     /* uninstall wjmljc plugin*/

      function wjmljc_uninstall(){
            delete_option('wjmljc_show_company');
            delete_option('wjmljc_show_seeker');
            delete_option('wjmljc_show_job');
            delete_option('wjmljc_ajax_timer');
     }

register_activation_hook( __FILE__, 'wjmljc_install');
register_deactivation_hook( __FILE__, 'wjmljc_uninstall');

?>
