<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('user') . ' ' . lang('details') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/user/update_details/<?php if (!empty($profile_info->account_details_id)) echo $profile_info->account_details_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('emp_id') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <input type="text" name="employment_id" required
                           placeholder="<?= lang('enter') . ' ' . lang('employment_id') ?>"
                           class="form-control" value="<?php
                    if (!empty($profile_info->employment_id)) {
                        echo $profile_info->employment_id;
                    }
                    ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('joining_date') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <div class="input-group">
                        <input type="text" name="joining_date" required
                               placeholder="<?= lang('enter') . ' ' . lang('joining_date') ?>"
                               class="form-control datepicker" value="<?php
                        if (!empty($profile_info->joining_date)) {
                            echo $profile_info->joining_date;
                        }
                        ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('gender') ?>
                    <span class="required">*</span></label>
                <div class="col-sm-7">
                    <select name="gender" class="form-control" required>
                        <option
                            value="male" <?= (!empty($profile_info->gender) && $profile_info->gender == 'male' ? 'selected' : null) ?>><?= lang('male') ?></option>
                        <option
                            value="female" <?= (!empty($profile_info->gender) && $profile_info->gender == 'female' ? 'selected' : null) ?>><?= lang('female') ?></option>

                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('date_of_birth') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <div class="input-group">
                        <input type="text" name="date_of_birth"
                               placeholder="<?= lang('enter') . ' ' . lang('date_of_birth') ?>"
                               class="form-control datepicker" required value="<?php
                        if (!empty($profile_info->date_of_birth)) {
                            echo $profile_info->date_of_birth;
                        }
                        ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('maratial_status') ?>
                    <span class="required">*</span></label>

                <div class="col-sm-7">
                    <select name="maratial_status" class="form-control" required>
                        <option
                            value="married" <?= (!empty($profile_info->maratial_status) && $profile_info->maratial_status == 'married' ? 'selected' : null) ?>><?= lang('married') ?></option>
                        <option
                            value="unmarried" <?= (!empty($profile_info->maratial_status) && $profile_info->maratial_status == 'unmarried' ? 'selected' : null) ?>><?= lang('unmarried') ?></option>
                        <option
                            value="widowed" <?= (!empty($profile_info->maratial_status) && $profile_info->maratial_status == 'widowed' ? 'selected' : null) ?>><?= lang('widowed') ?></option>
                        <option
                            value="divorced" <?= (!empty($profile_info->maratial_status) && $profile_info->maratial_status == 'divorced' ? 'selected' : null) ?>><?= lang('divorced') ?></option>

                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('fathers_name') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <input type="text" name="father_name" required
                           placeholder="<?= lang('enter') . ' ' . lang('fathers_name') ?>"
                           class="form-control" value="<?php
                    if (!empty($profile_info->father_name)) {
                        echo $profile_info->father_name;
                    }
                    ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('mother_name') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <input type="text" name="mother_name" required
                           placeholder="<?= lang('enter') . ' ' . lang('mother_name') ?>"
                           class="form-control" value="<?php
                    if (!empty($profile_info->mother_name)) {
                        echo $profile_info->mother_name;
                    }
                    ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"><strong><?= lang('phone') ?> </strong></label>
                <div class="col-sm-5">
                    <input type="text" class="input-sm form-control" value="<?php
                    if (!empty($profile_info)) {
                        echo $profile_info->phone;
                    }
                    ?>" name="phone" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_phone') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong><?= lang('mobile') ?> </strong></label>
                <div class="col-sm-5">
                    <input type="text" class="input-sm form-control" value="<?php
                    if (!empty($profile_info)) {
                        echo $profile_info->mobile;
                    }
                    ?>" name="mobile"
                           placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_mobile') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong><?= lang('skype_id') ?> </strong></label>
                <div class="col-sm-5">
                    <input type="text" class="input-sm form-control" value="<?php
                    if (!empty($profile_info)) {
                        echo $profile_info->skype;
                    }
                    ?>" name="skype" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_skype') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong><?= lang('passport') ?> </strong></label>
                <div class="col-sm-5">
                    <input type="text" class="input-sm form-control" value="<?php
                    if (!empty($profile_info)) {
                        echo $profile_info->passport;
                    }
                    ?>" name="passport">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong><?= lang('present_address') ?> </strong></label>
                <div class="col-sm-5">
                        <textarea class="input-sm form-control" value="" name="present_address"><?php
                            if (!empty($profile_info)) {
                                echo $profile_info->present_address;
                            }
                            ?></textarea>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>

