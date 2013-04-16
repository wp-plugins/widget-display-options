<?php if( !class_exists( 'WDOCallbacks' ) ) {
	
	/**
	 * Defines the callback input field properties & methods for handling non WP conditionals
	 *
	 * @package Widget Display Options Lite
	 * @author Randall Runnels <randy@dojodigital.com>
	 * @version 1.0
	 * @since 1.0
	 */
	class WDOCallbacks {
		
		/**
		 * The Callbacks array
		 * @access private
		 * @var bool
		 */
		private $callbacks = array();
			
		/**
		 * PHP 5 Constructor. Builds an array of callback data.
		 * 
		 * @access public
		 * @param bool $mobble True if the mobble plugin is installed
		 * @return void
		 */		
		public function __construct( $mobble = false ){
			
			$this->callbacks['Common']['is_front_page'] = array(
				'callback'	=> 'is_front_page',
				'label'		=> __( 'Front Page', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is the Main Page (or Home Page) of the site.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_front_page',
				'params'	=> false
			);
			
			$this->callbacks['Common']['is_home'] = array(
				'callback'	=> 'is_home',
				'label'		=> __( 'Blog Index', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is the Blog Posts Index Page.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_home',
				'params'	=> false
			);
			
			$this->callbacks['Common']['is_page'] = array(
				'callback'	=> 'is_page',
				'label'		=> __( 'Page', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a singular Page (has a post type of "page").', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_page',
				'params'	=> __( 'Page ID, Page Title or Page Slug (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Common']['is_single'] = array(
				'callback'		=> 'is_single',
				'label'			=> __( 'Single', 'wdo_plugin' ),
				'desc'			=> __( 'Checks if the current page being displayed is singular (as opposed to an Archive).', 'wdo_plugin' ),
				'more'			=> 'http://codex.wordpress.org/Function_Reference/is_single',
				'params'		=> __( 'Post ID, Post Title or Post Slug (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Common']['is_singular'] = array(
				'callback'	=> 'is_singular',
				'label'		=> __( 'Singular Post Type', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is singular and has a given Post Type(s). If left blank this will return true for any post type if the content is singular.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_singular',
				'params'	=> __( 'Post Type (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Common']['is_post_type'] = array(
				'callback'	=> array( get_class( $this ), 'is_post_type' ),
				'label'		=> __( 'Post Type', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed has a given Post Type(s). Works for archives or singular views. If left blank this will always return false!', 'wdo_plugin' ),
				'more'		=> false,
				'params'	=> __( 'Post Type (required, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Required, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Common']['is_search'] = array(
				'callback'	=> 'is_search',
				'label'		=> __( 'Search Results', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a Search Results page.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_search',
				'params'	=> false
			);
			
			$this->callbacks['Common']['is_page_template'] = array(
				'callback'	=> 'is_page_template',
				'label'		=> __( 'Page Template', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed uses a given Page Template. If left blank this will return true if the page uses any Page Template.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_page_template',
				'params'	=> __( 'Full template filename with ext (optional)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional Filename with ext', 'wdo_plugin' ),
				'accepts'		=> 'string'
			);
			
			$this->callbacks['Common']['is_sticky'] = array(
				'callback'	=> 'is_sticky',
				'label'		=> __( 'Sticky Post', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a Sticky Post.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_sticky',
				'params'	=> __( 'The post ID (optional)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional Post ID', 'wdo_plugin' ),
				'accepts'		=> 'string'
			);
			
			$this->callbacks['Common']['is_attachment'] = array(
				'callback'	=> 'is_attachment',
				'label'		=> __( 'Attachment', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is an Attachment.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_attachment',
				'params'	=> false
			);
			
			
			$this->callbacks['Common']['is_404'] = array(
				'callback'	=> 'is_404',
				'label'		=> __( '404', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if a 404 error is being displayed.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_404',
				'params'	=> false
			);
			
			$this->callbacks['Archive']['is_archive'] = array(
				'callback'	=> 'is_archive',
				'label'		=> __( 'Archive (common)', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a common Archive (Category, Tag, Author or Date).', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_archive',
				'params'	=> false
			);
			
			$this->callbacks['Archive']['is_date'] = array(
				'callback'	=> 'is_date',
				'label'		=> __( 'Date Archive', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a Date Archive.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_date',
				'params'	=> false
			);
			
			$this->callbacks['Archive']['is_tag'] = array(
				'callback'	=> 'is_tag',
				'label'		=> __( 'Tag Archive', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a Tag Archive. If left blank this will return true for any Tag Archive.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_tag',
				'params'	=> __( 'Tag slug (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Archive']['is_category'] = array(
				'callback'	=> 'is_category',
				'label'		=> __( 'Category Archive', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a Category Archive. If left blank this will return true for any Category Archive.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_category',
				'params'	=> __( 'Category ID, Category Title, Category Slug (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Archive']['is_tax'] = array(
				'callback'	=> 'is_tax',
				'label'		=> __( 'Taxonomy Archive', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is a Taxonomy Archive. If left blank this will return true for any Taxonomy Archive <strong>except Category and Tag archives</strong>.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_tax',
				'params'	=> __( 'Taxonomy slug or slugs (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Archive']['is_post_type_archive'] = array(
				'callback'	=> 'is_post_type_archive',
				'label'		=> __( 'Post Type Archive', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is an Archive of a given Post Type(s). If left blank this will return true for any Post Type Archive.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_post_type_archive',
				'params'	=> __( 'Post Type (optional, comma separated)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional, comma separated', 'wdo_plugin' ),
				'accepts'		=> 'array'
			);
			
			$this->callbacks['Archive']['is_author'] = array(
				'callback'	=> 'is_author',
				'label'		=> __( 'Author Archive', 'wdo_plugin' ),
				'desc'		=> __( 'Checks if the current page being displayed is an Author Archive. If left blank this will return true for any Author Archive.', 'wdo_plugin' ),
				'more'		=> 'http://codex.wordpress.org/Function_Reference/is_author',
				'params'	=> __( 'Author ID or Author Nickname (optional)', 'wdo_plugin' ),
				'placeholder'	=> __( 'Optional Author ID or Nickname', 'wdo_plugin' ),
				'accepts'		=> 'string'
			);
			
			
			// Load AJAX methods
			if( is_admin() ){
				add_action( 'wp_ajax_wdo_input_helper', array( $this, 'wdo_input_helper' ) );
			}
			
		} // __construct()
		
		
		/**
		 * Returns a multi-dimensional array of WDOConditional instances
		 * 
		 * @access public
		 * @param bool $useTooltips
		 * @param bool $useHelpers
		 * @return array
		 */
		public function get_conditionals( $useTooltips, $useHelpLinks ){
			
			$conditionals = array();
			
			foreach( $this->callbacks as $section => $callback ){
				$conditionals[ $section ] = array();
				foreach( $callback as $slug => $data ){
					$data['use_tooltips'] =  $useTooltips;
					$data['use_help_links'] =  $useHelpLinks;
					$conditionals[ $section ][ $slug ] = new WDOConditional( $slug, $data);
				}
			}
			
			return $conditionals;
			
		} // get_conditionals()
		
		
		/** THE CUSTOM CALLBACKS **/
		
		/**
		 * Determine if current page is of a given Post Type(s)
		 * 
		 * @access public
		 * @param mixed $postTypes
		 * @return bool
		 */
		public function is_post_type( $postTypes = array() ){
			
			if( empty( $postTypes ) ) return false;
			
			foreach( $postTypes as $postType ){
				if( $postType == get_post_type() ) return true;
			}
			
			return false;
		    
		} // is_post_type()
		
		
		
		/** UTILITY METHODS **/
		
		/**
		 * Limits a given string to a given length of characters
		 * 
		 * @access private
		 * @param string $str
		 * @param int $len
		 * @return string
		 */
		private function max_length( $str, $len ){
	
			if( strlen( $str ) < ( $len + 1 ) ){
				$newStr = $str;
			} else {
				$newStr = substr( $str, 0, $len ) . '&hellip;';
			}
			
			return $newStr;
		
		} // max_length()
		
		
	} // class WDOCallbacks 
	
// if class_exists( 'WDOCallbacks' )
} 