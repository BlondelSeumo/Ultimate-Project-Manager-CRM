<style>

    .menu-border-transparent {
        border-color: transparent !important;
        height: 40px;
        color: #a9a3a3;
        background-color: rgba(255, 255, 255, .1);
        /*width: 100%;*/
    }

    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
    .inner-addon {
        position: relative;
    }
    .left-addon .fa {
        left: 0px;
    }
    .inner-addon .fa {
        position: absolute;
        pointer-events: none;
        padding: 13px;
    }
    .left-addon input {
        padding-left: 30px;
    }


</style>
<aside class="aside">
    <!-- START Sidebar (left)-->
    <?php
    $user_id = $this->session->userdata('user_id');
    $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
    ?>
    <div class="aside-inner">
        <nav data-sidebar-anyclick-close="" class="sidebar <?= config_item('show-scrollbar') ?>">
            <!-- START sidebar nav-->
            <ul class="nav">
                <!-- START user info-->
                <li class="has-user-block">
                    <a href="<?= base_url('admin/user/user_details/' . $user_id) ?>">
                        <div id="user-block" class="block">
                            <div class="item user-block">
                                <!-- User picture-->
                                <div class="user-block-picture">
                                    <div class="user-block-status">
                                        <img src="<?= base_url() . $profile_info->avatar ?>" alt="Avatar" width="60"
                                             height="60"
                                             class="img-thumbnail img-circle">
                                        <div class="circle circle-success circle-lg"></div>
                                    </div>
                                </div>
                                <!-- Name and Job-->
                                <div class="user-block-info">
                                    <span class="user-block-name"><?= $profile_info->fullname ?></span>
                                    <span class="user-block-role"></i> <?= lang('online') ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- END user info-->
            <div class="inner-addon left-addon" style="width: 95%">
                <i class="fa fa-search"></i>
                <input type="search" id="s-menu" class="form-control menu-border-transparent" placeholder="<?= lang('search_menu') ?>"/>
            </div>
            <br/>

            <?php
            echo $this->menu->dynamicMenu();
            $all_pinned_details = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_pinaction')->result();
            if (!empty($all_pinned_details)) {
                foreach ($all_pinned_details as $v_pinned_details) {
                    $pinned_details[$v_pinned_details->module_name] = $this->db->where('pinaction_id', $v_pinned_details->pinaction_id)->get('tbl_pinaction')->result();
                }
            }
            if (!empty($pinned_details)) {
                ?>
                <ul class="nav pinned">
                    <li class="nav-heading"><?= lang('pinned') . ' ' . lang('list') ?>
                        <span class="badge bg-primary pull-right mr-sm"><?= count($all_pinned_details) ?></span></li>
                    <?php foreach ($pinned_details as $module => $v_module_info) {
                        if (!empty($v_module_info)) {
                            foreach ($v_module_info as $v_module) { ?>
                                <?php if ($v_module->module_name == 'project') {
                                    $project_info = $this->db->where('project_id', $v_module->module_id)->get('tbl_project')->row();
                                    if (!empty($project_info)) {
                                        $progress = $this->items_model->get_project_progress($project_info->project_id);
                                        ?>
                                        <li class="pinned_list">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/projects/project_details/<?= $project_info->project_id ?>">
                                                <span style="font-size: 12px;"><?= $project_info->project_name ?></span>
                                                <div class="progress progress-xxs mb-lg ">
                                                    <div
                                                            class="progress-bar progress-bar-<?php echo ($progress >= 100) ? 'success' : 'primary'; ?>"
                                                            style="width: <?= $progress ?>%;">
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($v_module->module_name == 'tasks') {
                                    $task_info = $this->db->where('task_id', $v_module->module_id)->get('tbl_task')->row();
                                    if (!empty($task_info)) {
                                        ?>
                                        <li class="pinned_list mb">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/tasks/view_task_details/<?= $task_info->task_id ?>">
                                                <span style="font-size: 12px;"><?= $task_info->task_name ?></span>
                                                <div class="progress progress-xxs mb-lg ">
                                                    <div
                                                            class="progress-bar progress-bar-<?php echo ($task_info->task_progress >= 100) ? 'success' : 'primary'; ?>"
                                                            style="width: <?= $task_info->task_progress ?>%;">
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($v_module->module_name == 'invoice') {
                                    $invoice_info = $this->db->where('invoices_id', $v_module->module_id)->get('tbl_invoices')->row();
                                    if (!empty($invoice_info)) {
                                        ?>
                                        <li class="pinned_list mb">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $invoice_info->invoices_id ?>">
                                                <span style="font-size: 12px;"><?= $invoice_info->reference_no ?></span>
                                                <?php
                                                $payment_status = $this->invoice_model->get_payment_status($invoice_info->invoices_id);
                                                if (strtotime($invoice_info->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                                                    $text = 'text-danger';
                                                } else {
                                                    $text = '';
                                                } ?>
                                                <div style="font-size: 8px;margin-top: -3px">
                                                    <?= lang('overdue') ?>
                                                    :<span
                                                            class="<?= $text ?>"><?= strftime(config_item('date_format'), strtotime($invoice_info->due_date)) ?></span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($v_module->module_name == 'estimates') {
                                    $estimates_info = $this->db->where('estimates_id', $v_module->module_id)->get('tbl_estimates')->row();
                                    if (!empty($estimates_info)) {
                                        ?>
                                        <li class="pinned_list mb">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/estimates/index/estimates_details/<?= $estimates_info->estimates_id ?>">
                                                <span
                                                        style="font-size: 12px;"><?= $estimates_info->reference_no ?></span>
                                                <?php
                                                if (strtotime($estimates_info->due_date) < strtotime(date('Y-m-d')) && $estimates_info->status == 'Pending') {
                                                    $text = 'text-danger';
                                                } else {
                                                    $text = '';
                                                } ?>
                                                <div style="font-size: 8px;margin-top: -3px">
                                                    <?= lang('expired') ?>
                                                    :<span
                                                            class="<?= $text ?>"><?= strftime(config_item('date_format'), strtotime($estimates_info->due_date)) ?></span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($v_module->module_name == 'tickets') {
                                    $tickets_info = $this->db->where('tickets_id', $v_module->module_id)->get('tbl_tickets')->row();
                                    if (!empty($tickets_info)) {
                                        if ($tickets_info->status == 'open') {
                                            $s_label = 'danger';
                                        } elseif ($tickets_info->status == 'closed') {
                                            $s_label = 'success';
                                        } else {
                                            $s_label = 'default';
                                        }
                                        ?>
                                        <li class="pinned_list mb">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/tickets/index/tickets_details/<?= $tickets_info->tickets_id ?>">
                                                <span style="font-size: 12px;"><?= $tickets_info->subject ?></span>
                                                <div style="font-size: 8px;margin-top: -3px">
                                                    <?= lang('status') ?>
                                                    :<span
                                                            class="text-<?= $s_label ?>"><?= lang($tickets_info->status) ?></span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($v_module->module_name == 'leads') {
                                    $leads_info = $this->db->where('leads_id', $v_module->module_id)->get('tbl_leads')->row();
                                    if (!empty($leads_info)) {
                                        $lead_status = $this->db->where('lead_status_id', $leads_info->lead_status_id)->get('tbl_lead_status')->row();
                                        ?>
                                        <li class="pinned_list mb">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/leads/leads_details/<?= $leads_info->leads_id ?>">
                                                <span style="font-size: 12px;"><?= $leads_info->lead_name ?></span>
                                                <div style="font-size: 8px;margin-top: -3px">
                                                    <?= lang('status') ?>
                                                    :<span><?= $lead_status->lead_status ?></span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($v_module->module_name == 'bugs') {
                                    $bugs_info = $this->db->where('bug_id', $v_module->module_id)->get('tbl_bug')->row();
                                    if (!empty($bugs_info)) {
                                        $reporter = $this->db->where('user_id', $bugs_info->reporter)->get('tbl_users')->row();
                                        if ($bugs_info->bug_status == 'unconfirmed') {
                                            $b_label = 'warning';
                                        } elseif ($bugs_info->bug_status == 'confirmed') {
                                            $b_label = 'info';
                                        } elseif ($bugs_info->bug_status == 'in_progress') {
                                            $b_label = 'primary';
                                        } else {
                                            $b_label = 'success';
                                        }
                                        ?>
                                        <li class="pinned_list mb">
                                            <a title="<?= lang('pinned') . ' ' . lang($module) ?>"
                                               data-placement="top" data-toggle="tooltip"
                                               href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $bugs_info->bug_id ?>">
                                                <span style="font-size: 12px;"><?= $bugs_info->bug_title ?></span>
                                                <div style="font-size: 8px;margin-top: -3px">
                                                    <?= lang('status') ?>
                                                    :<span
                                                            class="text-<?= $b_label ?>"><?= lang("$bugs_info->bug_status") ?></span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            <?php }
                        }
                    }
                    ?>
                </ul>
            <?php } ?>
            <!-- Iterates over all sidebar items-->
            <ul class="nav pinned">
                <?php
                $this->db->select("tbl_project.*", FALSE);
                $this->db->select("tbl_users.*", FALSE);
                $this->db->select("tbl_account_details.*", FALSE);
                $this->db->join('tbl_users', 'tbl_users.user_id = tbl_project.timer_started_by');
                $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_project.timer_started_by');
                $this->db->where(array('timer_status' => 'on'));
                $project_timers = $this->db->get('tbl_project')->result_array();

                $this->db->select("tbl_task.*", FALSE);
                $this->db->select("tbl_users.*", FALSE);
                $this->db->select("tbl_account_details.*", FALSE);
                $this->db->join('tbl_users', 'tbl_users.user_id = tbl_task.timer_started_by');
                $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_task.timer_started_by');
                $this->db->where(array('timer_status' => 'on'));
                $task_timers = $this->db->get('tbl_task')->result_array();

                $user_id = $this->session->userdata('user_id');
                $role = $this->admin_model->check_by(array('user_id' => $user_id), 'tbl_users');
                ?>

                <?php
                if (!empty($project_timers)):
                    ?>
                    <li class="nav-heading"><?= lang('project') . ' ' . lang('start') ?> </li>
                <?php foreach ($project_timers as $p_timer) : if ($role->role_id == 1 || ($role->role_id == 2 && $user_id == $p_timer['user_id'])) : ?>
                    <li class="active mb-sm" start="<?php echo $p_timer['timer_status']; ?>">
                        <a title="<?php echo $p_timer['project_name'] . " (" . $p_timer['username'] . ")"; ?>"
                           data-placement="top" data-toggle="tooltip"
                           href="<?= base_url() ?>admin/projects/project_details/<?= $p_timer['project_id'] ?>">
                            <img src="<?= base_url() . $p_timer['avatar'] ?>" width="30" height="30"
                                 class="img-thumbnail img-circle">
                            <span id="project_hour_timer_<?= $p_timer['project_id'] ?>"> 0 </span>
                            <!-- SEPARATOR -->
                            :
                            <!-- MINUTE TIMER -->
                            <span id="project_minute_timer_<?= $p_timer['project_id'] ?>"> 0 </span>
                            <!-- SEPARATOR -->
                            :
                            <!-- SECOND TIMER -->
                            <span id="project_second_timer_<?= $p_timer['project_id'] ?>"> 0 </span>
                            <b class="label label-danger pull-right"> <i class="fa fa-clock-o fa-spin"></i></b>
                        </a>
                    </li>
                <?php
                //RUNS THE TIMER IF ONLY TIMER_STATUS = 1
                if ($p_timer['timer_status'] == 'on') :

                $project_current_moment_timestamp = strtotime(date("H:i:s"));
                $project_timer_starting_moment_timestamp = $this->db->get_where('tbl_project', array('project_id' => $p_timer['project_id']))->row()->start_time;
                $project_total_duration = $project_current_moment_timestamp - $project_timer_starting_moment_timestamp;

                $project_total_hour = intval($project_total_duration / 3600);
                $project_total_duration -= $project_total_hour * 3600;
                $project_total_minute = intval($project_total_duration / 60);
                $project_total_second = intval($project_total_duration % 60);
                ?>

                    <script type="text/javascript">
                        // SET THE INITIAL VALUES TO TIMER PLACES
                        var timer_starting_hour = <?php echo $project_total_hour; ?>;
                        document.getElementById("project_hour_timer_<?= $p_timer['project_id'] ?>").innerHTML = timer_starting_hour;
                        var timer_starting_minute = <?php echo $project_total_minute; ?>;
                        document.getElementById("project_minute_timer_<?= $p_timer['project_id'] ?>").innerHTML = timer_starting_minute;
                        var timer_starting_second = <?php echo $project_total_second; ?>;
                        document.getElementById("project_second_timer_<?= $p_timer['project_id'] ?>").innerHTML = timer_starting_second;

                        // INITIALIZE THE TIMER WITH SECOND DELAY
                        var timer = timer_starting_second;
                        var mytimer = setInterval(function () {
                            task_run_timer()
                        }, 1000);

                        function task_run_timer() {
                            timer++;

                            if (timer > 59) {
                                timer = 0;
                                timer_starting_minute++;
                                document.getElementById("project_minute_timer_<?= $p_timer['project_id'] ?>").innerHTML = timer_starting_minute;
                            }

                            if (timer_starting_minute > 59) {
                                timer_starting_minute = 0;
                                timer_starting_hour++;
                                document.getElementById("project_hour_timer_<?= $p_timer['project_id'] ?>").innerHTML = timer_starting_hour;
                            }

                            document.getElementById("project_second_timer_<?= $p_timer['project_id'] ?>").innerHTML = timer;
                        }
                    </script>

                <?php endif; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php
                if (!empty($task_timers)):
                    ?>
                    <li class="nav-heading"><?= lang('tasks') . ' ' . lang('start') ?> </li>
                <?php
                foreach ($task_timers as $v_task_timer):
                if ($role->role_id == 1 || ($role->role_id == 2 && $user_id == $v_task_timer['user_id'])) :
                ?>
                    <li class="mb-sm active" start="<?php echo $v_task_timer['timer_status']; ?>">
                        <a title="<?php echo $v_task_timer['task_name'] . " (" . $v_task_timer['username'] . ")"; ?>"
                           data-placement="top" data-toggle="tooltip"
                           href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task_timer['task_id'] ?>">
                            <img src="<?= base_url() . $v_task_timer['avatar'] ?>" width="30" height="30"
                                 class="img-thumbnail img-circle">
                            <span id="tasks_hour_timer_<?= $v_task_timer['task_id'] ?>"> 0 </span>
                            <!-- SEPARATOR -->
                            :
                            <!-- MINUTE TIMER -->
                            <span id="tasks_minute_timer_<?= $v_task_timer['task_id'] ?>"> 0 </span>
                            <!-- SEPARATOR -->
                            :
                            <!-- SECOND TIMER -->
                            <span id="tasks_second_timer_<?= $v_task_timer['task_id'] ?>"> 0 </span>
                            <b class="label label-danger pull-right"> <i class="fa fa-clock-o fa-spin"></i></b>
                        </a>
                    </li>
                <?php
                //RUNS THE TIMER IF ONLY TIMER_STATUS = 1
                if ($v_task_timer['timer_status'] == 'on') :

                $task_current_moment_timestamp = strtotime(date("H:i:s"));
                $task_timer_starting_moment_timestamp = $this->db->get_where('tbl_task', array('task_id' => $v_task_timer['task_id']))->row()->start_time;
                $task_total_duration = $task_current_moment_timestamp - $task_timer_starting_moment_timestamp;

                $task_total_hour = intval($task_total_duration / 3600);
                $task_total_duration -= $task_total_hour * 3600;
                $task_total_minute = intval($task_total_duration / 60);
                $task_total_second = intval($task_total_duration % 60);
                ?>

                    <script type="text/javascript">
                        // SET THE INITIAL VALUES TO TIMER PLACES
                        var timer_starting_hour = <?php echo $task_total_hour; ?>;
                        document.getElementById("tasks_hour_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_hour;
                        var timer_starting_minute = <?php echo $task_total_minute; ?>;
                        document.getElementById("tasks_minute_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_minute;
                        var timer_starting_second = <?php echo $task_total_second; ?>;
                        document.getElementById("tasks_second_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_second;

                        // INITIALIZE THE TIMER WITH SECOND DELAY
                        var timer = timer_starting_second;
                        var mytimer = setInterval(function () {
                            task_run_timer()
                        }, 1000);

                        function task_run_timer() {
                            timer++;

                            if (timer > 59) {
                                timer = 0;
                                timer_starting_minute++;
                                document.getElementById("tasks_minute_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_minute;
                            }

                            if (timer_starting_minute > 59) {
                                timer_starting_minute = 0;
                                timer_starting_hour++;
                                document.getElementById("tasks_hour_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_hour;
                            }

                            document.getElementById("tasks_second_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer;
                        }
                    </script>

                <?php endif; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>

            </ul>
            <!-- END sidebar nav-->
        </nav>
    </div>
    <!-- END Sidebar (left)-->
</aside>
