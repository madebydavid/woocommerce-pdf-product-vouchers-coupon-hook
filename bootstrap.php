<?php
/**
 * Plugin Name: WooCommerce Basket Avenger
 * Plugin URI: https://github.com/madebydavid/woocommerce-basket-avenger
 * Description: Basket Avenger Plugin for Woocommerce - Used on Suzu Website
 * Version: 0.1
 * Author: madebydavid
 * Author URI: https://github.com/madebydavid/
 * License: 
 */


function MadeByDavid_WooCommerceBasketAvenger_Autoloader($classname) {
	
	if (false === stripos($classname, "MadeByDavid")) return;
	
    if (!file_exists($filename = dirname(__FILE__) . '/src/' . str_replace('\\', '/', $classname) . '.php')) return;
	
	require $filename;
}

/* check WooCommerce is active */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	
    /* this is a hack - TODO: think about how to handle composer'd deps of a WP plugin */
    require_once WP_CONTENT_DIR . '/../vendor/autoload.php';
    
	define('WOOCOMMERCE_BASKETAVENGER_DIR', dirname(__FILE__));
	define('WOOCOMMERCE_BASKETAVENGER_URL', plugin_dir_url(__FILE__));
	
	spl_autoload_register('MadeByDavid_WooCommerceBasketAvenger_Autoloader');

	$GLOBALS['\MadeByDavid\WooCommerceBasketAvenger\Plugin'] = new \MadeByDavid\WooCommerceBasketAvenger\Plugin();
}
