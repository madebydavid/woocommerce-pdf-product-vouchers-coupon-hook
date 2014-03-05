<div class="wrap">
    <?php screen_icon('basket-avenger'); ?>
    <h2>PDF Vouchers To Coupon</h2>
    <p>This plugin generates coupons when a voucher product is purchased.</p>
    <p>The configuration is Global for the site - so you should only be selling one type of PDF voucher linked to one type of product.</p>
    <p>Future versions will not have this restriction</p>
    <form id="mbd_wcppvch_admin_form" method="post" action="options.php">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Product</th>
                <td>
                    <select id="product" name="product">
                        <option value="">Please select</option>
                        <?php $productPosts = new WP_Query(['post_type' => 'product', 'nopaging' => true]); ?>
                        <?php $selectedProductID = $this->plugin->getConfiguration()->getProductID(); ?>
                        <?php foreach ($productPosts->posts as $product): ?>
                            <option value="<?php echo $product->ID?>" 
                                <?php echo ($product->ID == $selectedProductID) ? "selected='selected'" : "" ?>><?php echo $product->post_title;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Voucher Code Prefix</th>
                <td>
                    <input type="text" name="voucherCodePrefix" value="<?php echo ($this->plugin->getConfiguration()->getVoucherPrefix())?>" />
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>
