<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('all_users') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form_validation"
              action="<?php echo base_url() ?>admin/opportunities/update_member/<?php if (!empty($opportunities_info->opportunities_id)) echo $opportunities_info->opportunities_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('assined_to') ?> <span
                        class="required">*</span></label>
                <div class="col-sm-9">
                    <div class="checkbox c-radio needsclick">
                        <label class="needsclick">
                            <input id="" <?php
                            if (!empty($opportunities_info->permission) && $opportunities_info->permission == 'all') {
                                echo 'checked';
                            } elseif (empty($opportunities_info)) {
                                echo 'checked';
                            }
                            ?> type="radio" name="permission" value="everyone">
                            <span class="fa fa-circle"></span><?= lang('everyone') ?>
                            <i title="<?= lang('permission_for_all') ?>"
                               class="fa fa-question-circle" data-toggle="tooltip"
                               data-placement="top"></i>
                        </label>
                    </div>
                    <div class="checkbox c-radio needsclick">
                        <label class="needsclick">
                            <input id="" <?php
                            if (!empty($opportunities_info->permission) && $opportunities_info->permission != 'all') {
                                echo 'checked';
                            }
                            ?> type="radio" name="permission" value="custom_permission"
                            >
                            <span class="fa fa-circle"></span><?= lang('custom_permission') ?> <i
                                title="<?= lang('permission_for_customization') ?>"
                                class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top"></i>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group <?php
            if (!empty($opportunities_info->permission) && $opportunities_info->permission != 'all') {
                echo 'show';
            }
            ?>" id="permission_user">
                <label for="field-1"
                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                    <span
                        class="required">*</span></label>
                <div class="col-sm-9">
                    <?php
                    if (!empty($assign_user)) {
                        foreach ($assign_user as $key => $v_user) {

                            if ($v_user->role_id == 1) {
                                $disable = true;
                                $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                            } else {
                                $disable = false;
                                $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                            }

                            ?>
                            <div class="checkbox c-checkbox needsclick">
                                <label class="needsclick">
                                    <input type="checkbox"
                                        <?php
                                        if (!empty($opportunities_info->permission) && $opportunities_info->permission != 'all') {
                                            $get_permission = json_decode($opportunities_info->permission);
                                            foreach ($get_permission as $user_id => $v_permission) {
                                                if ($user_id == $v_user->user_id) {
                                                    echo 'checked';
                                                }
                                            }

                                        }
                                        ?>
                                           value="<?= $v_user->user_id ?>"
                                           name="assigned_to[]"
                                           class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                </label>

                            </div>
                            <div class="action p
                                                <?php

                            if (!empty($opportunities_info->permission) && $opportunities_info->permission != 'all') {
                                $get_permission = json_decode($opportunities_info->permission);

                                foreach ($get_permission as $user_id => $v_permission) {
                                    if ($user_id == $v_user->user_id) {
                                        echo 'show';
                                    }
                                }

                            }
                            ?>
                                                " id="action_<?= $v_user->user_id ?>">
                                <label class="checkbox-inline c-checkbox">
                                    <input <?php if (!empty($disable)) {
                                        echo 'disabled' . ' ' . 'checked';
                                    } ?> id="<?= $v_user->user_id ?>" checked type="checkbox"
                                           name="action_<?= $v_user->user_id ?>[]"
                                           disabled
                                           value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                </label>
                                <label class="checkbox-inline c-checkbox">
                                    <input <?php if (!empty($disable)) {
                                        echo 'disabled' . ' ' . 'checked';
                                    } ?> id="<?= $v_user->user_id ?>"
                                        <?php

                                        if (!empty($opportunities_info->permission) && $opportunities_info->permission != 'all') {
                                            $get_permission = json_decode($opportunities_info->permission);

                                            foreach ($get_permission as $user_id => $v_permission) {
                                                if ($user_id == $v_user->user_id) {
                                                    if (in_array('edit', $v_permission)) {
                                                        echo 'checked';
                                                    };

                                                }
                                            }

                                        }
                                        ?>
                                           type="checkbox"
                                           value="edit" name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                </label>
                                <label class="checkbox-inline c-checkbox">
                                    <input id="<?= $v_user->user_id ?>"
                                        <?php

                                        if (!empty($opportunities_info->permission) && $opportunities_info->permission != 'all') {
                                            $get_permission = json_decode($opportunities_info->permission);
                                            foreach ($get_permission as $user_id => $v_permission) {
                                                if ($user_id == $v_user->user_id) {
                                                    if (in_array('delete', $v_permission)) {
                                                        echo 'checked';
                                                    };
                                                }
                                            }

                                        }
                                        ?>
                                           name="action_<?= $v_user->user_id ?>[]"
                                           type="checkbox"
                                           value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                </label>
                                <input id="<?= $v_user->user_id ?>" type="hidden"
                                       name="action_<?= $v_user->user_id ?>[]" value="view">

                            </div>


                            <?php
                        }
                    }
                    ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>
