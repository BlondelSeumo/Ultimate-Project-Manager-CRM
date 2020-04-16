<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="panel panel-custom">
    <header class="panel-heading">
        <div class="panel-title"><strong><?= $title ?></strong>
            <div class="pull-right hidden-print">
                <div class="pull-right "><a href="<?php echo base_url() ?>assets/sample/transactions_sample.xlsx"
                                            class="btn btn-primary"><i
                            class="fa fa-download"> <?= lang('download_sample') ?></i></a>
                </div>
            </div>

        </div>
    </header>
    <div class="panel-body">
        <form role="form" enctype="multipart/form-data" data-parsley-validate="" novalidate=""
              action="<?php echo base_url(); ?>admin/transactions/save_imported" method="post"
              class="form-horizontal  ">
            <div class="panel-body">
                <div class="form-group">
                    <label for="field-1" class="col-sm-3 control-label">
                        <?= lang('choose_file') ?><span class="required">*</span></label>
                    <div class="col-sm-5">
                        <div style="display: inherit;margin-bottom: inherit" class="fileinput fileinput-new"
                             data-provides="fileinput">
                    <span class="btn btn-default btn-file"><span
                            class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="upload_file">
                                                        </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="type" value="<?= $type ?>">
                <div class="form-group ">
                    <label class="col-lg-3 control-label mt-lg"><?= lang('account') ?> <span
                            class="text-danger">*</span> </label>
                    <div class="col-lg-5 mt-lg">
                        <select class="form-control select_box" style="width: 100%" name="account_id" required <?php
                        if (!empty($deposit_info)) {
                            echo 'disabled';
                        }
                        ?>>

                            <?php
                            $account_info = $this->db->get('tbl_accounts')->result();
                            if (!empty($account_info)) {
                                foreach ($account_info as $v_account) {
                                    ?>
                                    <option value="<?= $v_account->account_id ?>"
                                        <?php
                                        if (!empty($deposit_info->account_id)) {
                                            echo $deposit_info->account_id == $v_account->account_id ? 'selected' : '';
                                        }
                                        ?>
                                    ><?= $v_account->account_name ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('deposit_category') ?> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%" name="category_id">
                            <option value="0"><?= lang('none') ?></option>
                            <?php
                            $category_info = $this->db->get('tbl_income_category')->result();
                            if (!empty($category_info)) {
                                foreach ($category_info as $v_category) {
                                    ?>
                                    <option value="<?= $v_category->income_category_id ?>"
                                        <?php
                                        if (!empty($deposit_info->category_id)) {
                                            echo $deposit_info->category_id == $v_category->income_category_id ? 'selected' : '';
                                        }
                                        ?>
                                    ><?= $v_category->income_category ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('paid_by') ?> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%" name="paid_by">
                            <option value="0"><?= lang('select_payer') ?></option>
                            <?php
                            $all_client = $this->db->get('tbl_client')->result();
                            if (!empty($all_client)) {
                                foreach ($all_client as $v_client) {
                                    ?>
                                    <option value="<?= $v_client->client_id ?>"
                                        <?php
                                        if (!empty($deposit_info)) {
                                            echo $deposit_info->paid_by == $v_client->client_id ? 'selected' : '';
                                        }
                                        ?>
                                    ><?= ucfirst($v_client->name); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('payment_method') ?> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%" name="payment_methods_id">
                            <option value="0"><?= lang('select_payment_method') ?></option>
                            <?php
                            $payment_methods = $this->db->get('tbl_payment_methods')->result();
                            if (!empty($payment_methods)) {
                                foreach ($payment_methods as $p_method) {
                                    ?>
                                    <option value="<?= $p_method->payment_methods_id ?>" <?php
                                    if (!empty($deposit_info)) {
                                        echo $deposit_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
                                    }
                                    ?>><?= $p_method->method_name ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div id="add_new">
                    <div class="form-group" style="margin-bottom: 0px">
                        <label for="field-1"
                               class="col-sm-3 control-label"><?= lang('attachment') ?></label>
                        <div class="col-sm-4">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <?php
                                if (!empty($deposit_info->attachement)) {
                                    $attachement = json_decode($deposit_info->attachement);
                                }
                                if (!empty($attachement)):foreach ($attachement as $v_files): ?>
                                    <div class="">
                                        <input type="hidden" name="path[]"
                                               value="<?php echo $v_files->path ?>">
                                        <input type="hidden" name="fileName[]"
                                               value="<?php echo $v_files->fileName ?>">
                                        <input type="hidden" name="fullPath[]"
                                               value="<?php echo $v_files->fullPath ?>">
                                        <input type="hidden" name="size[]"
                                               value="<?php echo $v_files->size ?>">
                                    <span class=" btn btn-default btn-file">
                                    <span class="fileinput-filename"> <?php echo $v_files->fileName ?></span>
                                    <a href="javascript:void(0);" class="remCFile" style="float: none;">×</a>
                                    </span>
                                        <strong>
                                            <a href="javascript:void(0);" class="RCF"><i
                                                    class="fa fa-times"></i>&nbsp;Remove</a></strong>
                                        <p></p>
                                    </div>

                                <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="btn btn-default btn-file"><span
                                            class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="attachement[]">
                                                        </span>
                                    <span class="fileinput-filename"></span>
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                                       style="float: none;">&times;</a>
                                <?php endif; ?>
                            </div>
                            <div id="msg_pdf" style="color: #e11221"></div>
                        </div>
                        <div class="col-sm-2">
                            <strong><a href="javascript:void(0);" id="add_more" class="addCF "><i
                                        class="fa fa-plus"></i>&nbsp;<?= lang('add_more') ?>
                                </a></strong>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                            class="required">*</span></label>
                    <div class="col-sm-9">
                        <div class="checkbox c-radio needsclick">
                            <label class="needsclick">
                                <input id="" <?php
                                if (!empty($deposit_info->permission) && $deposit_info->permission == 'all') {
                                    echo 'checked';
                                } elseif (empty($deposit_info)) {
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
                                if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
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
                if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                    echo 'show';
                }
                ?>" id="permission_user_1">
                    <label for="field-1"
                           class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                        <span
                            class="required">*</span></label>
                    <div class="col-sm-9">
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
                                            if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                $get_permission = json_decode($deposit_info->permission);
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

                                if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                    $get_permission = json_decode($deposit_info->permission);

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

                                            if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                $get_permission = json_decode($deposit_info->permission);

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

                                            if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                $get_permission = json_decode($deposit_info->permission);
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
                    <label class="col-lg-3 control-label"></label>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"></i> <?= lang('upload') ?></button>
                    </div>
                </div>
            </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {

            var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('attachment') ?></label>\n\
        <div class="col-sm-4">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="attachement[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
            maxAppend++;
            $("#add_new").append(add_new);

        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>
