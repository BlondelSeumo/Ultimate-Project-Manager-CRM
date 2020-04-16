<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('export_report') ?></title>
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>

    <style type="text/css">
        @font-face {
            font-family: "Source Sans Pro", sans-serif;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: "Source Sans Pro", sans-serif;
            width: 100%;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        .name {
            font-size: 15px;
            font-weight: normal;
            margin: 0;
        <?php if(!empty($RTL)){?> text-align: right; /* RTL confidation*/
        <?php }?>
        }

        /* RTL confidation*/
        #details tr td {
        <?php if(!empty($RTL)){?> text-align: right; /* RTL confidation*/
        <?php }?>
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 1.5em;
            line-height: 1em;
            font-weight: normal;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-top: 10px;
        <?php if(!empty($RTL)){?> text-align: right; /* RTL confidation*/
        <?php }?>
        }

        table.items th {
            white-space: nowrap;
            font-weight: normal;
            background: #00175f;
            color: #fff;
            padding: .5rem .5rem;
        <?php if(!empty($RTL)){?> text-align: right; /* RTL confidation*/
        <?php }?>
        }

        table.items td {
            padding: .5rem .5rem;
            border: 1px solid #c6cad5;
            border-top: 0px;
            border-right: 0px;
        <?php if(!empty($RTL)){?> text-align: right; /* RTL confidation*/
        <?php }?>
        }

        table.items td:last-child {
            border-right: 1px solid #c6cad5;
        <?php if(!empty($RTL)){?> text-align: right; /* RTL confidation*/
        <?php }?>
        }

    </style>
</head>
<body>
<?php

if (!empty($project_details->client_id)) {
    $currency = $this->items_model->client_currency_symbol($project_details->client_id);
    $client_info = $this->invoice_model->check_by(array('client_id' => $project_details->client_id), 'tbl_client');
} else {
    $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
}
$comment_details = $this->db->where('project_id', $project_details->project_id)->get('tbl_task_comment')->result();
$all_milestones_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_milestones')->result();
$all_task_info = $this->db->where('project_id', $project_details->project_id)->order_by('task_id', 'DESC')->get('tbl_task')->result();
$all_bugs_info = $this->db->where('project_id', $project_details->project_id)->order_by('bug_id', 'DESC')->get('tbl_bug')->result();
$total_timer = $this->db->where(array('project_id' => $project_details->project_id, 'start_time !=' => 0, 'end_time !=' => 0,))->get('tbl_tasks_timer')->result();
$all_invoice_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_invoices')->result();
$invoice_outstanding = $this->invoice_model->client_outstanding($project_details->client_id, $project_details->project_id);
$all_estimates_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_estimates')->result();

$all_tickets_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_tickets')->result();

$all_expense_info = $this->db->where(array('project_id' => $project_details->project_id, 'type' => 'Expense'))->get('tbl_transactions')->result();

$total_expense = $this->db->select_sum('amount')->where(array('project_id' => $project_details->project_id, 'type' => 'Expense'))->get('tbl_transactions')->row();
$billable_expense = $this->db->select_sum('amount')->where(array('project_id' => $project_details->project_id, 'type' => 'Expense', 'billable' => 'Yes'))->get('tbl_transactions')->row();
$not_billable_expense = $this->db->select_sum('amount')->where(array('project_id' => $project_details->project_id, 'type' => 'Expense', 'billable' => 'No'))->get('tbl_transactions')->row();

$activities_info = $this->db->where(array('module' => 'project', 'module_field_id' => $project_details->project_id))->order_by('activity_date', 'desc')->get('tbl_activities')->result();

$project_hours = $this->items_model->calculate_project('project_hours', $project_details->project_id);

if ($project_details->billing_type == 'tasks_hours' || $project_details->billing_type == 'tasks_and_project_hours') {
    $tasks_hours = $this->items_model->total_project_hours($project_details->project_id, '', true);
}
$project_cost = $this->items_model->calculate_project('project_cost', $project_details->project_id);
$progress = $this->items_model->get_project_progress($project_details->project_id);
$this->load->helper('date');
$totalDays = round((human_to_unix($project_details->end_date . ' 00:00') - human_to_unix($project_details->start_date . ' 00:00')) / 3600 / 24);
$TotalGone = $totalDays;
$tprogress = 100;
if (human_to_unix($project_details->start_date . ' 00:00') < time() && human_to_unix($project_details->end_date . ' 00:00') > time()) {
    $TotalGone = round((human_to_unix($project_details->end_date . ' 00:00') - time()) / 3600 / 24);
    $tprogress = $TotalGone / $totalDays * 100;

}
if (human_to_unix($project_details->end_date . ' 00:00') < time()) {
    $TotalGone = 0;
    $tprogress = 0;
}
if (strtotime(date('Y-m-d')) > strtotime($project_details->end_date . '00:00')) {
    $lang = lang('days_gone');
} else {
    $lang = lang('days_left');
} ?>
<h2><?= lang('project_name') ?>
    : <?php if (!empty($project_details->project_name)) echo $project_details->project_name; ?></h2>
<blockquote><?php
    if (!empty($project_details->description)) {
        echo $project_details->description;
    }
    ?></blockquote>
<table id="details" class="clearfix">
    <tr>
        <td style="width: 40%;float: right">
            <div class="name"><strong><?= lang('project_no') ?> :</strong><?= $project_details->project_no; ?></div>
            <div class="name"><strong><?= lang('billing_type') ?> :</strong><?= lang($project_details->billing_type); ?></div>
            <div class="name"><strong><?= lang('project_cost') ?>
                    :</strong><?= display_money($project_cost, $currency->symbol); ?>
                <?php if (!empty($project_details) && $project_details->billing_type == 'project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_and_project_hours') { ?>
                <small style="font-size: font-size: 85%; color: #909fa7;">
                    <?= $project_details->hourly_rate . "/" . lang('hour') ?>
                    <?php } ?>
                </small>
            </div>
            <div class="name"><strong><?= lang('estimate_hours') ?>
                    :</strong><?= ($project_details->estimate_hours); ?> m
                <?php if (!empty($project_details) && $project_details->billing_type == 'project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_and_project_hours') { ?>
                <small style="font-size: font-size: 85%; color: #909fa7;">
                    <?= $project_details->hourly_rate . "/" . lang('hour') ?>
                    <?php } ?>
                </small>
            </div>
            <div class="name"><strong><?= lang('status') ?> :</strong><?= lang($project_details->project_status); ?>
            </div>
            <div class="name"><strong><?= lang('created_date') ?>
                    :</strong><?= strftime(config_item('date_format'), strtotime($project_details->created_time)) . ' ' . lang('on') . ' ' . display_time($project_details->created_time) ?>
            </div>
            <div class="name"><strong><?= lang('start_date') ?>
                    :</strong><?= strftime(config_item('date_format'), strtotime($project_details->start_date)) ?></div>
            <div class="name"><strong><?= lang('end_date') ?> :</strong><?php
                $text = '';
                if ($project_details->project_status != 'completed') {
                    if ($totalDays < 0) {
                        $overdueDays = $totalDays . ' ' . lang('days_gone');
                    }
                }

                ?>
                <?= strftime(config_item('date_format'), strtotime($project_details->end_date)) ?>
                <?php if (!empty($overdueDays)) {
                    echo lang('overdue') . ' ' . $overdueDays;
                } ?>
            </div>
            <div class="name"><strong><?= lang('completed') ?> : </strong><?= $progress ?> %
            </div>
            <br/>
            <?php

            $estimate_hours = $project_details->estimate_hours;
            $percentage = $this->items_model->get_estime_time($estimate_hours);
            $logged_hour = $this->items_model->calculate_project('project_hours', $project_details->project_id);
            if (!empty($tasks_hours)) {
                $logged_tasks_hours = $tasks_hours;
            } else {
                $logged_tasks_hours = 0;
            }
            $total_logged_hours = $logged_hour + $logged_tasks_hours;

            if ($total_logged_hours < $percentage) {
                $total_time = $percentage - $total_logged_hours;
                $worked = '<storng style="font-size: 15px;color:red">' . lang('left_works') . '</storng>';
            } else {
                $total_time = $total_logged_hours - $percentage;
                $worked = '<storng style="font-size: 15px;color:red" >' . lang('extra_works') . '</storng>';
            }

            $completed = count($this->db->where(array('project_id' => $project_details->project_id, 'task_status' => 'completed'))->get('tbl_task')->result());

            $total_task = count($all_task_info);
            if (!empty($total_task)) {
                if ($total_task != 0) {
                    $task_progress = $completed / $total_task * 100;
                }
                if ($task_progress > 100) {
                    $task_progress = 100;
                }
            } else {
                $task_progress = 0;

            }
            if (!empty($tasks_hours)) {
                $col_ = 'with:50%';
            } else {
                $col_ = '';
            }
            ?>
            <div class="name"><strong><?= $lang; ?> : </strong><?= $totalDays ?></div>

            <?php if (!empty($col_)) { ?>
                <div class="name"><strong><?= lang('project_hours') ?>
                        : </strong><?= $this->items_model->get_spent_time($project_hours); ?></div>
                <div class="name"><strong><?= lang('task_hours') ?>
                        : </strong><?= $this->items_model->get_spent_time($tasks_hours); ?></div>

                <div class="name"><strong><?= lang('billable') ?>
                        : </strong><?= $this->items_model->get_spent_time($tasks_hours); ?></div>
                <div class="name"><strong><?= lang('not_billable') ?>
                        <?php
                        $non_billable_time = 0;
                        foreach ($all_task_info as $v_n_tasks) {
                            if (!empty($v_n_tasks->billable) && $v_n_tasks->billable == 'No') {
                                $non_billable_time += $this->items_model->task_spent_time_by_id($v_n_tasks->task_id);
                            }
                        } ?>
                        : </strong><?= $this->items_model->get_spent_time($non_billable_time); ?></div>
            <?php } ?>
            <div class="name"><strong><?= lang('total_logged_hour'); ?> : </strong>
                <?php if ($project_details->billing_type == 'tasks_and_project_hours' || !empty($col_)) {
                    $total_hours = $project_hours + $tasks_hours;
                } else {
                    $total_hours = $project_hours;
                }
                ?>
                <?= $this->items_model->get_spent_time($total_hours); ?>
            </div>
            <div class="name"><strong><?= $worked ?>: </strong><?= $this->items_model->get_spent_time($total_time) ?>
            </div>
            <div class="name"><strong><?= lang('total_bill') ?>
                    : </strong><?= display_money($project_cost, $currency->symbol) ?></div>

        </td>
        <?php
        $paid_expense = 0;
        foreach ($all_expense_info as $v_expenses) {
            if ($v_expenses->invoices_id != 0) {
                $paid_expense += $this->invoice_model->calculate_to('paid_amount', $v_expenses->invoices_id);
            }
        }

        ?>
        <td style="width: 30%;float: right">
            <div class="name"><strong><?= lang('total') . ' ' . lang('expense') ?>
                    :</strong> <?= (display_money($total_expense->amount, $currency->symbol)) ?></div>
            <div class="name"><strong><?= lang('billable') . ' ' . lang('expense') ?>
                    :</strong> <?= (display_money($billable_expense->amount, $currency->symbol)) ?></div>
            <div class="name"><strong><?= lang('not_billable') . ' ' . lang('expense') ?>
                    :</strong> <?= (display_money($not_billable_expense->amount, $currency->symbol)) ?></div>
            <div class="name"><strong><?= lang('billed') . ' ' . lang('expense') ?>
                    :</strong> <?= (display_money($paid_expense, $currency->symbol)) ?></div>
            <div class="name"><strong><?= lang('unbilled') . ' ' . lang('expense') ?>
                    :</strong> <?= (display_money($billable_expense->amount - $paid_expense, $currency->symbol)) ?>
            </div>
            <br>
            <div class="name"><strong><?= lang('total') . ' ' . lang('invoice') . ' ' . lang('created') ?>
                    :</strong> <?= (!empty($all_invoice_info) ? count($all_invoice_info) : 0) ?></div>
            <div class="name"><strong><?= lang('outstanding_invoices') ?>
                    :</strong> <?= (display_money($invoice_outstanding, $currency->symbol)) ?></div>
            <div class="name"><strong><?= lang('overdue') . ' ' . lang('invoices') ?> :</strong> <?php
                $overdue = 0;
                foreach ($all_invoice_info as $v_invoices) {
                    $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                    if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                        $overdue += $this->invoice_model->calculate_to('invoice_cost', $v_invoices->invoices_id);
                    }

                }
                echo display_money($overdue, $currency->symbol);
                ?></div>
            <div class="name">
                <strong><?= lang('paid') . ' ' . lang('invoices') ?> :</strong> <?php
                $paid_invoice = 0;
                foreach ($all_invoice_info as $v_invoices) {
                    $paid_invoice += $this->invoice_model->get_sum('tbl_payments', 'amount', $array = array('invoices_id' => $v_invoices->invoices_id));
                }
                echo display_money($paid_invoice, $currency->symbol); ?>
            </div>
            </br>


        </td>
        <td style="width: 30%;float: right">
            <?php
            if (!empty($client_info)) {
                $client_name = $client_info->name;
                $address = $client_info->address;
                $city = $client_info->city;
                $zipcode = $client_info->zipcode;
                $country = $client_info->country;
                $phone = $client_info->phone;
            } else {
                $client_name = '-';
                $address = '-';
                $city = '-';
                $zipcode = '-';
                $country = '-';
                $phone = '-';
            }
            ?>
            <div>
                <strong style="font-size: 18px"><?= lang('client_info') ?> </strong>
                <div class="name"><?= $client_name; ?>
                    <?php if (!empty($address)) { ?><br><?= $address ?><?php } ?>
                    <?php if (!empty($city) || !empty($zipcode)) { ?><br><?= $city ?>, <?= $zipcode ?><?php } ?>
                    <?php if (!empty($country)) { ?><br><?= $country ?><?php } ?>
                    <?php if (!empty($phone)) { ?><br><?= lang('phone') ?>: <?= $phone ?><?php } ?>
                    <br>
                </div>
            </div>
            <br/>
            <div class="name"><strong><?= lang('total') . ' ' . lang('comments') ?>
                    :</strong> <?= (!empty($comment_details) ? count($comment_details) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('attachment') ?>
                    :</strong> <?= (!empty($project_files_info) ? count($project_files_info) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('milestones') ?>
                    :</strong> <?= (!empty($all_milestones_info) ? count($all_milestones_info) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('tasks') ?>
                    :</strong> <?= (!empty($all_task_info) ? count($all_task_info) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('bugs') ?>
                    :</strong> <?= (!empty($all_bugs_info) ? count($all_bugs_info) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('timesheet') ?>
                    :</strong> <?= (!empty($total_timer) ? count($total_timer) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('tickets') ?>
                    :</strong> <?= (!empty($all_tickets_info) ? count($all_tickets_info) : 0) ?></div>

            <div class="name"><strong><?= lang('total') . ' ' . lang('estimates') ?>
                    :</strong> <?= (!empty($all_estimates_info) ? count($all_estimates_info) : 0) ?></div>
        </td>

    </tr>
</table>
<br/>
<br/>
<strong style="font-size: 18px;"><?= lang('project') . ' ' . lang('member') . ' ' . lang('overview') ?> </strong>
<br/>
<table class="items" width="100%">
    <thead>
    <tr>
        <th><?= lang('name') ?></th>
        <th><?= lang('total') . ' ' . lang('comments') ?></th>
        <th><?= lang('total') . ' ' . lang('attachment') ?></th>
        <th><?= lang('total') . ' ' . lang('tasks') . ' ' . lang('assigned') ?></th>
        <th><?= lang('total') . ' ' . lang('bugs') . ' ' . lang('assigned') ?></th>
        <?php if (!empty($col_)) { ?>
            <th><?= lang('project_hours') ?></th>
            <th><?= lang('task_hours') ?></th>
        <?php } ?>
        <th><?= lang('time_spend') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $permission_users = $this->items_model->allowed_user('57');

    foreach ($permission_users as $v_users) {
        $user_info = $this->db->where('user_id', $v_users->user_id)->get('tbl_account_details')->row();
        $where = array('user_id' => $v_users->user_id, 'project_id' => $project_details->project_id);
        $total_u_comments = count($this->db->where($where)->get('tbl_task_comment')->result());
        $total_u_attachment = count($this->db->where($where)->get('tbl_task_attachment')->result());
        $total_u_timer = $this->db->where($where)->get('tbl_tasks_timer')->result();

        $total_u_tasks = 0;
        if (!empty($all_task_info)) {
            foreach ($all_task_info as $v_u_tasks) {
                if ($v_u_tasks->permission == 'all') {
                    $total_u_tasks += 1;
                    $total_u_task_hours[$v_users->user_id][$v_u_tasks->task_id] = $this->items_model->task_spent_time_by_staff($v_u_tasks->task_id, $v_users->user_id);
                } else {
                    $t_c_permission = json_decode($v_u_tasks->permission);
                    foreach ($t_c_permission as $u_user_id => $u_v_permission) {
                        if ($u_user_id == $v_users->user_id) {
                            $total_u_tasks += 1;
                            $total_u_task_hours[$v_users->user_id][$v_u_tasks->task_id] = $this->items_model->task_spent_time_by_staff($v_u_tasks->task_id, $v_users->user_id);
                        }
                    }
                }
            }
        }
        $total_u_bugs = 0;
        if (!empty($all_bugs_info)) {
            foreach ($all_bugs_info as $v_u_bugs) {
                if ($v_u_bugs->permission == 'all') {
                    $total_u_bugs += 1;
                } else {
                    $t_c_permission = json_decode($v_u_bugs->permission);
                    foreach ($t_c_permission as $u_user_id => $u_v_permission) {
                        if ($u_user_id == $v_users->user_id) {
                            $total_u_bugs += 1;
                        }
                    }
                }
            }
        }

        $total_timespend = 0;
        if (!empty($total_u_timer)) {
            foreach ($total_u_timer as $u_timer) {
                $total_timespend += $u_timer->end_time - $u_timer->start_time;
            }
        }
        $total_u_timespend = 0;

        if (!empty($col_)) {
            if (!empty($total_u_task_hours)) {
                foreach (end($total_u_task_hours) as $u_t_timer) {
                    $total_u_timespend += $u_t_timer;
                }
            }
        }

        ?>
        <tr>
            <td><?= $user_info->fullname ?></td>
            <td style="text-align: center"><?= $total_u_comments ?></td>
            <td style="text-align: center"><?= $total_u_attachment ?></td>
            <td style="text-align: center"><?= $total_u_tasks ?></td>
            <td style="text-align: center"><?= $total_u_bugs ?></td>
            <?php if (!empty($col_)) { ?>
                <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($total_timespend) ?></td>
                <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($total_u_timespend) ?></td>
            <?php } ?>
            <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($total_timespend + $total_u_timespend) ?></td>

        </tr>
    <?php }
    ?>
    </tbody>
</table>
<br/>
<br/>
<div class=""><strong style="font-size: 18px;"><?= lang('milestone') . ' ' . lang('overview') ?> </strong>
    <br/>
    <table class="items" width="100%">
        <thead>
        <tr>
            <th><?= lang('milestone_name') ?></th>
            <th><?= lang('description') ?></th>
            <th class="col-date"><?= lang('start_date') ?></th>
            <th class="col-date"><?= lang('due_date') ?></th>
            <th><?= lang('responsible') ?></th>
            <th><?= lang('total') . ' ' . lang('tasks') . ' ' . lang('assigned') ?></th>
            <th><?= lang('progress') ?></th>
            <th><?= lang('logged_time') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($all_milestones_info)) {
            foreach ($all_milestones_info as $key => $v_milestones) {
                $progress = $this->items_model->calculate_milestone_progress($v_milestones->milestones_id);
                $all_milestones_task = $this->db->where('milestones_id', $v_milestones->milestones_id)->get('tbl_task')->result();

                $logged_hour = 0;
                foreach ($all_milestones_task as $v_m_taskss) {
                    $logged_hour += $this->items_model->task_spent_time_by_id($v_m_taskss->task_id);
                }
                $responsibe = $this->db->where('user_id', $v_milestones->user_id)->get('tbl_account_details')->row();
                ?>
                <tr>
                    <td><?= $v_milestones->milestone_name ?></td>
                    <td><?= $v_milestones->description ?></td>
                    <td style="text-align: center"><?= strftime(config_item('date_format'), strtotime($v_milestones->start_date)) ?></td>
                    <?php
                    $due_date = $v_milestones->end_date;
                    $due_time = strtotime($due_date);
                    $current_time = strtotime(date('Y-m-d'));
                    ?>
                    <td style="text-align: center"><?php echo strftime(config_item('date_format'), strtotime($due_date));
                        if ($current_time > $due_time && $progress < 100) {
                            echo "<span style='color:red'>" . lang('overdue') . "</span>";
                        }
                        ?></td>
                    <td style="text-align: center"><?= $responsibe->fullname ?></td>
                    <td style="text-align: center"><?= count($all_milestones_task) ?></td>
                    <td style="text-align: center"><?= $progress ?></td>
                    <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($logged_hour) ?></td>
                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td colspan="8"><?= lang('nothing_to_display') ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>
<br/>
<br/>
<div class=""><strong style="font-size: 18px;"><?= lang('tasks') . ' ' . lang('overview') ?> </strong>
    <br/>
    <table class="items" width="100%">
        <thead>
        <tr>
            <th><?= lang('tasks') . ' ' . lang('name') ?></th>
            <th><?= lang('status') ?></th>
            <th><?= lang('start_date') ?></th>
            <th><?= lang('due_date') ?></th>
            <th><?= lang('total') . ' ' . lang('member') . ' ' . lang('assigned') ?></th>
            <th><?= lang('total') . ' ' . lang('comments') ?></th>
            <th><?= lang('total') . ' ' . lang('attachment') ?></th>
            <th><?= lang('time_spend') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($all_task_info)) {
            foreach ($all_task_info as $v_u_tasks) {
                if ($v_u_tasks->permission == 'all') {
                    $task_users = count($this->items_model->allowed_user('54'));
                } else {
                    $task_users = 0;
                    $total_task_users = json_decode($v_u_tasks->permission);
                    foreach ($total_task_users as $user_id => $u_v_permission) {
                        $task_users += count($user_id);
                    }
                }
                $t_where = array('task_id' => $v_u_tasks->task_id);
                $total_u_comments = count($this->db->where($t_where)->get('tbl_task_comment')->result());
                $total_u_attachment = count($this->db->where($t_where)->get('tbl_task_attachment')->result());
                ?>
                <tr>
                    <td><?= $v_u_tasks->task_name ?></td>
                    <td style="text-align: center"><?= lang($v_u_tasks->task_status) ?></td>
                    <td style="text-align: center"><?= strftime(config_item('date_format'), strtotime($v_u_tasks->task_start_date)) ?></td>
                    <td style="text-align: center"><?= strftime(config_item('date_format'), strtotime($v_u_tasks->due_date)) ?></td>
                    <td style="text-align: center"><?= $task_users ?></td>
                    <td style="text-align: center"><?= $total_u_comments ?></td>
                    <td style="text-align: center"><?= $total_u_attachment ?></td>
                    <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($this->items_model->task_spent_time_by_id($v_u_tasks->task_id)) ?></td>

                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td colspan="7"><?= lang('nothing_to_display') ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>
<br/>
<br/>
<div class=""><strong style="font-size: 18px;"><?= lang('bugs') . ' ' . lang('overview') ?> </strong>
    <br/>
    <table class="items" width="100%">
        <thead>
        <tr>
            <th><?= lang('bug_title') ?></th>
            <th><?= lang('status') ?></th>
            <th><?= lang('priority') ?></th>
            <th><?= lang('reporter') ?></th>
            <th><?= lang('total') . ' ' . lang('tasks') ?></th>
            <th><?= lang('total') . ' ' . lang('member') . ' ' . lang('assigned') ?></th>
            <th><?= lang('total') . ' ' . lang('comments') ?></th>
            <th><?= lang('total') . ' ' . lang('attachment') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($all_bugs_info)) {
            foreach ($all_bugs_info as $v_u_bugs) {
                $b_reporter = $this->db->where('user_id', $v_u_bugs->reporter)->get('tbl_users')->row();
                if ($v_u_bugs->permission == 'all') {
                    $bugs_users = count($this->items_model->allowed_user('58'));
                } else {
                    $bugs_users = 0;
                    $total_bugs_users = json_decode($v_u_bugs->permission);
                    foreach ($total_bugs_users as $user_id => $u_v_permission) {
                        $bugs_users += count($user_id);
                    }
                }
                $t_where = array('bug_id' => $v_u_bugs->bug_id);
                $total_u_comments = count($this->db->where($t_where)->get('tbl_task_comment')->result());
                $total_u_attachment = count($this->db->where($t_where)->get('tbl_task_attachment')->result());
                $total_u_tasks = count($this->db->where($t_where)->get('tbl_task')->result());
                ?>
                <tr>
                    <td><?= $v_u_bugs->bug_title ?></td>
                    <td style="text-align: center"><?= lang($v_u_bugs->bug_status) ?></td>
                    <td style="text-align: center"><?= ucfirst($v_u_bugs->priority) ?></td>
                    <td style="text-align: center"><?= $b_reporter->username ?></td>
                    <td style="text-align: center"><?= $total_u_tasks ?></td>
                    <td style="text-align: center"><?= $bugs_users ?></td>
                    <td style="text-align: center"><?= $total_u_comments ?></td>
                    <td style="text-align: center"><?= $total_u_attachment ?></td>

                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td colspan="7"><?= lang('nothing_to_display') ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>

<br/>
<br/>
<div class=""><strong
        style="font-size: 18px;"><?= lang('project') . ' ' . lang('timesheet') . ' ' . lang('overview') ?> </strong>
    <br/>
    <table class="items" width="100%">
        <thead>
        <tr>
            <th><?= lang('member') ?></th>
            <th><?= lang('start_time') ?></th>
            <th><?= lang('stop_time') ?></th>
            <th><?= lang('reason') ?></th>
            <th class="col-time"><?= lang('time_spend') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($total_timer)) {
            foreach ($total_timer as $v_timer) {
                $aproject_info = $this->db->where(array('project_id' => $v_timer->project_id))->get('tbl_project')->row();
                if (!empty($aproject_info)) {
                    $profile_info = $this->db->where(array('user_id' => $v_timer->user_id))->get('tbl_account_details')->row();
                    $edit_user_info = $this->db->where(array('user_id' => $v_timer->edited_by))->get('tbl_users')->row();
                    ?>
                    <tr>
                        <td><?= $profile_info->fullname ?></td>
                        <td style="text-align: center"><?= display_datetime($v_timer->start_time, true) ?></td>
                        <td style="text-align: center"><?= display_datetime($v_timer->end_time, true) ?></td>
                        <td><?= $v_timer->reason ?></td>
                        <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($v_timer->end_time - $v_timer->start_time) ?></td>
                    </tr>
                <?php }
            }
        } else {
            ?>
            <tr>
                <td colspan="7"><?= lang('nothing_to_display') ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>
<?php
if (!empty($all_task_info)) {
    ?>
    <br/>
    <br/>
    <div class=""><strong
            style="font-size: 18px;"><?= lang('tasks') . ' ' . lang('timesheet') . ' ' . lang('overview') ?> </strong>
        <br/>
        <table class="items" width="100%">
            <thead>
            <tr>
                <th><?= lang('member') ?></th>
                <th><?= lang('task_name') ?></th>
                <th><?= lang('start_time') ?></th>
                <th><?= lang('stop_time') ?></th>
                <th><?= lang('reason') ?></th>
                <th class="col-time"><?= lang('time_spend') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($all_task_info as $v_u_tasks) {
                $total_tasks_timer = $this->db->where(array('task_id' => $v_u_tasks->task_id))->get('tbl_tasks_timer')->result();
                if (!empty($total_tasks_timer)) {
                    foreach ($total_tasks_timer as $v_task_timer) {
                        $task_info = $this->db->where(array('task_id' => $v_u_tasks->task_id))->get('tbl_task')->row();
                        if (!empty($task_info)) {
                            $profile_info = $this->db->where(array('user_id' => $v_task_timer->user_id))->get('tbl_account_details')->row();
                            $edit_user_info = $this->db->where(array('user_id' => $v_task_timer->edited_by))->get('tbl_users')->row();
                            ?>
                            <tr>
                                <td><?= $profile_info->fullname ?></td>
                                <td><?= $v_u_tasks->task_name ?></td>
                                <td style="text-align: center"><?= display_datetime($v_task_timer->start_time, true) ?></td>
                                <td style="text-align: center"><?= display_datetime($v_task_timer->end_time, true) ?></td>
                                <td><?= $v_task_timer->reason ?></td>
                                <td style="text-align: center"><?= $this->items_model->get_time_spent_pain_result($v_task_timer->end_time - $v_task_timer->start_time) ?></td>
                            </tr>
                        <?php }
                    }
                }
            }
            ?>

            </tbody>
        </table>
    </div>
<?php } ?>
<br/>
<br/>
<div class=""><strong
        style="font-size: 18px;"><?= lang('tickets') . ' ' . lang('overview') ?> </strong>
    <br/>
    <table class="items" width="100%">
        <thead>
        <tr>
            <th><?= lang('ticket_code') ?></th>
            <th><?= lang('subject') ?></th>
            <th class="col-date"><?= lang('date') ?></th>
            <?php if ($this->session->userdata('user_type') == '1') { ?>
                <th><?= lang('reporter') ?></th>
            <?php } ?>
            <th><?= lang('department') ?></th>
            <th><?= lang('status') ?></th>
            <th><?= lang('total') . ' ' . lang('comments') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($all_tickets_info)) {
            foreach ($all_tickets_info as $v_tickets_info) {
                $profile_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_account_details')->row();
                $tickets_commets = count($this->db->where(array('tickets_id' => $v_tickets_info->tickets_id))->get('tbl_tickets_replies')->result());
                $dept_info = $this->db->where(array('departments_id' => $v_tickets_info->departments_id))->get('tbl_departments')->row();
                if (!empty($dept_info)) {
                    $dept_name = $dept_info->deptname;
                } else {
                    $dept_name = '-';
                }
                ?>
                <tr>
                    <td><?= $v_tickets_info->ticket_code ?></td>
                    <td><?= $v_tickets_info->subject ?></td>
                    <td style="text-align: center"><?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)) ?></td>
                    <?php if ($this->session->userdata('user_type') == '1') { ?>
                        <td><?= $profile_info->fullname ?></td>
                    <?php } ?>
                    <td style="text-align: center"><?= $dept_name ?></td>
                    <?php
                    if ($v_tickets_info->status == 'in_progress') {
                        $status = 'In Progress';
                    } else {
                        $status = $v_tickets_info->status;
                    }
                    ?>
                    <td style="text-align: center"><?= ucfirst($status) ?></td>
                    <td style="text-align: center"><?= $tickets_commets ?></td>
                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td colspan="7"><?= lang('nothing_to_display') ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
