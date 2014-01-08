<?php

namespace MadeByDavid\WooCommerceBasketAvenger;

class PluginAdmin {
    
    const ADMIN_SCRIPT_ID = 'madebydavid-woocommercebasketavenger-admin-javascript';
    const ADMIN_AJAX_ACTION = 'madebydavid-woocommercebasketavenger-admin-ajax-action';
    
    private $plugin;
    
    function __construct($plugin) { 
        
        if (!is_admin()) {
            return false;
        }
        
        $this->plugin = $plugin;
        
        /* priority must be low so that the woocommerce plugin adds the menu first */
        add_action('admin_menu', array($this, 'registerAdminMenu'), 100);
        add_action('admin_head', array($this, 'registerAdminCss'));
        add_action('admin_init', array($this, 'registerAdminJavascript'));
        
        $this->registerAdminAjax();
    }
    
    function registerAdminMenu() {
        $optionsPage = add_submenu_page(
            'woocommerce',
            __( 'Basket Avenger', Plugin::TRANSLATE_DOMAIN),
            __( 'Basket Avenger', Plugin::TRANSLATE_DOMAIN),
            'manage_woocommerce',
            Plugin::TRANSLATE_DOMAIN,
            array($this, 'showAdminOptions')
        );
        add_action('admin_print_scripts-' . $optionsPage, array($this, 'enqueueAdminJavascript'));
    }
    
    function getProductCategories() {
        return get_terms('product_cat', array('taxonomy' => 'product_cat'));
    }
    
    function registerAdminCss() {
        echo '<link rel="stylesheet" type="text/css" href="'.WOOCOMMERCE_BASKETAVENGER_URL.'css/admin.css"></link>';
    }
    
    function registerAdminJavascript() {
        wp_register_script(
            self::ADMIN_SCRIPT_ID,
            WOOCOMMERCE_BASKETAVENGER_URL . 'js/admin.js',
            array('jquery')
        );
    }
    
    function registerAdminAjax() {
        add_action(
            'wp_ajax_'.self::ADMIN_AJAX_ACTION,
            array($this, 'adminAjaxCallback')
        );
    }
    
    function adminAjaxCallback() {
        header( "Content-Type: application/json" );
    
        if (!current_user_can('manage_woocommerce')) {
            wp_die( __('You do not have sufficient permissions to access this page.'));
        }
    
        if (!wp_verify_nonce(sanitize_text_field($_POST['nonce']), self::ADMIN_SCRIPT_ID)) {
            echo json_encode(array('error' => __('Invalid nonce', Plugin::TRANSLATE_DOMAIN)));
            die();
        }
        
        /* extract the real options from the POST into an array */
        parse_str($_POST['options'], $options);
        
        if (array_key_exists('selfishCategory', $options)) {
            $this->plugin->getConfiguration()->setSelfishCategoryID($options['selfishCategory']);
        }
        
        die();
        
    }
    
    function enqueueAdminJavascript() {
        wp_enqueue_script(self::ADMIN_SCRIPT_ID);
        /* setup script variables for webservice */
        wp_localize_script(
            self::ADMIN_SCRIPT_ID,
            'WooCommerceBasketAvenger',
            array(
                'webServiceUrl' => admin_url('admin-ajax.php'),
                'webServiceAction' => self::ADMIN_AJAX_ACTION,
                'webServiceNonce' => wp_create_nonce(self::ADMIN_SCRIPT_ID)
            )
        );
    }
    
    public function showAdminOptions() {
    
        if (!current_user_can('manage_woocommerce')) {
            wp_die( __('You do not have sufficient permissions to access this page.'));
        }
     
        include WOOCOMMERCE_BASKETAVENGER_DIR . '/templates/admin-options.php';
    }
    
}
