<?php

namespace MadeByDavid\WooCommerceBasketAvenger;

class Plugin {
    
    const TRANSLATE_DOMAIN = 'madebydavid-woocommercebasketavenger';
    
    private $configuration;
    private $admin;
    
    function __construct() {
        
        add_action('init', array($this, 'init'), 0);
        
        /* class to load the settings */
        $this->configuration = new PluginConfiguration();
        
        add_filter('woocommerce_add_to_cart_validation', array($this, 'avenge'), 10, 3);
        add_filter('woocommerce_get_checkout_url', array($this, 'skipCheckout'), 10, 1);
        
        add_filter('woocommerce_add_cart_item_data', array($this, 'addItemMeta'), 10, 2);
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'getCartItemFromSession'), 10, 2);
        add_filter('woocommerce_get_item_data',array($this, 'getOrderItemMeta'), 10, 2);
        add_action('woocommerce_add_order_item_meta', array($this, 'addOrderItemMeta'), 10, 2);
        
        if (is_admin()) {
            $this->admin = new PluginAdmin($this);
        }
        
    }
    
    public function init(){
        
    }
    
    function addOrderItemMeta($itemId, $cartItem) {
        if (isset($cartItem['TESTING'])) {
            woocommerce_add_order_item_meta( $itemId, 'SOMETHING', $cartItem['TESTING'] );
        }
    }
    
    function getOrderItemMeta($otherData, $cartItem) {
        
        if (isset($cartItem['TESTING'])) {
            error_log("setting");
            $otherData[] = array(
            	'name' => 'SOMETHING',
                'value'=> $cartItem['TESTING'],
                 'display' => 'yes'
            );
        }
        
        return $otherData;
    }
    
    function getCartItemFromSession($cartItem, $values) {
        
        if (isset($values['TESTING'])) {
            $cartItem['TESTING'] = $values['TESTING'];
        }
        
        
        return $cartItem;
    }
    
    function addItemMeta($itemMeta, $productId) {

        
        $itemMeta['TESTING'] = $_POST['meta-test'];
        return $itemMeta;
    }
    
    function skipCheckout($url) {
        
        global $woocommerce;
        
        if (!$this->getConfiguration()->getSkipCheckout()) {
            return $url;
        }
        
        if (0 === (int)$this->getConfiguration()->getSelfishCategoryID()) {
            return $url;
        }
        
        foreach ($woocommerce->cart->get_cart() as $key => $item) {
            if ($this->isASelfishproduct($item['product_id'])) {
                /* if it is a selfish product then we only have to loop once as
                 * they are selfish and only exist by themselves
                 */
                error_log("skipCheckout:$url");
                global $woocommerce;
                $woocommerce_checkout = $woocommerce->checkout();
                $woocommerce_checkout->process_checkout();
                /* only works if we have 1 payment gateway enabled */
                if (1 != count($woocommerce->payment_gateways->get_available_payment_gateways())) {
                    return $url;
                }
                
           
                
                
            }
        }
        return $url;
        error_log("skipCheckout:$url");
        
    }
    
    function avenge($valid, $product_id, $quantity) {
        
        global $woocommerce;
        
        if (0 === (int)$this->getConfiguration()->getSelfishCategoryID()) {
            return true;
        }
        
        $product = new \WC_Product($product_id);
        if (!$product->exists()) {
            throw new \Exception('Invalid product Id added to basket');
        }
        
        if ($this->isASelfishProduct($product_id)) {
            /* we're adding the selfish category - so empty the cart */
            $woocommerce->cart->remove_coupons();
            $woocommerce->cart->empty_cart();
            return true;
        }
        
        /* otherwise make sure there are no selfish items in the cart */
        foreach ($woocommerce->cart->get_cart() as $key => $item) {
            if ($this->isASelfishproduct($item['product_id'])) {
                $woocommerce->cart->set_quantity($key, 0);
            }
        }
        
        return true;
    }
    
    public function isASelfishProduct($productId) {
        
        if (false === ($categories = get_the_terms($productId, 'product_cat'))) {
            return false;
        }
        
        foreach ($categories as $category) {
            if ($this->getConfiguration()->getSelfishCategoryID() == $category->term_id) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getConfiguration() {
        return $this->configuration;
    }
    

}
