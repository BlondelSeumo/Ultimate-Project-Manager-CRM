<?php include_once 'asset/admin-ajax.php'; ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('request_advance_salary') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form data-parsley-validate="" novalidate="" role="form" enctype="multipart/form-data"
              action="<?php echo base_url() ?>admin/payroll/save_advance_salary/<?php
              if (!empty($advance_salary->advance_salary_id)) {
                  echo $advance_salary->advance_salary_id;
              }
              ?>" method="post" class="form-horizontal">

            <?php if ($this->session->userdata('user_type') == 1) { ?>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('employee') ?> <span
                                class="required">*</span></label>
                    <div class="col-sm-7">
                        <select name="user_id" style="width: 100%" id="user_id" class="form-control select_box">
                            <option value=""><?= lang('select_employee') ?>...</option>
                            <?php if (!empty($all_employee)): ?>
                                <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                    <optgroup label="<?php echo $dept_name; ?>">
                                        <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                            <option value="<?php echo $v_employee->user_id; ?>"
                                                <?php
                                                if (!empty($advance_salary->user_id)) {
                                                    $user_id = $advance_salary->user_id;
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
            <?php } else { ?>
                <input type="hidden" id="user_id" value="<?php echo $this->session->userdata('user_id') ?>">
            <?php } ?>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('amount') ?> <span
                            aria-required="true" class="required"> *</span></label>
                <div class="col-sm-7">
                    <input required type="text" data-parsley-type="number" name="advance_amount" class="form-control"
                           onchange="check_advance_amount(this.value)">
                    <div class="required" id="advance_amount"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('deduct_month') ?> <span
                            aria-required="true"
                            class="required"> *</span></label>
                <div class="col-sm-7">
                    <div class="input-group ">
                        <input required class="form-control monthyear" value="<?php
                        if (!empty($advance_salary->deduct_month)) {
                            echo $advance_salary->deduct_month;
                        } else {
                            echo date('Y-m', strtotime('+1 month'));
                        }
                        ?>" name="deduct_month" type="text">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('reason') ?></label>

                <div class="col-sm-7">
                <textarea name="reason" rows="4" class="form-control" id="field-1"
                          placeholder="<?= lang('enter_your') . ' ' . lang('reason') ?>"><?php
                    if (!empty($advance_salary->reason)) {
                        echo $advance_salary->reason;
                    }
                    ?></textarea>
                </div>
            </div>

            <div class="form-group margin">
                <label for="field-1" class="col-sm-3 control-label"></label>
                <div class="col-sm-3">
                    <button id="sbtn" type="submit" name="sbtn" value="1"
                            class="btn btn-primary btn-block"><?= lang('save') ?></button>
                </div>
            </div>

        </form>
    </div>
</div>


