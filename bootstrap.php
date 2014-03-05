<?php
/**
 * Plugin Name: WooCommerce PDF Product Vouchers Coupon Hook
 * Plugin URI: https://github.com/madebydavid/woocommerce-pdf-product-vouchers-coupon-hook
 * Description: Creates shop coupons from PDF product vouchers
 * Version: 0.1
 * Author: madebydavid
 * Author URI: https://github.com/madebydavid/
 * License: MIT
 */


function MadeByDavid_WooCommercePDFProductVouchersCouponHook_Autoloader($classname) {
	
	if (false === stripos($classname, "MadeByDavid")) return;
	
    if (!file_exists($filename = dirname(__FILE__) . '/src/' . str_replace('\\', '/', $classname) . '.php')) return;
	
	require $filename;
}

/* check WooCommerce is active */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	
    /* this is a hack - TODO: think about how to handle composer'd deps of a WP plugin */
    require_once WP_CONTENT_DIR . '/../vendor/autoload.php';
    
	define('WOOCOMMERCE_PDFPRODUCTVOUCHERSCOUPONHOOK_DIR', dirname(__FILE__));
	define('WOOCOMMERCE_PDFPRODUCTVOUCHERSCOUPONHOOK_URL', plugin_dir_url(__FILE__));
	
	spl_autoload_register('MadeByDavid_WooCommercePDFProductVouchersCouponHook_Autoloader');

	$GLOBALS['\MadeByDavid\WooCommercePDFProductVouchersCouponHook\Plugin'] = new \MadeByDavid\WooCommercePDFProductVouchersCouponHook\Plugin();
}
