<?php if( !class_exists( 'WDOSettings' ) ) {

    /**
 	 * Builds an options page with some optional settings under options-general.php
	 *
	 * @package Widget Display Options Lite
	 * @author Randall Runnels <randy@dojodigital.com>
	 * @version 1.0
	 * @since 1.0
	 */
	class WDOSettings {

		/**
		 * Stores the options for this page
		 * @access public
		 * @var array
		 */
		public $options;

		/**
		 * Current version of wdo
		 * @access private
		 * @var float
		 */
		private $version;
		
			
		/**
		 * PHP 5 Constructor. Builds an array of callback data.
		 * 
		 * @access public
		 * @return void
		 */		
		public function __construct( $version ){
			
			$this->version = $version;
			
			// Store the options
			$this->options = get_option( 'wdo-plugin-settings-fields' );
			
			// Add the menu
			add_action( 'admin_menu', array( &$this, 'admin_add_page' ) );
			
			// Configure Settings
			add_action('admin_init', array( &$this, 'configure_settings' ) );
			
		} // __construct()
		
		
		/**
		 * Load the admin page. 
		 * 
		 * @access public
		 * @return void
		 */
		public function admin_add_page(){
			
			add_options_page( 
				__( 'Widget Display Options' ), 
				__( 'Widget Display Options' ), 
				'manage_options', 
				'wdo-plugin-settings', 
				array( &$this, 'build_page' ) 
			);
			
		} // admin_add_page()
		
		
		/**
		 * Build the admin page.
		 * 
		 * @access public
		 * @return void
		 */
		public function build_page(){ ?>
			
<div class="wrap">

	<?php screen_icon(); ?>
	
	<h2><?php _e( 'Widget Display Options - Settings' ); ?></h2>
	
	<form action="options.php" method="post">
	
		<?php settings_fields( 'wdo-plugin-settings-fields' ); ?>
		
		<?php do_settings_sections( 'wdo-plugin-settings' ); ?>
		 
		<p><input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wdo_plugin' ); ?>" /></p>
		
	</form>

</div>

		<?php } // build_page()
		
		
		/**
		 * Configures the settings.
		 * 
		 * @access public
		 * @return void
		 */
		public function configure_settings(){
			
			register_setting( 'wdo-plugin-settings-fields', 'wdo-plugin-settings-fields', array( &$this, 'validate_options' ) );
			
			//Help Settings
			add_settings_section( 'wdo-plugin-settings-fields_help', __( 'Help Settings', 'wdo_plugin' ), array( &$this, 'help_text' ), 'wdo-plugin-settings' );
			
			// Tooltips
			add_settings_field( 
				'inline_help',
				__( 'Inline Help', 'wdo_plugin' ),
				 array( &$this, 'inline_help' ),
				'wdo-plugin-settings',
				'wdo-plugin-settings-fields_help'
			);
			
			// Helpers
			add_settings_field( 
				'use_helpers',
				__( 'Parameter Helpers', 'wdo_plugin' ),
				 array( &$this, 'use_helpers' ),
				'wdo-plugin-settings',
				'wdo-plugin-settings-fields_help'
			);
			
			//Print Settings
			add_settings_section( 'wdo-plugin-settings-fields_print', __( 'Print Settings', 'wdo_plugin' ), array( &$this, 'print_text' ), 'wdo-plugin-settings' );
			
			// Print
			add_settings_field( 
				'hide_print',
				__( 'Hide From Print', 'wdo_plugin' ),
				 array( &$this, 'hide_print' ),
				'wdo-plugin-settings',
				'wdo-plugin-settings-fields_print'
			);
			
			
			//Mobble Settings
			add_settings_section( 'wdo-plugin-settings-fields_mobble', __( 'mobble Integration', 'wdo_plugin' ), array( &$this, 'mobble_text' ), 'wdo-plugin-settings' );
				
			// Print
			add_settings_field( 
				'use_mobble',
				__( 'Use mobble', 'wdo_plugin' ),
				 array( &$this, 'use_mobble' ),
				'wdo-plugin-settings',
				'wdo-plugin-settings-fields_mobble'
			);
			
			
			// Version
			add_settings_field( 
				'wdo_version',
				null,
				array( &$this, 'wdo_version' ),
				'wdo-plugin-settings',
				'wdo-plugin-settings-fields_help'
			);
			
		} // configure_settings()
		
		
		/**
		 * output the Help settings text
		 * 
		 * @access public
		 * @return void
		 */
		public function help_text(){

			echo '<p>' . __( 'If you have a LOT of widgets and find the widgets interface running slow, try changing Inline Help from <em>Tooltips</em> to <em>Link</em>.', 'wdo_plugins' ) . '</p>';
			
		} // help_text()
		
		
		/**
		 * output the Allow Inline Help text
		 * 
		 * @access public
		 * @return void
		 */
		public function inline_help(){ 
			echo $this->radio( 'inline_help',
				array(
					'link' 		=>  __( '<strong>Link:</strong> Link help buttons to external manual (recommended).', 'wdo_plugin' ),
					'tooltips' 	=>  __( '<strong>Tooltips:</strong> Display help as inline tooltips (This can slow down your widget interface if you have a lot of widgets).', 'wdo_plugin' ),
					'none' 		=>  __( '<strong>None:</strong> Do not display inline help at all.', 'wdo_plugin' )
				)
			); 
		} // inline_help()
		
		
		/**
		 * output the Allow Parameter Helpers text
		 * 
		 * @access public
		 * @return void
		 */
		public function use_helpers(){ 
			echo 'This feature is available in the <a href="http://dojodigital.com/downloads/widget-display-options-pro/" title="Widget Display Options Pro" target="_BLANK">Pro Version</a>';
			echo '<input type="hidden" name="wdo-plugin-settings-fields[use_helpers]" value="' . $this->options[ 'use_helpers' ] . '" />';
		} // use_helpers()
		
		
		/**
		 * output the Print settings text
		 * 
		 * @access public
		 * @return void
		 */
		public function print_text(){

			// echo '<p>' . __( 'If your theme already hides widgets from print (Twenty Twelve does this) you won\'t need the Hide From Print option.', 'wdo_plugins' ) . '</p>';
			
		} // print_text()
		
		
		/**
		 * output the Hide from Print text
		 * 
		 * @access public
		 * @return void
		 */
		public function hide_print(){ 
			echo 'This feature is available in the <a href="http://dojodigital.com/downloads/widget-display-options-pro/" title="Widget Display Options Pro" target="_BLANK">Pro Version</a>';
			echo '<input type="hidden" name="wdo-plugin-settings-fields[hide_print]" value="' . $this->options[ 'hide_print' ] . '" />';
		} // hide_print()
		
		
		/**
		 * output the Mobble settings text
		 * 
		 * @access public
		 * @return void
		 */
		public function mobble_text(){

			//echo '<p>' . __( 'Since you have the mobble plugin installed, we can use the conditionals it provides.', 'wdo_plugins' ) . '</p>';
			
		} // mobble_text()
		
		
		/**
		 * output the Use Mobble text
		 * 
		 * @access public
		 * @return void
		 */
		public function use_mobble(){ 
			echo 'This feature is available in the <a href="http://dojodigital.com/downloads/widget-display-options-pro/" title="Widget Display Options Pro" target="_BLANK">Pro Version</a>';
			echo '<input type="hidden" name="wdo-plugin-settings-fields[use_mobble]" value="' . $this->options[ 'use_mobble' ] . '" />';
		} // use_mobble()
		
		
		/**
		 * output the hidden Version field
		 * 
		 * @access public
		 * @return void
		 */
		public function wdo_version(){ echo '<input type="hidden" name="wdo-plugin-settings-fields[wdo_version]" value="' . $this->version . '" />'; } // wdo_version()
		
		
		/**
		 * Validate the settings
		 * @todo this
		 * 
		 * @access public
		 * @return void
		 */
		public function validate_options( $input ){
			
			return $input;
			
		} // validate_options()
		
		
		/**
		 * Output a checkbox
		 * 
		 * @access private
		 * @param string $slug
		 * @return bool
		 */
		public function checkbox( $slug, $label = '' ){
			return '<label><input id="' . $slug . '" name="wdo-plugin-settings-fields[' . $slug . ']" type="checkbox" ' . checked( isset( $this->options[ $slug ] ) && $this->options[ $slug ], true, false ) . ' /> ' . $label . '</label>';
		} // checkbox()
		
		
		/**
		 * Output a radio set
		 * 
		 * @access private
		 * @param string $slug
		 * @param array $fields
		 * @return bool
		 */
		public function radio( $slug, $fields ){
			
			$output = '';
			
			foreach ( $fields as $value => $label ){

				$output .= '<label><input name="wdo-plugin-settings-fields[' . $slug . ']" type="radio" value="' . $value .'" ';
				$output .= checked( ( isset( $this->options[ $slug ] ) && ($value == $this->options[ $slug ] ) ), true, false );				
				$output .= ' /> ' . $label . '</label><br />';
				
			}
			
			return $output;
			
		} // radio()
		
		 
		 
		 /** 
		  * Load the default settings
		  * 
		  * @access public
		  * @param bool $upgraded
		  * @return void
		  */
		 public function load_defaults(){
		 	
		 	$checkForOptions = get_option( 'wdo-plugin-settings-fields' ); 
		 	
		 	if( is_array( $checkForOptions ) && !WDO_UPGRADED ) return;
		 	
		 	$defaults = array( 
	 			'wdo_version'	=> $this->version,
	 			'inline_help' 	=> 'link',
	 			'use_helpers'	=> true,
	 			'hide_print'	=> true,
	 			'use_mobble'	=> true
	 		);
		 	
		 	if( is_array( $checkForOptions ) ){
		 		
		 		// Update the options table
		 		update_option( 'wdo-plugin-settings-fields', array_merge( $defaults, $checkForOptions ) );
		 		
		 	} else {
		 	
			 	// add the defaults to the options table
			 	add_option( 'wdo-plugin-settings-fields', $defaults );
			 	
		 	}
		 	
		 } // load_defaults()	
		
		
	} // class WDOSettings 
	

} // if !class_exists( 'WDOSettings' )