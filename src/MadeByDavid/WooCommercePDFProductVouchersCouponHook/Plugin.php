<?php

namespace MadeByDavid\WooCommercePDFProductVouchersCouponHook;

class Plugin {
    
    const TRANSLATE_DOMAIN = 'madebydavid-woocommercepdfproductvoucherscouponhook';
    const COUPON_ORDER_ID_META_KEY = 'mbd-wcppvch-order-id';
    const DEBUG = true;
    
    private $configuration;
    private $admin;
    
    function __construct() {
        
        add_action('init', array($this, 'init'), 0);
        
        /* class to load the settings */
        $this->configuration = new PluginConfiguration();
        
        add_filter('woocommerce_voucher_number', array($this, 'getVoucherNumber'), 0, 2);
        
        if (is_admin()) {
            $this->admin = new PluginAdmin($this);
        }
        
    }
    
    public function init(){
        
    }
    
    public static function log($message) {
        if (self::DEBUG) {
            error_log('PDFVOUCHER:'.$message);
        }
    }
    
    public function getVoucherNumber($voucherNumber, $wcVoucher) {
        
        self::log("getVoucherNumber:".$voucherNumber);
        self::log("expiry:".$wcVoucher->get_expiry());
        
        $orderId = $wcVoucher->get_order()->id;
        
        /* if no prefix set - do nothing */
        if (0 == strlen($prefix = $this->getConfiguration()->getVoucherPrefix())) {
            return $voucherNumber;
        }
        
        /* if no product set - do nothing */
        if (null == ($productId = $this->getConfiguration()->getProductID())) {
            return $voucherNumber;
        }
        
        if (false == ($coupon = $this->getCouponFromVoucherOrderId($voucherNumber))) {
            self::log("coupon not found - need to create");
            $coupon = $this->generateCouponForVoucherOrder(
                $voucherNumber,
                $prefix,
                $productId,
                $wcVoucher->get_expiry()
            );
        } else {
            self::log("existing coupon found");
        }
        
        self::log("coupon code is: ".$coupon->post_title);
        
        return $coupon->post_title;
        
    }
    
    private function generateCouponForVoucherOrder($orderId, $voucherPrefix, $productId, $expiryDays) {

        self::log("generating coupon for orderId:$orderId with prefix:$voucherPrefix for product:$productId");
        
        /* TODO: we should check for duplicates */
        $voucherCode = $voucherPrefix. strtoupper(hash('crc32', mt_rand()));
        
        $coupon = array(
            'post_title' => $voucherCode,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );
        
        $newCouponId = wp_insert_post($coupon);
        
        update_post_meta($newCouponId, 'discount_type', 'fixed_cart');
        
        $orderIdParts = explode('-', $orderId);
        
        if (2 == count($orderIdParts)) {
            
            $fixedOrderId = $orderIdParts[0];
            $voucherNumber = $orderIdParts[1];
            
            /* determine the quantity for this voucher and make sure the coupon has
             * the correct coupon_amount set based on that */
            $order = new \WC_Order($fixedOrderId);
            $orderItems = $order->get_items();
            
            $voucherOrderItemQuantity = null;
            
            foreach ($orderItems as $item) {
                
                if (!array_key_exists('voucher_number', $item)) {
                    continue;
                }
                
                if ($item['voucher_number'] == $voucherNumber) {
                    $voucherOrderItemQuantity = $item['qty'];
                }
                
            }
            
        } else {
            /* From looking at the db, this has happened - will log and use default
             * Will investigate further */
        }
        
        if (null === $voucherOrderItemQuantity) {
            /* this should not happen - enhanced logging and 
             * error reporting whilst still beta testing this */
            $error =
            "----ERROR GETTING ORDER ITEM QUANTITY ----\n\n".
            "orderId: $orderId\n".
            "fixedOrderId: $fixedOrderId\n".
            "productId: $productId\n";
            
            error_log($error);
            
            @wp_mail(
                get_option('admin_email'),
                'WooCommerce PDF Product Vouchers Coupon Hook Error',
                $error
            );
            
            /* default to 1 so that we don't kill the order process */
            $voucherOrderItemQuantity = 1;
            
        }
        
        $couponAmount  = get_post_meta($productId, '_regular_price', true) * $voucherOrderItemQuantity;
        
        update_post_meta($newCouponId, 'coupon_amount', $couponAmount);
        update_post_meta($newCouponId, 'individual_use', 'yes');
        update_post_meta($newCouponId, 'product_ids', $productId);
        update_post_meta($newCouponId, 'exclude_product_ids', '');
        update_post_meta($newCouponId, 'usage_limit', '1');
        update_post_meta($newCouponId, 'expiry_date', '');
        update_post_meta($newCouponId, 'apply_before_tax', 'yes');
        update_post_meta($newCouponId, 'free_shipping', 'no');
        
        $today = new \DateTime();
        $today->add(\DateInterval::createFromDateString($expiryDays.' days'));
        update_post_meta($newCouponId, 'expiry_date', $today->format('Y-m-d'));
        
        update_post_meta($newCouponId, self::COUPON_ORDER_ID_META_KEY, $orderId);
        
        return $this->getCouponFromVoucherOrderId($orderId);
    }
    
    private function getCouponFromVoucherOrderId($orderId) {
        
        $couponQuery = new \WP_Query(
            "post_type=shop_coupon&meta_key=".self::COUPON_ORDER_ID_META_KEY."&meta_value=".$orderId
        );
        
        foreach ($couponQuery->posts as $coupon) {
            return $coupon;
        }
        
        return false;
    }
    
    public function getConfiguration() {
        return $this->configuration;
    }
    
}
