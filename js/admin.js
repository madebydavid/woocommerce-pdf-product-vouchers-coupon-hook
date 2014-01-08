

jQuery(document).ready(function($) { 
    
    $('#mbd_wcba_admin_form').submit(function(e) {

        e.preventDefault();
        $.post(
            WooCommerceBasketAvenger.webServiceUrl,
            {
                'action': WooCommerceBasketAvenger.webServiceAction,
                'nonce': WooCommerceBasketAvenger.webServiceNonce,
                'options': $( this ).serialize() 
            },
            function(response) {
                WooCommerceBasketAvenger_displayPointerMessage('Changes Saved', $);
                console.log(response);
                     
                if (response.error) {
                                
                }
          }
                
        );    
        
        
    })
    
    
});


function WooCommerceBasketAvenger_displayPointerMessage(message, $) {

    $('#wpadminbar').pointer({
        content: '<h3>WooCommerce Basket Avenger</h3><p>' + message + '</p>',
        position: {
            my: 'left top',
            at: 'center bottom',
            offset: '-25 0'
        },
        close: function() {
            
        }
    }).pointer('open');

}
