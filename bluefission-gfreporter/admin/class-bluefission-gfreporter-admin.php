<?php
/**
 * Plugin Name.
 *
 * @package   BlueFission_GFReporter_Admin
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2014 Devon Scott, BlueFission.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-bluefission-gfreporter.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package BlueFission_GFReporter_Admin
 * @author  Devon Scott <dscott@bluefission.com>
 */
class BlueFission_GFReporter_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	/**
	 * Name of options settings variable
	 * 
	 * @since	   1.0.0
	 * 
	 * @var      array
	 */	 	 	 	 	 	
	protected $option_var = BFGFR_OPTIONS_VAR;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * TODO:
		 *
		 * - Rename "BlueFission_GFReporter" to the name of your initial plugin class
		 *
		 */
		$plugin = BlueFission_GFReporter::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		
		// Initiate and register settings
		add_action('admin_init', array($this, 'plugin_admin_init') );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		
		if(!class_exists('GFForms')){
			GFFroms::register_scripts();
		}

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'TODO', array( $this, 'action_method_name' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * TODO:
	 *
	 * - Rename "BlueFission_GFReporter" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), BlueFission_GFReporter::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * TODO:
	 *
	 * - Rename "BlueFission_GFReporter" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), BlueFission_GFReporter::VERSION );
		}		
		$scripts = array(
			'gform_form_admin',
			'gform_field_filter',
			'sack'
		);
		
		foreach($scripts as $script){
			wp_enqueue_script($script);
		}             

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'BlueFission Gravity Forms Reporter', $this->plugin_slug ),
			__( 'Report Scheduling', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		if ( current_user_can( 'manage_options' ) )
		{
			include_once( 'views/admin.php' );
		}
		else
		{
			echo __("Please log in as an Administrator to access these options", $this->plugin_slug);
		}
	}
	
	public function plugin_admin_init() {
		register_setting( $this->plugin_slug . '-options', $this->option_var, array($this, 'plugin_admin_validate') );
		add_settings_section($this->plugin_slug . '-main', 'Scheduling Settings', array($this, 'plugin_admin_section_text'), $this->plugin_slug);
		add_settings_field('plugin_admin_last_run', '', array($this, 'plugin_admin_field_0'), $this->plugin_slug, $this->plugin_slug . '-main');
		add_settings_field('plugin_admin_date', 'Day of Month to Send Reports', array($this, 'plugin_admin_field_1'), $this->plugin_slug, $this->plugin_slug . '-main');
		add_settings_field('plugin_admin_email', 'Email of Recipient', array($this, 'plugin_admin_field_2'), $this->plugin_slug, $this->plugin_slug . '-main');
		add_settings_field('plugin_admin_message', 'Additional Message', array($this, 'plugin_admin_field_3'), $this->plugin_slug, $this->plugin_slug . '-main');
		add_settings_field('plugin_admin_form', 'Gravity Form to Report', array($this, 'plugin_admin_field_4'), $this->plugin_slug, $this->plugin_slug . '-main');
		add_settings_field('plugin_admin_fields', 'Choose Form Fields', array($this, 'plugin_admin_field_5'), $this->plugin_slug, $this->plugin_slug . '-main');
	}
	
	public function plugin_admin_section_text() {
		// Pretty much do nothing
	}
	
	public function plugin_admin_field_0() {
		$options = get_option($this->option_var);
		$last = isset($options['last_run']) && $options['last_run'] != '' ? $options['last_run'] : date('Y-m-d', strtotime('two years ago'));;
		
		echo "<input id='plugin_admin_last_run' name='" . $this->option_var . "[last_run]' type='hidden' value='{$last}' />";	
	}
	public function plugin_admin_field_1() {
		$options = get_option($this->option_var);
		$date = isset($options['date']) && $options['date'] != '' ? $options['date'] : '1';
		
		echo "<input id='plugin_admin_date' name='" . $this->option_var . "[date]' size='2' style='width:30px;' type='text' value='{$date}' />";	
	}
	public function plugin_admin_field_2() {
		$options = get_option($this->option_var);
		$email = isset($options['email']) && $options['email'] != '' ? $options['email'] : get_bloginfo( 'admin_email' );
		
		echo "<input id='plugin_admin_email' name='" . $this->option_var . "[email]' size='90' style='width:250px;' type='text' value='{$email}' />";
	}
	public function plugin_admin_field_3() {
		$options = get_option($this->option_var);
		$message = isset($options['message']) && $options['message'] != '' ? $options['message'] : __('Attached are your recent form submissions from your website', $this->plugin_slug);
		
		echo "<textarea id='plugin_admin_message' name='" . $this->option_var . "[message]' size='90' style='width:250px;'>{$message}</textarea>";
	}
	public function plugin_admin_field_4() {
		$options = get_option($this->option_var);
		if(!class_exists('GFForms')){
			echo "<input id='plugin_admin_form' name='" . $this->option_var . "[form]' size='90' style='width:250px;' type='text' value='{$options['form']}' />";
			return 0;
		}
		//
		?>
		<select id="plugin_admin_form"  style="width:250px;" name="<?php echo $this->option_var; ?>[form]" onchange="SelectExportForm(jQuery(this).val());">
              <option value=""><?php _e("Select a form", "gravityforms"); ?></option>
              <?php
              $forms = RGFormsModel::get_forms(null, "title");
              foreach($forms as $form){
                  ?>
                  <option <?php echo ($options['form'] == absint($form->id) ? 'selected="selected"' : '' ) ?> value="<?php echo absint($form->id) ?>"><?php echo esc_html($form->title) ?></option>
                  <?php
              }
              ?>
          </select>
          <?php
	}
	
	public function plugin_admin_field_5() {
		$options = get_option($this->option_var);
		$list = '';
		if ( is_array($options['fields']) ) {
			echo implode( ', ', $options['fields'] );
			foreach ( $options['fields'] as $a ) {
				$list .= "<li style='display:none;'><input type='hidden' name='" . $this->option_var . "[fields][]' value='{$a}' /></li>\n";
			}
		} else {
			$list = "<li style='display:none;'><input type='hidden' name='" . $this->option_var . "[fields][]' value='{$options['fields']}' /></li>";
		}
          echo '<ul id="export_field_list">'; 
		echo $list;        
          echo "</ul>";
	}
	
	// validate our options
	public function plugin_admin_validate($input) {
		
		//$newinput['text_string'] = trim($input['date']);
		
		if( !is_numeric($input['date']) || $input['date'] > '31' ) {
			$input['date'] = 1;
		}
		if( !is_email( $input['email'] ) ) {
			$input['email'] = get_bloginfo( 'admin_email' );
		}
		if( $input['form'] == '' ) {
			$input['form'] = '';
		}
		return $input;
	}



	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}
