<?php
   /*
   Plugin Name: SimpleAlert
   Plugin URI: http://SimpleAlert.com
   Description: a plugin to create SimpleAlert and spread joy
   Version: 1.0
   Author: Mr. Awesome
   Author URI: http://wordpress.com
   License: GPL2
   */
   
   // create custom plugin settings menu
     
add_action('admin_menu', 'simple_alert_plugin_create_menu');

function simple_alert_plugin_create_menu() {

	//create new top-level menu
	add_submenu_page('options-general.php','Simple Alert settings page', 'Alert Settings', 'administrator', __FILE__, 'simple_alert_settings_page' , plugins_url('/images/icon.png', __FILE__) );

	//call register settings function
	add_action( 'admin_init', 'register_simple_alert_plugin_settings' );
}


function register_simple_alert_plugin_settings() {
	//register our settings
	register_setting( 'simple_alert_settings_group', 'alert_text' );
	$post_types = get_post_types( array('public'   => true,'_builtin' => false), "names", "and" );
	if (empty($post_types)) {
		$post_types = array('post','page');
	}	
	foreach ( $post_types  as $post_type ) {
		$newpost = "my".$post_type;
		register_setting( 'simple_alert_settings_group', $newpost);
	}
	register_setting( 'simple_alert_settings_group', 'mypost' );
	
}	
	


function simple_alert_settings_page() {
?>
<div class="wrap">
<h1>Simple Alert</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'simple_alert_settings_group' ); ?>
    <?php do_settings_sections( 'simple_alert_settings_group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Alert text</th>
        <td><input type="text" name="alert_text" value="<?php echo esc_attr( get_option('alert_text') ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Select post type </th>
        <td>
			<?php 	 
			$post_types = get_post_types( array('public'   => true,'_builtin' => false), "names", "and" ); 
			if (empty($post_types)) {
				$post_types = array('post','page');
			}
			foreach ( $post_types  as $post_type ) {
			$newpost = "my".$post_type;
			$value = get_option($newpost);
			$checked = '';
			$checked = (  $post_type == $value ) ? 'checked="checked"': '';
			$selectedposttype[] = (  $post_type == $value ) ? $post_type : 'false';
			echo "<input type='checkbox' name='$newpost' value='$post_type' $checked />$post_type<bR>";
			 } ?>  	
		</td>
        </tr>
		<tr valign="top">
        <th scope="row">Select posts to show <br> alertbox on that page</th>
        <td>
			<?php 	 
			$savepost = get_option("mypost");
			echo "<select multiple name='mypost[]' id='allpost'>";
			foreach ( $selectedposttype as $posttype ) {
				if ( $posttype) {
					$posts = get_posts(array('post_type' => $posttype));
					foreach($posts as $post){
						$selected='';
						if(in_array($post->ID,$savepost)){
							$selected = "selected";
							echo "if". $selected;
						}
						echo "<option value='{$post->ID}' $selected >{$post->post_title}</option>";
					}
				}
			}
			echo "</select>";?>  	
		</td>
        </tr>
	</table>
    <?php submit_button(); 
		 	?>

</form>
</div>
<?php }


function myPlugin_admin_scripts() {

	if ( isset($_GET['page']) && $_GET['page'] == 'simple-alert/SimpleAlert.php' ) {
		wp_enqueue_script( 'alert-script',plugins_url( '/js/myform.js', __FILE__ ) );
		wp_enqueue_style('adminstyle', plugins_url( 'css/simplealert_admin.css' , __FILE__ ), false, '2.2', 'all' );
		wp_localize_script( 'alert-script', 'alert_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
	}
}

add_action( 'admin_init', 'myPlugin_admin_scripts' );

 
function getajaxdata() {
   $post_types = $_POST['post_type']; 
   
   //print_r($post_types);
   $option='';
   $savepost = get_option('mypost');
   
   
   foreach ( $post_types as $posttype ) {
	   
		if ( $posttype) {
			$posts = get_posts(array('post_type' => $posttype));
			foreach($posts as $post){
				$selected='';
						if(in_array($post->ID,$savepost)){
							$selected = "selected";
							echo "if". $selected;
						}
				$options.= "<option value='{$post->ID}' $selected>{$post->post_title}</option>";
			}
		}
	}
   echo $options;
   
   
   wp_die();
}
add_action('wp_ajax_getajaxdata', 'getajaxdata');
add_action( 'wp_ajax_nopriv_getajaxdata', 'getajaxdata' );



/* Add simple alert text on selected posts
 * @param string $content
 * @returns string
 */
function alert_popup( $content ) {
	$text = get_option('alert_text');
	$myposts = get_option('mypost');
	
    if((is_single() || is_page()) && in_array(get_the_id(),$myposts)) {
		wp_enqueue_script('lightboxscript', plugins_url( 'js/fancybox.js' , __FILE__ ), array('jquery'), '1.0', false);
		wp_enqueue_script('simplealertscript', plugins_url( 'js/simplealert.js' , __FILE__ ), array('jquery'), '1.0', false);
		wp_enqueue_style('lightboxcss', plugins_url( 'css/fancybox.css' , __FILE__ ), false, '2.2', 'all' );
		wp_enqueue_style('simplealertcss', plugins_url( 'css/simplealert.css' , __FILE__ ), false, '2.2', 'all' );
    
		$html .= '<div id="modal" style="display:none">' . $text . '</div><a id="simplealert" data-fancybox="modal" data-src="#modal" href="javascript:;">Inline Content</a>';
        $content .= $html;
    }
    return $content;       
} 
add_filter( 'the_excerpt', 'alert_popup' );
add_filter( 'the_content', 'alert_popup' );
 ?>
   
   
