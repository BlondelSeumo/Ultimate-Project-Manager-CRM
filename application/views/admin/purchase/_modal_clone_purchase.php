<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('clone') . ' ' . lang('invoice') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <?php echo form_open(base_url('admin/purchase/cloned_purchase/' . $purchase_info->purchase_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('supplier') ?> <span
                    class="text-danger">*</span>
            </label>
            <div class="col-lg-7">
                <select class="form-control select_box" style="width: 100%" name="supplier_id" required>
                    <?php
                    if (!empty($all_supplier)) {
                        foreach ($all_supplier as $v_supplier) {
                            if (!empty($purchase_info->supplier_id)) {
                                $supplier_id = $purchase_info->supplier_id;
                            }
                            ?>
                            <option value="<?= $v_supplier->supplier_id ?>"
                                <?php
                                if (!empty($supplier_id)) {
                                    echo $supplier_id == $v_supplier->supplier_id ? 'selected' : null;
                                }
                                ?>
                            ><?= $v_supplier->name ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label
                class="col-lg-3 control-label"><?= lang('purchase') . ' ' . lang('date') ?></label>
            <div class="col-lg-7">
                <div class="input-group">
                    <input type="text" name="purchase_date"
                           class="form-control datepicker"
                           value="<?php
                           if (!empty($purchase_info->purchase_date)) {
                               echo $purchase_info->purchase_date;
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
                           if (!empty($purchase_info->due_date)) {
                               echo $purchase_info->due_date;
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
