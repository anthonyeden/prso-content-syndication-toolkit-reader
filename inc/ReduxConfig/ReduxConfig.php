<?php
/**
	!!!HOW TO SETUP!!!
	
	Replace PrsoPluginFrameworkOptionConfig with your plugin slug to create unique class name!!!
	
	*Also update the $text_domain arg for this unique plugin!!
	*Also update the $options_name arg for this unique plugin!!
	
**/


/**
	ReduxFramework Sample Config File
	For full documentation, please visit: https://github.com/ReduxFramework/ReduxFramework/wiki
**/

if ( !class_exists( "ReduxFramework" ) ) {
	return;
} 

if ( !class_exists( "PrsoSyndToolkitReaderOptions" ) ) {
	class PrsoSyndToolkitReaderOptions {

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;
		public $text_domain 	= PRSOSYNDTOOLKITREADER__DOMAIN;
		public $options_name 	= PRSOSYNDTOOLKITREADER__OPTIONS_NAME;

		public function __construct( ) {

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();
			
			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();
			
			if ( !isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}
			
			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);

			// If Redux is running as a plugin, this will remove the demo notice and links
			add_action( 'redux/plugin/hooks', array( $this, 'remove_demo' ) );
			
			// Function to test the compiler hook and demo CSS output.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2); 
			// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.

			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
			
			// Change the default value of a field after it's been set, but before it's been used
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

			// Dynamically add a section. Can be also used to modify sections/fields
			add_filter('redux/options/'.$this->args['opt_name'].'/sections', array( $this, 'dynamic_section' ) );

		}


		/**

			This is a test function that will let you see when the compiler hook occurs. 
			It only runs if a field	set with compiler=>true is changed.

		**/

		function compiler_action($options, $css) {
			echo "<h1>The compiler hook has run!";
			//print_r($options); //Option values
			
			// print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )
			/*
			// Demo of how to use the dynamic CSS and write your own static CSS file
		    $filename = dirname(__FILE__) . '/style' . '.css';
		    global $wp_filesystem;
		    if( empty( $wp_filesystem ) ) {
		        require_once( ABSPATH .'/wp-admin/includes/file.php' );
		        WP_Filesystem();
		    }

		    if( $wp_filesystem ) {
		        $wp_filesystem->put_contents(
		            $filename,
		            $css,
		            FS_CHMOD_FILE // predefined mode settings for WP files
		        );
		    }
			*/
		}



		/**
		 
		 	Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		 	Simply include this function in the child themes functions.php file.
		 
		 	NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		 	so you must use get_template_directory_uri() if you want to use any of the built in icons
		 
		 **/

		function dynamic_section($sections){
		    //$sections = array();
		    $sections[] = array(
		        'title' => __('Section via hook', $this->text_domain),
		        'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', $this->text_domain),
				'icon' => 'el-icon-paper-clip',
				    // Leave this as a blank section, no options just some intro text set above.
		        'fields' => array()
		    );

		    return $sections;
		}
		
		
		/**

			Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

		**/
		
		function change_arguments($args){
		    //$args['dev_mode'] = true;
		    
		    return $args;
		}
			
		
		/**

			Filter hook for filtering the default value of any given field. Very useful in development mode.

		**/

		function change_defaults($defaults){
		    $defaults['str_replace'] = "Testing filter hook!";
		    
		    return $defaults;
		}


		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {
			
			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if ( class_exists('ReduxFrameworkPlugin') ) {
				remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_meta_demo_mode_link'), null, 2 );
			}

			// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
			remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );	

		}


		public function setSections() {

			/**
			 	Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 **/


			// Background Patterns Reader
			$sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
			$sample_patterns_url  = ReduxFramework::$_url . '../sample/patterns/';
			$sample_patterns      = array();

			if ( is_dir( $sample_patterns_path ) ) :
				
			  if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
			  	$sample_patterns = array();

			    while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

			      if( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
			      	$name = explode(".", $sample_patterns_file);
			      	$name = str_replace('.'.end($name), '', $sample_patterns_file);
			      	$sample_patterns[] = array( 'alt'=>$name,'img' => $sample_patterns_url . $sample_patterns_file );
			      }
			    }
			  endif;
			endif;

			ob_start();

			$ct = wp_get_theme();
			$this->theme = $ct;
			$item_name = $this->theme->get('Name'); 
			$tags = $this->theme->Tags;
			$screenshot = $this->theme->get_screenshot();
			$class = $screenshot ? 'has-screenshot' : '';

			$customize_title = sprintf( __( 'Customize &#8220;%s&#8221;',$this->text_domain ), $this->theme->display('Name') );

			?>
			<div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
				<?php if ( $screenshot ) : ?>
					<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
					<a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr( $customize_title ); ?>">
						<img src="<?php echo esc_url( $screenshot ); ?>" alt="<?php esc_attr_e( 'Current theme preview' ); ?>" />
					</a>
					<?php endif; ?>
					<img class="hide-if-customize" src="<?php echo esc_url( $screenshot ); ?>" alt="<?php esc_attr_e( 'Current theme preview' ); ?>" />
				<?php endif; ?>

				<h4>
					<?php echo $this->theme->display('Name'); ?>
				</h4>

				<div>
					<ul class="theme-info">
						<li><?php printf( __('By %s',$this->text_domain), $this->theme->display('Author') ); ?></li>
						<li><?php printf( __('Version %s',$this->text_domain), $this->theme->display('Version') ); ?></li>
						<li><?php echo '<strong>'.__('Tags', $this->text_domain).':</strong> '; ?><?php printf( $this->theme->display('Tags') ); ?></li>
					</ul>
					<p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
					<?php if ( $this->theme->parent() ) {
						printf( ' <p class="howto">' . __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.' ) . '</p>',
							__( 'http://codex.wordpress.org/Child_Themes',$this->text_domain ),
							$this->theme->parent()->display( 'Name' ) );
					} ?>
					
				</div>

			</div>

			<?php
			$item_info = ob_get_contents();
			    
			ob_end_clean();

			$sampleHTML = '';
			if( file_exists( dirname(__FILE__).'/info-html.html' )) {
				/** @global WP_Filesystem_Direct $wp_filesystem  */
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH .'/wp-admin/includes/file.php');
					WP_Filesystem();
				}  		
				$sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__).'/info-html.html');
			}




			// ACTUAL DECLARATION OF SECTIONS
			$wp_users 		= get_users();
			$_user_array	= NULL;
			
			//Cache all users for select option 'post-author'
			foreach( $wp_users as $User ){
				$_user_array[ $User->ID ] = $User->user_nicename;
			}
			
			$this->sections[] = array(
				'title' => __('Account Settings', $this->text_domain),
				'desc' => __('Setup your Content Syndication Account here.', $this->text_domain),
				'icon' => 'el-icon-home',
				'fields' => array(
					array(
					    'id'       => 'api-url',
					    'type'     => 'text',
					    'title'    => __('API URL', $this->text_domain),
					    'subtitle' => __('The API URL for your account.', $this->text_domain),
					    'validate' => 'url',
					    'msg'      => __('Invalid URL', $this->text_domain),
					    'placeholder' => 'http://www.example.com'
					),
					array(
					    'id'          => 'api-password',
					    'type'        => 'password',
					    'username'    => true,
					    'title'       => 'API Account Details',
					    'placeholder' => array(
					        'username'   => __('Enter your Username', $this->text_domain),
					        'password'   => __('Enter your Password', $this->text_domain)
					    )
					),
					array(
					    'id'       => 'post-author',
					    'type'     => 'select',
					    'title'    => __('Post Author', $this->text_domain),
					    'placeholder' => __('Select a User', $this->text_domain),
					    'subtitle' => __('Select user you wish to set as the default author of ALL imported posts.', $this->text_domain),
					    'desc'     => __('You can change the author on an individual post via the post edit view.', $this->text_domain),
					    // Must provide key => value pairs for select options
					    'options'  => $_user_array
					),
					array(
					    'id'       => 'post-status',
					    'type'     => 'select',
					    'title'    => __('Post Status Override', $this->text_domain),
					    'placeholder' => __('Select a Status', $this->text_domain),
					    'subtitle' => __('Select post status for all syndicated content.', $this->text_domain),
					    'desc'     => __('Override the post status for all syndicated content.', $this->text_domain),
					    // Must provide key => value pairs for select options
					    'options'  => array(
					    	'auto' 		=> 	'Automatic, keep original status',
					    	'draft'		=>	'Draft',
					    	'private'	=>	'Private',
					    	'pending'	=>	'Pending',
					    	'publish'	=>	'Published'
					    )
					),
					array(
					    'id'       => 'post-seo-noindex',
					    'type'     => 'select',
					    'title'    => __('Yoast SEO Index', $this->text_domain),
					    'placeholder' => __('Select a Yoast SEO Index Setting', $this->text_domain),
					    'subtitle' => __('Select a Yoast SEO Index Setting for all syndicated content.', $this->text_domain),
					    'desc'     => __('Override the Yoast SEO Index Setting for all syndicated content.', $this->text_domain),
					    // Must provide key => value pairs for select options
					    'options'  => array(
					    	'0' 		=> 	'Site default',
					    	'2'		    =>	'Index',
					    	'1'	        =>	'No Index'
					    )
					)
				)
			);
			
			$this->sections[] = array(
				'title' => __('Tools', $this->text_domain),
				'desc' => __('Plugin specific tools.', $this->text_domain),
				'icon' => 'el-icon-cogs',
				'fields' => array(
					array(
						'id'=>"pull_content",
						'type' => 'callback',
						'title' => __('Pull Content', $this->text_domain), 
						'subtitle' => __('Manually pull new content from the syndication server.', $this->text_domain),
						'desc' => '',
						'callback' => 'redux_pull_content'
					),
					array(
						'id'=>"reset_index",
						'type' => 'callback',
						'title' => __('Reset Post Index', $this->text_domain), 
						'subtitle' => __('If you feel you are missing content, reset the post index. Then manually pull content from the syndication server.', $this->text_domain),
						'desc' => 'Only use this if you are sure there is missing content.',
						'callback' => 'redux_reset_index'
					)
				)
			);


			if(file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
			    $tabs['docs'] = array(
					'icon' => 'el-icon-book',
					    'title' => __('Documentation', $this->text_domain),
			        'content' => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
			    );
			}

		}	

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			$this->args['help_tabs'][] = array(
			    'id' => 'redux-opts-1',
			    'title' => __('Theme Information 1', $this->text_domain),
			    'content' => __('<p>This is the tab content, HTML is allowed.</p>', $this->text_domain)
			);

			$this->args['help_tabs'][] = array(
			    'id' => 'redux-opts-2',
			    'title' => __('Theme Information 2', $this->text_domain),
			    'content' => __('<p>This is the tab content, HTML is allowed.</p>', $this->text_domain)
			);

			// Set the help sidebar
			$this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', $this->text_domain);

		}


		/**
			
			All the possible arguments for Redux.
			For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

		 **/
		public function setArguments() {
			
			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
	            
	            // TYPICAL -> Change these values as you need/desire
				'opt_name'          	=> $this->options_name, // This is where your data is stored in the database and also becomes your global variable name.
				'display_name'			=> 'Content Syndication Toolkit Reader', // Name that appears at the top of your panel
				'display_version'		=> PRSOSYNDTOOLKITREADER__VERSION, // Version that appears at the top of your panel
				'menu_type'          	=> 'menu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'     	=> true, // Show the sections below the admin menu item or not
				'menu_title'			=> __( 'Content Syndication', $this->text_domain ),
	            'page'		 	 		=> __( 'Content Syndication', $this->text_domain ),
	            'google_api_key'   	 	=> '', // Must be defined to add google fonts to the typography module
	            'global_variable'    	=> '', // Set a different name for your global variable other than the opt_name
	            'dev_mode'           	=> false, // Show the time the page took to load, etc
	            'customizer'         	=> false, // Enable basic customizer support

	            // OPTIONAL -> Give you extra features
	            'page_priority'      	=> null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	            'page_type'   			=> 'submenu', // set to “menu” for a top level menu, or “submenu” to add below an existing item
	            'page_parent'        	=> 'options-general.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	            'page_permissions'   	=> 'manage_options', // Permissions needed to access the options panel.
	            'menu_icon'          	=> '', // Specify a custom URL to an icon
	            'last_tab'           	=> '', // Force your panel to always open to a specific tab (by id)
	            'page_icon'          	=> 'icon-themes', // Icon displayed in the admin panel next to your menu_title
	            'page_slug'          	=> $this->options_name.'_options', // Page slug used to denote the panel
	            'save_defaults'      	=> true, // On load save the defaults to DB before user clicks save or not
	            'default_show'       	=> false, // If true, shows the default value next to each field that is not the default value.
	            'default_mark'       	=> '', // What to print by the field's title if the value shown is default. Suggested: *


	            // CAREFUL -> These options are for advanced use only
	            'transient_time' 	 	=> 60 * MINUTE_IN_SECONDS,
	            'output'            	=> true, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
	            'output_tag'            	=> true, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
	            //'domain'             	=> 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
	            //'footer_credit'      	=> '', // Disable the footer credit of Redux. Please leave if you can help it.
	            

	            // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
	            'database'           	=> '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	            
	        
	            'show_import_export' 	=> false, // REMOVE
	            'system_info'        	=> false, // REMOVE
	            
	            'help_tabs'          	=> array(),
	            'help_sidebar'       	=> '', // __( '', $this->args['domain'] );            
				);


			// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.		
			$this->args['share_icons'][] = array(
			    'url' => 'https://github.com/ReduxFramework/ReduxFramework',
			    'title' => 'Visit us on GitHub', 
			    'icon' => 'el-icon-github'
			    // 'img' => '', // You can use icon OR img. IMG needs to be a full URL.
			);		
			$this->args['share_icons'][] = array(
			    'url' => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
			    'title' => 'Like us on Facebook', 
			    'icon' => 'el-icon-facebook'
			);
			$this->args['share_icons'][] = array(
			    'url' => 'http://twitter.com/reduxframework',
			    'title' => 'Follow us on Twitter', 
			    'icon' => 'el-icon-twitter'
			);
			$this->args['share_icons'][] = array(
			    'url' => 'http://www.linkedin.com/company/redux-framework',
			    'title' => 'Find us on LinkedIn', 
			    'icon' => 'el-icon-linkedin'
			);

			
	 
			// Panel Intro text -> before the form
			if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false ) {
				if (!empty($this->args['global_variable'])) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace("-", "_", $this->args['opt_name']);
				}
				$this->args['intro_text'] = NULL;
			} else {
				$this->args['intro_text'] = NULL;
			}

			// Add content after the form.
			$this->args['footer_text'] = NULL;

		}
	}
	new PrsoSyndToolkitReaderOptions();

}


/** 

	Custom function for the callback referenced above

 */
if ( !function_exists( 'redux_my_custom_field' ) ):
	function redux_my_custom_field($field, $value) {
	    print_r($field);
	    print_r($value);
	}
endif;

function redux_pull_content($field, $value) {
	
	?>
	<a class="button button-primary pcst-pull-content" rel="pull-content" href="javascript:void(0);"><?php _ex( 'Pull Content', 'text', PRSOSYNDTOOLKITREADER__DOMAIN ); ?></a>
	<span class="spinner pull-content" style="float:left;"></span>
	<span class="pcst-pull-error" style="display:none;clear:both;color:red;"></span>
	<?php
	
}

function redux_reset_index($field, $value) {
	
	?>
	<a class="button button-primary pcst-reset-index" rel="reset-index" href="javascript:void(0);"><?php _ex( 'Reset Index', 'text', PRSOSYNDTOOLKITREADER__DOMAIN ); ?></a>
	<span class="spinner reset-index" style="float:left;"></span>
	<span class="pcst-reset-index-error" style="display:none;clear:both;color:red;"></span>
	<?php
	
}

/**
 
	Custom function for the callback validation referenced above

**/
if ( !function_exists( 'redux_validate_callback_function' ) ):
	function redux_validate_callback_function($field, $value, $existing_value) {
	    $error = false;
	    $value =  'just testing';
	    /*
	    do your validation
	    
	    if(something) {
	        $value = $value;
	    } elseif(something else) {
	        $error = true;
	        $value = $existing_value;
	        $field['msg'] = 'your custom error message';
	    }
	    */
	    
	    $return['value'] = $value;
	    if($error == true) {
	        $return['error'] = $field;
	    }
	    return $return;
	}
endif;
