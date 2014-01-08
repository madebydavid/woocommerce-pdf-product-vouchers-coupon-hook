<?php

namespace MadeByDavid\WooCommerceBasketAvenger;

class Plugin {
    
    const SCRIPT_ID = 'madebydavid-woocommercebasketavenger-javascript';
    const TRANSLATE_DOMAIN = 'madebydavid-woocommercebasketavenger';
    
    const AJAX_ACTION = 'madebydavid-woocommercebasketavenger-reschedule';
    
    private $configuration;
    private $admin;
    
    function __construct() {
        
        add_action('init', array($this, 'init'), 0);
        
        /* class to load the settings */
        $this->configuration = new PluginConfiguration();
        
        add_action('wp_enqueue_scripts', array($this, 'registerJavascript'));
        
        
        if (is_admin()) {
            $this->admin = new PluginAdmin($this);
        }
        
        $this->registerAjax();
        
    }
    
    public function init(){

        
    }
    
    public function getConfiguration() {
        return $this->configuration;
    }
    
    function registerAjax() {
        
        add_action(
            'wp_ajax_'.self::AJAX_ACTION,
            array($this, 'ajaxCallback')
        );
    }
    
    function ajaxCallback() {
        
            
    }
    
    function registerJavascript() {
        
            
        wp_enqueue_style(
            Plugin::SCRIPT_ID,
            WOOCOMMERCE_BASKETAVENGER_URL . 'css/plugin.css'
        );
        
        /* include js file for the plugin, jquery is a dependancy */
        wp_enqueue_script(
            self::SCRIPT_ID,
            WOOCOMMERCE_BASKETAVENGER_URL . 'js/plugin.js',
            array('jquery')
        );
        
        /* setup script variables for webservice */
        wp_localize_script(
            self::SCRIPT_ID,
            'WooCommerceBasketAvenger',
            array(
                'webServiceUrl' => admin_url('admin-ajax.php'),
                'webServiceAction' => self::AJAX_ACTION,
                'webServiceNonce' => wp_create_nonce(self::SCRIPT_ID)
            )
        );
            
    }
    
}
