<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('export_report') ?></title>
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
        }

        header {

            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        .name {
            font-size: 15px;
            font-weight: normal;
            margin: 0;
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
        }

        table.items th {
            white-space: nowrap;
            font-weight: normal;
            background: #00175f;
            color: #fff;
            padding: .5rem .5rem;
        }

        table.items td {
            padding: .5rem .5rem;
            border: 1px solid #c6cad5;
            border-top: 0px;
            border-right: 0px;

        }

        table.items td:last-child {
            border-right: 1px solid #c6cad5;
        }

    </style>
</head>
<body>
<?php
$currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
$comment_details = $this->db->where('task_id', $task_details->task_id)->get('tbl_task_comment')->result();
$total_timer = $this->db->where(array('task_id' => $task_details->task_id, 'start_time !=' => 0, 'end_time !=' => 0,))->get('tbl_tasks_timer')->result();
$task_time = $this->tasks_model->task_spent_time_by_id($task_details->task_id);
?>
<h2><?= lang('project_name') ?>
    : <?php if (!empty($task_details->task_name)) echo $task_details->task_name; ?></h2>
<blockquote style="word-wrap: break-word;"><?php
    if (!empty($task_details->task_description)) {
        echo $task_details->task_description;
    }
    ?></blockquote>
<table id="details" class="clearfix">
    <tr>
        <td style="width: 50%">
            <?php
            if (!empty($task_details->project_id)):
                $project_info = $this->db->where('project_id', $task_details->project_id)->get('tbl_project')->row();
                $milestones_info = $this->db->where('milestones_id', $task_details->milestones_id)->get('tbl_milestones')->row();
                ?>
                <div class="name"><strong><?= lang('project_name') ?>: <?php if (!empty($project_info->project_name)){ echo $project_info->project_name;}else{echo '-';} ?></strong></div>
                <div class="name"><strong><?= lang('milestone') ?>: <?php if (!empty($milestones_info->milestone_name)){ echo $milestones_info->milestone_name;}else{ echo '-';} ?></strong></div>
            <?php endif ?>
            <?php
            if (!empty($task_details->opportunities_id)):
                $opportunity_info = $this->db->where('opportunities_id', $task_details->opportunities_id)->get('tbl_opportunities')->row();
                ?>
                <div class="name"><strong><?= lang('opportunity_name') ?>
                        :</strong><?php if (!empty($opportunity_info->opportunity_name)) echo $opportunity_info->opportunity_name; ?>
                </div>
            <?php endif ?>
            <?php
            if (!empty($task_details->leads_id)):
                $leads_info = $this->db->where('leads_id', $task_details->leads_id)->get('tbl_leads')->row();
                ?>
                <div class="name"><strong><?= lang('leads_name') ?>
                        :</strong><?php if (!empty($leads_info->lead_name)) echo $leads_info->lead_name; ?></div>
            <?php endif ?>
            <?php
            if (!empty($task_details->bug_id)):
                $bugs_info = $this->db->where('bug_id', $task_details->bug_id)->get('tbl_bug')->row();
                ?>
                <div class="name"><strong><?= lang('bug_title') ?>
                        :</strong><?php if (!empty($bugs_info->bug_title)) echo $bugs_info->bug_title; ?></div>
            <?php endif ?>
            <?php
            if (!empty($task_details->goal_tracking_id)):
                $goal_tracking_info = $this->db->where('goal_tracking_id', $task_details->goal_tracking_id)->get('tbl_goal_tracking')->row();
                ?>
                <div class="name"><strong><?= lang('goal_tracking') ?>
                        :</strong><?php if (!empty($goal_tracking_info->subject)) echo $goal_tracking_info->subject; ?>
                </div>
            <?php endif ?>
            <div class="name"><strong><?= lang('task_status') ?> :</strong><?= lang($task_details->task_status); ?>
            </div>
            <?php if (!empty($task_details->billable) && $task_details->billable == 'Yes') {
                $total_time = $task_time / 3600;
                $total_cost = $total_time * $task_details->hourly_rate;
                ?>
                <div class="name"><strong><?= lang('project_cost') ?>
                        :</strong><?= display_money($total_cost, $currency->symbol); ?>
                    <?php if (!empty($task_details->hourly_rate)) { ?>
                    <small style="font-size: font-size: 85%; color: #909fa7;">
                        <?= $task_details->hourly_rate . "/" . lang('hour') ?>
                        <?php } ?>
                    </small>
                </div>
            <?php } ?>
            <div class="name"><strong><?= lang('estimate_hours') ?>
                    :</strong><?php if (!empty($task_details->task_hour)) echo $task_details->task_hour; ?> m
                <?php if (!empty($task_details->hourly_rate)) { ?>
                <small style="font-size: font-size: 85%; color: #909fa7;">
                    <?= $task_details->hourly_rate . "/" . lang('hour') ?>
                    <?php } ?>
                </small>
            </div>
            <div class="name"><strong><?= lang('created_by') ?>
                    :</strong><?php
                if (!empty($task_details->created_by)) {
                    echo $this->db->where('user_id', $task_details->created_by)->get('tbl_account_details')->row()->fullname;
                }
                ?>
            </div>
            <div class="name"><strong><?= lang('created_date') ?>
                    :</strong><?= strftime(config_item('date_format'), strtotime($task_details->task_created_date)) . ' ' . lang('on') . ' ' . display_time($task_details->task_created_date) ?>
            </div>
            <div class="name"><strong><?= lang('start_date') ?>
                    :</strong><?= strftime(config_item('date_format'), strtotime($task_details->task_start_date)) ?>
            </div>
            <div class="name"><strong><?= lang('due_date') ?> :</strong>
                <?= strftime(config_item('date_format'), strtotime($task_details->due_date)) ?></div>

        </td>

        <td style="width: 50%">
            <div class="name"><strong><?= lang('billable') ?>
                    :</strong><?php if ($task_details->billable == 'Yes') {
                    $text = lang('yes');
                } else {
                    $text = lang('no');
                };
                echo $text;
                ?>
            </div>
            <div class="name"><strong><?= lang('completed') ?> : </strong><?= $task_details->task_progress ?> %</div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('comments') ?>
                    :</strong> <?= (!empty($comment_details) ? count($comment_details) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('attachment') ?>
                    :</strong> <?= (!empty($project_files_info) ? count($project_files_info) : 0) ?></div>
            <div class="name"><strong><?= lang('total') . ' ' . lang('timesheet') ?>
                    :</strong> <?= (!empty($total_timer) ? count($total_timer) : 0) ?></div>
            <?php
            if (!empty($task_details->billable) && $task_details->billable == 'Yes') {
                $total_time = $task_time / 3600;
                $total_cost = $total_time * $task_details->hourly_rate;
                ?>
                <div class="name"><strong><?= lang('total_bill') ?>
                        : </strong><?= display_money($total_cost, $currency->symbol) ?></div>
            <?php } ?>
            <?php
            $estimate_hours = $task_details->task_hour;
            $percentage = $this->tasks_model->get_estime_time($estimate_hours);
            if ($task_time < $percentage) {
                $total_time = $percentage - $task_time;
                $worked = '<storng style="font-size: 15px;"  class="required">' . lang('left_works') . '</storng>';
            } else {
                $total_time = $task_time - $percentage;
                $worked = '<storng style="font-size: 15px" class="required">' . lang('extra_works') . '</storng>';
            } ?>

            <div class="name"><strong><?= lang('tasks_hours') ?>
                    : </strong><?= $this->tasks_model->get_spent_time($task_time); ?></div>
            <div class="name"><strong><?= $worked ?>: </strong><?= $this->tasks_model->get_spent_time($total_time) ?>
            </div>

        </td>

    </tr>
</table>
<br/>
<br/>
<strong style="font-size: 18px;"><?= lang('member') . ' ' . lang('overview') ?> </strong>
<br/>
<table class="items" width="100%">
    <thead>
    <tr>
        <th><?= lang('name') ?></th>
        <th><?= lang('total') . ' ' . lang('comments') ?></th>
        <th><?= lang('total') . ' ' . lang('attachment') ?></th>
        <th><?= lang('time_spend') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $permission_users = $this->tasks_model->allowed_user('54');

    foreach ($permission_users as $v_users) {
        $user_info = $this->db->where('user_id', $v_users->user_id)->get('tbl_account_details')->row();
        $where = array('user_id' => $v_users->user_id, 'task_id' => $task_details->task_id);
        $total_u_comments = count($this->db->where($where)->get('tbl_task_comment')->result());
        $total_u_attachment = count($this->db->where($where)->get('tbl_task_attachment')->result());
        $total_u_timer = $this->db->where($where)->get('tbl_tasks_timer')->result();

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
            <td style="text-align: center"><?= $this->tasks_model->get_time_spent_pain_result($total_timespend + $total_u_timespend) ?></td>

        </tr>
    <?php }
    ?>
    </tbody>
</table>
<br/>
<br/>
<div class=""><strong
        style="font-size: 18px;"><?= lang('timesheet') . ' ' . lang('overview') ?> </strong>
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
        $total_tasks_timer = $this->db->where(array('task_id' => $task_details->task_id))->get('tbl_tasks_timer')->result();
        if (!empty($total_tasks_timer)) {
            foreach ($total_tasks_timer as $v_task_timer) {
                $task_info = $this->db->where(array('task_id' => $task_details->task_id))->get('tbl_task')->row();
                if (!empty($task_info)) {
                    $profile_info = $this->db->where(array('user_id' => $v_task_timer->user_id))->get('tbl_account_details')->row();
                    $edit_user_info = $this->db->where(array('user_id' => $v_task_timer->edited_by))->get('tbl_users')->row();
                    ?>
                    <tr>
                        <td><?= $profile_info->fullname ?></td>
                        <td style="text-align: center"><?= strftime(config_item('date_format'), ($v_task_timer->start_time)) . ' ' . display_time($v_task_timer->start_time, true) ?></td>
                        <td style="text-align: center"><?= strftime(config_item('date_format'), ($v_task_timer->end_time)) . ' ' . display_time($v_task_timer->end_time, true) ?></td>
                        <td><?= $v_task_timer->reason ?></td>
                        <td style="text-align: center"><?= $this->tasks_model->get_time_spent_pain_result($v_task_timer->end_time - $v_task_timer->start_time) ?></td>
                    </tr>
                <?php }
            }
        }
        ?>

        </tbody>
    </table>
</div>
</body>
</html>
