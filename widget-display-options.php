<?php
/*
Plugin Name: Widget Display Options
Plugin URI: http://dojodigital.com/themes-and-plugins/usage-manuals/widget-display-options-manual/
Description: Adds a section to all widgets allowing control over when each widget is displayed and the ability to add custom classes to each widget wrapper.
Version: 1.0.0
Author: Dojo Digital
Author URI: http://dojodigital.com
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WidgetDisplayOptionsLite' ) ) {

	define( WDO_MANUAL, 'http://dojodigital.com/themes-and-plugins/usage-manuals/widget-display-options-manual/' );
	
	// We need this to check for other plugins
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	require_once 'classes/WDOSettings.class.php';
	require_once 'classes/WDOConditional.class.php';
	require_once 'classes/WDOCallbacks.class.php';
	
	class WidgetDisplayOptionsLite {  
		    			    	
		/**
		 * Current version of wdo
		 * @access private
		 * @var string
		 */
		private $version = '1.0';
		    			    	
		/**
		 * Minimum version of WordPress required
		 * @access private
		 * @var string
		 */
		private $minWP = '3.0';
		    			    		
		/**
		 * Stores the instances of WDOConditionals
		 * @access private
		 * @var array
		 */
		private $conditionals = array();
		    	
		/**
		 * Use tooltips?
		 * @access private
		 * @var bool                                               
		 */
		private $useTooltips = true;
		    	
		/**
		 * Use help links?
		 * @access private
		 * @var bool                                               
		 */
		private $useHelpLinks = true;
		    			    	
		/**
		 * Stores the settings object
		 * @access private
		 * @var WDOSettings
		 */
		private $settings;
		    			    	
		/**
		 * Stores the current settings
		 * @access private
		 * @var array
		 */
		private $options;
		
		
		/**
		 * PHP 5 Constructor. Detects the WP Version, loads the conditionals and sets up the WP hooks.
		 * 
		 * @access public
		 * @return void
		 */ 
		public function __construct() { 
			
			global $wp_version;
			
        	if ( $wp_version >= $this->minWP ) {
        		
        		
        		// Load the Settings panel
				$this->settings = new WDOSettings( $this->version );
				$this->options = $this->settings->options;
        	
				// set up the tooltips
				$this->useTooltips = ( isset( $this->options['inline_help'] ) && ( 'tooltips' == $this->options['inline_help'] ) ) ? true : false;
				
				// Add a body class if tooltips are being used in the admin
				if( $this->useTooltips && is_admin() ){
					add_action( 'admin_body_class', array( &$this, 'add_tooltip_body_class') );
				}
        	
				$this->useHelpLinks = ( isset( $this->options['inline_help'] ) && ( 'link' == $this->options['inline_help'] ) ) ? true : false;
				
				// Instantiate the callbacks
				$WDOCallbacks = new WDOCallbacks();
			
				// Build the conditional instances and store them in an array
				$this->conditionals = $WDOCallbacks->get_conditionals( $this->useTooltips, $this->useHelpLinks );
        	
				// Load the hooks
				add_filter( 'plugin_action_links', array( &$this, 'plugin_settings_link'), 10, 2 );
				add_filter( 'widget_update_callback', array( &$this, 'update_extend' ), 10, 2 );
				add_filter( 'in_widget_form', array( &$this, 'form_extend' ), 10, 3 );
				add_filter( 'widget_display_callback', array( &$this, 'display_extend' ), 10, 1 );			
				add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
				add_filter( 'dynamic_sidebar_params', array( &$this, 'dynamic_sidebar_params_extend' ) );
				
				// Activation call
				register_activation_hook( __FILE__, array( &$this->settings, 'load_defaults' ) );
				
			} else {
				// Notify the user of insufficient WordPress version
				add_action( 'admin_notices', array( $this, 'insufficient_version_alert' ) );
			}
			
			
		} // __construct()
		
		
		/** 
		 * Adds a Settings link to the plugin page
		 * 
		 * @access public
		 * @param array $links
		 * @param string $file
		 * @return array
		 */
		public function plugin_settings_link( $links, $file ) {

        	if ( $file == plugin_basename( __FILE__ ) ){
        		$links[] = '<a href="' . admin_url( 'options-general.php' ) . '?page=wdo-plugin-settings">' . __( 'Settings', 'wdo_plugin' ) . '</a>';
        	}
        	
        	return $links;

    	} //  plugin_settings_link()
		
		
		/**
		 * Adds a class to the body tag for tooltips
		 * 
		 * @access public 
		 * @param string $classes
		 * @return string
		 */
		public function add_tooltip_body_class( $classes ){
			$classes .= ' wdo-use-tooltips';
			return $classes;
		} // add_tooltip_body_class()
		
		
		/** 
		 * Updates the instance settings for the widgets via the 'widget_update_callback' filter.
		 *
		 * @access public
		 * @param array $instance 
		 * @param array $new_instance  
		 * @return array
		 */
		public function update_extend( $instance, $new_instance ){
			
			// Conditional Display Toggle
			$instance['wdo-logic'] = $new_instance['wdo-logic'];
			
			// Hide Panel
			$instance['wdo-hide-panel'] = $new_instance['wdo-hide-panel'];
			
			// Hide or Show Toggle. Use "hide" by default.
			$displayOptions = array( 'hide', 'show' );
			
			if( isset( $instance['wdo-display'] ) && in_array( $instance['wdo-display'], $displayOptions ) ){
				$instance['wdo-display'] = $new_instance['wdo-display'];
			} else {
				$instance['wdo-display'] = 'hide';
			}
			
			// AND or OR Toggle. Use "or" by default.
			$andOrOptions = array( 'and', 'or' );
			
			if( isset( $instance['wdo-and-or'] ) && in_array( $instance['wdo-and-or'], $andOrOptions ) ){
				$instance['wdo-and-or'] = $new_instance['wdo-and-or'];
			} else {
				$instance['wdo-and-or'] = 'or';
			}
			
			// Custom classnames
			if( isset( $new_instance[ 'wdo-classes' ] ) ){		
				$instance[ 'wdo-classes' ] = implode( ' ', $this->fetch_classes_array( $new_instance[ 'wdo-classes' ] ) );
			} else {
				$instance[ 'wdo-classes' ] = '';
			}

			// We need a counter for active conditionals
			$activeConditionals = 0;
			
			// Set conditional values and thier parameters
			foreach( $this->conditionals as $section => $conditions ){
				foreach( $conditions as $slug => $condition ){
					// Append our prefix to the slug
					$slug = 'wdo_' . $slug;
					
					$instance[ $slug ] = $new_instance[ $slug ] ? '1' : '0';
					
					// Iterate our active contitionals
					if( $instance[ $slug ] ) $activeConditionals++;
					
					$instance[ $slug . '-not' ] = $new_instance[ $slug . '-not' ] ? '1' : '0';
					if( $condition->accepts_parameters() ){
						$instance[ $slug . '-params' ] = strip_tags( $new_instance[ $slug . '-params' ] );
					}
				}
			} 
			
			// If no conditionals are active, shut off the main logic boolen,
			// this way we're not showing the whole interface for inactive conditionals
			if( !$activeConditionals ) $instance['wdo-logic'] = 0;
			
			return $instance;
			
		} // update_extend()
		
		
		/** 
		 * Outputs the HTML form elements for the conditionals via the 'widget_form_callback' filter.
		 *
		 * @access public
		 * @param array $instance  
		 * @param array $return  
		 * @param array $widget
		 * @return array Returns the widget instance
		 */
		public function form_extend( $widget, $return, $instance ){
				
			$hidePanel = ( isset( $instance['wdo-hide-panel'] ) && $instance['wdo-hide-panel'] ) ? '1' : '0';
			
			?>
		
			<br style="clear:both" />
		
			<div style="text-align:right"><a href="#" class="wdo-display-options-toggle button-secondary" <?php if( !$hidePanel ) : ?>style="display: none;"<?php endif; ?> data-toggles="<?php echo $widget->id; ?>" id="wdo-display-options-toggle-<?php echo $widget->id; ?>"><?php _e( 'Display Options', 'wdo_plugin' ); ?></a></div>
				
			<input type="hidden" name="<?php echo $widget->get_field_name( 'wdo-hide-panel' ); ?>" id="<?php echo $widget->get_field_id( 'wdo-hide-panel' ); ?>" value="<?php echo $hidePanel; ?>"  />
		
			<div class="wdo-display-options<?php if( $hidePanel ) : ?> wdo-hide<?php endif; ?>" id="wdo-display-options-<?php echo $widget->id; ?>">
			
				<h4 class="wdo-top"><?php _e( 'Display Options', 'wdo_plugin' ); ?> <a href="#" class="wdo-display-options-hide-me" id="wdo-hide-me-<?php echo $widget->id; ?>" data-toggles="<?php echo $widget->id; ?>"><?php _e( 'Hide', 'wdo_plugin' ); ?></a></h4>
					
				<div class="wdo-section wdo-info">
						
					<table width="100%"><tr><td width="40px">
					
						<label for="<?php echo $widget->get_field_id( 'wdo-classes' ); ?>"><strong><?php _e( 'Classes:', 'wdo_plugin' ); ?></strong></label>
						
					</td><td>
					
						<input type="text" class="wdo-class-list" id="<?php echo $widget->get_field_id( 'wdo-classes' ); ?>" name="<?php echo $widget->get_field_name( 'wdo-classes' ); ?>" placeholder="<?php esc_attr_e( 'Optional', 'wdo_plugin' ); ?>" value="<?php echo ( isset( $instance[ 'wdo-classes' ] ) ) ? $instance[ 'wdo-classes' ] : '' ; ?>" />
					
					</td>
					
						<?php if( $this->useTooltips ) : ?>
						
						<td width="12px">
							<a href="#" class="wdo-tooltip" data-qtitle="<?php _e( 'Classes', 'wdo_plugin' ); ?>" title="<?php esc_attr_e( '<p>Add your own classnames to this widget\'s containing element.</p><p class="wdo-accepts"><strong>Accepts: </strong> A space seperated list of classes.</p>', 'wdo_plugin' ); ?>"><?php _e( 'Help', 'wdo_plugin' ); ?></a>
						</td>
					
						<?php elseif( $this->useHelpLinks ) : ?>
						
						<td width="12px">
							<a href="<?php echo WDO_MANUAL; ?>#usage-classes" class="wdo-help" title="<?php esc_attr_e( 'Learn More', 'wdo_plugin' ); ?>" target="_BLANK"><?php _e( 'Help', 'wdo_plugin' ); ?></a>
						</td>
						
						<?php endif; ?>
						
					</tr></table>
					
				</div>
								
				<!-- CONDITIONALS -->
		
				<h4 class="wdo-logic-toggles">
				
					<label data-id="<?php echo $widget->id; ?>">
				
						<input type="checkbox"  name="<?php echo $widget->get_field_name( 'wdo-logic' ); ?>" id="<?php echo $widget->get_field_id( 'wdo-logic' ); ?>" <?php if( isset( $instance['wdo-logic'] ) && $instance['wdo-logic'] ) { echo 'checked="checked"'; } ?>  data-id="<?php echo $widget->id; ?>" />
				
						<?php _e( 'Conditional Display', 'wdo_plugin' ); ?>
						
						<?php if( $this->useTooltips ) : ?>
						
							<a href="#" class="wdo-tooltip" data-qtitle="<?php _e( 'Conditional Display', 'wdo_plugin' ); ?>" title="<?php esc_attr_e( '<p>Hide/Display this widget only if a set of chosen criteria relating to the current page view are met.</p>', 'wdo_plugin' ); ?>"><?php _e( 'Help', 'wdo_plugin' ); ?></a>
						
						<?php elseif( $this->useHelpLinks ) : ?>
						
							&nbsp;<a href="<?php echo WDO_MANUAL; ?>#usage-conditionals" class="wdo-help" title="<?php esc_attr_e( 'Learn More', 'wdo_plugin' ); ?>" target="_BLANK"><?php _e( 'Help', 'wdo_plugin' ); ?></a>
					
						<?php endif; ?>
						
					</label>
						
				</h4>
				
				<div class="wdo-conditionals wdo-hide" id="wdo-conditionals-<?php echo $widget->id; ?>" data-id="<?php echo $widget->id; ?>">
					
					<div class="wdo-relationship wdo-section">
						<select name="<?php echo $widget->get_field_name( 'wdo-display' ); ?>" id="<?php echo $widget->get_field_id( 'wdo-display' ); ?>" class="wdo-display<?php if( $this->useTooltips ) : ?> wdo-tooltip<?php endif; ?>"<?php if( $this->useTooltips ) : ?> data-qtitle="<?php esc_attr_e( 'Display Method', 'wdo_plugin' ); ?>" title="<?php esc_attr_e( '<ul><li><strong>Hide</strong> - Displayed by default and only hidden if the conditions are met.</li><li><strong>Show</strong> - Hidden by default and only displayed if the conditions are met.', 'wdo_plugin' ); ?>"<?php endif; ?>>
							<option value="hide" <?php selected( $instance['wdo-display'] == 'hide' ); ?>>Hide</option>
							<option value="show"<?php selected( $instance['wdo-display'] == 'show' ); ?>>Show</option>
						</select> <?php _e( 'this widget if any of the following conditions apply:', 'wdo_plugin' ); ?>
					
					</div>
					
					<div class="wdo-section">
						
					<?php 
					
					foreach( $this->conditionals as $section => $conditions ) {
						
						echo '<fieldset class="wdo-fieldset" id="wdo-fieldset-' . sanitize_html_class( $section ) . '-' . $widget->id . '"><legend><a href="#" class="wdo-toggle-fieldset" id="wdo-toggle-fieldset-' .$widget->id  . '" data-toggles="wdo-' . sanitize_html_class( $section ) . '-functions-' .$widget->id  . '"><span>[+]</span> <strong>' . $section . '</strong></a> </legend><div class="wdo-functions wdo-hide" id="wdo-' . sanitize_html_class( $section ) . '-functions-' .$widget->id  . '">'; 
						
						foreach( $conditions as $slug => $condition ){
							if( $condition->set_instance( $widget, $instance ) ){
								echo $condition->get_inputs();
							}
						}
							
						echo '</div></fieldset>'; 
						
					} 
					
					?>
					
					</div>
					
				</div>
				
				<!-- END CONDITIONALS -->
				
			</div>
		
			<br style="clear:both" />
			
			<?php
			
			return $instance;
			
		} // form_extend()
		
		
		/**
		 * Checks to see if this widget is suppose to be hidden and if so returns false
		 * to the 'widget_display_callback' filter.
		 * 
		 * @todo Create a dropdown list of hide options ( Suppress Output, Display None(CSS), Hide Visually(CSS) )
		 *
		 * @access public
		 * @param array $instance
		 * @return mixed
		 */
		public function display_extend( $instance ) {
			return ( $this->is_hidden( $instance ) ) ? false : $instance;
		} // display_extend()
		
		
		/**
		 * Adds any new classes to the widget wrapper via the 'dynamic_sidebar_params' filter
		 *
		 * @access public
		 * @param array $params
		 * @return array
		 */
		public function dynamic_sidebar_params_extend( $params ){
		
			global $wp_registered_widgets;
			
			// Access the current widget data
			$widget_id	= $params[0]['widget_id'];
			$widget_obj	= $wp_registered_widgets[ $widget_id ];
			$widget_opt	= get_option( $widget_obj['callback'][0]->option_name );
			$widget_num	= $widget_obj['params'][0]['number'];
		
			// Inject our new classnames
			if ( isset( $widget_opt[ $widget_num ][ 'wdo-classes' ] ) && !empty( $widget_opt[ $widget_num ][ 'wdo-classes' ] ) )
				$params[0]['before_widget'] = preg_replace( '/class="/', 'class="' . $widget_opt[$widget_num][ 'wdo-classes' ] . ' ', $params[0]['before_widget'], 1 );
		
			return $params;
			
		} // dynamic_sidebar_params_extend()
		
		
		/**
		 * Returns true if this widget should be hidden on this page.
		 *
		 * @param array $instance
		 * @return bool
		 */
		private function is_hidden( $instance ){
		
			// return false immediately if no conditions are applied
			if( !isset( $instance['wdo-logic'] ) || !$instance['wdo-logic'] ) return false;
			
			// set the master boolean to keep track of whether we're showing or hiding
			$bool = ( isset( $instance['wdo-display'] ) && 'show' == $instance['wdo-display'] ) ? false : true;
						
			// Loop through each WDOProConditional instance
			foreach( $this->conditionals as $section => $conditions  ){
			
				foreach( $conditions as $slug => $condition  ){
					
					// Append our prefix to the slug
					$slug = 'wdo_' . $slug;
					
					// Skip this condition if it's not set
					if( !isset( $instance[ $slug ] ) || !$instance[ $slug ] ) continue;
					
					// If the condition doesn't accept parameters, there's no need to process them
					if( !$condition->accepts_parameters() ){
											
						// No parameters needed, just run the callback. 
						if ( call_user_func( $condition->get_callback() ) ){
							return $bool;
						}
						
					} else {
						
						// If the condition expects an array, make it so
						if( $condition->accepts( 'array' ) ){	
						
							// Split the parameters into an array. This always expects comma seperated values.
							$args = ( isset( $instance[ $slug . '-params' ] ) ) ? explode( ',', $instance[ $slug . '-params' ] ) : array();
							
							// Clean up the parameters by trimming whitespace and removing empty elements.
							for( $i = 0; $i < count( $args ); $i++ ){
								$args[$i] = trim( $args[$i] );
								// Use this Classes has_string() method to check for a value
								if( !$this->has_string( $args[$i] ) ){
									unset( $args[$i] );
								}
							}
							
							// Make sure to strip duplicates
							$args = array_unique( $args );
							
						// We just need a sting. Trim the whitespace and move along.
						} else {
							$args = trim( $instance[ $slug . '-params' ] );
						}
						
						
						// Run the callback with parameters.
						if( call_user_func( $condition->get_callback(), $args ) ){
							return $bool;	
						}
						
					}
					
				}
				
			}
			
			// none of the conditions passed, so flip the master boolean and return it
			return !$bool;
			
		} // is_hidden()
		
		
		/**
		 * Load scripts & styles for the widgets.php page
		 * 
		 * @access public
		 * @return void
		 */
		public function admin_enqueue_scripts(){
						
			if( 'widgets.php' == basename( $_SERVER['PHP_SELF'] ) ){
				wp_enqueue_script( 'livequery', plugins_url( 'js/jquery.livequery.min.js', __FILE__ ), array( 'jquery' ), null, true );
				wp_enqueue_script( 'wdo', plugins_url( 'js/wdo.js', __FILE__ ), array( 'jquery', 'livequery' ), null, true );
				wp_enqueue_style( 'wdo-css', plugins_url( 'css/wdo.css', __FILE__ ) );
				
				// Don't load qTip if we don't need to...
				if( !$this->useHelpers && !$this->useTooltips ) return;
				
				// qTip
				wp_enqueue_script( 'wdo-qtip', plugins_url( 'js/jquery.qtip.min.js', __FILE__ ), array( 'jquery' ), null, true );
				wp_enqueue_script( 'wdo-tooltips', plugins_url( 'js/wdo.tooltips.js', __FILE__ ), array( 'jquery', 'livequery', 'wdo-qtip' ), null, true );
				
			}
			
		} // admin_enqueue_scripts()
		
		
		/**
		 * Verifies that the given value contains a string with
		 * at least one non-whitespace character.
		 *
		 * @access private
		 * @param mixed $str
		 * @return bool
		 */
		private function has_string( $str ){
	
			if( !isset( $str ) || !is_string( $str ) || '' == trim( $str ) ) return false;	
					
			return true;
		
		} // has_string()
		
		
		/**
		 * Cleans up the class names and splits them into an array.
		 * 
		 * @access private
		 * @param string $classes
		 * @return array
		 */
		private function fetch_classes_array( $classes ){
		
			// Split the string into an array. This always expects Space Seperated Values.
			$classesArray = explode(' ', $classes );
			$classesCount = count( $classesArray );
						
			for( $i = 0; $i < $classesCount; $i++ ){
				// Sanitize to make sure these are valid CSS classnames
				$classesArray[$i] = sanitize_html_class( $classesArray[$i] );
			}
						
			return $classesArray;
		
		} // fetch_classes_array()
		
		
		/**
		 * Alert the admin that the version of WordPress is insufficient.
		 * 
		 * @access public
		 * @return void
		 */
		 public function insufficient_version_alert(){
		 	echo '<div class="error">
			       <p><strong>' . __( 'Sorry!', 'wdo-plugin' ) . '</strong>' . __( 'The Widget Display Options plugin requires WordPress Version 3.0 or greater. Please upgrade your WordPress install or deactivate the plugin.', 'wdo-plugin' ) . '</p>
			    </div>';
		 } // insufficient_version_alert()
		
		
	} // WidgetDisplayOptionsLite
	
	// Autoload
	$WidgetDisplayOptionsLite = new WidgetDisplayOptionsLite();
	
} // if ( !class_exists( 'WidgetDisplayOptions' ) )