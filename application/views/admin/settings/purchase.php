<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form action="<?php echo base_url() ?>admin/settings/save_purchase" enctype="multipart/form-data"
              class="form-horizontal" method="post">
            <div class="panel panel-custom">
                <header class="panel-heading  "><?= lang('purchase') . ' ' . lang('settings') ?></header>
                <div class="panel-body">
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('purchase') . ' ' . lang('prefix') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="purchase_prefix" class="form-control" style="width:260px"
                                   value="<?= config_item('purchase_prefix') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('purchase') . ' ' . lang('start_no') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="purchase_start_no" class="form-control" style="width:260px"
                                   value="<?= config_item('purchase_start_no') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('purchase') . ' ' . lang('number_format') ?></label>
                        <div class="col-lg-5">
                            <input type="text" name="purchase_number_format" class="form-control" style="width:260px"
                                   value="<?php
                                   if (empty(config_item('purchase_number_format'))) {
                                       echo '[' . config_item('purchase_prefix') . ']' . '[yyyy][mm][dd][number]';
                                   } else {
                                       echo config_item('purchase_number_format');
                                   } ?>">
                            <small>ex [<?= config_item('purchase_prefix') ?>] = <?= lang('purchase_prefix') ?>,[yyyy] =
                                'Current Year (<?= date('Y') ?>)'[yy] ='Current Year (<?= date('y') ?>)',[mm] =
                                'Current Month(<?= date('M') ?>)',[m] =
                                'Current Month(<?= date('m') ?>)',[dd] = 'Current Date (<?= date('d') ?>)',[number] =
                                'Invoice Number (<?= sprintf('%04d', config_item('purchase_start_no')) ?>)'
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('return_stock') . ' ' . lang('prefix') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="return_stock_prefix" class="form-control" style="width:260px"
                                   value="<?= config_item('return_stock_prefix') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('return_stock') . ' ' . lang('start_no') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="return_stock_start_no" class="form-control" style="width:260px"
                                   value="<?= config_item('return_stock_start_no') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('return_stock') . ' ' . lang('number_format') ?></label>
                        <div class="col-lg-5">
                            <input type="text" name="return_stock_number_format" class="form-control"
                                   style="width:260px"
                                   value="<?php
                                   if (empty(config_item('return_stock_number_format'))) {
                                       echo '[' . config_item('return_stock_prefix') . ']' . '[yyyy][mm][dd][number]';
                                   } else {
                                       echo config_item('return_stock_number_format');
                                   } ?>">
                            <small>ex [<?= config_item('return_stock_prefix') ?>] = <?= lang('return_stock_prefix') ?>
                                ,[yyyy] ='Current Year (<?= date('Y') ?>)'[yy] ='Current Year (<?= date('y') ?>)',[mm] =
                                'Current Month(<?= date('M') ?>)',[m] =
                                'Current Month(<?= date('m') ?>)',[dd] = 'Current Date (<?= date('d') ?>)',[number] =
                                'Invoice Number (<?= sprintf('%04d', config_item('return_stock_start_no')) ?>)'
                            </small>
                        </div>
                    </div>
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('purchase_notes') ?></label>
                        <div class="col-lg-9">
                            <textarea class="form-control textarea"
                                      name="purchase_notes"><?= config_item('purchase_notes') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 control-label"></div>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Form -->
</div>