<?php

class EddTwitterCards {

	/**
	 * Constructor. Register hooks & filters.
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'generate_twitter_card_tags' ) );
	}

	/**
	 * Generate twitter card tags.
	 *
	 * If the current page is a download, then construct an array of values for this download, and
	 * output the data onto the page.
	 */
	public function generate_twitter_card_tags() {

		$object = get_queried_object();

		// Do nothing unless this is a download.
		if ( ! $object instanceof WP_Post || 'download' != $object->post_type ) {
			return;
		}

		// Populate the basic data.
		$meta = array(
			'twitter:card'        => 'product',
			'twitter:site'        => get_bloginfo( 'name', 'raw' ),
			'twitter:title'       => $object->post_title,
			'twitter:description' => substr( $this->excerpt_by_id( $object->ID, 5 ), 0, 200 ),
			'twitter:label1'      => 'Price',
			'twitter:data1'       => $this->get_prices( $object ),
			'twitter:label2'      => 'Category',
			'twitter:data2'       => implode(
				', ',
				wp_get_object_terms(
					array( $object->ID ),
					array( 'download_category' ),
					array( 'fields' => 'names' )
				)
			),
		);

		// Add featured image if it exists.
		$image_src = $this->get_post_thumbnail_src( $object );
		if ( ! empty( $image_src ) ) {
			$meta['twitter:image'] = $image_src;
		}

		// Output the meta. Give other plugins an opportunity to filter the output.
		$this->do_meta(
			apply_filters(
				'edd_twitter_cards_meta',
				$meta,
				$object
			)
		);
	}

	/**
	 * Get the src for the featured image if it exists.
	 * @param  WP_Post  $object  The download post object.
	 * @return string            The download's featured image src, or empty string.
	 */
	private function get_post_thumbnail_src( $object ) {
		$image_id = get_post_thumbnail_id( $object->ID );
		if ( ! $image_id ) {
			return '';
		}
		$image_url = wp_get_attachment_image_src( $image_id, 'full' );
		return $image_url[0];
	}

	/*
	 * Gets the excerpt of a specific post ID or object
	 * @param - $post - object/int - the ID or object of the post to get the excerpt of
	 * @param - $length - int - the length of the excerpt in words
	 * @param - $tags - string - the allowed HTML tags. These will not be stripped out
	 * @param - $extra - string - text to append to the end of the excerpt
	 *
	 * Source: https://pippinsplugins.com/blog/a-better-wordpress-excerpt-by-id-function/
	 */
	function excerpt_by_id( $post, $length = 10, $tags = '<a><em><strong>', $extra = ' . . .' ) {

		if ( is_int( $post ) ) {
			// get the post object of the passed ID
			$post = get_post( $post );
		} elseif ( ! is_object( $post ) ) {
			return false;
		}

		if ( has_excerpt( $post->ID ) ) {
			$the_excerpt = $post->post_excerpt;
			return $the_excerpt;
		} else {
			$the_excerpt = $post->post_content;
		}

		$the_excerpt = strip_shortcodes( strip_tags( $the_excerpt ), $tags );
		$the_excerpt = preg_split( '/\b/', $the_excerpt, $length * 2 + 1 );
		array_pop( $the_excerpt );
		$the_excerpt = implode( $the_excerpt );
		$the_excerpt .= $extra;

		return $the_excerpt;
	}

	/**
	 * Get the output for the price taking into account non-variable, variable & free downloads.
	 *
	 * @param  WP_Post $download  The download post object.
	 * @return string             The text for the price string.
	 */
	function get_prices( $download ) {
		if ( edd_has_variable_prices( $download->ID ) ) {
			$prices = edd_get_variable_prices( $download->ID );
			$min = $max = 0;
			foreach ( $prices as $price ) {
				if ( $price['amount'] > $max ) {
					$max = $price['amount'];
				}
				if ( $price['amount'] < $min || 0 == $min ) {
					$min = $price['amount'];
				}
			}
			return sprintf(
				'%s - %s',
				edd_currency_filter( $min ),
				edd_currency_filter( $max )
			);
		} else {
			if ( 0 == edd_get_download_price( $download->ID ) ) {
				return 'Free';
			} else {
				return edd_currency_filter( edd_get_download_price( $download->ID ) );
			}
		}
	}

	/**
	 * Output the meta tags
	 *
	 * @param  array $meta  An array of meta names and content.
	 */
	private function do_meta( $meta ) {
		foreach ( $meta as $key => $value ) {
			echo '<meta name="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '">';
		}
	}
}