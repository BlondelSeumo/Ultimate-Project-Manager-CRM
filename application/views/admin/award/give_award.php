<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title"
            id="myModalLabel"><?= lang('give_award')

            ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form data-parsley-validate="" novalidate="" action="<?php echo base_url() ?>admin/award/save_employee_award/<?php
        if (!empty($award_info->employee_award_id)) {
            echo $award_info->employee_award_id;
        }
        ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('employee') ?> <span
                        class="required">*</span></label>
                <div class="col-sm-7">
                    <select name="user_id" style="width: 100%" id="employee" required
                            class="form-control select_box">
                        <?php if (!empty($all_employee)): ?>
                            <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                <optgroup label="<?php echo $dept_name; ?>">
                                    <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                        <option value="<?php echo $v_employee->user_id; ?>"
                                            <?php
                                            if (!empty($award_info->user_id)) {
                                                $user_id = $award_info->user_id;
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
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('award_name') ?>
                    <span class="required">*</span></label>

                <div class="col-sm-7">
                    <input required type="text" name="award_name"
                           placeholder="<?= lang('enter') . ' ' . lang('award_name') ?>"
                           class="form-control" value="<?php
                    if (!empty($award_info->award_name)) {
                        echo $award_info->award_name;
                    }
                    ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1"
                       class="col-sm-3 control-label"><?= lang('gift_item') ?></label>

                <div class="col-sm-7">
                    <input type="text" name="gift_item" class="form-control"
                           placeholder="<?= lang('enter') . ' ' . lang('gift_item') ?>" value="<?php
                    if (!empty($award_info->gift_item)) {
                        echo $award_info->gift_item;
                    }
                    ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1"
                       class="col-sm-3 control-label"><?= lang('cash_price') ?></label>

                <div class="col-sm-7">
                    <input type="text" data-parsley-type="number" name="award_amount"
                           placeholder="<?= lang('enter') . ' ' . lang('cash_price') ?>"
                           class="form-control" value="<?php
                    if (!empty($award_info->award_amount)) {
                        echo $award_info->award_amount;
                    }
                    ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('select_month') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <div  class="input-group">
                        <input required type="text" name="award_date"
                               placeholder="<?= lang('enter') . ' ' . lang('month') ?>"
                               class="form-control monthyear" value="<?php
                        if (!empty($award_info->award_date)) {
                            echo $award_info->award_date;
                        }
                        ?>" data-format="dd-mm-yyyy">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('award_date') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <div class="input-group">
                        <input required type="text" name="given_date"
                               placeholder="<?= lang('enter') . ' ' . lang('award_date') ?>"
                               class="form-control datepicker" value="<?php
                        if (!empty($award_info->given_date)) {
                            echo $award_info->given_date;
                        }
                        ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-7">
                    <button type="submit" id="sbtn" name="sbtn" value="1"
                            class="btn btn-primary"><?= lang('save') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
