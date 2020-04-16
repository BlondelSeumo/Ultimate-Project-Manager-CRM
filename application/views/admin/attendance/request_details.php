<?php echo message_box('success'); ?>

<div class="panel panel-custom">
    <!-- Default panel contents -->

    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
        <div class="panel-title">
            <strong><?= lang('change_request_details') ?></strong>
        </div>
    </div>
    <form method="post"
          action="<?php echo base_url() ?>admin/attendance/set_time_status/<?php echo $clock_history->clock_history_id ?>"
    <div class="panel-body form-horizontal">
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('emp_id') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $clock_history->employment_id; ?></p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('name') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $clock_history->fullname; ?></p>
            </div>
        </div>
        <div style="margin-top: 80px;margin-bottom: 20px;">
            <div class="col-md-12">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('old_time_in') ?> : </strong></label>
                </div>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php
                        if ($clock_history->clockin_time != "00:00:00") {
                            echo '<span class="text-danger">' . display_time($clock_history->clockin_time) . '</span>';
                        }
                        ?></p>
                </div>
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('new_time_in') ?> : </strong></label>
                </div>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php
                        if ($clock_history->clockin_time != "00:00:00") {
                            echo '<span class="text-danger">' . display_time($clock_history->clockin_time) . '</span>';
                        }
                        ?></p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('old_time_out') ?> : </strong></label>
                </div>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php
                        if ($clock_history->clockout_time != "00:00:00") {
                            echo '<span class="text-danger">' . display_time($clock_history->clockout_time) . '</span>';
                        }
                        ?></p>
                </div>
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('new_time_out') ?> : </strong></label>
                </div>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php
                        if ($clock_history->clockout_time != "00:00:00") {
                            echo '<span class="text-danger">' . display_time($clock_history->clockout_time) . '</span>';
                        }
                        ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('reason') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $clock_history->reason; ?></p>
            </div>
        </div>
        <?php
        $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
        $role = $this->session->userdata('user_type');
        if ($role == 1 || !empty($check_head)) {
            if ($clock_history->status != 2) {
                ?>
                <div class="col-md-12 margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('action') ?> : </strong></label>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-control" name="status">
                            <option
                                value="1" <?php echo $clock_history->status == 1 ? 'selected' : '' ?>> <?= lang('pending') ?></option>
                            <option
                                value="2" <?php echo $clock_history->status == 2 ? 'selected' : '' ?>> <?= lang('accepted') ?></option>
                            <option
                                value="3" <?php echo $clock_history->status == 3 ? 'selected' : '' ?>> <?= lang('rejected') ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 mt">
                    <div class="col-sm-4">

                    </div>
                    <div class="col-sm-4 margin">
                        <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
                    </div>
                </div>
            <?php } else {
                ?>
                <div class="col-md-12">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('status') ?> : </strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><span class="label label-success"><?= lang('accepted') ?></span>
                        </p>
                    </div>
                </div>
            <?php }
        } else {
            if ($clock_history->status == 1) {
                $label = lang('pending');
                $text = 'warning';
            }
            if ($clock_history->status == 2) {
                $label = lang('accepted');
                $text = 'success';
            }
            if ($clock_history->status == 3) {
                $label = lang('rejected');
                $text = 'danger';
            }
            ?>
            <div class="col-md-12">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('status') ?> : </strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><span
                            class="label label-<?= $text ?>"><?= $label ?></span>
                    </p>
                </div>
            </div>
        <?php }
        ?>
        <!--- Hidden input data  --->
        <input type="hidden" name="clock_id" value="<?php echo $clock_history->clock_id ?>">
        <input type="hidden" name="clockin_time" value="<?php
        if ($clock_history->clockin_edit != "00:00:00") {
            echo $clock_history->clockin_edit;
        }
        ?>">
        <input type="hidden" name="clockout_time" value="<?php
        if ($clock_history->clockout_edit != "00:00:00") {
            echo $clock_history->clockout_edit;
        }
        ?>">
    </div>
</div>






