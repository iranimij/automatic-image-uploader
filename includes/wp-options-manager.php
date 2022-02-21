<?php

if ( ! class_exists( 'Wp_Options_Manager' ) ) {

	/**
	 * Class Wp_Options_Manager
	 *
	 * @since 1.0.0
	 */
	class Wp_Options_Manager {

		/**
		 * The key of option in options table.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $key;

		/**
		 * Options data.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private static $options;

		/**
		 * Class instance.
		 *
		 * @since 1.0.0
		 * @var Wp_Options_Manager
		 */
		private static $instance = null;

		/**
		 * Get a class instance.
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Options key.
		 */
		public static function get_instance( $plugin_slug ) {
			if ( null === self::$instance ) {
				self::$instance = new self( $plugin_slug );
			}

			return self::$instance;
		}

		/**
		 * Wp_Background_Process constructor.
		 *
		 * @since 1.0.0
		 * @param string $plugin_slug The plugin slug.
		 */
		public function __construct( $plugin_slug ) {
			$this->key = $plugin_slug;

			$this->set();
		}

		/**
		 * Gets all options data related to the key.
		 *
		 * @since 1.0.0
		 * @param string $plugin_slug The plugin slug.
		 */
		private function set() {
			if ( ! empty( self::$options ) && is_array( self::$options ) ) {
				return false;
			}

			self::$options = get_option( $this->key );

			if ( empty( self::$options ) ) {
				self::$options = [ 'created_at' => time() ];
			}

			if ( ! is_array( self::$options ) ) {
				return new WP_Error( 'something_went_wrong' );
			}
		}

		/**
		 * Updates an item.
		 *
		 * @since 1.0.0
		 * @param string $key The key.
		 * @param string $value The value.
		 */
		public function update( $key, $value ) {
			self::$options[ $key ] = $value;

			return $this;
		}

		/**
		 * Deletes an item.
		 *
		 * @since 1.0.0
		 * @param string $key The key pf the item.
		 */
		public function delete( $key ) {
			if ( ! empty( self::$options[ $key ] ) ) {
				unset( self::$options[ $key ] );
			}
		}

		/**
		 * Selects an item.
		 *
		 * @since 1.0.0
		 * @param string $key The key pf the item.
		 */
		public function select( $key ) {
			if ( ! empty( self::$options[ $key ] ) ) {
				return self::$options[ $key ];
			}

			return false;
		}

		/**
		 * Delete an item.
		 *
		 * @since 1.0.0
		 * @param string $key The key pf the item.
		 */
		public function get() {
			return self::$options;
		}

		/**
		 * Saves an item.
		 *
		 * @since 1.0.0
		 * @param string $key The key.
		 * @param string $value The value.
		 */
		public function save() {
			update_option( $this->key, self::$options );
		}
	}
}

/**
 * Helper function
 *
 * @since 1.0.0
 * @param $plugin_slug
 * @return Wp_Options_Manager|null
 */
function wp_options_manager( $plugin_slug ) {
	return Wp_Options_Manager::get_instance( $plugin_slug );
}
