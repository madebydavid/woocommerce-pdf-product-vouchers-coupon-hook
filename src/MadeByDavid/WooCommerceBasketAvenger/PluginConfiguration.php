<?php

namespace MadeByDavid\WooCommerceBasketAvenger;

class PluginConfiguration {
    
    const OPTION_NAME_PREFIX = 'MBD\\WCBA';
    const OPTION_NAME_SELFISH_CATEGORY_ID = 'selfish_cat_id';
    const OPTION_NAME_SKIP_CHECKOUT = 'skip_checkout';
    
    public function buildOptionName($optionName) {
        return implode('::', array(
            PluginConfiguration::OPTION_NAME_PREFIX,
            $optionName
        ));
    }
    
    public function getSelfishCategoryID() {
        return get_option(
            self::buildOptionName(self::OPTION_NAME_SELFISH_CATEGORY_ID),
            null
        );
    }
    
    public function setSelfishCategoryID($categoryID) {
        return update_option(
            self::buildOptionName(self::OPTION_NAME_SELFISH_CATEGORY_ID),
            $categoryID
        );
    }
    
    public function getSkipCheckout() {
        return (bool)get_option(
            self::buildOptionName(self::OPTION_NAME_SKIP_CHECKOUT),
            false
        );
    }
    
    public function setSkipCheckout($skip) {
        return update_option(
            self::buildOptionName(self::OPTION_NAME_SKIP_CHECKOUT),
            (bool)$skip
        );
    }
    
}
