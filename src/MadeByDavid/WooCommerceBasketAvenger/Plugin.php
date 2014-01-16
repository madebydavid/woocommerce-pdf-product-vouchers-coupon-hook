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
        
        if (is_admin()) {
            $this->admin = new PluginAdmin($this);
        }
        
    }
    
    public function init(){
        
    }
   
    
    function skipCheckout($url) {
        
        global $woocommerce;
        
        if (!is_user_logged_in()) {
            return $url;
        }
        
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
                
                if (1 != count($woocommerce->payment_gateways->get_available_payment_gateways())) {
                    return $url;
                }
                
                $currentUser = wp_get_current_user();
                /* check we have first, last and email */
                switch (true) {
                    case (0 == strlen($currentUser->user_firstname)):
                    case (0 == strlen($currentUser->user_lastname)):
                    case (0 == strlen($currentUser->user_email)):
                        wp_logout();
                        return $url;
                }
                
                $_POST['billing_first_name'] = $currentUser->user_firstname;
                $_POST['billing_last_name'] = $currentUser->user_lastname;
                $_POST['billing_email'] = $currentUser->user_email;
                
                /* use the only selected payment method */
                $_POST['payment_method'] = key($woocommerce->payment_gateways->get_available_payment_gateways());
                /* nonce */
                $_REQUEST['_n'] = wp_create_nonce('woocommerce-process_checkout');
                
                /* hack - force no shipping in this situation */
                $oldOption = get_option('woocommerce_calc_shipping');
                update_option('woocommerce_calc_shipping', 'no');
                /* process the checkout - make the order etc */
                $woocommerce->checkout()->process_checkout();
                /* revert the old setting */
                update_option('woocommerce_calc_shipping', $oldOption);
                
                /* there's an error - probably the selfish category is full (we use this plugin with the booking system mainly) */
                if (0 !== count($woocommerce->get_errors())) {
                    /* for some reason we get dups in the errors */
                    $woocommerce->errors = array_unique($woocommerce->errors);
                    
                    /* send the user back to the product page and display the error there */
                    /* returning the url doesnt work we need to redirect */
                    wp_safe_redirect(get_permalink($item['product_id']));
                    
                    return get_permalink($item['product_id']);
                }
                
                $payment_page = get_permalink(woocommerce_get_page_id('pay'));
                
                if (get_option('woocommerce_force_ssl_checkout' ) == 'yes') {
                    $payment_page = str_replace( 'http:', 'https:', $payment_page );
                }
                
                return $payment_page;
                
                
            }
        }
        
        return $url;
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
