<?php if( !class_exists( 'WDOConditional' ) ) {

	/**
 	 * Builds the logic for handling a single conditional.
	 *
	 * @package Widget Display Options Lite
	 * @author Randall Runnels <randy@dojodigital.com>
	 * @version 1.0
	 * @since 1.0
	 */
	class WDOConditional {
		
		/**
		 * A unqique identifier. This is usually the callback name.
		 * @access protected
		 * @var string
		 */
		protected $slug;
		
		/**
		 * The name of the callback function to test the conditional
		 * @access protected
		 * @var string
		 */
		protected $callback;
		
		/**
		 * An id attibute prefix for input fields
		 * @access protected
		 * @var string
		 */
		protected $id;
		
		/**
		 * A name attibute prefix for input fields
		 * @access protected
		 * @var string
		 */
		protected $name;
		
		/**
		 * The parameters for this conditional
		 * @access protected
		 * @var string
		 */
		protected $parameters = '';
		
		/**
		 * An id attibute for the parameter input fields
		 * @access protected
		 * @var string
		 */
		protected $paramsID;
		
		/**
		 * A name attibute for the parameter input fields
		 * @access protected
		 * @var string
		 */
		protected $paramsName;
		
		/**
		 * An id attibute for the helper button
		 * @access protected
		 * @var string
		 */
		protected $helperID;
		
		/**
		 * Whether this conditional is turned on
		 * @access protected
		 * @var bool
		 */
		protected $is_on = false;
		
		/**
		 * Stores all of the callback data
		 * @access protected
		 * @var array
		 */
		protected $data;
		
		/**
		 * A label for this conditional
		 * @access protected
		 * @var string
		 */
		protected $label;
		
		/**
		 * Does this conditional accept parameters?
		 * @access protected
		 * @var bool
		 */
		protected $acceptsParameters = false;
		
		/**
		 * Stores any errors generated
		 * @access protected
		 * @var array
		 */
		protected $errors = array();
		
		/**
		 * PHP 5 Constructor. Sets up the WP actions and initializes some of the properties.
		 * 
		 * @access public
		 * @param string $slug A unique string to identify this instance
		 * @param array $data The callback data
		 * @return void
		 */		
		public function __construct( $slug, $data ){
						
			// Load error handling
			add_action( 'admin_notices', array( &$this, 'admin_alerts' ) );
			
			// Validate the callback function
			add_action( 'init', array( &$this, 'validate_callback' ) );
			
			// Prefix the slug with "wdo_" so we (hopefully) don't interfere with other widget parameters.
			$this->slug = 'wdo_' . $slug;
			
			$this->data = $data;
			
			// If no label is available use the non-prefixed slug
			$this->data['label'] = ( isset( $this->data['label'] ) && $this->data['label'] != '' ) ? $this->data['label'] : $slug;
			
			// Does this condition accept paramters?
			$this->acceptsParameters = ( $this->data['params'] ) ? true : false;
			
		} // __construct()
		
		
		/**
		 * Validate that the callback function is callable.
		 * 
		 * @access public
		 * @return void
		 */
		public function validate_callback(){
			
			// Validate
			if( is_callable( $this->data['callback'] ) ){
				$this->callback = $this->data['callback'];
				
			// Failed, build an error alert.
			} else {
				
				// Get the name of the callback function for reporting.
				$functionName = $this->data['callback'];
				
				// Class methods will be in an array, so we need to build the name using the elements.
				if( is_array( $functionName ) ){
					$functionName = $this->data['callback'][0] . '::' . $this->data['callback'][1];
				}
				
				// Store the error message for later.
				$this->errors[] = __( '<strong>Widget Display Options:</strong> Unable to find function/method: <em>' . $functionName . '</em>' );
			}
			
		} // validate_callback()
		
		
		/** GETTERS @note These properties are currently set in the constructor, thus there are no setters. **/
		
		
		/**
		 * Getter for the $acceptsParameters property. 
		 * 
		 * @access public
		 * @return bool
		 */
		public function accepts_parameters(){ return $this->acceptsParameters; }
		
		
		/**
		 * Getter for the $slug property
		 * 
		 * @access public
		 * @return string
		 */
		public function get_slug(){ return $this->slug; }
		
	
		/**
		 * Getter for the $data['use_tooltips'] property
		 * 
		 * @access public
		 * @return mixed
		 */
		public function use_tooltips(){
			return (  
				isset( $this->data['use_tooltips'] ) 
				&& $this->data['use_tooltips']  
			) ? true : false;
		} // use_tooltips()
		
	
		/**
		 * Getter for the $data['use_help_links'] property
		 * 
		 * @access public
		 * @return mixed
		 */
		public function use_help_links(){
			return (  
				isset( $this->data['use_help_links'] ) 
				&& $this->data['use_help_links']  
			) ? true : false;
		} // use_help_links()
		
	
		/**
		 * Getter for the $callback property
		 * 
		 * @access public
		 * @return mixed
		 */
		public function get_callback(){ return $this->callback; }
	
		
		/** END GETTERS **/
		
		
		/**
		 * Checks if this condition accepts a given data type.
		 * 
		 * @access public
		 * @param string $type Can be 'string' or 'array'
		 * @return bool 
		 */
		public function accepts( $type ){ 
			
			$allowed = array( 'string', 'array' );
			
			if( $this->accepts_parameters() 
				&& isset( $this->data['accepts'] ) 
				&& in_array( $this->data['accepts'], $allowed ) 
				&& in_array( $type, $allowed ) 
			){
				return ( $type == $this->data['accepts'] ); 
			}
			
			return false;
			
		} // get_accepts()
		
		
		/**
		 * Injects the widget instance properties for this conditional.
		 * @note This method must be run BEFORE get_inputs().
		 * 
		 * @param object $widget An instance of a WordPress widget. We need this to build id and name attributes.
		 * @param array $instance The current state of the widget parameters.
		 * @return bool Returns false if the instance is not updated
		 */
		public function set_instance( $widget, $instance ){
			
			if( is_object( $widget ) ){
				// Populate the instance properties.
				$this->widgetID = $widget->id;
				$this->id = $widget->get_field_id( $this->slug );
				$this->name = $widget->get_field_name( $this->slug );
				$this->paramsID = $widget->get_field_id( $this->slug . '-params' );
				$this->paramsName = $widget->get_field_name( $this->slug . '-params' );
			} else {
				return false;
			}
			
			if( is_array( $instance ) ){
				
				// Is this conditional active?
				$this->is_on = ( isset( $instance[ $this->slug ] ) && $instance[ $this->slug ] ) ? true : false;
				
				// The parameters for this conditional
				if( $this->accepts_parameters() && isset( $instance[ $this->slug . '-params' ] ) ){
					$this->parameters = $instance[ $this->slug . '-params' ];
				}
				
			} else {
				return false;
			}
			
			// All is well...
			return true;
			
		} // set_instance()
		
		
		/**
		 * Builds the input fields for the widget instance form.
		 * 
		 * @access public
		 * @return string The input field for this conditional.
		 */
		public function get_inputs(){
			
			$output = '';
			
			// Wrap these fields in a div.
			$output .= "\n\n" . '<div class="wdo-container">';
			
			// Start the Primary label.
			$output .= '<label>';
			

			// Primary checkbox Field. This field toggles whether or not this condition is active.
			$output .= "\n\t" . '<input type="checkbox" class="wdo-checkbox" 
							id="' . $this->id . '" 
							name="' . $this->name . '" ' .
							checked( $this->is_on, true, false ) . 
						'/> ';
			
			// Print the label text
			$output .=  $this->data['label'];
			
			// The Description formatted for a tooltip inside this labels' "title" attribute. @ref http://calebjacob.com/tooltipster/
			if( $this->use_tooltips() && isset( $this->data['desc'] ) && '' != trim( $this->data['desc'] ) ){
				
				$desc = '<p>' . $this->data['desc'] . '</p>';
				
				// Build a seperate paragraph for any ACCEPTS data
				if( $this->data['params'] ){
					$desc .= '<p class="wdo-accepts"><strong>' . __( 'Accepts:', 'wdo_plugin' ) . ' </strong>' . $this->data['params'] . '</p>';
				}
				
				// Build a seperate paragraph for any MORE INFO data
				if( isset( $this->data['more'] ) && '' != $this->data['more'] ){
					$desc .= '<p class="wdo-more"><strong>' . __( 'More Info:', 'wdo_plugin' ) . '  </strong><a href="' . $this->data['more'] . '" target="_BLANK">' . str_replace( 'wdo_', '', $this->slug ) . '()</a></p>';
				}
				
				$output .= ' <a href="#" class="wdo-tooltip" data-qtitle="' . $this->data['label'] . '" title="' . esc_attr( $desc ) . '">' . __( 'Help', 'wdo_plugin' ) . '</a>';
			
			} elseif( $this->use_help_links() ){
				
				$output .= ' <a href="' . WDO_MANUAL . '#' . str_replace( 'wdo_', '', $this->slug ) . '" class="wdo-help" title="' . esc_attr__( 'Learn More', 'wdo_plugin' ) . '" target="_BLANK">' . __( 'Help', 'wdo_plugin' ) . '</a>';
			
			}
			
			$output .= '</label>';
			
			
			// Parameters text field. This field stores any applicable parameters.
			if( $this->accepts_parameters() ){ 
				
				$output .= "\n\t" . '<input type="text" 
								id="' . $this->paramsID . '" 
								name="' . $this->paramsName . '"
								value="' . $this->parameters . '"
								class="wdo-textfield ';
				
				if( !$this->is_on ) $output .=  'wdo-hide';

				$output .= '" placeholder="' . $this->data['placeholder'] . '" />';
			
				
			}

			// Close the wrapper.
			$output .= '</div>';
			
			return $output;
			
		} // get_inputs()
		
		
		/**
		 * Alert the admin if any errors were generated.
		 * 
		 * @access public
		 * @return void
		 */
		 public function admin_alerts(){
		 	
		 	if( $this->errors ){
		 		echo '<div class="error"><ul>';
		 		foreach( $this->errors as $error ){ echo '<li>' . $error . '</li>'; }
			 	echo '</div>';
		 	}
		 	
		 } // admin_alerts()
		
		
	} // class WDOConditional 
	
} // if !class_exists( 'WDOConditional' )