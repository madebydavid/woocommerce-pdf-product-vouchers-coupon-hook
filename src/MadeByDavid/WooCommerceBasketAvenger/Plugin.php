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
        
        if (is_admin()) {
            $this->admin = new PluginAdmin($this);
        }
        
    }
    
    public function init(){
        
    }
    
    public function getConfiguration() {
        return $this->configuration;
    }
    

}
