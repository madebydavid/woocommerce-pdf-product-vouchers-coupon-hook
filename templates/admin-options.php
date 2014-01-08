<div class="wrap">
    <?php screen_icon('basket-avenger'); ?>
    
    <h2>Basket Avenger Settings</h2>
    
    <form id="mbd_wcbr_admin_form" method="post" action="options.php">
    
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Something</th>
                <td><input type="text" name="reschedulePeriodDays" value="<?php //echo $this->plugin->getConfiguration()->getReschedulePeriodDays() ?>" /></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>
