<?php
/**
 * Plugin Name: NRW Direct View WooCommerce
 * Plugin URI: https://wp.nrwone.in
 * Description: Opens WooCommerce downloadable product files directly in a new tab using the original download URL instead of WooCommerce protected redirect links.
 * Version: 1.0.0
 * Author: NRW India
 * Author URI: https://wp.nrwone.in
 * License: GPL2
 * Text Domain: nrw-direct-view-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Replace WooCommerce download links with direct file URLs
 */
add_filter( 'woocommerce_customer_get_downloadable_products', 'nrw_direct_view_download_links' );

function nrw_direct_view_download_links( $downloads ) {

    if ( empty( $downloads ) ) {
        return $downloads;
    }

    foreach ( $downloads as $key => $download ) {

        if ( isset( $download['product_id'] ) && isset( $download['download_id'] ) ) {

            $product = wc_get_product( $download['product_id'] );

            if ( $product && $product->is_downloadable() ) {

                $files = $product->get_downloads();

                foreach ( $files as $file_id => $file ) {

                    if ( $file_id === $download['download_id'] ) {

                        // Replace WooCommerce protected URL
                        $downloads[$key]['download_url'] = $file['file'];
                    }
                }
            }
        }
    }

    return $downloads;
}

/**
 * Open download links in new tab
 */
add_action( 'wp_footer', 'nrw_direct_view_script' );

function nrw_direct_view_script() {

    if ( ! is_account_page() ) {
        return;
    }
    ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {

        const downloadLinks = document.querySelectorAll(
            '.woocommerce-MyAccount-downloads-file a'
        );

        downloadLinks.forEach(function(link) {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        });

    });
    </script>

    <?php
}
?>