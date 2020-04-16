<script type="text/javascript">
    $(document).ready(function () {
        init_selectpicker();
    });
</script>
<?php
if (!empty($invoices_to_merge) && count($invoices_to_merge) > 0) { ?>
    <div class="form-group "
         id="border-none">
        <label for="field-1"
               class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('invoices') ?>
            <span class="required">*</span></label>
        <div class="col-sm-7">
            <select name="invoices_id" data-live-search="true" id="getInvIitems"
                    class="selectpicker m0 getItemsInfo" data-width="100%">
                <option value=""><?= lang('none') ?></option>
                <?php foreach ($invoices_to_merge as $_inv) {
                    ?>
                    <option value="<?= $_inv->invoices_id ?>"
                    ><?= $_inv->reference_no ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php }
if (!empty($purchases_to_merge) && count($purchases_to_merge) > 0) { ?>
    <div class="form-group "
         id="border-none">
        <label for="field-1"
               class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('purchase') ?>
            <span class="required">*</span></label>
        <div class="col-sm-7">
            <select name="purchase_id" data-live-search="true" id="getPurIitems"
                    class="selectpicker m0 getItemsInfo" data-width="100%">
                <option value=""><?= lang('none') ?></option>
                <?php foreach ($purchases_to_merge as $purchases) {
                    ?>
                    <option value="<?= $purchases->purchase_id ?>"
                    ><?= $purchases->reference_no ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php } ?>
