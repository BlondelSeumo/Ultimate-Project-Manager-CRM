<?php
$created = can_action('70', 'created');
$edited = can_action('70', 'edited');
if (!empty($created) || !empty($edited)) {
    ?>
    <form method="post" data-parsley-validate="" novalidate=""
          action="<?= base_url() ?>admin/departments/save_departments/<?php
          if (!empty($departments_info->departments_id)) {
              echo $departments_info->departments_id;
          }
          ?>"
          class="form-horizontal">
        <?php if (!empty($departments_info)) {
            $details = $departments_info->deptname . ' ⇒ ' . $designations_info->designations . ' ' . lang('details');
        } else {
            $details = lang('new_department');
        } ?>
        <div class="panel panel-custom">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= $details ?></strong>
                <span class="pull-right">
                    <button type="submit" name="save" value="1"
                            class="btn btn-primary "><?php echo !empty($departments_info->deptname) ? lang('update') : lang('save') ?></button>

                    <button type="submit" name="save" value="2"
                            class="btn btn-primary hidden-xs "><?php echo !empty($departments_info->deptname) ? lang('update') . ' & ' . lang('add_more') : lang('save') . ' & ' . lang('add_more') ?></button>
                </span>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="form-group">
                    <label class="col-lg-4 control-label"><?= lang('select') . ' ' . lang('department') ?>
                        <span
                            class="text-danger">*</span>
                    </label>
                    <div class="col-lg-8">
                        <select class="form-control select_box" style="width: 100%" name="departments_id"
                                id="new_departments">
                            <option value=""><?= lang('new_department') ?></option>

                            <?php $all_department = $this->db->get('tbl_departments')->result();
                            if (!empty($all_department)) {
                                foreach ($all_department as $v_departments) { ?>
                                    <option <?= (!empty($departments_info->departments_id) && $departments_info->departments_id == $v_departments->departments_id ? 'selected' : null) ?>
                                        value="<?= $v_departments->departments_id ?>"><?php
                                        if (!empty($v_departments->deptname)) {
                                            $deptname = $v_departments->deptname;
                                        } else {
                                            $deptname = lang('undefined_department');
                                        }
                                        echo $deptname;
                                        ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group new_departments"
                     style="display: <?= (!empty($departments_info->departments_id) ? 'none' : 'block') ?>">
                    <label class="col-sm-4 control-label"><?= lang('new_department') ?></label>
                    <div class="col-sm-8">
                        <input <?= (!empty($departments_info->departments_id) ? 'disabled' : '') ?>
                            type="text" name="deptname" class="form-control new_departments"
                            value=""/>
                    </div>
                </div>
                <div class="form-group">
                    <label class=" col-sm-4 control-label"><?= lang('designation') ?><span
                            class="required">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="designations" required class="form-control"
                               value="<?php if (!empty($designations_info->designations)) echo $designations_info->designations; ?>"/>
                    </div>
                </div>
                <input type="hidden" name="designations_id" class="form-control"
                       value="<?php if (!empty($designations_info->designations_id)) echo $designations_info->designations_id; ?>"/>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>
                            <div class="checkbox c-checkbox ">
                                <label class="needsclick" data-toggle="tooltip" data-placement="top"
                                       title="<?= lang('select_all') . ' ' . lang('permission') ?>">
                                    <input id="select_all" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <strong><?= lang('permission') ?></strong>
                                </label>
                            </div>
                        </th>
                        <th>
                            <div class="checkbox c-checkbox ">
                                <label class="needsclick" data-toggle="tooltip" data-placement="top"
                                       title="<?= lang('view_help') ?>">
                                    <input id="select_all_view" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <strong><?= lang('view') ?></strong>
                                </label>
                            </div>
                        </th>
                        <th>
                            <div class="checkbox c-checkbox ">
                                <label class="needsclick" data-toggle="tooltip" data-placement="top"
                                       title="<?= lang('select_all') . ' ' . lang('create') ?>">
                                    <input id="select_all_create" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <strong><?= lang('create') ?></strong>
                                </label>
                            </div>
                        </th>
                        <th>
                            <div class="checkbox c-checkbox ">
                                <label class="needsclick" data-toggle="tooltip" data-placement="top"
                                       title="<?= lang('select_all') . ' ' . lang('edit') ?>">
                                    <input id="select_all_edit" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <strong><?= lang('edit') ?></strong>
                                </label>
                            </div>
                        </th>
                        <th>
                            <div class="checkbox c-checkbox ">
                                <label class="needsclick" data-toggle="tooltip" data-placement="top"
                                       title="<?= lang('select_all') . ' ' . lang('delete') ?>">
                                    <input id="select_all_delete" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <strong><?= lang('delete') ?></strong>
                                </label>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $menu_info = $this->db->where('status !=', 0)->order_by('sort')->get('tbl_menu')->result();
                    foreach ($menu_info as $items) {
                        $menu['parents'][$items->parent][] = $items;
                        ?>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                <?php if($items->label == 'dashboard' || $items->label == 'calendar'){?>
                                $('#<?= $items->menu_id?>').prop('checked', true);
                                $('.<?= $items->menu_id?>').prop('checked', true);
                                $('.<?= $items->menu_id?>').prop('disabled', true);
                                $('.view .<?= $items->menu_id?>').prop('disabled', false);
                                $(".<?= $items->menu_id?>").next().css('display', 'none');
                                $(".view .<?= $items->menu_id?>").next().css('display', 'block');
                                <?php }?>
                                <?php if($items->label == 'transfer_report' || $items->label == 'transactions_report' || $items->label == 'balance_sheet' || $items->label == 'time_history' || $items->label == 'timechange_request'
                            || $items->label == 'attendance_report' || $items->label == 'jobs_applications' || $items->label == 'manage_salary_details' || $items->label == 'employee_salary_list' || $items->label == 'make_payment'
                            || $items->label == 'payroll_summary' || $items->label == 'generate_payslip' || $items->label == 'provident_fund' || $items->label == 'filemanager' || $items->label == 'mailbox' || $items->label == 'leave_management' || $items->label == 'advance_salary' || $items->label == 'quotations_list'
                            || $items->label == 'tasks_assignment' || $items->label == 'bugs_assignment' || $items->label == 'project_report' || $items->label == 'tasks_report' || $items->label == 'bugs_report' || $items->label == 'tickets_report' || $items->label == 'client_report'
                            || $items->label == 'account_statement' || $items->label == 'income_report' || $items->label == 'expense_report' || $items->label == 'income_expense' || $items->label == 'date_wise_report' || $items->label == 'all_income' || $items->label == 'all_expense' || $items->label == 'all_transaction'
                            || $items->label == 'report_by_month' || $items->label == 'stock_history' || $items->label == 'assign_stock_report' || $items->label == 'stock_report' || $items->label == 'overtime' || $items->label == 'performance_report' || $items->label == 'database_backup' || $items->link == 'admin/knowledgebase'
                            || $items->label == 'company_details' || $items->label == 'system_settings' || $items->label == 'email_settings' || $items->label == 'email_templates' || $items->label == 'email_integration' || $items->label == 'payment_settings' || $items->label == 'invoice_settings' || $items->label == 'estimate_settings'
                            || $items->label == 'estimate_settings' || $items->label == 'tickets_leads_settings' || $items->label == 'theme_settings' || $items->label == 'working_days' || $items->label == 'payment_method' || $items->label == 'cronjob' || $items->label == 'menu_allocation' || $items->label == 'notification'
                            || $items->label == 'email_notification' || $items->label == 'translations' || $items->label == 'dashboard_settings' || $items->label == 'system_update' || $items->label == 'private_chat'
                                ){?>
                                $('.<?= $items->menu_id?>').prop('disabled', true);
                                $(".<?= $items->menu_id?>").next().css('display', 'none');
                                $('.view .<?= $items->menu_id?>').prop('disabled', false);
                                $(".view .<?= $items->menu_id?>").next().css('display', 'block');

                                <?php }?>
                                <?php
                                if ($items->label == 'performance_indicator' || $items->label == 'give_appraisal') {?>
                                $('.delete .<?= $items->menu_id?>').prop('disabled', true);
                                $(".delete .<?= $items->menu_id?>").next().css('display', 'none');
                                <?php }?>
                                $('#select_all').change(function () {
                                    var c = this.checked;
                                    $(':checkbox').prop('checked', c);
                                });

                                //select select_all_view
                                $("#select_all_view").change(function () {  //"select all" change
                                    $(".view > input").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $(".view > input").map(function () {
                                        if ($(".view > input").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });
                                //select select_all_create
                                $("#select_all_create").change(function () {  //"select all" change
                                    $(".create > input").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $(".create > input").map(function () {
                                        if ($(".create > input").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });

                                //select select_all_create
                                $("#select_all_edit").change(function () {  //"select all" change
                                    $(".edit > input").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $(".edit > input").map(function () {
                                        if ($(".edit > input").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });
                                //select select_all_create
                                $("#select_all_delete").change(function () {  //"select all" change
                                    $(".delete > input").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $(".delete > input").map(function () {
                                        if ($(".delete > input").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });
                                //select all view
                                $("#all_view_<?= $items->menu_id;?>").change(function () {  //"select all" change
                                    $(".view .<?= $items->menu_id;?>").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $('.view .<?= $items->menu_id;?>').map(function () {
                                        if ($(".view .<?= $items->menu_id;?>").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });
                                //select all all_create
                                $("#all_create_<?= $items->menu_id;?>").change(function () {  //"select all" change
                                    $(".create .<?= $items->menu_id;?>").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $('.create .<?= $items->menu_id;?>').map(function () {
                                        if ($(".create .<?= $items->menu_id;?>").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });
                                //select all all_edit
                                $("#all_edit_<?= $items->menu_id;?>").change(function () {  //"select all" change
                                    $(".edit .<?= $items->menu_id;?>").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $('.edit .<?= $items->menu_id;?>').map(function () {
                                        if ($(".edit .<?= $items->menu_id;?>").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });
                                //select all all_edit
                                $("#all_delete_<?= $items->menu_id;?>").change(function () {  //"select all" change
                                    $(".delete .<?= $items->menu_id;?>").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                    var values = $('.delete .<?= $items->menu_id;?>').map(function () {
                                        if ($(".delete .<?= $items->menu_id;?>").is(":checked")) {
                                            $("#" + this.value).prop('checked', true);
                                        } else {
                                            if ($('.' + this.value + ':checked').length == 0) {
                                                $("#" + this.value).prop('checked', false);
                                            }
                                        }
                                    }).get();
                                });


                                //select all checkboxes
                                $("#<?= $items->menu_id;?>").change(function () {  //"select all" change
                                    $(".<?= $items->menu_id;?>").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                                });
                                if ($("input#<?= $items->menu_id;?>").is(':checked')) {
                                    $('.c_<?= $items->menu_id;?>').show();
                                    $("#parent_<?= $items->menu_id;?>").addClass('minus');
                                    $("#parent_<?= $items->menu_id;?>").removeClass('plus');
                                }
                                $("#parent_<?= $items->menu_id;?>").click(function () {
                                    $("#parent_<?= $items->menu_id;?>").toggleClass('minus');
                                    $("#parent_<?= $items->menu_id;?>").toggleClass('plus');
                                    $('.c_<?= $items->menu_id;?>').slideToggle('fast');
                                });
                                //".checkbox" change
                                $('.<?= $items->menu_id;?>').change(function () {
                                    //check "select all" if all checkbox items are checked
                                    if ($('.<?= $items->menu_id;?>:checked').length) {
                                        $("#<?= $items->menu_id;?>").prop('checked', true);
                                    }
                                    if ($('.<?= $items->menu_id;?>:checked').length == 0) {
                                        $("#<?= $items->menu_id;?>").prop('checked', false); //change "select all" checked status to false
                                    }
                                });
                            });
                        </script>

                    <?php }
                    $CI =& get_instance();
                    $all_menus = $CI->buildChild(0, $menu);
                    if (!empty($all_menus)) {
                        foreach ($all_menus as $parent => $v_parent) {
                            if (is_array($v_parent)) { // if this have child
                                if (!empty($v_parent)) {
                                    foreach ($v_parent as $parent_id => $v_child) { ?>
                                        <style type="text/css">
                                            .plus {
                                                background: url(<?= base_url()?>asset/img/icon_plus.png) no-repeat 4px 5px;
                                                background-size: 8px 8px;
                                            }

                                            .minus {
                                                background: url(<?= base_url()?>asset/img/icon_minus.png) no-repeat 4px 8px;
                                                background-size: 8px 2px;
                                            }

                                            .parent {
                                                width: 4%;
                                                margin-top: 6px;
                                                cursor: pointer;
                                            }

                                            .parent span {
                                                visibility: hidden;
                                            }

                                            .child {
                                                display: none
                                            }
                                        </style>
                                        <tr style="background: #e2e2e2;">
                                            <th>
                                                <div id="parent_<?= $parent_id; ?>" class="parent plus pull-left">
                                                    <span>X</span></div>
                                                <div class="checkbox c-checkbox pull-left">
                                                    <label class="needsclick " data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="<?= lang('select_all') ?>">
                                                        <input <?php
                                                        if (!empty($roll[$parent_id])) {
                                                            echo $roll[$parent_id] ? 'checked' : '';
                                                        }
                                                        ?> id="<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" type="checkbox" name="menu[]" value="<?= $parent_id; ?>">
                                                        <span class="fa fa-check"></span>
                                                        <strong><?= lang($parent); ?></strong>
                                                    </label>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="checkbox c-checkbox ">
                                                    <label class="needsclick view" data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="<?= lang('select_all') ?>">
                                                        <input id="all_view_<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" class="<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" type="checkbox" name="view_<?= $parent_id ?>"
                                                               value="<?= $parent_id ?>">
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="checkbox c-checkbox ">
                                                    <label class="needsclick create" data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="<?= lang('select_all') ?>">
                                                        <input id="all_create_<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" class="<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" type="checkbox" name="created_<?= $parent_id ?>"
                                                               value="<?= $parent_id ?>">
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="checkbox c-checkbox">
                                                    <label class="needsclick edit" data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="<?= lang('select_all') ?>">
                                                        <input id="all_edit_<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" class="<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" type="checkbox" name="edited_<?= $parent_id ?>"
                                                               value="<?= $parent_id ?>">
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="checkbox c-checkbox">
                                                    <label class="needsclick delete" data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="<?= lang('select_all') ?>">
                                                        <input id="all_delete_<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" class="<?php if (!empty($parent_id)) {
                                                            echo $parent_id;
                                                        } ?>" type="checkbox" name="deleted_<?= $parent_id ?>"
                                                               value="<?= $parent_id ?>">
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </th>
                                        </tr>
                                        <?php
                                        if (!empty($v_child)) {
                                            foreach ($v_child as $child => $v_sub_child) {
                                                if (is_array($v_sub_child)) {
                                                    foreach ($v_sub_child as $sub_chld => $v_sub_chld) { ?>
                                                        <tr style="background: #eeeeee">
                                                            <td style="display: block;padding-left: 35px;">
                                                                <div id="parent_<?= $sub_chld; ?>"
                                                                     class="parent plus pull-left">
                                                                    <span>X</span></div>
                                                                <div class="checkbox c-checkbox pull-left">
                                                                    <label class="needsclick " data-toggle="tooltip"
                                                                           data-placement="top"
                                                                           title="<?= lang('select_all') ?>">
                                                                        <input <?php
                                                                        if (!empty($roll[$sub_chld])) {
                                                                            echo $roll[$sub_chld] ? 'checked' : '';
                                                                        }
                                                                        ?> class="<?= $parent_id; ?>"
                                                                           id="<?= $sub_chld; ?>" type="checkbox"
                                                                           name="menu[]" value="<?= $sub_chld; ?>">
                                                                        <span class="fa fa-check"></span>
                                                                        <strong><?= lang($child); ?></strong>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php
                                                        foreach ($v_sub_chld as $sub_chld_name => $sub_chld_id) {
                                                            if (is_array($sub_chld_id)) {
                                                                foreach ($sub_chld_id as $sub_chld_1 => $v_sub_chld_1) { ?>
                                                                    <tr style="background: #e2e2e2">
                                                                        <td style="display: block;padding-left: 60px;">
                                                                            <div id="parent_<?= $sub_chld_1; ?>"
                                                                                 class="parent plus pull-left">
                                                                                <span>X</span></div>
                                                                            <div class="checkbox c-checkbox pull-left">
                                                                                <label class="needsclick "
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="top"
                                                                                       title="<?= lang('select_all') ?>">
                                                                                    <input
                                                                                        <?php
                                                                                        if (!empty($roll[$sub_chld_1])) {
                                                                                            echo $roll[$sub_chld_1] ? 'checked' : '';
                                                                                        }
                                                                                        ?>
                                                                                        class="<?= $parent_id . ' ' . $sub_chld; ?>"
                                                                                        id="<?= $sub_chld_1; ?>"
                                                                                        type="checkbox" name="menu[]"
                                                                                        value="<?= $sub_chld_1; ?>">
                                                                                    <span class="fa fa-check"></span>
                                                                                    <strong><?= lang($sub_chld_name); ?></strong>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                                    <?php
                                                                    foreach ($v_sub_chld_1 as $sub_chld_name_1 => $v_sub_chld_2) {
                                                                        if (is_array($v_sub_chld_2)) {
                                                                            foreach ($v_sub_chld_2 as $sub_chld_name_2 => $v_sub_chld_3) {
                                                                                ?>
                                                                                <tr style="background: #eeeeee">
                                                                                    <td style="display: block;padding-left: 85px;">
                                                                                        <div
                                                                                            id="parent_<?= $sub_chld_name_2; ?>"
                                                                                            class="parent plus pull-left">
                                                                                            <span>X</span></div>
                                                                                        <div
                                                                                            class="checkbox c-checkbox pull-left">
                                                                                            <label class="needsclick "
                                                                                                   data-toggle="tooltip"
                                                                                                   data-placement="top"
                                                                                                   title="<?= lang('select_all') ?>">
                                                                                                <input
                                                                                                    <?php if (!empty($roll[$sub_chld_name_2])) {
                                                                                                        echo $roll[$sub_chld_name_2] ? 'checked' : '';
                                                                                                    }
                                                                                                    ?>
                                                                                                    class="<?= $parent_id . ' ' . $sub_chld . ' ' . $sub_chld_1; ?>"
                                                                                                    id="<?= $sub_chld_name_2; ?>"
                                                                                                    type="checkbox"
                                                                                                    name="menu[]"
                                                                                                    value="<?= $sub_chld_name_2; ?>">
                                                                                        <span
                                                                                            class="fa fa-check"></span>
                                                                                                <strong><?= lang($sub_chld_name_1); ?></strong>
                                                                                            </label>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                </tr>
                                                                                <?php
                                                                                foreach ($v_sub_chld_3 as $sub_chld_name_3 => $v_sub_chld_4) {
                                                                                    if (is_array($v_sub_chld_4)) {

                                                                                    } else {
                                                                                        ?>
                                                                                        <tr class="child c_<?= $sub_chld_name_2; ?>">
                                                                                            <td style="display: block;padding-left: 110px">
                                                                                                <div
                                                                                                    class="checkbox c-checkbox">
                                                                                                    <label
                                                                                                        class="needsclick "
                                                                                                        data-toggle="tooltip"
                                                                                                        data-placement="top"
                                                                                                        title="<?= lang('select') ?>">
                                                                                                        <input <?php if (!empty($roll[$v_sub_chld_4])) {
                                                                                                            echo $roll[$v_sub_chld_4] ? 'checked' : '';
                                                                                                        }
                                                                                                        ?> id="<?= $v_sub_chld_4; ?>"
                                                                                                           class="<?= $parent_id . ' ' . $sub_chld . ' ' . $sub_chld_name_2 . ' ' . $sub_chld_1; ?>"
                                                                                                           type="checkbox"
                                                                                                           name="menu[]"
                                                                                                           value="<?= $v_sub_chld_4; ?>">
                                                                                                <span
                                                                                                    class="fa fa-check"></span>
                                                                                                        <strong><?= lang($sub_chld_name_3); ?></strong>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div
                                                                                                    class="checkbox c-checkbox ">
                                                                                                    <label
                                                                                                        class="needsclick view"
                                                                                                        data-toggle="tooltip"
                                                                                                        data-placement="top"
                                                                                                        title="<?= lang('view_help') ?>">
                                                                                                        <input
                                                                                                            <?php if (!empty($roll[$v_sub_chld_4])) {
                                                                                                                echo $roll[$v_sub_chld_4] ? 'checked' : '';
                                                                                                            }
                                                                                                            ?>
                                                                                                            class="<?= $sub_chld . ' ' . $v_sub_chld_4 . ' ' . $sub_chld_name_2 . ' ' . $parent_id . ' ' . $sub_chld_1; ?>"
                                                                                                            type="checkbox"
                                                                                                            name="view_<?= $v_sub_chld_4; ?>"
                                                                                                            value="<?= $v_sub_chld_4; ?>">
                                                                                                <span
                                                                                                    class="fa fa-check"></span>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div
                                                                                                    class="checkbox c-checkbox ">
                                                                                                    <label
                                                                                                        class="needsclick create"
                                                                                                        data-toggle="tooltip"
                                                                                                        data-placement="top"
                                                                                                        title="<?= lang('can') . ' ' . lang('create') ?>">
                                                                                                        <input
                                                                                                            <?php if (!empty($roll[$v_sub_chld_4])) {
                                                                                                                echo $roll[$v_sub_chld_4]->created == $v_sub_chld_4 ? 'checked' : '';
                                                                                                            }
                                                                                                            ?>class="<?= $sub_chld . ' ' . $v_sub_chld_4 . ' ' . $sub_chld_name_2 . ' ' . $parent_id . ' ' . $sub_chld_1; ?>"
                                                                                                            type="checkbox"
                                                                                                            name="created_<?= $v_sub_chld_4; ?>"
                                                                                                            value="<?= $v_sub_chld_4; ?>">
                                                                                                <span
                                                                                                    class="fa fa-check"></span>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div
                                                                                                    class="checkbox c-checkbox">
                                                                                                    <label
                                                                                                        class="needsclick edit"
                                                                                                        data-toggle="tooltip"
                                                                                                        data-placement="top"
                                                                                                        title="<?= lang('can') . ' ' . lang('edit') ?>">
                                                                                                        <input <?php
                                                                                                        if (!empty($roll[$v_sub_chld_4])) {
                                                                                                            echo $roll[$v_sub_chld_4]->edited == $v_sub_chld_4 ? 'checked' : '';
                                                                                                        }
                                                                                                        ?>
                                                                                                            class="<?= $sub_chld . ' ' . $v_sub_chld_4 . ' ' . $sub_chld_name_2 . ' ' . $parent_id . ' ' . $sub_chld_1; ?>"
                                                                                                            type="checkbox"
                                                                                                            name="edited_<?= $v_sub_chld_4; ?>"
                                                                                                            value="<?= $v_sub_chld_4; ?>">
                                                                                                <span
                                                                                                    class="fa fa-check"></span>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div
                                                                                                    class="checkbox c-checkbox">
                                                                                                    <label
                                                                                                        class="needsclick delete"
                                                                                                        data-toggle="tooltip"
                                                                                                        data-placement="top"
                                                                                                        title="<?= lang('can') . ' ' . lang('delete') ?>">
                                                                                                        <input <?php
                                                                                                        if (!empty($roll[$v_sub_chld_4])) {
                                                                                                            echo $roll[$v_sub_chld_4]->deleted == $v_sub_chld_4 ? 'checked' : '';
                                                                                                        }
                                                                                                        ?> class="<?= $sub_chld . ' ' . $v_sub_chld_4 . ' ' . $sub_chld_name_2 . ' ' . $parent_id . ' ' . $sub_chld_1; ?>"
                                                                                                           type="checkbox"
                                                                                                           name="deleted_<?= $v_sub_chld_4; ?>"
                                                                                                           value="<?= $v_sub_chld_4; ?>">
                                                                                                <span
                                                                                                    class="fa fa-check"></span>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                            }
                                                                        } else { ?>
                                                                            <tr class="child c_<?= $sub_chld_1; ?>">
                                                                                <td style="display: block;padding-left: 85px">
                                                                                    <div class="checkbox c-checkbox ">
                                                                                        <label class="needsclick "
                                                                                               data-toggle="tooltip"
                                                                                               data-placement="top"
                                                                                               title="<?= lang('select') ?>">
                                                                                            <input <?php if (!empty($roll[$v_sub_chld_2])) {
                                                                                                echo $roll[$v_sub_chld_2] ? 'checked' : '';
                                                                                            }
                                                                                            ?> id="<?= $v_sub_chld_2; ?>"
                                                                                               class="<?= $parent_id . ' ' . $sub_chld . ' ' . $sub_chld_1; ?>"
                                                                                               type="checkbox"
                                                                                               name="menu[]"
                                                                                               value="<?= $v_sub_chld_2; ?>">
                                                                                        <span
                                                                                            class="fa fa-check"></span>
                                                                                            <strong><?= lang($sub_chld_name_1); ?></strong>
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="checkbox c-checkbox ">
                                                                                        <label class="needsclick view"
                                                                                               data-toggle="tooltip"
                                                                                               data-placement="top"
                                                                                               title="<?= lang('view_help') ?>">
                                                                                            <input <?php if (!empty($roll[$v_sub_chld_2])) {
                                                                                                echo $roll[$v_sub_chld_2] ? 'checked' : '';
                                                                                            }
                                                                                            ?>
                                                                                                class="<?= $sub_chld . ' ' . $parent_id . ' ' . $v_sub_chld_2 . ' ' . $sub_chld_1; ?>"
                                                                                                type="checkbox"
                                                                                                name="view_<?= $v_sub_chld_2; ?>"
                                                                                                value="<?= $v_sub_chld_2; ?>">
                                                                                        <span
                                                                                            class="fa fa-check"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="checkbox c-checkbox ">
                                                                                        <label class="needsclick create"
                                                                                               data-toggle="tooltip"
                                                                                               data-placement="top"
                                                                                               title="<?= lang('can') . ' ' . lang('create') ?>">
                                                                                            <input <?php if (!empty($roll[$v_sub_chld_2])) {
                                                                                                echo $roll[$v_sub_chld_2]->created == $v_sub_chld_2 ? 'checked' : '';
                                                                                            } ?>
                                                                                                class="<?= $sub_chld . ' ' . $parent_id . ' ' . $v_sub_chld_2 . ' ' . $sub_chld_1; ?>"
                                                                                                type="checkbox"
                                                                                                name="created_<?= $v_sub_chld_2; ?>"
                                                                                                value="<?= $v_sub_chld_2; ?>">
                                                                                        <span
                                                                                            class="fa fa-check"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="checkbox c-checkbox">
                                                                                        <label class="needsclick edit"
                                                                                               data-toggle="tooltip"
                                                                                               data-placement="top"
                                                                                               title="<?= lang('can') . ' ' . lang('edit') ?>">
                                                                                            <input <?php
                                                                                            if (!empty($roll[$v_sub_chld_2])) {
                                                                                                echo $roll[$v_sub_chld_2]->edited == $v_sub_chld_2 ? 'checked' : '';
                                                                                            }
                                                                                            ?> class="<?= $sub_chld_1 . ' ' . $sub_chld . ' ' . $v_sub_chld_2 . ' ' . $parent_id; ?>"
                                                                                               type="checkbox"
                                                                                               name="edited_<?= $v_sub_chld_2; ?>"
                                                                                               value="<?= $v_sub_chld_2; ?>">
                                                                                        <span
                                                                                            class="fa fa-check"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="checkbox c-checkbox">
                                                                                        <label class="needsclick delete"
                                                                                               data-toggle="tooltip"
                                                                                               data-placement="top"
                                                                                               title="<?= lang('can') . ' ' . lang('delete') ?>">
                                                                                            <input <?php
                                                                                            if (!empty($roll[$v_sub_chld_2])) {
                                                                                                echo $roll[$v_sub_chld_2]->deleted == $v_sub_chld_2 ? 'checked' : '';
                                                                                            }
                                                                                            ?>
                                                                                                class="<?= $sub_chld_1 . ' ' . $sub_chld . ' ' . $v_sub_chld_2 . ' ' . $parent_id; ?>"
                                                                                                type="checkbox"
                                                                                                name="deleted_<?= $v_sub_chld_2; ?>"
                                                                                                value="<?= $v_sub_chld_2; ?>">
                                                                                        <span
                                                                                            class="fa fa-check"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                            } else { ?>
                                                                <tr class="child c_<?= $sub_chld; ?>">
                                                                    <td style="display: block;padding-left: 60px">
                                                                        <div class="checkbox c-checkbox ">
                                                                            <label class="needsclick "
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="<?= lang('select') ?>">
                                                                                <input <?php
                                                                                if (!empty($roll[$sub_chld_id])) {
                                                                                    echo $roll[$sub_chld_id] ? 'checked' : '';
                                                                                }
                                                                                ?> id="<?= $sub_chld_id; ?>"
                                                                                   class="<?= $parent_id . ' ' . $sub_chld; ?>"
                                                                                   type="checkbox"
                                                                                   name="menu[]"
                                                                                   value="<?= $sub_chld_id; ?>">
                                                                                <span class="fa fa-check"></span>
                                                                                <strong><?= lang($sub_chld_name); ?></strong>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="checkbox c-checkbox ">
                                                                            <label class="needsclick view"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="<?= lang('view_help') ?>">
                                                                                <input <?php
                                                                                if (!empty($roll[$sub_chld_id])) {
                                                                                    echo $roll[$sub_chld_id] ? 'checked' : '';
                                                                                }
                                                                                ?> class="<?= $sub_chld . ' ' . $sub_chld_id . ' ' . $parent_id; ?>"
                                                                                   type="checkbox"
                                                                                   name="view_<?= $sub_chld_id; ?>"
                                                                                   value="<?= $sub_chld_id; ?>">
                                                                                <span class="fa fa-check"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="checkbox c-checkbox ">
                                                                            <label class="needsclick create"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="<?= lang('can') . ' ' . lang('create') ?>">
                                                                                <input <?php
                                                                                if (!empty($roll[$sub_chld_id])) {
                                                                                    echo $roll[$sub_chld_id]->created == $sub_chld_id ? 'checked' : '';
                                                                                }
                                                                                ?>
                                                                                    class="<?= $sub_chld . ' ' . $sub_chld_id . ' ' . $parent_id; ?>"
                                                                                    type="checkbox"
                                                                                    name="created_<?= $sub_chld_id; ?>"
                                                                                    value="<?= $sub_chld_id; ?>">
                                                                                <span class="fa fa-check"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="checkbox c-checkbox">
                                                                            <label class="needsclick edit"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="<?= lang('can') . ' ' . lang('edit') ?>">
                                                                                <input <?php
                                                                                if (!empty($roll[$sub_chld_id])) {
                                                                                    echo $roll[$sub_chld_id]->edited == $sub_chld_id ? 'checked' : '';
                                                                                }
                                                                                ?>
                                                                                    class="<?= $sub_chld . ' ' . $sub_chld_id . ' ' . $parent_id; ?>"
                                                                                    type="checkbox"
                                                                                    name="edited_<?= $sub_chld_id; ?>"
                                                                                    value="<?= $sub_chld_id; ?>">
                                                                                <span class="fa fa-check"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="checkbox c-checkbox">
                                                                            <label class="needsclick delete"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="<?= lang('can') . ' ' . lang('delete') ?>">
                                                                                <input <?php
                                                                                if (!empty($roll[$sub_chld_id])) {
                                                                                    echo $roll[$sub_chld_id]->deleted == $sub_chld_id ? 'checked' : '';
                                                                                }
                                                                                ?>
                                                                                    class="<?= $sub_chld . ' ' . $sub_chld_id . ' ' . $parent_id; ?>"
                                                                                    type="checkbox"
                                                                                    name="deleted_<?= $sub_chld_id; ?>"
                                                                                    value="<?= $sub_chld_id; ?>">
                                                                                <span class="fa fa-check"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                } else { ?>
                                                    <tr class="child c_<?= $parent_id; ?>">
                                                        <td style="display: block;padding-left: 35px;">
                                                            <div class="checkbox c-checkbox ">
                                                                <label class="needsclick " data-toggle="tooltip"
                                                                       data-placement="top"
                                                                       title="<?= lang('select') ?>">
                                                                    <input <?php
                                                                    if (!empty($roll[$v_sub_child])) {
                                                                        echo $roll[$v_sub_child] ? 'checked' : '';
                                                                    }
                                                                    ?> id="<?= $v_sub_child; ?>"
                                                                       class="<?= $parent_id; ?>"
                                                                       type="checkbox"
                                                                       name="menu[]" value="<?= $v_sub_child; ?>">
                                                                    <span class="fa fa-check"></span>
                                                                    <strong><?= lang($child); ?></strong>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox c-checkbox ">
                                                                <label class="needsclick view" data-toggle="tooltip"
                                                                       data-placement="top"
                                                                       title="<?= lang('view_help') ?>">
                                                                    <input <?php
                                                                    if (!empty($roll[$v_sub_child])) {
                                                                        echo $roll[$v_sub_child] ? 'checked' : '';
                                                                    }
                                                                    ?> class="<?= $parent_id . ' ' . $v_sub_child; ?>"
                                                                       type="checkbox"
                                                                       name="view_<?= $v_sub_child; ?>"
                                                                       value="<?= $v_sub_child; ?>">
                                                                    <span class="fa fa-check"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox c-checkbox ">
                                                                <label class="needsclick create" data-toggle="tooltip"
                                                                       data-placement="top"
                                                                       title="<?= lang('can') . ' ' . lang('create') ?>">
                                                                    <input <?php
                                                                    if (!empty($roll[$v_sub_child])) {
                                                                        echo $roll[$v_sub_child]->created == $v_sub_child ? 'checked' : '';
                                                                    }
                                                                    ?> class="<?= $parent_id . ' ' . $v_sub_child; ?>"
                                                                       type="checkbox"
                                                                       name="created_<?= $v_sub_child; ?>"
                                                                       value="<?= $v_sub_child; ?>">
                                                                    <span class="fa fa-check"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox c-checkbox">
                                                                <label class="needsclick edit" data-toggle="tooltip"
                                                                       data-placement="top"
                                                                       title="<?= lang('can') . ' ' . lang('edit') ?>">
                                                                    <input
                                                                        class="<?= $parent_id . ' ' . $v_sub_child; ?>"
                                                                        type="checkbox"
                                                                        name="edited_<?= $v_sub_child; ?>"
                                                                        value="<?= $v_sub_child; ?>" <?php
                                                                    if (!empty($roll[$v_sub_child])) {
                                                                        echo $roll[$v_sub_child]->edited == $v_sub_child ? 'checked' : '';
                                                                    }
                                                                    ?>>
                                                                    <span class="fa fa-check"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox c-checkbox">
                                                                <label class="needsclick delete" data-toggle="tooltip"
                                                                       data-placement="top"
                                                                       title="<?= lang('can') . ' ' . lang('delete') ?>">
                                                                    <input <?php
                                                                    if (!empty($roll[$v_sub_child])) {
                                                                        echo $roll[$v_sub_child]->deleted == $v_sub_child ? 'checked' : '';
                                                                    }
                                                                    ?> class="<?= $parent_id . ' ' . $v_sub_child; ?>"
                                                                       type="checkbox"
                                                                       name="deleted_<?= $v_sub_child; ?>"
                                                                       value="<?= $v_sub_child; ?>">
                                                                    <span class="fa fa-check"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            }
                                        } ?>

                                    <?php }
                                }
                            } else { ?>
                                <tr>
                                    <td>
                                        <div class="checkbox c-checkbox ">
                                            <label class="needsclick " data-toggle="tooltip" data-placement="top"
                                                   title="<?= lang('select') ?>">
                                                <input id="<?= $v_parent; ?>" type="checkbox" name="menu[]"
                                                       value="<?= $v_parent; ?>" <?php if (!empty($roll[$v_parent])) {
                                                    echo $roll[$v_parent] ? 'checked' : '';
                                                }
                                                ?>>
                                                <span class="fa fa-check"></span>
                                                <strong><?= lang($parent); ?></strong>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox c-checkbox ">
                                            <label class="needsclick view" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="<?= lang('view_help') ?>">
                                                <input id="<?= $v_parent; ?>"
                                                    <?php
                                                    if (!empty($roll[$v_parent])) {
                                                        echo $roll[$v_parent] ? 'checked' : '';
                                                    }
                                                    ?>
                                                       class="<?= $v_parent; ?>" type="checkbox"
                                                       name="view_<?= $v_parent; ?>"
                                                       value="<?= $v_parent; ?>">
                                                <span class="fa fa-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox c-checkbox ">
                                            <label class="needsclick create" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="<?= lang('can') . ' ' . lang('create') ?>">
                                                <input
                                                    <?php
                                                    if (!empty($roll[$v_parent])) {
                                                        echo $roll[$v_parent]->created == $v_parent ? 'checked' : '';
                                                    }
                                                    ?>
                                                    class="<?= $v_parent; ?>" type="checkbox"
                                                    name="created_<?= $v_parent; ?>"
                                                    value="<?= $v_parent; ?>">
                                                <span class="fa fa-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick edit" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="<?= lang('can') . ' ' . lang('edit') ?>">
                                                <input <?php
                                                if (!empty($roll[$v_parent])) {
                                                    echo $roll[$v_parent]->edited == $v_parent ? 'checked' : '';
                                                }
                                                ?> class="<?= $v_parent; ?>" type="checkbox"
                                                   name="edited_<?= $v_parent; ?>"
                                                   value="<?= $v_parent; ?>">
                                                <span class="fa fa-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick delete" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="<?= lang('can') . ' ' . lang('delete') ?>">
                                                <input <?php
                                                if (!empty($roll[$v_parent])) {
                                                    echo $roll[$v_parent]->deleted == $v_parent ? 'checked' : '';
                                                }
                                                ?> class="<?= $v_parent; ?>" type="checkbox"
                                                   name="deleted_<?= $v_parent; ?>"
                                                   value="<?= $v_parent; ?>">
                                                <span class="fa fa-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        }
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </form>
<?php } ?>