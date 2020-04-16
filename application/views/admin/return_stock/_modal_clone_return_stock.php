<?php include_once 'assets/admin-ajax.php'; ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('clone') . ' ' . lang('invoice') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <?php echo form_open(base_url('admin/return_stock/cloned_return_stock/' . $return_stock_info->return_stock_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
        <div class="form-group" id="border-none">
            <label for="field-1"
                   class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
            <div class="col-sm-7">
                <select name="module" class="form-control select_box"
                        id="check_related" required
                        onchange="get_related_moduleName(this.value,true)" style="width: 100%">
                    <option> <?= lang('none') ?> </option>
                    <option
                            value="supplier" <?= (!empty($supplier) ? 'selected' : '') ?>> <?= lang('supplier') ?> </option>
                    <option
                            value="client" <?= (!empty($client_id) ? 'selected' : '') ?>> <?= lang('client') ?> </option>
                </select>
            </div>
        </div>
        <div class="form-group" id="related_to">

        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('return_stock') . ' ' . lang('date') ?></label>
            <div class="col-lg-7">
                <div class="input-group">
                    <input type="text" name="return_stock_date"
                           class="form-control datepicker"
                           value="<?php
                           if (!empty($return_stock_info->return_stock_date)) {
                               echo $return_stock_info->return_stock_date;
                           } else {
                               echo date('Y-m-d H:i');
                           }
                           ?>"
                           data-date-format="<?= config_item('date_picker_format'); ?>">
                    <div class="input-group-addon">
                        <a href="#"><i class="fa fa-calendar"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('due_date') ?></label>
            <div class="col-lg-7">
                <div class="input-group">
                    <input type="text" name="due_date"
                           class="form-control datepicker"
                           value="<?php
                           if (!empty($return_stock_info->due_date)) {
                               echo $return_stock_info->due_date;
                           } else {
                               echo strftime(date('Y-m-d H:i', strtotime("+" . config_item('invoices_due_after') . " days")));
                           }
                           ?>"
                           data-date-format="<?= config_item('date_picker_format'); ?>">
                    <div class="input-group-addon">
                        <a href="#"><i class="fa fa-calendar"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary"><?= lang('clone') ?></button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
