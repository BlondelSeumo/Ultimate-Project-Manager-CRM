<?php
echo message_box('success');
echo message_box('error');
$created = can_action('149', 'created');
$edited = can_action('149', 'edited');
?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <?= $title ?></header>

    <?php
    if (!empty($created) || !empty($edited)) {
        ?>
        <form method="post" id="lead_statuss" action="<?= base_url() ?>admin/supplier/saved_supplier/inline"
              class="form-horizontal" data-parsley-validate="" novalidate="">
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('name') ?> <span
                            class="text-danger">*</span></label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" value="<?php
                    if (!empty($supplier_info)) {
                        echo $supplier_info->name;
                    }
                    ?>" name="name" required="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('mobile') ?> <span
                            class="text-danger">*</span></label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" value="<?php
                    if (!empty($supplier_info)) {
                        echo $supplier_info->mobile;
                    }
                    ?>" name="mobile" required="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('phone') ?></label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" value="<?php
                    if (!empty($supplier_info)) {
                        echo $supplier_info->phone;
                    }
                    ?>" name="phone">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('email') ?> <span
                            class="text-danger">*</span></label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" value="<?php
                    if (!empty($supplier_info)) {
                        echo $supplier_info->email;
                    }
                    ?>" name="email" required="">
                </div>
            </div>
            <!-- End discount Fields -->
            <div class="form-group terms">
                <label class="col-lg-3 control-label"><?= lang('address') ?> </label>
                <div class="col-lg-5">
                        <textarea name="address" class="form-control"><?php
                            if (!empty($supplier_info)) {
                                echo $supplier_info->address;
                            }
                            ?></textarea>
                </div>
            </div>

            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                            class="required">*</span></label>
                <div class="col-sm-9">
                    <div class="checkbox c-radio needsclick">
                        <label class="needsclick">
                            <input id="" <?php
                            if (!empty($supplier_info->permission) && $supplier_info->permission == 'all') {
                                echo 'checked';
                            } elseif (empty($supplier_info)) {
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
                            if (!empty($supplier_info->permission) && $supplier_info->permission != 'all') {
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
            if (!empty($supplier_info->permission) && $supplier_info->permission != 'all') {
                echo 'show';
            }
            ?>" id="permission_user">
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
                                        if (!empty($supplier_info->permission) && $supplier_info->permission != 'all') {
                                            $get_permission = json_decode($supplier_info->permission);
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

                            if (!empty($supplier_info->permission) && $supplier_info->permission != 'all') {
                                $get_permission = json_decode($supplier_info->permission);

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

                                        if (!empty($supplier_info->permission) && $supplier_info->permission != 'all') {
                                            $get_permission = json_decode($supplier_info->permission);

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

                                        if (!empty($supplier_info->permission) && $supplier_info->permission != 'all') {
                                            $get_permission = json_decode($supplier_info->permission);
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
                <button type="submit"
                        class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </form>
    <?php } ?>
</div>
<script type="text/javascript">
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/supplier/saved_supplier/inline')?>') {
            event.preventDefault();
        }
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize()
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.status == 'success') {
                if (typeof (response.id) != 'undefined') {
                    var groups = $('select[name="supplier_id"]');
                    groups.prepend('<option selected value="' + response.id + '">' + response.name + '</option>');
                    var select2Instance = groups.data('select2');
                    var resetOptions = select2Instance.options.options;
                    groups.select2('destroy').select2(resetOptions)
                }
                toastr[response.status](response.message);
            }
            $('#myModal').modal('hide');
        }).fail(function () {
            alert('There was a problem with AJAX');
        });
    });
</script>