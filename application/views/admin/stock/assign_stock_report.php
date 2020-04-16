<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('assign_stock_report') ?></strong>
                </div>
            </div>
            <div class="panel-body">

                <form id="form" action="<?php echo base_url() ?>admin/stock/assign_stock_report/<?php
                if (!empty($expense_category_info->expense_category_id)) {
                    echo $expense_category_info->expense_category_id;
                }
                ?>" method="post" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('select_employee') ?><span
                                class="required">*</span></label>

                        <div class="col-sm-5">
                            <select class="form-control select_box" name="user_id">
                                <option value=""><?= lang('select_employee') ?></option>
                                <?php if (!empty($all_employee)): ?>
                                    <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                        <optgroup label="<?php echo $dept_name; ?>">
                                            <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                                <option value="<?php echo $v_employee->user_id; ?>"
                                                    <?php
                                                    if (!empty($employee_info->user_id)) {
                                                        echo $v_employee->user_id == $employee_info->user_id ? 'selected' : '';
                                                    }
                                                    ?>><?php echo $v_employee->fullname . ' ( ' . $v_employee->designations . ' )' ?></option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" id="sbtn" value="1" name="flag"
                                    class="btn btn-primary"><?= lang('go') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br/>
        <?php if (!empty($flag)): ?>

            <div class="row">
                <div class="col-sm-12" data-offset="0">
                    <div class="panel panel-custom">
                        <!-- Default panel contents -->
                        <div class="panel-heading">
                            <div class="panel-title">
                                <strong><?php
                                    if (!empty($employee_info)) {
                                        echo $employee_info->fullname . (!empty($employee_info->employment_id) ? ' (' . $employee_info->employment_id . ') ' : '');
                                    }
                                    ?></strong>
                                <div class="pull-right hidden-print">
                                    <span><?php echo btn_pdf('admin/stock/assign_stock_pdf/' . $employee_info->user_id); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($assign_list)): foreach ($assign_list as $sub_category => $v_assign_list) : ?>
                            <?php if (!empty($v_assign_list)): ?>

                                <div class="box-heading">
                                    <div class="box-title"
                                         style="border-bottom: 1px solid #a0a0a0;padding-bottom:5px;">
                                        <strong><?php echo $sub_category ?></strong>
                                    </div>
                                </div>
                                <!-- Table -->
                                <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="col-sm-1"><?= lang('sl') ?></th>
                                    <th><?= lang('item_name') ?></th>
                                    <th><?= lang('assign_quantity') ?></th>
                                    <th><?= lang('assign_date') ?></th>
                                    <th class="col-sm-1 hidden-print"><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($v_assign_list as $key => $v_assign_stock) : ?>
                                    <tr id="table_assign_stock_<?= $v_assign_stock->assign_item_id ?>">
                                        <td><?php echo $key + 1 ?></td>
                                        <td><?php echo $v_assign_stock->item_name ?></td>
                                        <td><?php echo $v_assign_stock->assign_inventory ?></td>
                                        <td><?= strftime(config_item('date_format'), strtotime($v_assign_stock->assign_date)); ?></td>
                                        <td class="hidden-print">
                                            <?php echo ajax_anchor(base_url("admin/stock/delete_assign_stock/" . $v_assign_stock->assign_item_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_assign_stock_" . $v_assign_stock->assign_item_id)); ?>
                                        </td>

                                    </tr>
                                    <?php
                                endforeach;
                                ?>
                            <?php endif; ?>
                            </tbody>
                            </table>
                            <?php
                        endforeach;
                            ?>
                        <?php else : ?>
                            <div class="panel-body">
                                <strong><?= lang('nothing_to_display') ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


