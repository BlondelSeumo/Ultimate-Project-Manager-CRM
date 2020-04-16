<?= message_box('success');
$created = can_action('16', 'created');
$edited = can_action('16', 'edited');
$deleted = can_action('16', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('tax_rates') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                            data-toggle="tab"><?= lang('new_tax_rate') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('tax_rates') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('tax_rate_name') ?></th>
                            <th><?= lang('tax_rate_percent') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th class="hidden-print"><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "admin/invoice/taxList";
                            });
                        </script>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)) { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                <form method="post" data-parsley-validate="" novalidate=""
                      action="<?= base_url() ?>admin/invoice/save_tax_rate/<?php
                      if (!empty($tax_rates_info)) {
                          echo $tax_rates_info->tax_rates_id;
                      }
                      ?>" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><?= lang('tax_rate_name') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" required value="<?php
                            if (!empty($tax_rates_info)) {
                                echo $tax_rates_info->tax_rate_name;
                            }
                            ?>" name="tax_rate_name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label"><?= lang('tax_rate_percent') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" data-parsley-type="number" class="form-control" required value="<?php
                            if (!empty($tax_rates_info)) {
                                echo $tax_rates_info->tax_rate_percent;
                            }
                            ?>" name="tax_rate_percent">
                        </div>
                    </div>
                    <div class="form-group" id="border-none">
                        <label for="field-1" class="col-sm-4 control-label"><?= lang('permission') ?> <span
                                class="required">*</span></label>
                        <div class="col-sm-8">
                            <div class="checkbox c-radio needsclick">
                                <label class="needsclick">
                                    <input id="" <?php
                                    if (!empty($tax_rates_info) && $tax_rates_info->permission == 'all') {
                                        echo 'checked';
                                    } elseif (empty($tax_rates_info)) {
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
                                    if (!empty($tax_rates_info) && $tax_rates_info->permission != 'all') {
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
                    if (!empty($tax_rates_info) && $tax_rates_info->permission != 'all') {
                        echo 'show';
                    }
                    ?>" id="permission_user_1">
                        <label for="field-1"
                               class="col-sm-4 control-label"><?= lang('select') . ' ' . lang('users') ?>
                            <span
                                class="required">*</span></label>
                        <div class="col-sm-8">
                            <?php
                            if (!empty($permission_user)) {
                                foreach ($permission_user as $key => $v_user) {

                                    if ($v_user->role_id == 1) {
                                        $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                    } else {
                                        $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                    }

                                    ?>
                                    <div class="checkbox c-checkbox needsclick">
                                        <label class="needsclick">
                                            <input type="checkbox"
                                                <?php
                                                if (!empty($tax_rates_info) && $tax_rates_info->permission != 'all') {
                                                    $get_permission = json_decode($tax_rates_info->permission);
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

                                    if (!empty($tax_rates_info) && $tax_rates_info->permission != 'all') {
                                        $get_permission = json_decode($tax_rates_info->permission);

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
                                            <input id="<?= $v_user->user_id ?>"
                                                <?php

                                                if (!empty($tax_rates_info) && $tax_rates_info->permission != 'all') {
                                                    $get_permission = json_decode($tax_rates_info->permission);

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

                                                if (!empty($tax_rates_info) && $tax_rates_info->permission != 'all') {
                                                    $get_permission = json_decode($tax_rates_info->permission);
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
                    <div class="btn-bottom-toolbar text-right">
                        <?php
                        if (!empty($tax_rates_info)) { ?>
                            <button type="submit"
                                    class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                            <button type="button" onclick="goBack()"
                                    class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                        <?php } else {
                            ?>
                            <button type="submit"
                                    class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                        <?php }
                        ?>
                        <button type="submit" name="save" value="2" class="btn btn-sm btn-warning "><?php echo !empty($tax_rates_info->tax_rate_name) ? lang('update') . ' & ' . lang('add_more') : lang('save') . ' & ' . lang('add_more') ?></button>
                    </div>
                </form>
            <?php } else { ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>