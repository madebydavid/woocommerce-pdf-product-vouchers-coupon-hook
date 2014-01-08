<div class="wrap">
    <?php screen_icon('basket-avenger'); ?>
    <h2>Basket Avenger Settings</h2>
    <p>The when products in the selfish category are added to the basket, all other items are removed.</p>
    <p>When products not in the selfish category are added to the basket, all products in the selfish category are removed.</p>
    <form id="mbd_wcba_admin_form" method="post" action="options.php">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Selfish Category</th>
                <td>
                    <select id="selfishCategory" name="selfishCategory">
                        <option value="">Please select</option>
                        <?php foreach ($categories = $this->getProductCategories() as $category): ?>
                            <option value="<?php echo $category->term_id?>" 
                                <?php echo ($category->term_id == $this->plugin->getConfiguration()->getSelfishCategoryID()) ? "selected='selected'" : "" ?>><?php echo $category->name?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>
