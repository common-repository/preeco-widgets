<?php

class Preeco_Widgets_Content_Manager {

	protected $id;
	protected $cache_dir;
	protected $allowed_types = [ 'privacy-policy', 'information-duty' ];
	protected $is_caching_enabled = false;

	public function __construct( $id ) {
		$this->id                 = $id;
		$this->cache_dir          = WP_CONTENT_DIR . '/preeco-cache/';
		$this->is_caching_enabled = filter_var( esc_attr( get_option( 'preeco_widgets_caching_enabled' ) ), FILTER_VALIDATE_BOOLEAN );

		wp_mkdir_p( $this->cache_dir );
	}

	public function render() {
		if ( false === $this->is_caching_enabled ) {
			return $this->get_raw_from_database();
		}

		if ( $this->is_cached() ) {
			return file_get_contents( $this->cache_dir . $this->get_cached_filename() );
		}

		return $this->cache();
	}

	protected function is_cached() {
		return file_exists( $this->cache_dir . $this->get_cached_filename() );
	}

	public function cache() {
		$raw = $this->get_raw_from_database();

		if ( false === $this->is_caching_enabled ) {
			return $raw;
		}

		$pattern = '/data-type="([a-z\-]+)"\s+data-access-token="([a-zA-Z0-9]+)"\s+data-locale="([a-zA-Z\-]+)"[a-zA-Z\s\<\>]+data-preeco-loader\ssrc="([a-zA-Z0-9\.\-\/\:]+)"/';
		$result  = preg_match( $pattern, $raw, $matches );

		if ( false !== $result && count( $matches ) !== 5 ) {
			return $raw;
		}

		$type         = $matches[1];
		$access_token = $matches[2];
		$locale       = $matches[3];
		$src          = $matches[4];

		if ( ! in_array( $type, $this->allowed_types ) ) {
			return $raw;
		}

		$resultUrlParts = preg_match( "/^(.*:\/\/[A-Za-z0-9\-\.]+(:[0-9]+)?)\/(v[0-9]+)\/(.*)$/", $src, $urlParts );
		if ( false === $resultUrlParts ) {
			return $raw;
		}

		$base_url = $urlParts[1];
		$version  = $urlParts[3];
		$url      = $base_url . '/api/' . $version . '/';

		switch ( $type ) {
			case 'privacy-policy':
				$url .= 'privacy-policies/html';
				break;
			case 'information-duty':
				$url .= 'information-duties/html';
				break;
			default:
				return $raw;
		}

		$url .= '?access_token=' . $access_token . '&locale=' . $locale;

		$data = wp_remote_get( $url, [ 'headers' => [ 'Accept-Language' => $locale ] ] );
		if ( is_array( $data ) && ! empty( $data ) ) {
			if ( $data['response']['code'] >= 200 && $data['response']['code'] < 300 && ! empty( $data['body'] ) ) {
				file_put_contents( $this->cache_dir . $this->get_cached_filename(), trim( $data['body'] ) );

				return trim( $data['body'] );
			}
		}

		return $raw;
	}

	protected function get_cached_filename() {
		return 'preeco-widget-' . $this->id . '.html';
	}

	protected function get_raw_from_database() {
		$string = '';

		$args  = array(
			'post_type'   => 'preeco_widgets',
			'post_status' => 'publish',
			'p'           => $this->id
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$string .= get_the_content();
			endwhile;
		}

		wp_reset_postdata();

		return $string;
	}
}