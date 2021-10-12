<?php

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

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post', [ $this, 'save_post_images' ] );

		$post_content = $post->post_content;
		$urls = wp_extract_urls( $post_content );

		foreach ( $urls as $url ) {
			$file_type = wp_check_filetype($url);
			$file_ext = $file_type['ext'];
			$host_url = parse_url($url)['host'];

			if ( in_array( $file_ext, self::ALLOWED_FILE_EXTENTIONS, true ) ) {
				if ( empty( strpos( site_url(), $host_url) ) ) {
					$new_url = media_sideload_image( $url, $post_id, null, 'src' );

					$post_content = str_replace( $url, $new_url, $post_content );
				}
			}
		}

		$args = array(
			'ID'           => $post_id,
			'post_content' => $post_content,
		);

		wp_update_post( $args );
	}
}
