<?php

namespace MadeByDavid\WooCommercePDFProductVouchersCouponHook;

class PluginConfiguration {
    
    const OPTION_NAME_PREFIX = 'MBD\\WCPPVCH';
    const OPTION_NAME_PRODUCT_ID = 'product_id';
    const OPTION_NAME_VOUCHER_PREFIX  = 'voucher_prefix';
    
    public function buildOptionName($optionName) {
        return implode('::', array(
            PluginConfiguration::OPTION_NAME_PREFIX,
            $optionName
        ));
    }
    
    public function getProductID() {
        return get_option(
            self::buildOptionName(self::OPTION_NAME_PRODUCT_ID),
            null
        );
    }
    
    public function setProductID($productID) {
        return update_option(
            self::buildOptionName(self::OPTION_NAME_PRODUCT_ID),
            $productID
        );
    }
    
    public function getVoucherPrefix() {
        return get_option(
            self::buildOptionName(self::OPTION_NAME_VOUCHER_PREFIX),
            null
        );
    }
    
    public function setVoucherPrefix($prefix) {
        return update_option(
            self::buildOptionName(self::OPTION_NAME_VOUCHER_PREFIX),
            $prefix
        );
    }
}
