<?php
/**
 * Plugin Name: WooCommerce - Display Products by Custom Taxonomy and Product Category with Order by price
 * Description: List WooCommerce products by a custom taxonomy type and category for products using a shortcode, ex: [woo_products_custom_tax tax_name="collection" tax_tags="gold-jewellery, diamond-jewellery" category="7" qty="10" order="DESC"]
 * Version: 1.0
 * Author: Jonas Belcina
 * Author URI: http://jonasbelcina.com
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wpbct_no_woocommerce_notice() {
	?>
	<div class="error">
		<p><?php _e( '<strong>WooCommerce - Display Products by Custom Tax</strong> plugin requires <a target="_blank" href="https://wordpress.org/plugins/woocommerce/">Woocommerce</a> core plugin to be installed and active.', 'woocommerce-products-by-custom-tax' ); ?></p>
	</div>
	<?php
}

/*
 * List WooCommerce Products by custom taxonomy
 *
 * ex: [woo_products_custom_tax tax_name="collection" tax_tags="gold-jewellery, diamond-jewellery" category="7" qty="10" order="DESC"]
 */
function wpbct_shortcode( $atts, $content = null ) {
	global $woocommerce_loop;

	if ( empty( $atts ) ) return '';

	extract(shortcode_atts(array(
		'tax_name' => '', // Required
		'tax_tags' => '', // Required
		'category' => '', // Required
		'qty' => '-1', // Optional
		'order' => 'DESC' // Optional
		), $atts));

	if ( $tax_name === '' || $tax_tags === '' ) return '';

	ob_start();

	$args = array(
			'post_type' => 'product',
			'posts_per_page' => sanitize_text_field( $qty ),
			'order' => sanitize_text_field( $order ),
			'tax_query' => array(
								'relation' => 'AND',
								array(
									'taxonomy' => $tax_name,
									'field'    => 'slug',
									'terms'    => sanitize_text_field( $tax_tags ),
									'operator' => 'IN',
								),
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'term_id',
									'terms'    => sanitize_text_field( $category ),
									// 'operator' => 'NOT IN',
								),
							)
		);


	$products = new WP_Query( $args );

	$collection = [];

	if( $products->have_posts() ) : 

		while ( $products->have_posts() ) : $products->the_post();

			array_push($collection, get_the_ID()); // store into array to sort by price

		endwhile;

	else :

		_e('No product matching your criteria.');

	endif;

	wp_reset_postdata(); ?>



	<?php
	// new array
	if(!empty($collection)) {
		if($_GET['orderby']) {

			if($_GET['orderby'] == 'price') {
				$or = 'ASC';
			} elseif($_GET['orderby'] == 'price-desc') {
				$or = 'DESC';
			}

			$collection_args = array(
									'post_type' => 'product',
									'post__in' 	=> $collection,
									'orderby'   => 'meta_value_num',
									'meta_key'  => '_price',
									'order'		=> $or,
									'posts_per_page' => '5'
								);

		} else {
			$collection_args = array(
									'post_type' => 'product',
									'post__in' 	=> $collection,
									'posts_per_page' => '5'
								);
		}

		$collection_products = new WP_Query($collection_args);

		if( $collection_products->have_posts() ) :

			woocommerce_product_loop_start();

			woocommerce_product_subcategories();

				while ( $collection_products->have_posts() ) : $collection_products->the_post();

					wc_get_template_part( 'content', $template );

				endwhile;

			woocommerce_product_loop_end();

		endif;

	}


}

/**
 * Check if WooCommerce is active and add the short code, if not active display an error.
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_shortcode( 'woo_products_custom_tax', 'wpbct_shortcode' );
} else {
	add_action( 'admin_notices', 'wpbct_no_woocommerce_notice' );
}
