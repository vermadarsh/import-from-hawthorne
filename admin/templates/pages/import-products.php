<?php
/**
 * This file is used for templating the Hawthorne products import feature.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/pages
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// delete_transient( 'hawthorne_product_items' ); die;

// Fetch the products data from the transient.
$products = get_transient( 'hawthorne_product_items' );

// See if there are products in the transient.
if ( false === $products ) {
	$products = hawthorne_fetch_products(); // Shoot the API to get products.

	/**
	 * Store the response data in a cookie.
	 * This cookie data will be used to import the products in the database.
	 */
	if ( false !== $products ) {
		set_transient( 'hawthorne_product_items', wp_json_encode( $products ), ( 60*60*12 ) );
	}
} else {
	// If you're here, the data is already in transients.
	$products = json_decode( $products, true );
}

// Get the count of the products.
$total_products = count( $products );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Import Products', 'import-from-hawthorne' ); ?></h1>
	<section class="import-from-hawthorne-wrapper">
		<div class="card importing-card">
			<h2 class="heading"><?php esc_html_e( 'Importing', 'import-from-hawthorne' ); ?></h2>
			<p class="importing-notice"><?php echo sprintf( __( 'Your products are now being imported... %1$s0%3$s of %2$s%4$s%3$s imported', 'import-from-hawthorne' ), '<span class="imported-count">', '<span class="total-products-count">', '</span>', $total_products ); ?></p>
			<div class="progress-bar-wrapper">
				<progress class="importer-progress" max="100" value="0"></progress>
				<span class="value">0%</span>
			</div>
		</div>

		<div class="card finish-card" style="display: none;">
			<h2 class="heading"><?php esc_html_e( 'Import Complete!', 'import-from-hawthorne' ); ?></h2>
			<div class="importer-done">
				<span class="dashicons dashicons-yes-alt icon"></span>
				<p>
					<?php
					/* translators: 1: %s: total products count, 2: %s: strong tag open, 3: %s: strong tag closed */
					echo sprintf( __( '%1$s products imported. Newly added products: %2$s12%3$s Updated products: %2$s23%3$s', 'import-from-hawthorne' ), $total_products, '<strong>', '</strong>' );
					?>
				</p>
			</div>
			<div class="wc-actions text-right">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><?php esc_html_e( 'View products', 'import-from-hawthorne' ); ?></a>
				<a class="button button-secondary" href="javascript:void(0);"><?php esc_html_e( 'View import log', 'import-from-hawthorne' ); ?></a>
			</div>
		</div>
	</section>
</div>
