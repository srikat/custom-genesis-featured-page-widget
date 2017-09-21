<?php
/**
 * Plugin Name
 *
 * @package           Custom_Featured_Page_Widget
 * @author            Sridhar Katakam
 * @license           GPL-2.0+
 * @link              https://sridharkatakam.com/
 * @copyright         2017 Sridhar Katakam
 */

/**
 * Custom Featured Page widget class.
 *
 * @package Custom_Featured_Page_Widget
 * @author  Sridhar Katakam
 */
class Custom_Featured_Page extends Genesis_Featured_Page {

		/**
	 * Echo the widget content.
	 *
	 * @since 0.1.8
	 *
	 * @global WP_Query $wp_query Query object.
	 * @global int      $more
	 *
	 * @param array $args     Display arguments including `before_title`, `after_title`,
	 *                        `before_widget`, and `after_widget`.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		global $wp_query;

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget'];

		// Set up the author bio.
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
		}

		$wp_query = new WP_Query( array( 'page_id' => $instance['page_id'] ) );

		if ( have_posts() ) : while ( have_posts() ) : the_post();

			genesis_markup( array(
				'open'    => '<article %s>',
				'context' => 'entry',
				'params'  => array(
					'is_widget' => true,
				),
			) );

			$image = genesis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-page-widget',
				'attr'    => genesis_parse_attr( 'entry-image-widget', array ( 'alt' => get_the_title() ) ),
			) );

			if ( $image && $instance['show_image'] ) {
				$role = empty( $instance['show_title'] ) ? '' : 'aria-hidden="true"';
				printf( '<a href="%s" class="%s" %s>%s</a>', get_permalink(), esc_attr( $instance['image_alignment'] ), $role, wp_make_content_images_responsive( $image ) );
			}

			if ( ! empty( $instance['show_title'] ) ) {

				$title = get_the_title() ? get_the_title() : __( '(no title)', 'genesis' );

				/**
				 * Filter the featured page widget title.
				 *
				 * @since  2.2.0
				 *
				 * @param string $title    Featured page title.
				 * @param array  $instance {
				 *     Widget settings for this instance.
				 *
				 *     @type string $title           Widget title.
				 *     @type int    $page_id         ID of the featured page.
				 *     @type bool   $show_image      True if featured image should be shown, false
				 *                                   otherwise.
				 *     @type string $image_alignment Image alignment: `alignnone`, `alignleft`,
				 *                                   `aligncenter` or `alignright`.
				 *     @type string $image_size      Name of the image size.
				 *     @type bool   $show_title      True if featured page title should be shown,
				 *                                   false otherwise.
				 *     @type bool   $show_content    True if featured page content should be shown,
				 *                                   false otherwise.
				 *     @type int    $content_limit   Amount of content to show, in characters.
				 *     @type int    $more_text       Text to use for More link.
				 * }
				 * @param array  $args     {
				 *     Widget display arguments.
				 *
				 *     @type string $before_widget Markup or content to display before the widget.
				 *     @type string $before_title  Markup or content to display before the widget title.
				 *     @type string $after_title   Markup or content to display after the widget title.
				 *     @type string $after_widget  Markup or content to display after the widget.
				 * }
				 */
				$title = apply_filters( 'genesis_featured_page_title', $title, $instance, $args );
				$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';

				$entry_title = genesis_markup( array(
					'open'    => "<{$heading} %s>",
					'close'   => "</{$heading}>",
					'context' => 'entry-title',
					'content' => sprintf( '<a href="%s">%s</a>', get_permalink(), $title ),
					'params'  => array(
						'is_widget' => true,
						'wrap'      => $heading,
					),
					'echo'    => false,
				) );

				genesis_markup( array(
					'open'    => "<header %s>",
					'close'   => "</header>",
					'context' => 'entry-header',
					'content' => $entry_title,
					'params'  => array(
						'is_widget' => true,
					),
				) );

			}

			if ( ! empty( $instance['show_content'] ) ) {

				genesis_markup( array(
					'open'    => '<div %s>',
					'context' => 'entry-content',
					'params'  => array(
						'is_widget' => true,
					),
				) );

				if ( empty( $instance['content_limit'] ) ) {

					global $more;

					$orig_more = $more;
					$more = 0;

					the_content( genesis_a11y_more_link( $instance['more_text'] ) );

					$more = $orig_more;

				} else {
					the_content_limit( (int) $instance['content_limit'], genesis_a11y_more_link( esc_html( $instance['more_text'] ) ) );
				}

				genesis_markup( array(
					'close'   => '</div>',
					'context' => 'entry-content',
					'params'  => array(
						'is_widget' => true,
					),
				) );

			}

			genesis_markup( array(
				'close'   => '</article>',
				'context' => 'entry',
				'params'  => array(
					'is_widget' => true,
				),
			) );

			endwhile;
		endif;

		// Restore original query.
		wp_reset_query();

		echo $args['after_widget'];

	}

}
