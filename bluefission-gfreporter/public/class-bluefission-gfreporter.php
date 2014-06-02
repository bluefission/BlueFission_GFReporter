<?php
/**
 * BlueFission GFReporter.
 *
 * @package   BlueFission_GFReporter
 * @author    Devon Scott <dscott@bluefission.com.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2014 Devon Scott, BlueFission.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-bluefission-gfreporter-admin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package BlueFission_GFReporter
 * @author  Your Name <email@example.com>
 */
class BlueFission_GFReporter {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * TODO - Rename "bluefission-gfreporter" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'bluefission-gfreporter';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'TODO', array( $this, 'action_method_name' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Name of options settings variable
	 * 
	 * @since	   1.0.0
	 * 
	 * @var      array
	 */	 	 	 	 	 	
	protected $option_var = BFGFR_OPTIONS_VAR;
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			
			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here
		// TODO: Set scheduled task hook and create function
		
		if ( ! wp_next_scheduled( 'scheduled_maintenance' ) ) {
			wp_schedule_event( time(), MAINTENANCE_INTERVAL, 'scheduled_maintenance' );
		}
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	
		wp_clear_scheduled_hook( 'scheduled_maintenance' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

	public function scheduled_report() {
		// Is it time to send this?
		$options = get_option($this->option_var);
		$day = isset( $options['date'] ) ? $options['date'] : '';
		if ( date('j') != $day ) {
			return 0;	
		} else {
			$this->report();

			if ( ! wp_next_scheduled( 'scheduled_maintenance' ) ) {
				wp_schedule_event( time(), MAINTENANCE_INTERVAL, 'scheduled_maintenance' );
			}
		}
	}

	public function send_report() {
		$this->report();
	}

	public function send_report_ajax() {
		$plugin = self::get_instance();
		$plugin->report();

	    echo "true";

		die(); // this is required to return a proper result
	}

	private function report() {
		if(!class_exists('GFForms')){
			wp_mail(get_bloginfo( 'admin_email' ) , 'Error from your site', 'Gravity Forms not installed!!' );
			return 0;
		}
		
		$options = get_option($this->option_var);
		
		if ( !isset($options['form']) || !isset($options['fields']) ) {
			return 0;
		}
		
		$form_id = $options['form'];
		$email = isset( $options['email'] ) ? $options['email'] : '';
		$day = isset( $options['date'] ) ? $options['date'] : '';
		$message = isset( $options['message'] ) ? $options['message'] : '';
		$last = isset($options['last_run']) ? $options['last_run'] : date('Y-m-d', strtotime('last year'));
		
		$today = date('Y-m-d');
		
		$_POST["export_field"] = $options['fields'];
		$_POST["export_date_start"] = $last;
		$_POST["export_date_end"] = $today;
				
		$options['last_run'] = $today;
		
		$plugin_dir = $path = plugin_dir_path( __FILE__ );
		$filename = $plugin_dir . 'reports/WebsiteFormsReport-' . $today . '.csv';
		 
		update_option( $this->option_var, $options );
			
		// TODO: envoke form export and mail it off
		require_once(GFCommon::get_base_path() . "/export.php");
		
		$form = RGFormsModel::get_form_meta($form_id);
		
		ob_start();
		GFExport::start_export($form);
		$csv = ob_get_contents();
		ob_end_clean();


		if ( $csv ) {
			file_put_contents($filename, $csv);
	
			$attachments = array( $filename );
	   		$headers = 'From: Form Report <'.$email.'>' . "\r\n";
	
			wp_mail($email , __('Your Recent Form Submissions', $this->plugin_slug), $message, $headers, $attachments );
		}
		else
		{
			wp_mail($email , __('Your Recent Form Submissions', $this->plugin_slug), __('No entries to recieve for this time period.'), $headers );	
		}
	}
}
