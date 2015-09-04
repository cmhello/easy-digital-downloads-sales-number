<?php 
/**
 * Plugin Name: Easy Digital Downloads - Sales Number
 * Plugin URI: https://wordpress.org/plugins/easy-digital-downloads-sales-number/
 * Description: EDD extension plugin for displaying how many sales were made for certain product on the product purchase button area.
 * Version: 1.0.1
 * Author: Yudhistira Mauris
 * Author URI: http://www.yudhistiramauris.com/
 * Text Domain: eddsn
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: languages
 *
 * Copyright © 2015 Yudhistira Mauris (email: mauris@yudhistiramauris.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/gpl-2.0.txt>.
 */

/**
 * Check if EDD is installed and active
 * @return void
 */
function eddsn_check_EDD() {

	// Check if core EDD plugin is not active
	if ( ! class_exists( 'Easy_Digital_Downloads') ) {	

		// Check if plugin active
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			// Deactivate plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );

			// unset activation notice
	 		unset( $_GET[ 'activate' ] );

			// Add error notice on admin screen
			add_action( 'admin_notices', 'eddsn_admin_notice' );
		}
	}
}
add_action( 'admin_init', 'eddsn_check_EDD' );

/**
 * Callback function of eddsn_check_EDD()
 * @return void
 */
function eddsn_admin_notice() {

	ob_start();

	?>

	<div class="error">
		<p><?php _e( 'Easy Digital Downloads plugin is required to activate EDD sales number extension plugin. Please install and activate it first.', 'eddsn' ); ?></p>
	</div>

	<?php

	echo ob_get_clean();
}

/**
 * Display sales count on EDD purchase box before purchase link
 * @uses edd_get_download_sales_stats() See http://docs.easydigitaldownloads.com/article/552-edd-get-download-sales-stats
 * @return void
 */
function eddsn_sales_number_purchase_link_top() {
	
	$eddsn_download_id = get_the_ID();

	eddsn_output_sales_number( $eddsn_download_id, 'before-link' );
}
add_action( 'edd_purchase_link_top', 'eddsn_sales_number_purchase_link_top' );

/**
 * Display sales count on EDD purchase box before price options
 * @uses edd_get_download_sales_stats() See http://docs.easydigitaldownloads.com/article/552-edd-get-download-sales-stats
 * @return void
 */
function eddsn_sales_number_before_price_options() {

	$eddsn_download_id = get_the_ID();

	eddsn_output_sales_number( $eddsn_download_id, 'before-price' );
}
add_action( 'edd_before_price_options', 'eddsn_sales_number_before_price_options' );

function eddsn_output_sales_number( $download_id, $class_name = '' ) {

	$eddsn_variable_pricing = edd_has_variable_prices( $download_id );
	$eddsn_product_price    = edd_get_download_price( $download_id );

	if ( $eddsn_variable_pricing || 0 < $eddsn_product_price ) {

		$eddsn_sales_number = edd_get_download_sales_stats( $download_id );

		if ( $eddsn_sales_number > 0 ) {

			$eddsn_sales_text = $eddsn_sales_number > 1 ? _x( 'Sales', 'Plural form', 'eddsn' ) : _x( 'Sale', 'Singular form', 'eddsn' );

			$html = '<div class="edd-sales-number-' . $class_name . '">' . $eddsn_sales_number . ' ' . $eddsn_sales_text . '</div>';
			
			echo $html;
		}
	}
}