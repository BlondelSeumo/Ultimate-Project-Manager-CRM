<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('150', 'created');
$edited = can_action('150', 'edited');
$deleted = can_action('150', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
<?php include_once 'assets/admin-ajax.php'; ?>
<?php include_once 'assets/js/sales.php'; ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('manage') . ' ' . lang('purchase') ?></a>
        </li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new') . ' ' . lang('purchase') ?></a>
        </li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('manage_account') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables">
                        <thead>
                        <tr>
                            <th><?= lang('reference_no') ?></th>
                            <th><?= lang('supplier') ?></th>
                            <th><?= lang('purchase_date') ?></th>
                            <th><?= lang('due_amount') ?></th>
                            <th><?= lang('status') ?></th>
                            <?php $show_custom_fields = custom_form_table(20, null);
                            if (!empty($show_custom_fields)) {
                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                    if (!empty($c_label)) {
                                        ?>
                                        <th><?= $c_label ?> </th>
                                    <?php }
                                }
                            }
                            ?>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th class="col-options no-sort"><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            list = base_url + "admin/purchase/purchaseList";
                        </script>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)){
                if (!empty($purchase_info)) {
                    $purchase_id = $purchase_info->purchase_id;
                } else {
                    $purchase_id = null;
                }
                ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <?php echo form_open(base_url('admin/purchase/save_purchase/' . $purchase_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
                    <div class="mb-lg purchase accounting-template">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12 br pv">
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-7">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($purchase_info)) {
                                                echo $purchase_info->reference_no;
                                            } else {
                                                if (empty(config_item('proposal_number_format'))) {
                                                    echo config_item('purchase_prefix');
                                                }
                                                if (config_item('increment_purchase_number') == 'FALSE') {
                                                    $this->load->helper('string');
                                                    echo random_string('nozero', 6);
                                                } else {
                                                    echo $this->purchase_model->generate_purchase_number();
                                                }
                                            }
                                            ?>" name="reference_no">
                                        </div>
                                    </div>
                                    <div class="f_supplier_id">
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('supplier') ?> <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-7">
                                                <div class="input-group">
                                                    <select class="form-control select_box" style="width: 100%"
                                                            name="supplier_id" required="">
                                                        <option
                                                                value=""><?= lang('select') . ' ' . lang('supplier') ?></option>
                                                        <?php
                                                        if (!empty($all_supplier)) {
                                                            foreach ($all_supplier as $v_supplier) {
                                                                if (!empty($purchase_info->supplier_id)) {
                                                                    $supplier_id = $purchase_info->supplier_id;
                                                                }
                                                                ?>
                                                                <option value="<?= $v_supplier->supplier_id ?>"
                                                                    <?php
                                                                    if (!empty($supplier_id)) {
                                                                        echo $supplier_id == $v_supplier->supplier_id ? 'selected' : null;
                                                                    }
                                                                    ?>
                                                                ><?= $v_supplier->name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        $_created = can_action('151', 'created');
                                                        ?>
                                                    </select>
                                                    <?php if (!empty($_created)) { ?>
                                                        <div class="input-group-addon"
                                                             title="<?= lang('new') . ' ' . lang('supplier') ?>"
                                                             data-toggle="tooltip" data-placement="top">
                                                            <a data-toggle="modal" data-target="#myModal"
                                                               href="<?= base_url() ?>admin/supplier/new_supplier"><i
                                                                        class="fa fa-plus"></i></a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                                class="col-lg-3 control-label"><?= lang('purchase') . ' ' . lang('date') ?></label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                                <input type="text" name="purchase_date"
                                                       class="form-control datepicker"
                                                       value="<?php
                                                       if (!empty($purchase_info->purchase_date)) {
                                                           echo $purchase_info->purchase_date;
                                                       } else {
                                                           echo date('Y-m-d');
                                                       }
                                                       ?>"
                                                       data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('due_date') ?></label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                                <input type="text" name="due_date"
                                                       class="form-control datepicker"
                                                       value="<?php
                                                       if (!empty($purchase_info->due_date)) {
                                                           echo $purchase_info->due_date;
                                                       } else {
                                                           echo date('Y-m-d');
                                                       }
                                                       ?>"
                                                       data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                                    class="required">*</span></label>
                                        <div class="col-sm-7">
                                            <div class="checkbox c-radio needsclick">
                                                <label class="needsclick">
                                                    <input id="" <?php
                                                    if (!empty($purchase_info->permission) && $purchase_info->permission == 'all') {
                                                        echo 'checked';
                                                    } elseif (empty($purchase_info)) {
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
                                                    if (!empty($purchase_info->permission) && $purchase_info->permission != 'all') {
                                                        echo 'checked';
                                                    }
                                                    ?> type="radio" name="permission" value="custom_permission"
                                                    >
                                                    <span
                                                            class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                                    <i
                                                            title="<?= lang('permission_for_customization') ?>"
                                                            class="fa fa-question-circle" data-toggle="tooltip"
                                                            data-placement="top"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group <?php
                                    if (!empty($purchase_info->permission) && $purchase_info->permission != 'all') {
                                        echo 'show';
                                    }
                                    ?>" id="permission_user_1">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                            <span
                                                    class="required">*</span></label>
                                        <div class="col-sm-7">
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
                                                                if (!empty($purchase_info->permission) && $purchase_info->permission != 'all') {
                                                                    $get_permission = json_decode($purchase_info->permission);
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

                                                    if (!empty($purchase_info->permission) && $purchase_info->permission != 'all') {
                                                        $get_permission = json_decode($purchase_info->permission);

                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                echo 'show';
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input id="<?= $v_user->user_id ?>" checked
                                                                   type="checkbox"
                                                                   name="action_1<?= $v_user->user_id ?>[]"
                                                                   disabled
                                                                   value="view">
                                                            <span
                                                                    class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                                        </label>
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input id="<?= $v_user->user_id ?>"
                                                                <?php

                                                                if (!empty($purchase_info->permission) && $purchase_info->permission != 'all') {
                                                                    $get_permission = json_decode($purchase_info->permission);

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
                                                                   value="edit"
                                                                   name="action_<?= $v_user->user_id ?>[]">
                                                            <span
                                                                    class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                                        </label>
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input id="<?= $v_user->user_id ?>"
                                                                <?php

                                                                if (!empty($purchase_info->permission) && $purchase_info->permission != 'all') {
                                                                    $get_permission = json_decode($purchase_info->permission);
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
                                                               name="action_<?= $v_user->user_id ?>[]"
                                                               value="view">

                                                    </div>


                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                        if (!empty($purchase_info)) {
                                            $purchase_id = $purchase_info->purchase_id;
                                        } else {
                                            $purchase_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(20, $purchase_id); ?>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 br pv">

                                <div class="row">
                                    <div class="form-group">
                                        <label for="field-1"
                                               class="col-sm-4 control-label"><?= lang('sales') . ' ' . lang('agent') ?></label>
                                        <div class="col-sm-7">
                                            <select class="form-control select_box" required style="width: 100%"
                                                    name="user_id">
                                                <option
                                                        value=""><?= lang('select') . ' ' . lang('sales') . ' ' . lang('agent') ?></option>
                                                <?php
                                                $all_user = get_staff_details();
                                                if (!empty($all_user)) {
                                                    foreach ($all_user as $v_user) {
                                                        $profile_info = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                                                        if (!empty($profile_info)) {
                                                            ?>
                                                            <option value="<?= $v_user->user_id ?>"
                                                                <?php
                                                                if (!empty($purchase_info->user_id)) {
                                                                    echo $purchase_info->user_id == $v_user->user_id ? 'selected' : null;
                                                                } else {
                                                                    echo $this->session->userdata('user_id') == $v_user->user_id ? 'selected' : null;
                                                                }
                                                                ?>
                                                            ><?= $profile_info->fullname ?></option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_type"
                                               class="control-label col-sm-4"><?= lang('update_stock') ?></label>
                                        <div class="col-sm-7">
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="Yes" name="update_stock"
                                                    <?php if (isset($purchase_info) && $purchase_info->update_stock == 'Yes') {
                                                        echo 'checked';
                                                    } elseif (empty($purchase_info)) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="fa fa-circle"></span><?php echo lang('yes'); ?>
                                            </label>
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="No"
                                                       name="update_stock" <?php if (isset($purchase_info) && $purchase_info->update_stock == 'No') {
                                                    echo 'checked';
                                                } ?>>
                                                <span class="fa fa-circle"></span><?php echo lang('no'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_type"
                                               class="control-label col-sm-4"><?= lang('discount_type') ?></label>
                                        <div class="col-sm-7">
                                            <select name="discount_type" class="selectpicker" data-width="100%">
                                                <option value=""
                                                        selected><?php echo lang('no') . ' ' . lang('discount'); ?></option>
                                                <option value="before_tax" <?php
                                                if (isset($purchase_info)) {
                                                    if ($purchase_info->discount_type == 'before_tax') {
                                                        echo 'selected';
                                                    }
                                                } ?>><?php echo lang('before_tax'); ?></option>
                                                <option value="after_tax" <?php if (isset($purchase_info)) {
                                                    if ($purchase_info->discount_type == 'after_tax') {
                                                        echo 'selected';
                                                    }
                                                } ?>><?php echo lang('after_tax'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('notes') ?> </label>
                                        <div class="col-lg-11 row">
                        <textarea name="notes" class="textarea"><?php
                            if (!empty($purchase_info)) {
                                echo $purchase_info->notes;
                            } else {
                                echo $this->config->item('purchase_notes');
                            }
                            ?></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <style type="text/css">
                        .dropdown-menu > li > a {
                            white-space: normal;
                        }

                        .dragger {
                            background: url(<?= base_url()?>assets/img/dragger.png) 10px 32px no-repeat;
                            cursor: pointer;
                        }

                        <?php if (!empty($purchase_info)) { ?>
                        .dragger {
                            background: url(<?= base_url()?>assets/img/dragger.png) 10px 32px no-repeat;
                            cursor: pointer;
                        }

                        <?php }?>
                        .input-transparent {
                            box-shadow: none;
                            outline: 0;
                            border: 0 !important;
                            background: 0 0;
                            padding: 3px;
                        }

                    </style>

                    <?php
                    $saved_items = $this->invoice_model->get_all_items();
                    ?>
                    <div class="row bt">
                        <div class="col-md-4 mt">
                            <div class="form-group">
                                <select name="item_select" class="selectpicker m0" data-width="100%"
                                        id="item_select"
                                        data-none-selected-text="<?php echo lang('add_items'); ?>"
                                        data-live-search="true">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($saved_items)) {
                                        $saved_items = array_reverse($saved_items, true);
                                        foreach ($saved_items as $group_id => $v_saved_items) {
                                            if ($group_id != 0) {
                                                $group = $this->db->where('customer_group_id', $group_id)->get('tbl_customer_group')->row()->customer_group;
                                            } else {
                                                $group = '';
                                            }
                                            ?>
                                            <optgroup data-group-id="<?php echo $group_id; ?>"
                                                      label="<?php echo $group; ?>">
                                                <?php
                                                if (!empty($v_saved_items)) {
                                                    foreach ($v_saved_items as $v_item) { ?>
                                                        <option value="<?php echo $v_item->saved_items_id; ?>"
                                                                data-subtext="<?php echo strip_html_tags(mb_substr($v_item->item_desc, 0, 200)) . '...'; ?>">
                                                            (<?= display_money($v_item->unit_cost, default_currency()); ?>
                                                            ) <?php echo $v_item->item_name; ?></option>
                                                    <?php }
                                                }
                                                ?>
                                            </optgroup>

                                        <?php } ?>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5 pull-right">
                            <div class="form-group">
                                <label
                                        class="col-sm-4 control-label"><?php echo lang('show_quantity_as'); ?></label>
                                <div class="col-sm-8">
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="qty" id="<?php echo lang('qty'); ?>"
                                               name="show_quantity_as"
                                            <?php if (isset($purchase_info) && $purchase_info->show_quantity_as == 'qty') {
                                                echo 'checked';
                                            } else if (!isset($hours_quantity) && !isset($qty_hrs_quantity)) {
                                                echo 'checked';
                                            } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('qty'); ?>
                                    </label>
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="hours" id="<?php echo lang('hours'); ?>"
                                               name="show_quantity_as" <?php if (isset($purchase_info) && $purchase_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                            echo 'checked';
                                        } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('hours'); ?></label>
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="qty_hours"
                                               id="<?php echo lang('qty') . '/' . lang('hours'); ?>"
                                               name="show_quantity_as"
                                            <?php if (isset($purchase_info) && $purchase_info->show_quantity_as == 'qty_hours' || isset($qty_hrs_quantity)) {
                                                echo 'checked';
                                            } ?>>
                                        <span
                                                class="fa fa-circle"></span><?php echo lang('qty') . '/' . lang('hours'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive s_table">
                            <table class="table invoice-items-table items">
                                <thead style="background: #e8e8e8">
                                <tr>
                                    <th></th>
                                    <th><?= lang('item_name') ?></th>
                                    <th><?= lang('description') ?></th>
                                    <?php
                                    $invoice_view = config_item('invoice_view');
                                    if (!empty($invoice_view) && $invoice_view == '2') {
                                        ?>
                                        <th class="col-sm-2"><?= lang('hsn_code') ?></th>
                                    <?php } ?>
                                    <?php
                                    $qty_heading = lang('qty');
                                    if (isset($purchase_info) && $purchase_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                        $qty_heading = lang('hours');
                                    } else if (isset($purchase_info) && $purchase_info->show_quantity_as == 'qty_hours') {
                                        $qty_heading = lang('qty') . '/' . lang('hours');
                                    }
                                    ?>
                                    <th class="qty col-sm-1"><?php echo $qty_heading; ?></th>
                                    <th class="col-sm-2"><?= lang('price') ?></th>
                                    <th class="col-sm-2"><?= lang('tax_rate') ?> </th>
                                    <th class="col-sm-1"><?= lang('total') ?></th>
                                    <th class="hidden-print"><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (isset($purchase_info)) {
                                    echo form_hidden('isedit', $purchase_info->purchase_id);
                                }
                                ?>
                                <tr class="main">
                                    <td></td>
                                    <td>
                        <textarea name="item_name" class="form-control"
                                  placeholder="<?php echo lang('item_name'); ?>"></textarea>
                                    </td>
                                    <td>
                        <textarea name="item_desc" class="form-control"
                                  placeholder="<?php echo lang('description'); ?>"></textarea>
                                    </td>
                                    <?php
                                    $invoice_view = config_item('invoice_view');
                                    if (!empty($invoice_view) && $invoice_view == '2') {
                                        ?>
                                        <td><input type="text" name="hsn_code"
                                                   class="form-control"></td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" data-parsley-type="number" name="quantity" min="0"
                                               value="1"
                                               class="form-control"
                                               placeholder="<?php echo lang('qty'); ?>">

                                        <input type="text"
                                               placeholder="<?php echo lang('unit') . ' ' . lang('type'); ?>"
                                               name="unit"
                                               class="form-control input-transparent">
                                    </td>
                                    <td>
                                        <input type="hidden" name="new_itmes_id" class="form-control">
                                        <input type="hidden" name="saved_items_id" class="form-control">
                                        <input type="text" data-parsley-type="number" name="unit_cost"
                                               class="form-control"
                                               placeholder="<?php echo lang('price'); ?>">
                                    </td>
                                    <td>
                                        <?php
                                        $taxes = $this->db->order_by('tax_rate_percent', 'ASC')->get('tbl_tax_rates')->result();
                                        $default_tax = config_item('default_tax');
                                        if (!is_numeric($default_tax)) {
                                            $default_tax = unserialize($default_tax);
                                        }
                                        $select = '<select class="selectpicker tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="' . lang('no_tax') . '">';
                                        foreach ($taxes as $tax) {
                                            $selected = '';
                                            if (!empty($default_tax) && is_array($default_tax)) {
                                                if (in_array($tax->tax_rates_id, $default_tax)) {
                                                    $selected = ' selected ';
                                                }
                                            }
                                            $select .= '<option value="' . $tax->tax_rate_name . '|' . $tax->tax_rate_percent . '"' . $selected . 'data-taxrate="' . $tax->tax_rate_percent . '" data-taxname="' . $tax->tax_rate_name . '" data-subtext="' . $tax->tax_rate_name . '">' . $tax->tax_rate_percent . '%</option>';
                                        }
                                        $select .= '</select>';
                                        echo $select;
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>
                                        <?php
                                        $new_item = 'undefined';
                                        if (isset($purchase_info)) {
                                            $new_item = true;
                                        }
                                        ?>
                                        <button type="button"
                                                onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;"
                                                class="btn-xs btn btn-info"><i class="fa fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php if (isset($purchase_info) || isset($add_items)) {
                                    $i = 1;
                                    $items_indicator = 'items';
                                    if (isset($purchase_info)) {
                                        $add_items = $this->purchase_model->ordered_items_by_id($purchase_info->purchase_id);
                                        $items_indicator = 'items';
                                    }

                                    foreach ($add_items as $item) {
                                        $manual = false;
                                        $table_row = '<tr class="sortable item">';
                                        $table_row .= '<td class="dragger">';
                                        if (!is_numeric($item->quantity)) {
                                            $item->quantity = 1;
                                        }
                                        $invoice_item_taxes = $this->purchase_model->get_invoice_item_taxes($item->items_id, 'purchase');

                                        // passed like string
                                        if ($item->items_id == 0) {
                                            $invoice_item_taxes = $invoice_item_taxes[0];
                                            $manual = true;
                                        }
                                        $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][items_id]', $item->items_id);
                                        $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][saved_items_id]', $item->saved_items_id);
                                        $amount = $item->unit_cost * $item->quantity;
                                        $amount = ($amount);
                                        // order input
                                        $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]"><input type="hidden" name="items_id[]" value="' . $item->items_id . '"><input type="hidden" name="saved_items_id[]" value="' . $item->saved_items_id . '">';
                                        $table_row .= '</td>';
                                        $table_row .= '<td class="bold item_name"><textarea name="' . $items_indicator . '[' . $i . '][item_name]" class="form-control">' . $item->item_name . '</textarea></td>';
                                        $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][item_desc]" class="form-control" >' . $item->item_desc . '</textarea></td>';
                                        $invoice_view = config_item('invoice_view');
                                        if (!empty($invoice_view) && $invoice_view == '2') {
                                            $table_row .= '<td><input type="text" name="' . $items_indicator . '[' . $i . '][hsn_code]" class="form-control" value="' . $item->hsn_code . '"></td>';
                                        }
                                        $table_row .= '<td><input type="text" data-parsley-type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][quantity]" value="' . $item->quantity . '" class="form-control">';
                                        $unit_placeholder = '';
                                        if (!$item->unit) {
                                            $unit_placeholder = lang('unit');
                                            $item->unit = '';
                                        }
                                        $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item->unit . '">';
                                        $table_row .= '</td>';
                                        $table_row .= '<td class="rate"><input type="text" data-parsley-type="number" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][unit_cost]" value="' . $item->unit_cost . '" class="form-control"></td>';
                                        $table_row .= '<td class="taxrate">' . $this->admin_model->get_taxes_dropdown('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item->items_id, true, $manual) . '</td>';
                                        $table_row .= '<td class="amount">' . $amount . '</td>';
                                        $table_row .= '<td><a href="#" class="btn-xs btn btn-danger pull-left" onclick="delete_item(this,' . $item->items_id . '); return false;"><i class="fa fa-trash"></i></a></td>';
                                        $table_row .= '</tr>';
                                        echo $table_row;
                                        $i++;
                                    }
                                }
                                ?>

                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-xs-8 pull-right">
                                <table class="table text-right">
                                    <tbody>
                                    <tr id="subtotal">
                                        <td><span class="bold"><?php echo lang('sub_total'); ?> :</span>
                                        </td>
                                        <td class="subtotal">
                                        </td>
                                    </tr>
                                    <tr id="discount_percent">
                                        <td>
                                            <div class="row">
                                                <div class="col-md-7">
                                                            <span class="bold"><?php echo lang('discount'); ?>
                                                                (%)</span>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $discount_percent = 0;
                                                    if (isset($purchase_info)) {
                                                        if ($purchase_info->discount_percent != 0) {
                                                            $discount_percent = $purchase_info->discount_percent;
                                                        }
                                                    }
                                                    ?>
                                                    <input type="text" data-parsley-type="number"
                                                           value="<?php echo $discount_percent; ?>"
                                                           class="form-control pull-left" min="0" max="100"
                                                           name="discount_percent">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="discount_percent"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <span class="bold"><?php echo lang('adjustment'); ?></span>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" data-parsley-type="number"
                                                           value="<?php if (isset($purchase_info)) {
                                                               echo $purchase_info->adjustment;
                                                           } else {
                                                               echo 0;
                                                           } ?>" class="form-control pull-left"
                                                           name="adjustment">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="adjustment"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo lang('total'); ?> :</span>
                                        </td>
                                        <td class="total">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="removed-items"></div>
                        <div class="btn-bottom-toolbar text-right">
                            <?php
                            if (!empty($purchase_info)) { ?>
                                <button type="submit"
                                        class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                <button type="button" onclick="goBack()"
                                        class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                            <?php } else {
                                ?>
                                <input type="submit" value="<?= lang('save') ?>" name="update"
                                       class="btn btn-success">
                            <?php }
                            ?>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            <?php } else { ?>
        </div>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        init_items_sortable();

    });
</script>