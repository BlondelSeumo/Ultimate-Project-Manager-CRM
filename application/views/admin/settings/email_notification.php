
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$error_message = $this->session->userdata('error_message');
$error_type = $this->session->userdata('error_type');
if (!empty($error_message)) {
    foreach ($error_message as $key => $v_message) {
        ?>
        <div class="alert-<?php echo $error_type[$key] ?>"
             style="padding: 8px;margin-bottom: 21px;border: 1px solid transparent;}">
            <?php echo $v_message; ?>
        </div>
        <?php
    }
}
$this->session->unset_userdata('error_message');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="wrap-fpanel">
            <div class="panel panel-custom" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?php echo lang('email') . ' ' . lang('notification') . ' ' . lang('settings') ?></strong>
                    </div>
                </div>
                <div class="panel-body">

                    <form id="form" action="<?php echo base_url() ?>admin/settings/save_email_notification"
                          method="post"
                          class="form-horizontal form-groups-bordered">
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('send') . ' ' . lang('clock_email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="send_clock_email" value="1" <?php
                                $send_clock_email = config_item('send_clock_email');
                                if (!empty($send_clock_email) && $send_clock_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('leave') . ' ' . lang('email') ?>
                                : <span
                                        class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="leave_email" value="1" <?php
                                $leave_email = config_item('leave_email');
                                if (!empty($leave_email) && $leave_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('overtime') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="overtime_email" value="1" <?php

                                $overtime_email = config_item('overtime_email');
                                if (!empty($overtime_email) && $overtime_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('payslip') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="payslip_email" value="1" <?php

                                $payslip_email = config_item('payslip_email');
                                if (!empty($payslip_email) && $payslip_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('advance_salary') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="advance_salary_email" value="1" <?php

                                $advance_salary_email = config_item('advance_salary_email');
                                if (!empty($advance_salary_email) && $advance_salary_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('award') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="award_email" value="1" <?php

                                $award_email = config_item('award_email');
                                if (!empty($award_email) && $award_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('job_circular') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="job_circular_email" value="1" <?php

                                $job_circular_email = config_item('job_circular_email');
                                if (!empty($job_circular_email) && $job_circular_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('announcements') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="announcements_email" value="1" <?php

                                $announcements_email = config_item('announcements_email');
                                if (!empty($announcements_email) && $announcements_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('training') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="training_email" value="1" <?php

                                $training_email = config_item('training_email');
                                if (!empty($training_email) && $training_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('expense') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="expense_email" value="1" <?php

                                $expense_email = config_item('expense_email');
                                if (!empty($expense_email) && $expense_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('deposit') . ' ' . lang('email') ?>
                                : <span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <input data-toggle="toggle" name="deposit_email" value="1" <?php

                                $deposit_email = config_item('deposit_email');
                                if (!empty($deposit_email) && $deposit_email == 1) {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success btn-sm"
                                       data-offstyle="danger btn-sm" type="checkbox">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"></label>
                            <div class="col-sm-5">
                                <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('update') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>