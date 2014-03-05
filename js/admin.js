

jQuery(document).ready(function($) { 
	
    $('#mbd_wcppvch_admin_form').submit(function(e) {

        e.preventDefault();
        $.post(
            WooCommercePDFProductVouchersCouponHook.webServiceUrl,
            {
                'action': WooCommercePDFProductVouchersCouponHook.webServiceAction,
                'nonce': WooCommercePDFProductVouchersCouponHook.webServiceNonce,
                'options': $( this ).serialize() 
            },
            function(response) {
                WooCommercePDFProductVouchersCouponHook_displayPointerMessage('Changes Saved', $);
                
                console.log(response);
                     
                if (response.error) {
                                
                }
          }
                
        );    
        
        
    })
    
    
});


function WooCommercePDFProductVouchersCouponHook_displayPointerMessage(message, $) {

    $('#wpadminbar').pointer({
        content: '<h3>WooCommerce PDF Product Vouchers Coupon Hook</h3><p>' + message + '</p>',
        position: {
            my: 'left top',
            at: 'center bottom',
            offset: '-25 0'
        },
        close: function() {
            
        }
    }).pointer('open');

}
