<?php
/**
 * This file is used for templating the Hawthorne products import feature.
 *
 * @since 1.0.0
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/pages
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Shoot the API to get products.
$products = hawthorne_fetch_products();
debug( $products ); die;
?>
<section class="import-from-hawthorne-wrapper">

    <div class="card importing-card">
        <h2 class="heading">Importing</h2>
        <p>Your products are now being imported...</p>

        <div class="progress-bar-wrapper">
            <progress class="importer-progress" max="100" value="0">
            </progress>
            <span class="value">0%</span>
        </div>
    </div>

    <div class="card finish-card">
        <h2 class="heading">Importing products from a CSV file</h2>
        <p>This tool allows you to import (or merge) product data to your store from a CSV or TXT file.</p>

        <div class="importer-done">
            <span class="dashicons dashicons-yes-alt icon"></span>
            <p>
            Import complete! Failed to import <strong>26</strong> products.
            <a href="#" class="importer-done-view-errors">View import log</a>.
            File uploaded: <strong>wc-product-export-18-11-2021-1637230637423-1.csv</strong>
            </p>
        </div>
        <div class="wc-actions text-right">
            <a class="button button-primary" href="http://localhost/cmsminds/easy-reservations-system/wp-admin/edit.php?post_type=product">View products</a>
        </div>
    </div>

</section>