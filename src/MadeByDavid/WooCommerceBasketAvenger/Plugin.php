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
        
        if (is_admin()) {
            $this->admin = new PluginAdmin($this);
        }
        
    }
    
    public function init(){
        
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
    
    private function isASelfishProduct($productId) {
        
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
