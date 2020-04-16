<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="panel panel-custom">
    <header class="panel-heading">
        <div class="panel-title"><strong><?= lang('import') . ' ' . lang('project') ?></strong>
            <div class="pull-right hidden-print">
                <div class="pull-right "><a href="<?php echo base_url() ?>assets/sample/project_sample.xlsx"
                                            class="btn btn-primary"><i
                            class="fa fa-download"> <?= lang('download_sample') ?></i></a>
                </div>
            </div>
        </div>
    </header>
    <div class="panel-body">
        <form role="form" enctype="multipart/form-data" id="form"
              action="<?php echo base_url(); ?>admin/projects/save_imported" method="post"
              class="form-horizontal  ">
            <div class="panel-body">
                <div class="form-group">
                    <label for="field-1" class="col-sm-2 control-label">
                        <?= lang('choose_file') ?><span class="required">*</span></label>
                    <div class="col-sm-5">
                        <div style="display: inherit;margin-bottom: inherit" class="fileinput fileinput-new"
                             data-provides="fileinput">
                    <span class="btn btn-default btn-file"><span
                            class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="upload_file" >
                                                        </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('select_client') ?> <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <select name="client_id" class="form-control select_box" style="width: 100%" required="">
                            <option value=""><?= lang('select_client') ?></option>
                            <?php
                            $all_client = $this->db->get('tbl_client')->result();
                            if (!empty($all_client)) {
                                foreach ($all_client as $v_client) {
                                    ?>
                                    <option value="<?= $v_client->client_id ?>" <?php
                                    if (!empty($project_info) && $project_info->client_id == $v_client->client_id) {
                                        echo 'selected';
                                    }
                                    ?>><?= $v_client->name ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('assined_to') ?> <span
                            class="required">*</span></label>
                    <div class="col-sm-9">
                        <div class="checkbox c-radio needsclick">
                            <label class="needsclick">
                                <input id="" <?php
                                if (!empty($leads_info->permission) && $leads_info->permission == 'all') {
                                    echo 'checked';
                                } elseif (empty($leads_info)) {
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
                                if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
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
                if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                    echo 'show';
                }
                ?>" id="permission_user_1">
                    <label for="field-1"
                           class="col-sm-2 control-label"><?= lang('select') . ' ' . lang('users') ?>
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
                                            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                                $get_permission = json_decode($leads_info->permission);
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
                                <div class="action_1 p
                                                <?php

                                if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                    $get_permission = json_decode($leads_info->permission);

                                    foreach ($get_permission as $user_id => $v_permission) {
                                        if ($user_id == $v_user->user_id) {
                                            echo 'show';
                                        }
                                    }

                                }
                                ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                    <label class="checkbox-inline c-checkbox">
                                        <input id="<?= $v_user->user_id ?>" checked type="checkbox"
                                               name="action_1<?= $v_user->user_id ?>[]"
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

                                            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                                $get_permission = json_decode($leads_info->permission);

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
                                        <input <?php if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        } ?> id="<?= $v_user->user_id ?>"
                                            <?php

                                            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                                $get_permission = json_decode($leads_info->permission);
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


                <div class="form-group">
                    <label class="col-lg-2 control-label"></label>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"></i> <?= lang('upload') ?></button>
                    </div>
                </div>
            </div>
    </div>
</div>
