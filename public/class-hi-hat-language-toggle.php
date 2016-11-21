<?php
/**
 * Hi-hat Language Toggle
 *
 * @package   Hi_Hat_Language_Toggle
 * @author    Mike Turner <turner.mike@gmail.com>
 * @license   GPL-2.0+
 * @link      http://hi-hatconsulting.com
 * @copyright 2016 Hi-hat Consulting
 */


/**
 * @package Hi_Hat_Language_Toggle
 * @author  Your Name <email@example.com>
 */
class Hi_Hat_Language_Toggle {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
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
	protected $plugin_slug = 'hi-hat-language-toggle';

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

		// load plugin text domain
		add_action('init', array( $this, 'initialize'));

		// // enqueue scripts and styles
		// add_action('admin_enqueue_scripts', array( $this, 'enqueue_styles'));
		// add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts'));

		// // add the 'download' metabox
		// add_action('add_meta_boxes', array( $this, 'hihat_add_meta_boxes'));

		// // save post action
		// add_action('save_post', array($this, 'hihat_save_meta_boxes'));

		// shortcodes
		add_shortcode('hihat_language_toggle', array( $this, 'hihat_language_toggle_handler'));

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
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

		if ( function_exists('is_multisite') && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

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

		if ( function_exists('is_multisite') && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

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

		//debug
		ob_start();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function initialize() {

		$domain = $this->plugin_slug;
		$locale = apply_filters('plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/');

		// register_post_type('related_download',
		// array(
		//   'labels' => array(
		//     'name' => __('Related Downloads'),
		//     'singular_name' => __('Related Download'),
		//   ),
		//   'public' => true,
		//   'has_archive' => true,
		//   // 'supports' => array('title', 'thumbnail')
		//   'supports' => array('title')
		// )
		// );

		// register_taxonomy(
		// 	'related_download_tag',
		// 	// array('related_download', 'post', 'page'),
		// 	get_post_types(),
		// 	array(
		// 		'label' => __('Related Download Tags'),
		// 		'rewrite' => array('slug' => 'related_download_tag'),
		// 	)
		// );

	}


    public function hihat_language_toggle_handler($attributes) {

        //get optional attributes and assign default values if not present
        extract( shortcode_atts( array(
            'test_var' => false
        ), $attributes ));

        return self::output_view($test_var, NULL, NULL);


    }

    public static function output_view($test_var=false, $instance, $args){

		if(isset($args)) extract($args);

		$output = '';
		$langs = icl_get_languages('skip_missing=0&orderby=KEY&order=DIR&link_empty_to=/');

		// shortcode parameter (not in use, here for referrence if needed)
		if($test_var == NULL){
			$test_var = $instance['test_var'];
		}

		//loop through each langauge
		foreach($langs as $lang){
			if(! $lang['active']){
				// echo "<pre>";
				// var_dump($lang);
				// echo "</pre>";
				//it's not active, generate a link
				$output .= '<a href="' . $lang['url'] . '" class="language-toggle">' . $lang['native_name'] . '</a>';
			}
		}

		return $output;

    }


}
