<?php

defined( 'ABSPATH' ) || die();

class AIU_Image_Uploader {

	const ALLOWED_FILE_EXTENTIONS = [
		'jpg',
		'jpeg',
		'jpe',
		'png',
		'gif',
		'bmp',
		'tiff',
		'tif',
	];

	public static $instance = null;

	public function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * AIU_Image_Uploader constructor.
	 */
	public function __construct() {
		add_action( 'save_post', [ $this, 'save_post_images' ], 10, 3 );

		add_action( 'wp_ajax_aiu_save_settings', [ $this, 'save_settings' ] );
	}

	/**
	 * Saving post images.
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	public function save_post_images( $post_id, $post, $update ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( wp_options_manager()->select( 'aiu_enable_uploader' ) === 'false' ) {
			return;
		}

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post', [ $this, 'save_post_images' ] );

		$post_content = $post->post_content;
		$urls = wp_extract_urls( $post_content );

		foreach ( $urls as $key => $url ) {
			$file_type = wp_check_filetype($url);
			$file_ext = $file_type['ext'];
			$host_url = parse_url($url)['host'];

			if ( in_array( $file_ext, self::ALLOWED_FILE_EXTENTIONS, true ) ) {
				if ( empty( strpos( site_url(), $host_url) ) ) {
					$attachment_id = media_sideload_image( $url, $post_id, null, 'id' );

					if ( 0 === $key && wp_options_manager()->select( 'set_first_image_as_thumbnail' ) === 'true' ) {
						set_post_thumbnail( $post_id, $attachment_id );
					}

					$post_content = str_replace( $url, wp_get_attachment_url( $attachment_id ), $post_content );
				}
			}
		}

		$args = array(
			'ID'           => $post_id,
			'post_content' => $post_content,
		);

		wp_update_post( $args );
	}

	public function save_settings() {
		wp_verify_nonce( 'aiu','nonce' );

		$enable_uploader = filter_input( INPUT_POST, 'enable_uploader', FILTER_SANITIZE_STRING );
		$first_image_as_thumbnail = filter_input( INPUT_POST, 'first_image_is_thumbnail', FILTER_SANITIZE_STRING );

		wp_options_manager()->update( 'aiu_enable_uploader', $enable_uploader )->update( 'set_first_image_as_thumbnail', $first_image_as_thumbnail )->save();

		wp_send_json_success( __( 'The data has been saved.', 'aiu' ) );
	}
}
