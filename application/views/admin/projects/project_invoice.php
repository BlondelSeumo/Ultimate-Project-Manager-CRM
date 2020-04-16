<form
    action="<?php echo base_url() ?>admin/projects/preview_invoice/<?php if (!empty($project_info->project_id)) echo $project_info->project_id; ?>"
    method="post" class="form-horizontal form-groups-bordered">
    <div class="panel panel-custom">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('preview_invoice') ?></h4>
        </div>
        <div class="modal-body wrap-modal wrap">

            <div class="col-sm-12">
                <div class="form-group" id="border-none">
                    <div class="checkbox c-radio needsclick">
                        <label
                            class="needsclick <?= ($project_info->billing_type == 'tasks_hours' || $project_info->billing_type == 'project_timer') ? 'disabled' : '' ?>">
                            <input checked type="radio" name="items_name"
                                   value="single_line" <?= ($project_info->billing_type == 'tasks_hours' || $project_info->billing_type == 'project_timer') ? 'disabled' : '' ?> >
                            <span class="fa fa-circle"></span><?= lang('single_line') ?>
                            <i title="<?= lang('single_line_help') ?>"
                               class="fa fa-question-circle" data-html="true" data-toggle="tooltip"
                               data-placement="top"></i>
                        </label>
                    </div>
                    <div class="checkbox c-radio needsclick">
                        <label
                            class="needsclick <?= ($project_info->billing_type == 'tasks_hours' || $project_info->billing_type == 'fixed_rate') ? 'disabled' : '' ?>">
                            <input <?= ($project_info->billing_type == 'tasks_hours' || $project_info->billing_type == 'fixed_rate') ? 'disabled' : '' ?>
                                type="radio" name="items_name" value="project_timer">
                            <span class="fa fa-circle"></span><?= lang('project_timer') ?>
                            <i title="<?= lang('project_timer_help') ?>"
                               class="fa fa-question-circle" data-html="true" data-toggle="tooltip"
                               data-placement="top"></i>
                        </label>
                    </div>
                    <div class="checkbox c-radio needsclick">
                        <label
                            class="needsclick <?= ($project_info->billing_type == 'fixed_rate') ? 'disabled' : '' ?>">
                            <input <?= ($project_info->billing_type == 'tasks_hours' || $project_info->billing_type == 'tasks_and_project_hours') ? 'checked' : '' ?> <?= ($project_info->billing_type == 'fixed_rate') ? 'disabled' : '' ?>
                                type="radio" name="items_name" value="task_per_item">
                            <span class="fa fa-circle"></span><?= lang('task_per_item') ?>
                            <i title="<?= lang('task_per_item_help') ?>"
                               class="fa fa-question-circle" data-html="true" data-toggle="tooltip"
                               data-placement="top"></i>
                        </label>
                    </div>
                    <div class="checkbox c-radio needsclick">
                        <label
                            class="needsclick <?= ($project_info->billing_type == 'fixed_rate') ? 'disabled' : '' ?>">
                            <input id="" <?= ($project_info->billing_type == 'fixed_rate') ? 'disabled' : '' ?>
                                   type="radio" name="items_name" value="all_timesheet_individually">
                            <span class="fa fa-circle"></span><?= lang('all_timesheet_individually') ?>
                            <i title="<?= lang('all_timesheet_individually_help') ?>"
                               class="fa fa-question-circle" data-toggle="tooltip" data-html="true"
                               data-placement="top"></i>
                        </label>
                    </div>
                </div>
                <?php
                $all_task_info = $this->db->where('project_id', $project_info->project_id)->order_by('task_id', 'DESC')->get('tbl_task')->result();
                $all_expense_info = $this->db->where(array('project_id' => $project_info->project_id, 'type' => 'Expense'))->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                if (!empty($all_task_info)) { ?>
                    <div class="form-group">
                        <a href="#"
                           onclick="slideToggle('#tasks_who_will_be_billed'); return false;"><b><?= lang('see_task_on_invoice') ?></b></a>
                        <div id="tasks_who_will_be_billed" style="display: none;">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox"
                                           id="select_all_tasks"
                                           class="invoice_select_all_tasks">
                                    <span class="fa fa-check"></span><?= lang('select_all') . ' ' . lang('task') ?>
                                </label>
                            </div>
                            <hr class="mr0 mb0 mt-sm">
                            <?php foreach ($all_task_info as $v_tasks) { ?>
                                <div class="col-sm-10">
                                    <div class="checkbox c-checkbox">
                                        <label>
                                            <input value="<?= $v_tasks->task_id ?>" checked name="tasks[]"
                                                   class="tasks_list" type="checkbox">
                                            <span class="fa fa-check"></span>
                                            <strong class="inline-block"><?= $v_tasks->task_name ?>
                                                <?php
                                                $time = $this->items_model->get_spent_time($this->items_model->task_spent_time_by_id($v_tasks->task_id), true);
                                                if ($time != "0 : 0 : 0") {
                                                    echo '<small><strong>' . $time . '</strong></small>';
                                                } else {
                                                    ?>
                                                    <small class="text-danger"><?= lang('no_timer_for_task') ?></small>
                                                <?php }
                                                ?>

                                        </label>
                                    </div>
                                </div>
                                <?php
                                if ($v_tasks->task_status == 'completed') {
                                    $label = 'success';
                                } elseif ($v_tasks->task_status == 'not_started') {
                                    $label = 'info';
                                } elseif ($v_tasks->task_status == 'deferred') {
                                    $label = 'danger';
                                } else {
                                    $label = 'warning';
                                }
                                ?>
                                <div class="col-sm-2 mt-sm ">
                                    <small class=""><strong
                                            class="inline-block label label-<?= $label ?>"><?= lang($v_tasks->task_status) ?></strong>
                                    </small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <strong class="text-danger"> <?= lang('no_tasks_to_bill_in_invoice'); ?></strong>
                <?php }
                if (!empty($all_expense_info)) {
                    ?>
                    <div class="form-group">
                        <a href="#"
                           onclick="slideToggle('#expense_who_will_be_billed'); return false;"><b><?= lang('see_expense_on_invoice') ?></b></a>
                        <div id="expense_who_will_be_billed" style="display: none;">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox"
                                           id="select_all_expense"
                                           class="invoice_select_all_tasks">
                                    <span class="fa fa-check"></span><?= lang('select_all') . ' ' . lang('expense') ?>
                                </label>
                            </div>
                            <hr class="mr0 mb0 mt-sm">
                            <?php foreach ($all_expense_info as $v_expense) {
                                $category_info = $this->db->where('expense_category_id', $v_expense->category_id)->get('tbl_expense_category')->row();
                                if (!empty($category_info)) {
                                    $category = $category_info->expense_category;
                                } else {
                                    $category = 'Undefined Category';
                                }
                                $curency = $this->items_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                ?>
                                <div class="col-sm-10">
                                    <div class="checkbox c-checkbox">
                                        <label>
                                            <input name="expense[]" value="<?= $v_expense->transactions_id ?>" checked
                                                   class="expense_list" type="checkbox">
                                            <span class="fa fa-check"></span>
                                            <strong
                                                class="inline-block"><?= $category . ' [' . $v_expense->name . ']' ?></strong>


                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2 mt-sm ">
                                    <small class=""><strong
                                            class="inline-block"><?= display_money($v_expense->amount, $curency->symbol) ?></strong>
                                    </small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
                if (!empty($all_task_info)) { ?>
                    <strong class="text-danger"><?= lang('all_billed_tasks_marked'); ?></strong>
                <?php } ?>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary"><?= lang('invoice_project') ?></button>
        </div>
    </div>
</form>
<script>
    function slideToggle($id) {
        $($id).slideToggle("slow");
    }
    $(document).ready(function () {
        $("#select_all_tasks").click(function () {
            $(".tasks_list").prop('checked', $(this).prop('checked'));
        });
        $("#select_all_expense").click(function () {
            $(".expense_list").prop('checked', $(this).prop('checked'));
        });
        $('[data-toggle="popover"]').popover();

    });
</script>
