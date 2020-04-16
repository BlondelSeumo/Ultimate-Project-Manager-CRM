<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('edit') . ' ' . lang('stock_category') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form_validation"  data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/stock/edit_stock_category/<?php if (!empty($stock_category_info->stock_category_id)) echo $stock_category_info->stock_category_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('edit') . ' ' . lang('stock_category') ?>
                    <span
                        class="required">*</span></label>
                <div class="col-sm-5">
                    <input
                        type="text" name="stock_category" required class="form-control"
                        value="<?= (!empty($stock_category_info->stock_category) ? $stock_category_info->stock_category : '') ?>"/>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>
