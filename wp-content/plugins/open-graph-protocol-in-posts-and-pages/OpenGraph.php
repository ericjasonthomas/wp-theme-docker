<?php
/**
 * Plugin Name: Open Graph Protocol In Posts and Pages v2
 * Plugin URI: http://www.hitreach.co.uk/wordpress-plugins/open-graph-protocol-in-posts-and-pages/
 * Description: Add open graph protocol meta tags on a per post/page basis
 * Author: Hit Reach
 * Version: 3.1
 * Author URI: http://www.hitreach.co.uk
 **/

 //global options declaration
 global $OpenGraphProtocol;

 //make sure there isn't a class conflict creating a 500 error.
 if( !class_exists( "OpenGraphProtocol" ) ){
	class OpenGraphProtocol{

		static $option_name = "OGP_Option";
		static $postmeta_name = "OGP";
		static $option_version = "2";

		static $mb_id = "ogp-settings";
		static $mb_title = "Open Graph Protocol Settings";
		static $mb_callback = "meta_box_content";
		static $mb_context = "normal";
		static $mb_priority = "default";

		static $default_options = array(
			"option_version" => "2",
			"_v3_format"=> 1,
			"fields" => array(
				"type" => array(
					"property_name"=>"og:type",
					"value" => "",
					"description" => "Object Type (usually Website)"
				),
				"title" => array(
					"property_name"=>"og:title",
					"value" => "",
					"description" => "Page Title",
				),
				"url" => array(
					"property_name"=>"og:url",
					"value" => "",
					"description" => "Page URL"
				),
				"image" => array(
					"property_name"=>"og:image",
					"value" => "",
					"description" => "Image Url (Comma Separate Images)"
				),
				"image_secure" => array(
					"property_name"=>"og:image:secure_url",
					"value" => "",
					"description" => "Secure Version of Images (Comma Separate Images, keep in same order as og:image)"
				),
				"description" => array(
					"property_name"=>"og:description",
					"value" => "",
					"description" => "Short Description of page contents"
				),
				"fb:admins" => array(
					"property_name"=>"fb:admins",
					"value" => "",
					"description" => "Facebook Admin Profile Number"
				),
				"fb:app_id" => array(
					"property_name"=>"fb:app_id",
					"value" => "",
					"description" => "Facebook Application Id"
				),
				"street-address" => array(
					"property_name"=>"og:street-address",
					"value" => "",
					"description" => "Street Address"
				),
				"city" => array(
					"property_name" => "og:city",
					"value" => "",
					"description" => "City"
				),
				"postal-code" => array(
					"property_name" => "og:postal-code",
					"value" => "",
					"description" => "Post Code / Zip Code"
				),
				"country-name" => array(
					"property_name" => "og:country-name",
					"value" => "",
					"description" => "Country name"
				),
				"email" => array(
					"property_name" => "og:email",
					"value" => "",
					"description" => "Email Address"
				),
				"phone_number" => array(
					"property_name" => "og:phone_number",
					"value" => "",
					"description" => "Phone Number"
				),
				"fax_number" => array(
					"property_name" => "og:fax_number",
					"value" => "",
					"description" => "Fax Number"
				),
				"video" => array(
					"property_name" => "og:video",
					"value" => "",
					"description" => "Video Url"
				),
				"video_secure" => array(
					"property_name" => "og:video:secure_url",
					"value" => "",
					"description" => "Secure Video Url"
				),
				"video_height" => array(
					"property_name" => "og:video:height",
					"value" => "",
					"description" => "Video Frame Height"
				),
				"video_width" => array(
					"property_name" => "og:video:width",
					"value" => "",
					"description" => "Video Frame Width"
				),
				"video_type" => array(
					"property_name" => "og:video:type",
					"value" => "",
					"description" => "Video MIME Type"
				),
				"audio" => array(
					"property_name" => "og:audio",
					"value" => "",
					"description" => "Audio Url"
				),
				"audio_secure" => array(
					"property_name" => "og:audio:secure_url",
					"value" => "",
					"description" => "Secure Audio Url"
				),
				"audio_type" => array(
					"property_name" => "og:audio:type",
					"value" => "",
					"description" => "Audio MIME Type"
				),
				"locale" => array(
					"property_name" => "og:locale",
					"value" => "",
					"description" => "Website Locale (Format language_TERRITORY e.g. en_US)"
				),
				"locale-alternative" => array(
					"property_name" => "og:locale-alternative",
					"value" => "",
					"description" => "Alternative Website Locale (see Locale)"
				),
			),
			"custom_fields" => array(),
			"sections" => array(
				array(
					"section_title" => "Website Data",
					"fields" => "type,title,url,description,locale,locale-alternative"
				),
				array(
					"section_title" => "Website Contact Details",
					"fields" => "street-address,city,postal-code,country-name,email,phone_number,fax_number"
				),
				array(
					"section_title" => "Website Image",
					"fields" => "image,image_secure"
				),
				array(
					"section_title" => "Website Video",
					"fields" => "video,video_secure,video_height,video_width,video_type"
				),
				array(
					"section_title" => "Website Audio",
					"fields" => "audio,audio_secure,audio_type"
				),
				array(
					"section_title" => "Facebook Data",
					"fields" => "fb:admins,fb:app_id"
				)
			),
			"additional" => array(
				"_aioseop" => 0,
			)
		);



		/**
		 * Class Constructor for the Open Graph Protocol Plugin
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function __construct(){
			self::load_option();
			if( is_admin() ){
				/*These hooks only activate on the /wp-admin menu */
				add_action( "admin_menu", array( __CLASS__, "register_setting_page" ) );
				add_action( 'admin_init', array( __CLASS__, "register_meta_box" ) );
				add_action( "save_post" , array( __CLASS__, "save_post_meta" ) );
				add_action( "edit_attachment" , array( __CLASS__, "save_post_meta" ) );
			}else{
				/*These hooks only activate on the main site */
				add_filter( "language_attributes", array( __CLASS__, "add_namespace" ) );
				add_action( "wp_head", array( __CLASS__, "output_to_head_tag" ) );
				add_filter( "ogp_output_fields", array( __CLASS__, "apply_tag_defaults" ) , 2);
				add_filter( "ogp_output_fields", array( __CLASS__, "apply_aioseop"),1 );
			}
		}



		/**
		 * Load the plugin's options into a global variable for use across the site (a global variable is used for a single load purpose)
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function load_option(){
			global $OpenGraphProtocol;
			$options = get_option( self::$option_name, self::$default_options );
			if( !isset( $options["_v3_format"] ) ){
				$options = self::convert_options_format( $options );
			}
			if( $options["option_version"] != self::$option_version ){
				$options = self::upgrade_options( $options );
			}
			$OpenGraphProtocol = $options;
		}



		/**
		 * Save the plugin's options to the database and update the global variable
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		 * @param Array $options options to be updated
		 * @return Bool confirmation if the option has been updated
		**/
		function save_option( $options ){
			global $OpenGraphProtocol;
			$o = update_option( self::$option_name, $options );
			if( $o == true ){
				$OpenGraphProtocol = $options;
				return true;
			}else{
				return false;
			}
		}



		/**
		 * Save ogp meta to the post
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		 * @param int $post_id the id of the post to save the meta too
		**/
		function save_post_meta( $post_id ){
			if(
				!isset( $_POST["_".self::$postmeta_name] ) ||
				!isset( $_POST[self::$postmeta_name] ) ||
				( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )||
				( defined('DOING_REVISION') && DOING_REVISION )
			)
				return;
			if( wp_verify_nonce ( $_POST["_".self::$postmeta_name],self::$postmeta_name ) ){
				$meta = self::get_ogp_by_id( $post_id );
				foreach( $_POST[self::$postmeta_name] as $section => $array ){
					foreach( $array as $key=>$val ){
						$meta[$section][$key]["value"]=$val;
					}
				}
				update_post_meta( $post_id, self::$postmeta_name, $meta );
			}
		}



		/**
		 * Handles the update of the plugins option page and checks for post submission
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function check_plugin_post(){
			if(
				!isset( $_POST["_".self::$option_name] ) ||
				!isset( $_POST[self::$option_name] ) ||
				!current_user_can("manage_options") ||
				!wp_verify_nonce( $_POST["_".self::$option_name], self::$option_name )
			) return;

			global $OpenGraphProtocol;
			$po = $_POST[self::$option_name];

			foreach( $po["fields"] as $id=>$value_arr ):
				$OpenGraphProtocol["fields"][$id]["value"] = $value_arr["value"];
			endforeach;

			foreach( $po["additional"] as $field=>$value):
				$OpenGraphProtocol["additional"][$field] = $value;
			endforeach;

			$OpenGraphProtocol["custom_fields"] = array();
			foreach( $po["custom_fields"] as $index=>$value):
				if( $value["property_name"] != "" && !isset( $value["delete"] ) ){
					$OpenGraphProtocol["custom_fields"][] = $value;
				}
			endforeach;

			if( self::save_option( $OpenGraphProtocol ) ){
				echo "<div class='updated'><p><strong>Open Graph Protocol options updated successfully</strong></p></div>";
			}else{
				echo "<div class='error'><p><strong>Open Graph Protocol options failed to update</strong></p></div>";
			}
		}



		/**
		 * Registers the plugin's options page within the administration area
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function register_setting_page(){
			add_options_page('Open Graph Protocol', 'Open Graph (OGP)', 'manage_options', 'open-graph-protocol', array( __CLASS__, "setting_page" ));
		}



		/**
		 * Registers meta boxes for use with the plugin
		 * Cycles though all public post types and added in a meta box for ogp settings
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function register_meta_box(){
			$post_types = get_post_types( array(
				"public"=>true,
			) );
			foreach( $post_types as $type ){
				add_meta_box(self::$mb_id, self::$mb_title, array( __CLASS__, self::$mb_callback) , $type, self::$mb_context, self::$mb_priority);
			}
		}



		/**
		 * Outputs content for the metabox onto the post page
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function meta_box_content( ){
?>
<style>
	.og_fields{
		display:block;
		overflow:hidden;
	}
	.og_field{
		width:49%; margin:0 0 10px 1%; float:left;
	}
	.og_field label{
		display:block; float:left; width:45%; text-align:right;
	}
	.og_field input{
		width:53%; float:right;
	}
</style>
<?php
			global $OpenGraphProtocol;
			$post_meta = self::get_post_ogp();
			wp_nonce_field( self::$postmeta_name, "_".self::$postmeta_name );

			echo "<div class='og_fields'>";
			foreach( $OpenGraphProtocol["sections"] as $index=>$array ){
				printf("<h2>%s</h2>", esc_html( $array["section_title"] ) );
				$fields = explode( ",", $array["fields"] );
				foreach( $fields as $field ){
					printf( "<div class='og_field'><label for='og_%s'>%s</label><input type='text' name='%s[fields][%s]' id='og_%s' value='%s' placeholder='%s' /></div>", $field, $OpenGraphProtocol["fields"][$field]["description"], self::$postmeta_name, $field, $field, $post_meta["fields"][$field]["value"], "Default Value: ".$OpenGraphProtocol["fields"][$field]["value"]);
				}
			}
			print("<h2>Custom Fields</h2>");
			foreach( $OpenGraphProtocol["custom_fields"] as $index=>$array ){
				printf( "<div class='og_field'><label for='ogc_%s'>%s</label><input type='text' name='%s[custom_fields][%s]' id='og_%s' value='%s' placeholder='%s' /></div>", $array["property_name"], $array["description"], self::$postmeta_name, $array["property_name"], $array["property_name"],$post_meta["custom_fields"][$field]["value"],"Default Value: ".$array["value"]);
			}
			echo "</div>";
		}




		/**
		 * Outputs the plugin's setting page within the adminstration menu
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function setting_page(){
			self::check_plugin_post();
			global $OpenGraphProtocol;
			$rolling_tab_index = 1;
			$rolling_custom_field_index = 0;
?>
	<div class="wrap">
		<h2>Open Graph Protocol by Hit Reach</h2>
		<form action="?page=open-graph-protocol" method="post">
			<?php wp_nonce_field( self::$option_name, "_".self::$option_name )?>
			<h3>Additional Options</h3>
			<p>Plugin <code>All in One SEO Pack</code> integration: <select name="<?php echo self::$option_name?>[additional][_aioseop]"><option value='0' <?php selected(0, $OpenGraphProtocol["additional"]["_aioseop"])?>>Disabled</option><option value='1' <?php selected(1, $OpenGraphProtocol["additional"]["_aioseop"])?>>Enabled</option></select> <span class='description' style='color:red'>We may drop this feature in the next update, let us know if you use it!</span></p>
			<p><input type="submit" class="button-primary" value="Update All Options" /></p>
			<h3>Default Settings</h3>
			<p class="description">These settings will also be applied to the homepage</p>
			<?php foreach( $OpenGraphProtocol["sections"] as $section ):?>
				<h4>Section: <?php echo $section["section_title"]?></h4>
				<ul class="section-fields" style="display:block;overflow:hidden">
					<?php	$fields = explode(",", $section["fields"]);
					foreach( $fields as $field ){
						$field_data = $OpenGraphProtocol["fields"][$field];
					?>
						<li style="padding:5px;border:1px #ccc solid; margin:5px; float:left; width:332px;"><label for='<?php echo $field;?>' style="height:32px; line-height:16px; display:block;font-weight:bold;"><?php echo $field_data["description"]?></label><input class="regular-text" id="<?php echo $field;?>" name="<?php echo self::$option_name?>[fields][<?php echo $field?>][value]" style="width:330px;" type="text" value="<?php echo esc_attr($field_data["value"])?>" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>"/></li>
					<?php } ?>
				</ul>
			<?php endforeach;?>
			<p><input type="submit" class="button-primary" value="Update All Options" /></p>
			<h3>Custom Fields</h3>
			<p class="description">This section allows for the creation of default fields.  Once a field is removed from below, it is retained within the post and will need to be removed manually</p>
			<?php if( sizeof( $OpenGraphProtocol["custom_fields"] ) < 1 ){
				echo "<h4>No Custom Fields</h4>";
			}?>
			<p><button id="add_new" class="button-secondary" onclick="return false">Add New Custom Field</button></p>
			<ul class="section-fields" style="display:block;overflow:hidden" id="custom-fields">
				<?php foreach( $OpenGraphProtocol["custom_fields"] as $index => $field ):?>
					<li style="border:1px #ccc solid; margin:5px; float:left;height:165px;">
						<table class="form-table" style="margin:0">
							<tr>
								<th><label for="cf-<?php echo $index ?>-description">Field Description</label></th>
								<td><input class="regular-text" id="cf-<?php echo $index;?>-description" name="<?php echo self::$option_name?>[custom_fields][<?php echo $index?>][description]" value="<?php echo esc_attr($field["description"] )?>" type="text" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>" /></td>
							</tr>
							<tr>
								<th><label for="cf-<?php echo $index ?>-property-name">Property Name( e.g. og:url )</label></th>
								<td><input class="regular-text" id="cf-<?php echo $index;?>-property-name" name="<?php echo self::$option_name?>[custom_fields][<?php echo $index?>][property_name]" value="<?php echo esc_attr($field["property_name"] )?>" type="text" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>" /></td>
							</tr>
							<tr>
								<th><label for="cf-<?php echo $index ?>-value">Default Value</label></th>
								<td><input class="regular-text" id="cf-<?php echo $index;?>-value" name="<?php echo self::$option_name?>[custom_fields][<?php echo $index?>][value]" value="<?php echo esc_attr($field["value"] )?>" type="text" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>" /></td>
							</tr>
							<tr>
								<th><label for="cf-<?php echo $index ?>-delete">Delete?</label></th>
								<td><input id="cf-<?php echo $index;?>-delete" name="<?php echo self::$option_name?>[custom_fields][<?php echo $index?>][delete]" value="1" type="checkbox" onclick="return confirm('Are you sure you want to delete this field?')" /></td>
							</tr>
						</table>
					</li>
				<?php $rolling_custom_field_index++; endforeach;?>

				<li style="border:1px #ccc solid; margin:5px; float:left;height:165px;">
					<table class="form-table" style="margin:0">
						<tr>
							<th colspan="2"><strong>New Custom Field</strong></th>
						</tr>
						<tr>
							<th><label for="cf-<?php echo $rolling_custom_field_index ?>-description">Field Description</label></th>
							<td><input class="regular-text" id="cf-<?php echo $rolling_custom_field_index;?>-description" name="<?php echo self::$option_name?>[custom_fields][<?php echo $rolling_custom_field_index?>][description]" value="" type="text" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>" /></td>
						</tr>
						<tr>
							<th><label for="cf-<?php echo $rolling_custom_field_index ?>-property-name">Property Name( e.g. og:url )</label></th>
							<td><input class="regular-text" id="cf-<?php echo $rolling_custom_field_index;?>-property-name" name="<?php echo self::$option_name?>[custom_fields][<?php echo $rolling_custom_field_index?>][property_name]" value="" type="text" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>" /></td>
						</tr>
						<tr>
							<th><label for="cf-<?php echo $rolling_custom_field_index ?>-value">Default Value</label></th>
							<td><input class="regular-text" id="cf-<?php echo $rolling_custom_field_index;?>-value" name="<?php echo self::$option_name?>[custom_fields][<?php echo $rolling_custom_field_index?>][value]" value="" type="text" tabindex="<?php echo $rolling_tab_index;$rolling_tab_index++;?>" /></td>
						</tr>
					</table>
				</li>
			</ul>

			<?php $rolling_custom_field_index++;?>
	<script type="text/javascript">
	var next_key = <?php echo $rolling_custom_field_index?>;
	var nextrow = '<li style="border:1px #ccc solid; margin:5px; float:left;height:165px;"><table class="form-table" style="margin:0"><tbody><tr><th colspan="2"><strong>New Custom Field</strong></th></tr><tr><th><label for="cf-+key+-description">Field Description</label></th><td><input type="text" value="" name="[custom_fields][+key+][description]" id="cf-+key+-description" class="regular-text"></td></tr><tr><th><label for="cf-+key+-property-name">Property Name( e.g. og:url )</label></th><td><input type="text" value="" name="[custom_fields][+key+][property_name]" id="cf-+key+-property-name" class="regular-text"></td></tr><tr><th><label for="cf-+key+-value">Default Value</label></th><td><input type="text" value="" name="[custom_fields][+key+][value]" id="cf-+key+-value" class="regular-text"></td></tr></tbody></table></li>';
	jQuery(document).ready(function($){
		$("button#add_new").bind("click",function(){
			var key = next_key;
			next_key = key+1;
			var code = nextrow;
			while(code.search(/\+key\+/) != "-1"){
				code = code.replace("\+key\+",key);
			}
			$("ul#custom-fields").append(code);
		});
	});
			</script>
			<p><input type="submit" class="button-primary" value="Update All Options" /></p>
		</form>
	</div>
<?php
		}



		/**
		 * Converts old options format into the new verion's options format.
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		 * @return Array new options
		 * @param Array $options new options version
		**/
		function convert_options_format($options){
			$new_options = self::$default_options;
			if( !empty ($options) ) {
				foreach( $options as $key => $val ){
					$new_options["fields"][$key]["value"] = $val;
				}
			}
			unset( $new_options["fields"]["_aioseop"] );
			unset( $new_options["fields"]["fb:admin"] );
			unset( $new_options["fields"]["videoheight"] );
			unset( $new_options["fields"]["videowidth"] );
			unset( $new_options["fields"]["videotype"] );

			if( isset( $options["_aioseop"] ) ){
				$new_options["additional"]["_aioseop"] = $options["_aioseop"];
			}
			return $new_options;
		}



		/**
		 * Converts old meta format into the new verion's meta format.
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		 * @return Array new options
		 * @param Array $options new options version
		**/
		function convert_meta_format($options){
			$new_options = array("fields"=>array(), "custom_fields"=>array());
			if( is_array( $options ) ){
				foreach( $options as $key => $val ){
					$new_options["fields"][$key]["value"] = $val;
				}
			}
			unset( $new_options["fields"]["_aioseop"] );
			unset( $new_options["fields"]["fb:admin"] );
			unset( $new_options["fields"]["videoheight"] );
			unset( $new_options["fields"]["videowidth"] );
			unset( $new_options["fields"]["videotype"] );
			return $new_options;
		}



		/**
		 * Upgrades the options
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		 * @return Array new options
		 * @param Array $options new options
		**/
		function upgrade_options($options){
			unset( $options["fields"]["_aioseop"] );
			unset( $options["fields"]["fb:admin"] );
			unset( $options["fields"]["videoheight"] );
			unset( $options["fields"]["videowidth"] );
			unset( $options["fields"]["videotype"] );
			$options["option_version"] = self::$option_version;
			return $options;
		}




		/**
		 * Added the OGP Namespace to the head tag
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function add_namespace( $existing_attributes ){
			return $existing_attributes . ' xmlns:og="http://ogp.me/ns#" xmlns:fb="http://ogp.me/ns/fb#"';
		}




		/**
		 * Prepares output of the OGP Content to the head
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function output_to_head_tag( ){
			global $OpenGraphProtocol;
			print( PHP_EOL.PHP_EOL."<!-- Open Graph Protocol Meta -->".PHP_EOL );
			print( "<meta property='og:site_name' value='".get_bloginfo("site_name")."'/>".PHP_EOL );
			if( is_home() ){
				self::do_home_ogp_tags();
			}elseif( is_archive() ){
				self::do_archive_ogp_tags();
			}elseif( is_single() || is_attachment() || is_page() ){
				self::do_single_ogp_tags();
			}
			print( "<!-- END Open Graph Protocol Meta -->".PHP_EOL.PHP_EOL );
		}




		/**
		 * Applies default values and creates additional tags which are generated on the fly
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function apply_tag_defaults( $ogp ){
			global $OpenGraphProtocol;
			if( $ogp["title"]["value"] == "" ){
				global $post;
				if( isset( $post->ID ) && is_single() ){
					$ogp["title"]["value"] = get_the_title( $post->ID );
				}else{
					$ogp["title"]["value"] = $OpenGraphProtocol["fields"]["title"]["value"];
				}
			}
			if( $ogp["url"]["value"] == "" ){
				global $post;
				if( isset( $post->ID ) && is_single() ){
					$ogp["url"]["value"] = get_permalink( $post->ID );
				}else{
					$ogp["url"]["value"] = $OpenGraphProtocol["fields"]["url"]["value"];
				}
			}
			if( $ogp["description"]["value"] == "" ){
				global $post;
				if( isset( $post->ID ) && is_single() ){
					$ogp["description"]["value"] = substr(esc_attr($post->post_content), 0, 120)."...";
				}else{
					$ogp["description"]["value"] = $OpenGraphProtocol["fields"]["description"]["value"];
				}
			}
			if( $ogp["image"]["value"] == "" ){
				$ogp["image"]["value"] = $OpenGraphProtocol["fields"]["image"]["value"];
			}
			if( $ogp["image_secure"]["value"] == "" ){
				$ogp["image_secure"]["value"] = $OpenGraphProtocol["fields"]["image_secure"]["value"];
			}
			if( $ogp["image"]["value"] != "" ){
				$images = explode(",", $ogp["image"]["value"] );
				$secure_images = "";
				if( $ogp["image_secure"]["value"] != "" ){
					$secure_images = explode( ",", $ogp["image_secure"]["value"] );
				}
				$o_image = array();
				foreach( $images as $index=>$image){
					if( $image != "" ){
						$o_image[$index]["og:image"] = $image;
						$o_image[$index]["og:image:url"] = $image;
						if( isset( $secure_images[$index] ) && $secure_images[$index] != "" )
							$o_image[$index]["og:image:secure_url"] = $secure_images[$index];
						$info = @getimagesize( $image );
						if( !empty( $info ) ){
							$o_image[$index]["og:image:width"] = $info[0];
							$o_image[$index]["og:image:height"] = $info[1];
							$o_image[$index]["og:image:type"] = $info["mime"];
						}
					}
				}
				$ogp["image"]["value"] = $o_image;
				unset( $ogp["image_secure"] );
			}
			return $ogp;
		}




		/**
		 * Prepares homepage OGP Tags
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function do_home_ogp_tags(){
			global $OpenGraphProtocol;
			$OpenGraphProtocol["fields"] = apply_filters( "ogp_output_fields", $OpenGraphProtocol["fields"], $locale = "home" );
			$OpenGraphProtocol["custom_fields"] = apply_filters( "ogp_output_custom_fields", $OpenGraphProtocol["custom_fields"], $locale = "home" );
			self::output_ogp_tags();
		}




		/**
		 * Prepares archive OGP Tags
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function do_archive_ogp_tags(){
			global $OpenGraphProtocol;
			$OpenGraphProtocol["fields"] = apply_filters( "ogp_output_fields", $OpenGraphProtocol["fields"], $locale = "archive" );
			$OpenGraphProtocol["custom_fields"] = apply_filters( "ogp_output_custom_fields", $OpenGraphProtocol["custom_fields"], $locale = "archive" );
			self::output_ogp_tags();
		}




		/**
		 * Prepares single item OGP Tags
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function do_single_ogp_tags(){
			global $OpenGraphProtocol;
			$ogp = $OpenGraphProtocol;
			$merge_fields = self::get_post_ogp();
			foreach( $merge_fields["fields"] as $field=>$val ){
				$ogp["fields"][$field]["value"] = $val["value"];
			}
			foreach( $merge_fields["custom_fields"] as $field=>$val ){
				$ogp["custom_fields"][$field]["value"] = $val["value"];
			}
			$OpenGraphProtocol["fields"] = apply_filters( "ogp_output_fields", $ogp["fields"], $locale = "single" );
			$OpenGraphProtocol["custom_fields"] = apply_filters( "ogp_output_custom_fields", $ogp["custom_fields"], $locale = "single" );
			self::output_ogp_tags();
		}





		/**
		 * Outputs the OGP Tags
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function output_ogp_tags(){
			global $OpenGraphProtocol;
			foreach( $OpenGraphProtocol["fields"] as $id=>$field ){
				if( $field["value"] != "" && !is_array($field["value"]) )
					printf( "<meta property='%s' content='%s' />".PHP_EOL, esc_attr( $field["property_name"] ), esc_attr( $field["value"] ) );
				elseif( is_array($field["value"]) ){
					foreach( $field["value"] as $a=>$b ){
						if( is_array($b) ){
							foreach( $b as $c=>$d ){
								printf( "<meta property='%s' content='%s' />".PHP_EOL, esc_attr( $c ), esc_attr( $d ) );
							}
						}else{
							printf( "<meta property='%s' content='%s' />".PHP_EOL, esc_attr( $a ), esc_attr( $b ) );
						}
					}
				}
			}
			foreach( $OpenGraphProtocol["custom_fields"] as $id=>$field ){
				if( $field["value"] != "" && !is_array($field["value"])  )
					printf( "<meta property='%s' content='%s' />".PHP_EOL, esc_attr( $field["property_name"] ), esc_attr( $field["value"] ) );
				elseif( is_array($field["value"]) ){
					foreach( $field["value"] as $a=>$b ){
						if( is_array($b) ){
							foreach( $b as $c=>$d ){
								printf( "<meta property='%s' content='%s' />".PHP_EOL, esc_attr( $c ), esc_attr( $d ) );
							}
						}else{
							printf( "<meta property='%s' content='%s' />".PHP_EOL, esc_attr( $a ), esc_attr( $b ) );
						}
					}
				}
			}
		}





		/**
		 * gets the ogp meta saved for a post
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function get_post_ogp(){
			global $post;
			$ogp = get_post_meta($post->ID,self::$postmeta_name, true );
			if( !isset( $ogp["_v3"] ) ){
				$ogp = self::convert_meta_format($ogp);
				$ogp["_v3"] = 1;
				update_post_meta( $post->ID, self::$postmeta_name, $ogp );
			}
			return $ogp;
		}



		/**
		 * gets the ogp meta saved for a post
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function get_ogp_by_id( $post_id ){
			$ogp = get_post_meta($post_id,self::$postmeta_name, true );
			if( !isset( $ogp["_v3"] ) ){
				$ogp = self::convert_meta_format($ogp);
				$ogp["_v3"] = 1;
				update_post_meta( $post->ID, self::$postmeta_name, $ogp );
			}
			return $ogp;
		}



		/**
		 * Loads All In One Seo Pack Options
		 * @version 1
		 * @since 3
		 * @author Jamie Fraser <jamie.fraser@hitreach.co.uk>
		**/
		function apply_aioseop( $ogp ){
			global $OpenGraphProtocol;
			if( $OpenGraphProtocol["additional"]["_aioseop"] == 1  && class_exists("All_in_One_SEO_Pack") ){
				if( is_single() ){
					global $post;
					if( $ogp["title"]["value"] == "" ){
						$ogp["title"]["value"] = get_post_meta( $post->ID, "_aioseop_title", true );

					}
					if( $ogp["description"]["value"] == "" ){
						$ogp["description"]["value"] = get_post_meta( $post->ID, "_aioseop_description", true );
					}
				}
			}
			if( is_home() ){
				$option = get_option( "aioseop_options" );
				if( $ogp["title"]["value"] == "" ){
					$ogp["title"]["value"] = $option["aiosp_home_title"];

				}
				if( $ogp["description"]["value"] == "" ){
					$ogp["description"]["value"] = $option["aiosp_home_description"];
				}
			}
			return $ogp;
		}
	}
	new OpenGraphProtocol();
 }