<?php echo message_box('success') ?>
<div class="row" xmlns="http://www.w3.org/1999/html">
    <!-- Start Form -->
    <section class="col-lg-12">
        <form role="form" id="form" action="<?php echo base_url(); ?>admin/settings/save_tickets" method="post"
              class="form-horizontal  ">
            <section class="panel panel-custom">
                <header class="panel-heading  "><?= lang('tickets_settings') ?></header>
                <div class="panel-body">
                    <?php echo validation_errors(); ?>
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_department') ?></label>
                        <div class="col-lg-5">
                            <select name="default_department" style="width: 100%" class="form-control select_box">
                                <?php
                                $department_info = $this->db->get('tbl_departments')->result();
                                if (!empty($department_info)) {
                                    foreach ($department_info as $v_department) : ?>
                                        <option
                                            value="<?= $v_department->departments_id ?>"<?= (config_item('default_department') == $v_department->departments_id ? ' selected="selected"' : '') ?>><?= $v_department->deptname ?></option>
                                    <?php endforeach;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('default_status') ?></label>
                        <div class="col-lg-5">
                            <select name="default_status" class="form-control">
                                <?php
                                $status_info = $this->db->get('tbl_status')->result();
                                if (!empty($status_info)) {
                                    foreach ($status_info as $v_status) {
                                        ?>
                                        <option
                                            value="<?= $v_status->status ?>"<?= (config_item('default_status') == $v_status->status ? ' selected="selected"' : '') ?>><?= lang($v_status->status) ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('default_priority') ?></label>
                        <div class="col-lg-5">
                            <?php
                            $all_priority = $this->db->get('tbl_priority')->result();
                            foreach ($all_priority as $priority) {
                                $options[$priority->priority] = $priority->priority;
                            }
                            echo form_dropdown('default_priority', $options, config_item('default_priority'), 'style="width:100%" class="form-control"'); ?>
                        </div>
                        <div class="col-lg-2">
                            <a data-toggle="modal" data-target="#myModal"
                               href="<?= base_url() ?>admin/settings/manage_status/priority"
                               class=""><?= lang('new') . ' ' . lang('priority') ?></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('notify_ticket_reopened') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('notify_ticket_reopened') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="notify_ticket_reopened">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <header class="panel-heading  "><?= lang('leads_settings') ?></header>
                        <div class="panel-body">
                            <div class="form-group">
                                <label
                                    class="col-lg-3 control-label"><?= lang('default') . ' ' . lang('source') ?></label>
                                <div class="col-lg-5">
                                    <select name="default_leads_source" style="width: 100%"
                                            class="form-control select_box">
                                        <?php
                                        $all_lead_source = $this->db->get('tbl_lead_source')->result();
                                        if (!empty($all_lead_source)) {
                                            foreach ($all_lead_source as $lead_source) {
                                                ?>
                                                <option
                                                    value="<?= $lead_source->lead_source_id ?>"<?= (config_item('default_leads_source') == $lead_source->lead_source_id ? ' selected="selected"' : '') ?>><?= $lead_source->lead_source ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <a href="<?= base_url() ?>admin/settings/lead_source"
                                       class=""><?= lang('new') . ' ' . lang('source') ?></a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-lg-3 control-label"><?= lang('default') . ' ' . lang('status') ?></label>
                                <div class="col-lg-5">
                                    <select name="default_lead_status" style="width: 100%"
                                            class="form-control select_box">
                                        <?php
                                        $all_lead_status = $this->db->get('tbl_lead_status')->result();

                                        if (!empty($all_lead_status)) {
                                            foreach ($all_lead_status as $lead_status) {
                                                ?>
                                                <option
                                                    value="<?= $lead_status->lead_status_id ?>"<?= (config_item('default_lead_status') == $lead_status->lead_status_id ? ' selected="selected"' : '') ?>><?= $lead_status->lead_status . ' (' . lang($lead_status->lead_type) . ' )' ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <a href="<?= base_url() ?>admin/settings/lead_status"
                                       class=""><?= lang('new') . ' ' . lang('status') ?></a>
                                </div>
                            </div>
                            <?php $lead_permission = config_item('default_lead_permission'); ?>
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('permission_for_new_leads') ?></label>
                                <div class="col-sm-9">
                                    <div class="checkbox c-radio needsclick">
                                        <label class="needsclick">
                                            <input id="" <?php
                                            if (isset($lead_permission) && $lead_permission == 'all') {
                                                echo 'checked';
                                            }
                                            ?> type="radio" name="default_lead_permission" value="everyone">
                                            <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                            <i title="<?= lang('permission_for_all') ?>"
                                               class="fa fa-question-circle" data-toggle="tooltip"
                                               data-placement="top"></i>
                                        </label>
                                    </div>
                                    <div class="checkbox c-radio needsclick">
                                        <label class="needsclick">
                                            <input id="" <?php
                                            if (isset($lead_permission) && $lead_permission != 'all') {
                                                echo 'checked';
                                            }
                                            ?> type="radio" name="default_lead_permission" value="custom_permission"
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
                            if (!empty($lead_permission) && $lead_permission != 'all') {
                                echo 'show';
                            }
                            ?>" id="permission_user_1">
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
                                                        if (!empty($lead_permission) && $lead_permission != 'all') {
                                                            $get_permission = json_decode(config_item('default_lead_permission'));
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

                                            if (!empty($lead_permission) && $lead_permission != 'all') {
                                                $get_permission = json_decode(config_item('default_lead_permission'));

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

                                                        if (!empty($lead_permission) && $lead_permission != 'all') {
                                                            $get_permission = json_decode(config_item('default_lead_permission'));

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

                                                        if (!empty($lead_permission) && $lead_permission != 'all') {
                                                            $get_permission = json_decode(config_item('default_lead_permission'));
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
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                        </div>
                    </div>
            </section>
        </form>
</div>
<!-- End Form -->
</div>