<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.iranimij.com
 * @since             1.0.0
 * @package           Automatic_Image_uploader
 *
 * @wordpress-plugin
 * Plugin Name:       automatic image uploader
 * Plugin URI:        http://www.iranimij.com
 * Description:       Upload your images automatically.
 * Version:           1.0.1
 * Author:            Iman Heydari
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       automatic-upload-images
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || die();

/**
 * Check If Automatic_Image_Uploader_Core Class exists.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
if ( ! class_exists( 'Automatic_Image_Uploader' ) ) {

	/**
	 * Automatic_Image_Uploader_Core class.
	 *
	 * @since NEXT
	 */
	class Automatic_Image_Uploader {

		/**
		 * Class instance.
		 *
		 * @since NEXT
		 * @var Automatic_Image_Uploader
		 */
		private static $instance = null;

		/**
		 * The plugin version number.
		 *
		 * @since NEXT
		 *
		 * @access private
		 * @var string
		 */
		private static $version;

		/**
		 * The plugin basename.
		 *
		 * @since NEXT
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_basename;

		/**
		 * The plugin name.
		 *
		 * @since NEXT
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_name;

		/**
		 * The plugin directory.
		 *
		 * @since NEXT
		 *
		 * @access private
		 * @var string
		 */
		public static $plugin_dir;

		/**
		 * The plugin URL.
		 *
		 * @since NEXT
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_url;

		/**
		 * The plugin assets URL.
		 *
		 * @since NEXT
		 * @access public
		 *
		 * @var string
		 */
		public static $plugin_assets_url;

		/**
		 * Database object.
		 *
		 * @since NEXT
		 * @access public
		 * @var database
		 */
		public $db;

		/**
		 * Get a class instance.
		 *
		 * @since NEXT
		 *
		 * @return Automatic_Image_Uploader Class
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Class constructor.
		 *
		 * @since NEXT
		 */
		public function __construct() {
			$this->define_constants();

			$this->load_files( [] );

			add_action( 'init', [ $this, 'init' ] );
			add_action( 'admin_init', [ $this, 'admin_init' ] );
			add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

			if ( ! $this->is_persian_kit_screen() ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
			add_action( 'admin_head', [ $this, 'remove_notice_for_persian_kit_pages' ] );
			add_filter( 'admin_body_class', [ $this, 'persian_kit_admin_body_class' ] );
		}

		/**
		 * Defines constants used by the plugin.
		 *
		 * @since NEXT
		 */
		protected function define_constants() {
			$plugin_data = get_file_data( __FILE__, array( 'Plugin Name', 'Version' ), 'aiu' );

			self::$plugin_basename   = plugin_basename( __FILE__ );
			self::$plugin_name       = array_shift( $plugin_data );
			self::$version           = array_shift( $plugin_data );
			self::$plugin_dir        = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$plugin_url        = trailingslashit( plugin_dir_url( __FILE__ ) );
			self::$plugin_assets_url = trailingslashit( self::$plugin_url . 'assets' );
		}

		/**
		 * Do some stuff on plugin activation.
		 *
		 * @since  NEXT
		 * @return void
		 */
		public function activation() {
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  NEXT
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Adding Automatic_Image_Uploader class to body.
		 *
		 * @param string $classes Automatic_Image_Uploader the Automatic_Image_Uploader class.
		 * @return string
		 */
		public function persian_kit_admin_body_class( $classes ) {
			return "{$classes} aiu";
		}

		/**
		 * Initialize admin.
		 *
		 * @since NEXT
		 */
		public function admin_init() {
			$this->load_files( [
				'class'
			] );

			new AIU_Image_Uploader();
		}

		/**
		 * Initialize.
		 *
		 * @since NEXT
		 */
		public function init() {
			$this->load_files( [] );
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since NEXT
		 */
		public function enqueue_admin_scripts() {
			wp_enqueue_script(
				'aiu',
				aiu()->plugin_url() . 'assets/dist/admin/admin.js',
				[ 'lodash', 'wp-element', 'wp-i18n', 'wp-util' ],
				aiu()->version(),
				true
			);

			wp_localize_script( 'aiu', 'aiu', [
				'nonce' => wp_create_nonce( 'aiu' ),
				'uploaderIsEnabled' => get_option( 'aiu_enable_uploader' ),
			] );

			wp_enqueue_style( 'aiu', aiu()->plugin_url() . 'assets/dist/admin/admin.css', [], self::version() );
		}

		/**
		 * Register admin menu.
		 *
		 * @since NEXT
		 * @SuppressWarnings(PHPMD.NPathComplexity)
		 */
		public function register_admin_menu() {
			add_submenu_page(
					'options-general.php',
					__( 'Automatic image uploader', 'aiu' ),
					__( 'AIU', 'aiu' ),
					'edit_theme_options',
					'aiu',
					[ $this, 'register_admin_menu_callback' ]
			);
		}

		/**
		 * Register admin menu callback.
		 *
		 * @since NEXT
		 */
		public function register_admin_menu_callback() {
			?>
			<div id="wrap" class="wrap">
				<!-- It's required for notices, otherwise WP adds the notices wherever it finds the first heading element. -->
				<h1></h1>
				<div id="aiu-root"></div>
			</div>
			<?php
		}

		/**
		 * Check if in aiu pages.
		 *
		 * @since NEXT
		 *
		 * @return boolean aiu screen.
		 */
		private function is_persian_kit_screen() {
			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

			return (
				is_admin() &&
				isset( $page ) &&
				strpos( $page, 'aiu' ) !== false
			);
		}

		/**
		 * Remove notices.
		 *
		 * @since NEXT
		 */
		public function remove_notice_for_persian_kit_pages() {
			?>
			<style>
				.notice {
					display: none;
				}
			</style>
			<?php
		}

		/**
		 * Loads specified PHP files from the plugin includes directory.
		 *
		 * @since NEXT
		 *
		 * @param array $file_names The names of the files to be loaded in the includes directory.
		 */
		public function load_files( $file_names = array() ) {

			foreach ( $file_names as $file_name ) {
				$path = self::plugin_dir() . 'includes/' . $file_name . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}
		}

		/**
		 * Returns the version number of the plugin.
		 *
		 * @since NEXT
		 *
		 * @return string
		 */
		public function version() {
			return self::$version;
		}

		/**
		 * Returns the plugin basename.
		 *
		 * @since NEXT
		 *
		 * @return string
		 */
		public function plugin_basename() {
			return self::$plugin_basename;
		}

		/**
		 * Returns the plugin name.
		 *
		 * @since NEXT
		 *
		 * @return string
		 */
		public function plugin_name() {
			return self::$plugin_name;
		}

		/**
		 * Returns the plugin directory.
		 *
		 * @since NEXT
		 *
		 * @return string
		 */
		public function plugin_dir() {
			return self::$plugin_dir;
		}

		/**
		 * Returns the plugin URL.
		 *
		 * @since NEXT
		 *
		 * @return string
		 */
		public function plugin_url() {
			return self::$plugin_url;
		}

		/**
		 * Returns the plugin assets URL.
		 *
		 * @since NEXT
		 *
		 * @return string
		 */
		public function plugin_assets_url() {
			return self::$plugin_assets_url;
		}
	}
}

if ( ! function_exists( 'aiu' ) ) {
	/**
	 * Initialize the aiu.
	 *
	 * @since NEXT
	 */
	function aiu() {
		return Automatic_Image_Uploader::get_instance();
	}
}

/**
 * Initialize the aiu application.
 *
 * @since NEXT
 */
aiu();
