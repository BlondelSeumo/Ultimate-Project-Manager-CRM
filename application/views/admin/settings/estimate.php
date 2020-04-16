<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form action="<?php echo base_url() ?>admin/settings/save_estimate" enctype="multipart/form-data"
              class="form-horizontal" method="post">
            <div class="panel panel-custom">
                <header class="panel-heading  "><?= lang('estimate_settings') ?></header>
                <div class="panel-body">
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('estimate_prefix') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="estimate_prefix" class="form-control" style="width:260px"
                                   value="<?= config_item('estimate_prefix') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('estimate_start_no') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="estimate_start_no" class="form-control" style="width:260px"
                                   value="<?= config_item('estimate_start_no') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('estimate') . ' ' . lang('number_format') ?></label>
                        <div class="col-lg-5">
                            <input type="text" name="estimate_number_format" class="form-control" style="width:260px"
                                   value="<?php
                                   if (empty(config_item('estimate_number_format'))) {
                                       echo '[' . config_item('estimate_prefix') . ']' . '[yyyy][mm][dd][number]';
                                   } else {
                                       echo config_item('estimate_number_format');
                                   } ?>">
                            <small>ex [<?= config_item('estimate_prefix') ?>] = <?= lang('estimate_prefix') ?>,[yyyy] =
                                'Current Year (<?= date('Y') ?>)'[yy] ='Current Year (<?= date('y') ?>)',[mm] =
                                'Current Month(<?= date('M') ?>)',[m] =
                                'Current Month(<?= date('m') ?>)',[dd] = 'Current Date (<?= date('d') ?>)',[number] =
                                'Invoice Number (<?= sprintf('%04d', config_item('estimate_start_no')) ?>)'
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('increment_estimate_number') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="hidden" value="off" name="increment_estimate_number"/>
                                    <input type="checkbox" <?php
                                    if (config_item('increment_estimate_number') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="increment_estimate_number">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('show_item_tax') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="hidden" value="off" name="show_estimate_tax"/>
                                    <input type="checkbox" <?php
                                    if (config_item('show_estimate_tax') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="show_estimate_tax">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('estimate_terms') ?></label>
                        <div class="col-lg-9">
                            <textarea class="form-control textarea"
                                      name="estimate_terms"><?= config_item('estimate_terms') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('estimate_footer') ?></label>
                        <div class="col-lg-9">
                        <textarea class="form-control textarea"
                                  name="estimate_footer"><?= config_item('estimate_footer') ?></textarea>
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