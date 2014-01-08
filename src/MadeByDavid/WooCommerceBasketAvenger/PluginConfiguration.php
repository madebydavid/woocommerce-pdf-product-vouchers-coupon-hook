<?php

namespace MadeByDavid\WooCommerceBasketAvenger;

class PluginConfiguration {
    
    const OPTION_NAME_PREFIX = 'MBD\\WCBA';
    
    public function buildOptionName($optionName) {
        return implode('::', array(
            PluginConfiguration::OPTION_NAME_PREFIX,
            $optionName
        ));
    }
    
        
}
    
