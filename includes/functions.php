<?php
/**
 * Various functions used by the plugin.
 *
 * @package    Recent_Posts_Widget_Extended
 * @since      0.9.4
 * @author     Satrya
 * @copyright  Copyright (c) 2014, Satrya
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Sets up the default arguments.
 *
 * @since  0.9.4
 */
function rpwe_get_default_args() {

	$css_defaults = ".rpwe-block ul{\nlist-style: none !important;\nmargin-left: 0 !important;\npadding-left: 0 !important;\n}\n\n.rpwe-block li{\nborder-bottom: 1px solid #eee;\nmargin-bottom: 10px;\npadding-bottom: 10px;\nlist-style-type: none;\n}\n\n.rpwe-block a{\ndisplay: inline !important;\ntext-decoration: none;\n}\n\n.rpwe-block h3{\nbackground: none !important;\nclear: none;\nmargin-bottom: 0 !important;\nmargin-top: 0 !important;\nfont-weight: 400;\nfont-size: 12px !important;\nline-height: 1.5em;\n}\n\n.rpwe-thumb{\nborder: 1px solid #eee !important;\nbox-shadow: none !important;\nmargin: 2px 10px 2px 0;\npadding: 3px !important;\n}\n\n.rpwe-summary{\nfont-size: 12px;\n}\n\n.rpwe-time{\ncolor: #bbb;\nfont-size: 11px;\n}\n\n.rpwe-comment{\ncolor: #bbb;\nfont-size: 11px;\npadding-left: 5px;\n}\n\n.rpwe-alignleft{\ndisplay: inline;\nfloat: left;\n}\n\n.rpwe-alignright{\ndisplay: inline;\nfloat: right;\n}\n\n.rpwe-aligncenter{\ndisplay: block;\nmargin-left: auto;\nmargin-right: auto;\n}\n\n.rpwe-clearfix:before,\n.rpwe-clearfix:after{\ncontent: \"\";\ndisplay: table !important;\n}\n\n.rpwe-clearfix:after{\nclear: both;\n}\n\n.rpwe-clearfix{\nzoom: 1;\n}\n";

	$defaults = array(
		'title'             => esc_attr__( 'Recent Posts', 'rpwe' ),
		'title_url'         => '',

		'limit'            => 5,
		'offset'           => 0,
		'order'            => 'DESC',
		'orderby'          => 'date',
		'cat'              => array(),
		'tag'              => array(),
		'taxonomy'         => '',
		'post_type'        => array( 'post' ),
		'post_status'      => 'publish',
		'ignore_sticky'    => 1,
		'exclude_current'  => 1,

		'excerpt'          => false,
		'length'           => 10,
		'thumb'            => true,
		'thumb_height'     => 45,
		'thumb_width'      => 45,
		'thumb_default'    => 'http://placehold.it/45x45/f0f0f0/ccc',
		'thumb_align'      => 'rpwe-alignleft',
		'date'             => true,
		'date_relative'    => false,
		'date_modified'    => false,
		'readmore'         => false,
		'readmore_text'    => __( 'Read More &raquo;', 'recent-posts-widget-extended' ),
		'comment_count'    => false,

		'styles_default'   => true,
		'css'              => $css_defaults,
		'cssID'            => '',
		'css_class'        => '',
		'before'           => '',
		'after'            => '',

		/* NEW ARGS */
		'share_icons'      => false

	);

	// Allow plugins/themes developer to filter the default arguments.
	return apply_filters( 'rpwe_default_args', $defaults );

}

/**
 * Outputs the recent posts.
 *
 * @since  0.9.4
 */
function rpwe_recent_posts( $args = array() ) {
	echo rpwe_get_recent_posts( $args );
}

/**
 * Generates the posts markup.
 *
 * @since  0.9.4
 * @param  array  $args
 * @return string|array The HTML for the random posts.
 */
function rpwe_get_recent_posts( $args = array() ) {

	// Set up a default, empty variable.
	$html = '';

	// Merge the input arguments and the defaults.
	$args = wp_parse_args( $args, rpwe_get_default_args() );

	// Extract the array to allow easy use of variables.
	extract( $args );

	// Allow devs to hook in stuff before the loop.
	do_action( 'rpwe_before_loop' );

	// Display the default style of the plugin.
	if ( $args['styles_default'] === true ) {
		rpwe_custom_styles();
	}

	// If the default style is disabled then use the custom css if it's not empty.
	if ( $args['styles_default'] === false && ! empty( $args['css'] ) ) {
		echo '<style>' . $args['css'] . '</style>';
	}
$html = '<hr>';
	// Get the posts query.
	$posts = rpwe_get_posts( $args );

	if ( $posts->have_posts() ) :

		// Recent posts wrapper
		$html = '<div ' . ( ! empty( $args['cssID'] ) ? 'id="' . sanitize_html_class( $args['cssID'] ) . '"' : '' ) . ' class="participants-list ' . ( ! empty( $args['css_class'] ) ? '' . sanitize_html_class( $args['css_class'] ) . '' : '' ) . '">';


				while ( $posts->have_posts() ) : $posts->the_post();

					// Thumbnails
					$thumb_id = get_post_thumbnail_id(); // Get the featured image id.
					$img_url  = wp_get_attachment_url( $thumb_id ); // Get img URL.

					// Display the image url and crop using the resizer.
					$image    = rpwe_resize( $img_url, $args['thumb_width'], $args['thumb_height'], true );

					// Start recent posts markup.
					$html .= '<div class="participant-text"><a class="participant-url" name="' . sanitize_title(get_the_title()). '"></a>';

						$html .= '<div class="container">';

							$html .= '<div class="row">';

								$html .= '<div class="col-lg-8">';

									$html .= '<h5><a id="' . sanitize_title(get_the_title()) . '" class="participant-title">' . esc_attr( get_the_title() ) . '</a></h5>';


									if ( $args['excerpt'] ) :
										$html .= '<div><p><glyph glyph-name="facebook-5"></glyph></a>';
											$html .= get_the_excerpt();
										$html .= '</p></div>';
									endif;

								$html .= '</div>';

								$html .= '<div class="col-lg-2 col-lg-offset-2">';

									$html .= '<div class="participant-hide"><h2>&times;</h2></div>';

								$html .= '</div>';

							$html .= '</div>';

							$html .= '<div class="row participant-body">';

								$html .= '<div class="col-lg-8">';

									$html .= '<div class="participant-content">' . get_the_content() . '<br><a href="//' . get_post_meta(get_the_ID(), 'website', true) . '" target="_blank">' . get_post_meta(get_the_ID(), 'website', true) . '</a></div>';

								$html .= '</div>';

								if ( $args['share_icons	'] ) :

											$html .= '<div class="col-lg-2 col-lg-offset-2 share">';

												$html .= 'Share';

												$html .= '<p><a class="icon-facebook" href="http://www.facebook.com/sharer/sharer.php?u=http://radio.serpentinegalleries.org/#'. sanitize_title(get_the_title()) .'&title=' . get_the_title() . ' - Transformation Marathon" target="_blank"></a></p>';


												$html .= '<p><a class="icon-twitter" href="http://twitter.com/intent/tweet?status='. get_the_title() .' - Transformation Marathon+http://radio.serpentinegalleries.org/#'. sanitize_title(get_the_title()) . '" target="_blank"></a></p>';

											$html .= '</div>';

								endif;


							$html .= '</div>';

						$html .= '</div>';

					$html .= '</div>';

					$html .= '<hr>';


				endwhile;


		$html .= '</div><!-- Generated by http://wordpress.org/plugins/recent-posts-widget-extended/ -->';

	endif;

	// Restore original Post Data.
	wp_reset_postdata();

	// Allow devs to hook in stuff after the loop.
	do_action( 'rpwe_after_loop' );

	// Return the  posts markup.
	return $args['before'] . apply_filters( 'rpwe_markup', $html ) . $args['after'];

}

/**
 * The posts query.
 *
 * @since  0.0.1
 * @param  array  $args
 * @return array
 */
function rpwe_get_posts( $args = array() ) {

	// Query arguments.
	$query = array(
		'offset'              => $args['offset'],
		'posts_per_page'      => $args['limit'],
		'orderby'             => $args['orderby'],
		'order'               => $args['order'],
		'post_type'           => $args['post_type'],
		'post_status'         => $args['post_status'],
		'ignore_sticky_posts' => $args['ignore_sticky'],
	);

	// Exclude current post
	if ( $args['exclude_current'] ) {
		$query['post__not_in'] = array( get_the_ID() );
	}

	// Limit posts based on category.
	if ( ! empty( $args['cat'] ) ) {
		$query['category__in'] = $args['cat'];
	}

	// Limit posts based on post tag.
	if ( ! empty( $args['tag'] ) ) {
		$query['tag__in'] = $args['tag'];
	}

	/**
	 * Taxonomy query.
	 * Prop Miniloop plugin by Kailey Lampert.
	 */
	if ( ! empty( $args['taxonomy'] ) ) {

		parse_str( $args['taxonomy'], $taxes );

		$operator  = 'IN';
		$tax_query = array();
		foreach( array_keys( $taxes ) as $k => $slug ) {
			$ids = explode( ',', $taxes[$slug] );
			if ( count( $ids ) == 1 && $ids['0'] < 0 ) {
				// If there is only one id given, and it's negative
				// Let's treat it as 'posts not in'
				$ids['0'] = $ids['0'] * -1;
				$operator = 'NOT IN';
			}
			$tax_query[] = array(
				'taxonomy' => $slug,
				'field'    => 'id',
				'terms'    => $ids,
				'operator' => $operator
			);
		}

		$query['tax_query'] = $tax_query;

	}

	// Allow plugins/themes developer to filter the default query.
	$query = apply_filters( 'rpwe_default_query_arguments', $query );

	// Perform the query.
	$posts = new WP_Query( $query );

	return $posts;

}

/**
 * Custom Styles.
 *
 * @since  0.8
 */
function rpwe_custom_styles() {
	?>
	<?php
}
