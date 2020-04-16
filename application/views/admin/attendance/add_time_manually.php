<div class="panel panel-custom" data-collapsed="0">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('add_time_manually') ?></strong>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        </div>
    </div>
    <div class="panel-body">
        <form id="time_validation" data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/attendance/saved_manual_time"
              method="post" class="form-horizontal form-groups-bordered">
            <?php
            $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
            $role = $this->session->userdata('user_type');
            if ($role == 1 || !empty($check_head)) {
                ?>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-4 control-label"><?= lang('employee') ?> <span
                            class="required">*</span></label>
                    <div class="col-sm-6">
                        <select name="user_id" style="width: 100%" id="employee"
                                class="form-control select_box">
                            <?php if (!empty($all_employee)): ?>
                                <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                    <optgroup label="<?php echo $dept_name; ?>">
                                        <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                            <option value="<?php echo $v_employee->user_id; ?>"
                                                <?php
                                                if (!empty($user_id)) {
                                                    $user_id = $user_id;
                                                } else {
                                                    $user_id = $this->session->userdata('user_id');
                                                }
                                                if (!empty($user_id)) {
                                                    echo $v_employee->user_id == $user_id ? 'selected' : '';
                                                }
                                                ?>><?php echo $v_employee->fullname . ' ( ' . $v_employee->designations . ' )' ?></option>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            <?php } else {
                ?>
                <input type="hidden" name="user_id" class="form-control"
                       value="<?= $this->session->userdata('user_id') ?>">
            <?php } ?>
            <div class="form-group" id="border-none">
                <div class="col-sm-6">
                    <label class="control-label col-sm-4"><?= lang('date_in') ?> </label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" name="date_in" class="form-control start_date"
                                   value="<?= date('Y-m-d') ?>" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="control-label col-sm-4"><?= lang('clock_in') ?> </label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" name="clockin_time" class="form-control timepicker"
                                   value="" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-clock-o"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group" id="border-none">
                <div class="col-sm-6">
                    <label class="control-label col-sm-4"><?= lang('day_out') ?> </label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" name="date_out" class="form-control end_date"
                                   value="<?= date('Y-m-d') ?>" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="control-label col-sm-4"><?= lang('clock_out') ?></label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" name="clockout_time" class="form-control timepicker"
                                   value="" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-clock-o"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group" id="border-none">
                <div class="col-sm-6">
                    <label class="control-label col-sm-4"></label>
                    <div class="col-sm-4 ">
                        <button type="submit"
                                class="btn btn-block btn-primary"><?= lang('update') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
