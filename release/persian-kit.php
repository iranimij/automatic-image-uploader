<?php
/**
 * Plugin Name: Persian_Kit Core.
 * Plugin URI: https://iranimij.com
 * Description: Persian_Kit core functionalities.
 * Version: 1.0.0
 * Author: Iman Heydari
 * Author URI: https://iranimij.com
 * Text Domain: aiu
 * License: GPL2
 *
 * @package Persian_Kit
 */

defined( 'ABSPATH' ) || die();

/**
 * Check If Persian_Kit_Core Class exists.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
if ( ! class_exists( 'Persian_Kit' ) ) {

	/**
	 * Persian_Kit_Core class.
	 *
	 * @since NEXT
	 */
	class Persian_Kit {

		/**
		 * Class instance.
		 *
		 * @since NEXT
		 * @var Persian_Kit
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
		 * @var Database
		 */
		public $db;

		/**
		 * Get a class instance.
		 *
		 * @since NEXT
		 *
		 * @return Persian_Kit Class
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
		 * Adding Persian_Kit class to body.
		 *
		 * @param string $classes Persian_Kit the Persian_Kit class.
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
			$this->load_files( [] );
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

		}

		/**
		 * Register admin menu.
		 *
		 * @since NEXT
		 * @SuppressWarnings(PHPMD.NPathComplexity)
		 */
		public function register_admin_menu() {
			$menu_icon    = 'dashicons-align-right';
			$menu_name    = __( 'Persian_Kit', 'aiu' );
			$initial_page = 'aiu';
			$submenu      = [
//				'aiu-analytic' => __( 'Analytics', 'aiu' ),
//				'aiu-funnel' => __( 'Funnels', 'aiu' ),
//				'aiu-dynamic-discount' => __( 'Dynamic Discounts', 'aiu' ),
//				'aiu-personalised-coupon' => __( 'Personalised Coupons', 'aiu' ),
//				'aiu-smart-alert' => __( 'Smart Alerts', 'aiu' ),
//				'aiu-settings' => __( 'Settings', 'aiu' ),
//				'aiu-component' => __( 'Components', 'aiu' ),
			];

			add_menu_page(
				$menu_name,
				$menu_name,
				'manage_options',
				$initial_page,
				[ $this, 'register_admin_menu_callback' ],
				$menu_icon,
				'3.5'
			);

			foreach ( $submenu as $slug => $title ) {
				add_submenu_page(
					$initial_page,
					"{$menu_name} {$title}",
					$title,
					'edit_theme_options',
					$slug,
					[ $this, 'register_admin_menu_callback' ]
				);
			}
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
	function persian_kit() {
		return Persian_Kit::get_instance();
	}
}

/**
 * Initialize the aiu application.
 *
 * @since NEXT
 */
persian_kit();
