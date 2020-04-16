<form name="myform" role="form" data-parsley-validate="" novalidate=""
      enctype="multipart/form-data"
      id="form"
      action="<?php echo base_url(); ?>admin/estimates/save_estimates/<?php
      if (!empty($estimates_info)) {
          echo $estimates_info->estimates_id;
      }
      ?>" method="post" class="form-horizontal  ">
    <div class="<?php if (!isset($estimates_info) || (isset($estimate_to_merge) && count($estimate_to_merge) == 0)) {
        echo ' hide';
    } ?>" id="invoice_top_info">
        <div class="panel-body">
            <div class="row">
                <div id="merge" class="col-md-8">
                    <?php if (isset($estimates_info) && !empty($estimate_to_merge)) {
                        $this->load->view('admin/estimates/merge_estimate', array('invoices_to_merge' => $estimate_to_merge));
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
    <?php include_once 'assets/admin-ajax.php'; ?>
    <?php include_once 'assets/js/sales.php'; ?>
    <?= message_box('success'); ?>
    <?= message_box('error'); ?>
    <?php
    $mdate = date('Y-m-d');
    $last_7_days = date('Y-m-d', strtotime('today - 7 days'));
    $all_goal_tracking = $this->estimates_model->get_permission('tbl_goal_tracking');

    $all_goal = 0;
    $bank_goal = 0;
    $complete_achivement = 0;
    if (!empty($all_goal_tracking)) {
        foreach ($all_goal_tracking as $v_goal_track) {
            $goal_achieve = $this->estimates_model->get_progress($v_goal_track, true);
            if ($v_goal_track->goal_type_id == 6) {
                if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                    if ($v_goal_track->email_send == 'no') {// check mail are send or not
                        if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                            if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                                $this->estimates_model->send_goal_mail('goal_achieve', $v_goal_track);
                            }
                        } else {
                            if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                                $this->estimates_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                            }
                        }
                    }
                }
                $all_goal += $v_goal_track->achievement;
                $complete_achivement += $goal_achieve['achievement'];
            }
        }
    }
    // 30 days before

    for ($iDay = 7; $iDay >= 0; $iDay--) {
        $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
        $where = array('date_saved >=' => $date . " 00:00:00", 'date_saved <=' => $date . " 23:59:59");
        $invoice_result[$date] = count($this->db->where($where)->get('tbl_estimates')->result());
    }

    $terget_achievement = $this->db->where(array('goal_type_id' => 6, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

    $total_terget = 0;
    if (!empty($terget_achievement)) {
        foreach ($terget_achievement as $v_terget) {
            $total_terget += $v_terget->achievement;
        }
    }
    $tolal_goal = $all_goal + $bank_goal;
    $curency = $this->estimates_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

    if ($this->session->userdata('user_type') == 1) {
        $margin = 'margin-bottom:30px';
        $h_s = config_item('estimate_state');
        ?>
        <div id="state_report" style="display: <?= $h_s ?>">
            <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
                <div class="col-md-4">
                    <div class="row row-table pv-lg">
                        <div class="col-xs-6">
                            <p class="m0 lead"><?= ($tolal_goal) ?></p>
                            <p class="m0">
                                <small><?= lang('achievement') ?></small>
                            </p>
                        </div>
                        <div class="col-xs-6 ">
                            <p class="m0 lead"><?= ($total_terget) ?></p>
                            <p class="m0">
                                <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row row-table ">
                        <div class="col-xs-6">
                            <p class="m0 lead"><?= ($complete_achivement) ?></p>
                            <p class="m0">
                                <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                            </p>
                        </div>
                        <div class="col-xs-6 pt">
                            <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                                 data-bar-spacing="6" data-chart-range-min="0" values="<?php
                            if (!empty($invoice_result)) {
                                foreach ($invoice_result as $v_invoice_result) {
                                    echo $v_invoice_result . ',';
                                }
                            }
                            ?>">
                            </div>
                            <p class="m0">
                                <small>
                                    <?php
                                    if (!empty($invoice_result)) {
                                        foreach ($invoice_result as $date => $v_invoice_result) {
                                            echo date('d', strtotime($date)) . ' ';
                                        }
                                    }
                                    ?>
                                </small>
                            </p>

                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="row row-table ">
                        <div class="col-xs-6">
                            <p class="m0 lead">
                                <?php
                                if ($tolal_goal < $complete_achivement) {
                                    $pending_goal = 0;
                                } else {
                                    $pending_goal = $tolal_goal - $complete_achivement;
                                } ?>
                                <?= ($pending_goal) ?></p>
                            <p class="m0">
                                <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                            </p>
                        </div>
                        <?php
                        if (!empty($tolal_goal)) {
                            if ($tolal_goal <= $complete_achivement) {
                                $total_progress = 100;
                            } else {
                                $progress = ($complete_achivement / $tolal_goal) * 100;
                                $total_progress = round($progress);
                            }
                        } else {
                            $total_progress = 0;
                        }
                        ?>
                        <div class="col-xs-6 text-center pt">
                            <div class="inline ">
                                <div class="easypiechart text-success"
                                     data-percent="<?= $total_progress ?>"
                                     data-line-width="5" data-track-Color="#f0f0f0"
                                     data-bar-color="#<?php
                                     if ($total_progress == 100) {
                                         echo '8ec165';
                                     } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                         echo '5d9cec';
                                     } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                         echo '7266ba';
                                     } else {
                                         echo 'fb6b5b';
                                     }
                                     ?>" data-rotate="270" data-scale-Color="false"
                                     data-size="50"
                                     data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                                    <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
            ?>
            <div class="row">
                <!-- END widget-->
                <?php
                $currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
                $expired = 0;
                $draft = 0;
                $total_draft = 0;
                $total_sent = 0;
                $total_declined = 0;
                $total_accepted = 0;
                $total_expired = 0;
                $sent = 0;
                $declined = 0;
                $accepted = 0;
                $pending = 0;
                $cancelled = 0;
                $all_estimates = $this->estimates_model->get_permission('tbl_estimates');
                if (!empty($all_estimates)) {
                    $all_estimates = array_reverse($all_estimates);
                    foreach ($all_estimates as $v_invoice) {
                        if (strtotime($v_invoice->due_date) < strtotime(date('Y-m-d')) && $v_invoice->status == ('pending') || strtotime($v_invoice->due_date) < strtotime(date('Y-m-d')) && $v_invoice->status == ('draft')) {
                            $total_expired += $this->estimates_model->estimate_calculation('total', $v_invoice->estimates_id);
                            $expired += count($v_invoice->estimates_id);;
                        }
                        if ($v_invoice->status == ('draft')) {
                            $draft += count($v_invoice->estimates_id);
                            $total_draft += $this->estimates_model->estimate_calculation('total', $v_invoice->estimates_id);
                        }
                        if ($v_invoice->status == ('sent')) {
                            $sent += count($v_invoice->estimates_id);
                            $total_sent += $this->estimates_model->estimate_calculation('total', $v_invoice->estimates_id);
                        }
                        if ($v_invoice->status == ('declined')) {
                            $declined += count($v_invoice->estimates_id);
                            $total_declined += $this->estimates_model->estimate_calculation('total', $v_invoice->estimates_id);
                        }
                        if ($v_invoice->status == ('accepted')) {
                            $accepted += count($v_invoice->estimates_id);
                            $total_accepted += $this->estimates_model->estimate_calculation('total', $v_invoice->estimates_id);
                        }
                        if ($v_invoice->status == ('pending')) {
                            $pending += count($v_invoice->estimates_id);
                        }
                        if ($v_invoice->status == ('cancelled')) {
                            $cancelled += count($v_invoice->estimates_id);
                        }
                    }
                }
                ?>

                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_draft, $currency->symbol) ?></h3>
                            <p class="m0"><?= lang('draft') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0"><?= display_money($total_sent, $currency->symbol) ?></h3>
                            <p class="text-primary m0"><?= lang('sent') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_expired, $currency->symbol) ?></h3>
                            <p class="text-danger m0"><?= lang('expired') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_declined, $currency->symbol) ?></h3>
                            <p class="text-warning m0"><?= lang('declined') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_accepted, $currency->symbol) ?></h3>
                            <p class="text-success m0"><?= lang('accepted') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
            </div>
            <?php if (!empty($all_estimates)) { ?>
                <div class="row">
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-purple filter_by_type" style="font-size: 15px"
                                           search-type="<?= lang('draft') ?>" id="draft"
                                           href="#"><?= lang('draft') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $draft ?>
                                        / <?= count($all_estimates) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                                         data-original-title="<?= ($draft / count($all_estimates)) * 100 ?>%"
                                         style="width: <?= ($draft / count($all_estimates)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths pr-lg">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-primary filter_by_type" style="font-size: 15px"
                                           search-type="<?= lang('sent') ?>" id="sent"
                                           href="#"><?= lang('sent') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $sent ?>
                                        / <?= count($all_estimates) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-aqua " data-toggle="tooltip"
                                         data-original-title="<?= ($sent / count($all_estimates)) * 100 ?>%"
                                         style="width: <?= ($sent / count($all_estimates)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-danger filter_by_type" style="font-size: 15px"
                                           search-type="<?= lang('expired') ?>" id="expired"
                                           href="#"><?= lang('expired') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $expired ?>
                                        / <?= count($all_estimates) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                                         data-original-title="<?= ($expired / count($all_estimates)) * 100 ?>%"
                                         style="width: <?= ($expired / count($all_estimates)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-warning filter_by_type" style="font-size: 15px"
                                           search-type="<?= lang('declined') ?>" id="declined"
                                           href="#"><?= lang('declined') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $declined ?>
                                        / <?= count($all_estimates) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                                         data-original-title="<?= ($declined / count($all_estimates)) * 100 ?>%"
                                         style="width: <?= ($declined / count($all_estimates)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-success filter_by_type" style="font-size: 15px"
                                           search-type="<?= lang('accepted') ?>" id="accepted"
                                           href="#"><?= lang('accepted') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $accepted ?>
                                        / <?= count($all_estimates) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                                         data-original-title="<?= ($accepted / count($all_estimates)) * 100 ?>%"
                                         style="width: <?= ($accepted / count($all_estimates)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php }
    $type = $this->uri->segment(5);
    if (!empty($type) && !is_numeric($type)) {
        $ex = explode('_', $type);
        if ($ex[0] == 'c') {
            $c_id = $ex[1];
            $type = '_' . date('Y');
        }
    }
    if (empty($type)) {
        $type = '_' . date('Y');
    }
    ?>
    <div class="btn-group mb-lg pull-left mr">
        <button class=" btn btn-xs btn-white dropdown-toggle"
                data-toggle="dropdown">
            <i class="fa fa-search"></i>

            <?php
            echo lang('filter_by'); ?>
            <span id="showed_result">
            <?php if (!empty($type) && !is_numeric($type)) {
                $ex = explode('_', $type);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        echo ' : ' . $ex[1];
                    } else {
                        echo ' : ' . lang($type);
                    }
                } else {
                    echo ' : ' . lang($type);
                }

            } ?>
                </span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu animated zoomIn">
            <li id="all" class="filter_by_type" search-type="<?= lang('all'); ?>"><a href="#"><?= lang('all'); ?></a>
            </li>
            <?php
            $invoiceFilter = $this->estimates_model->get_invoice_filter();
            if (!empty($invoiceFilter)) {
                foreach ($invoiceFilter as $v_Filter) {
                    ?>
                    <li class="filter_by_type" search-type="<?= $v_Filter['name'] ?>" id="<?= $v_Filter['value'] ?>">
                        <a href="#"><?= $v_Filter['name'] ?></a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
    <?php
    if ($this->session->userdata('user_type') == 1) {
        $type = 'estimate';
        if ($h_s == 'block') {
            $title = lang('hide_quick_state');
            $url = 'hide';
            $icon = 'fa fa-eye-slash';
        } else {
            $title = lang('view_quick_state');
            $url = 'show';
            $icon = 'fa fa-eye';
        }
        ?>
        <div onclick="slideToggle('#state_report')" id="quick_state" data-toggle="tooltip" data-placement="top"
             title="<?= $title ?>"
             class="btn-xs btn btn-purple pull-left">
            <i class="fa fa-bar-chart"></i>
        </div>
        <div class="btn-xs btn btn-white pull-left ml ">
            <a class="text-dark" id="change_report"
               href="<?= base_url() ?>admin/dashboard/change_report/<?= $url . '/' . $type ?>"><i
                        class="<?= $icon ?>"></i>
                <span><?= ' ' . lang('quick_state') . ' ' . lang($url) . ' ' . lang('always') ?></span></a>
        </div>
    <?php }
    $created = can_action('14', 'created');
    $edited = can_action('14', 'edited');
    $deleted = can_action('14', 'deleted');
    if (!empty($created) || !empty($edited)){
    ?>
    <a data-toggle="modal" data-target="#myModal"
       href="<?= base_url() ?>admin/invoice/zipped/estimate"
       class="btn btn-success btn-xs ml-lg"><?= lang('zip_estimate') ?></a>
    <div class="row">
        <div class="col-sm-12">
            <?php $is_department_head = is_department_head();
            if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
                <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                     data-title="<?php echo lang('filter_by'); ?>">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu group animated zoomIn"
                        style="width:300px;">
                        <li class="filter_by all_filter" id="all"><a href="#"><?php echo lang('all'); ?></a></li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu pull-left  " id="from_account">
                            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('project'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-left from_account"
                                style="">
                                <?php
                                $all_projects = $this->invoice_model->get_permission('tbl_project');
                                if (!empty($all_projects)) {
                                    foreach ($all_projects as $v_project) {
                                        ?>
                                        <li class="filter_by" id="<?= $v_project->project_id ?>"
                                            search-type="by_project">
                                            <a href="#"><?php echo $v_project->project_name; ?></a>
                                        </li>
                                    <?php }
                                }
                                ?>
                            </ul>
                        </li>
                        <div class="clearfix"></div>
                        <li class="dropdown-submenu pull-left  " id="from_reporter">
                            <a href="#"
                               tabindex="-1"><?php echo lang('by') . ' ' . lang('sales') . ' ' . lang('agent'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-left from_reporter"
                                style="">
                                <?php
                                $all_agent = $this->db->where('role_id != ', 2)->get('tbl_users')->result();
                                if (!empty($all_agent)) {
                                    foreach ($all_agent as $v_agent) {
                                        ?>
                                        <li class="filter_by" id="<?= $v_agent->user_id ?>"
                                            search-type="by_agent">
                                            <a href="#"><?php echo fullname($v_agent->user_id); ?></a>
                                        </li>
                                    <?php }
                                }
                                ?>
                            </ul>
                        </li>
                        <div class="clearfix"></div>
                        <li class="dropdown-submenu pull-left " id="to_account">
                            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('client'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-left to_account"
                                style="">
                                <?php
                                if (count($all_client) > 0) { ?>
                                    <?php foreach ($all_client as $v_client) {
                                        ?>
                                        <li class="filter_by" id="<?= $v_client->client_id ?>"
                                            search-type="by_client">
                                            <a href="#"><?php echo $v_client->name; ?></a>
                                        </li>
                                    <?php }
                                    ?>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs">
                    <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                        data-toggle="tab"><?= lang('all_estimates') ?></a>
                    </li>
                    <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                                        data-toggle="tab"><?= lang('create_estimate') ?></a>
                    </li>
                </ul>
                <div class="tab-content bg-white">
                    <!-- ************** general *************-->
                    <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                        <?php } else { ?>
                        <div class="panel panel-custom">
                            <header class="panel-heading ">
                                <div class="panel-title"><strong><?= lang('all_estimates') ?></strong></div>
                            </header>
                            <?php } ?>
                            <div class="table-responsive">
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('estimate') ?> #</th>
                                        <th><?= lang('estimate') . ' ' . lang('date') ?></th>
                                        <th><?= lang('expire') . ' ' . lang('date') ?></th>
                                        <th><?= lang('client_name') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <?php $show_custom_fields = custom_form_table(10, null);
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
                                            <th class="hidden-print"><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <script type="text/javascript">
                                        $(document).ready(function () {
                                            list = base_url + "admin/estimates/estimatesList";
                                            <?php if (admin_head()) { ?>
                                            $('.filtered > .dropdown-toggle').on('click', function () {
                                                if ($('.group').css('display') == 'block') {
                                                    $('.group').css('display', 'none');
                                                } else {
                                                    $('.group').css('display', 'block')
                                                }
                                            });
                                            $('.all_filter').on('click', function () {
                                                $('.to_account').removeAttr("style");
                                                $('.from_account').removeAttr("style");
                                                $('.from_reporter').removeAttr("style");
                                            });
                                            $('.from_account li').on('click', function () {
                                                if ($('.to_account').css('display') == 'block') {
                                                    $('.to_account').removeAttr("style");
                                                    $('.from_reporter').removeAttr("style");
                                                    $('.from_account').css('display', 'block');
                                                } else if ($('.from_reporter').css('display') == 'block') {
                                                    $('.to_account').removeAttr("style");
                                                    $('.from_reporter').removeAttr("style");
                                                    $('.from_account').css('display', 'block');
                                                } else {
                                                    $('.from_account').css('display', 'block')
                                                }
                                            });

                                            $('.to_account li').on('click', function () {
                                                if ($('.from_account').css('display') == 'block') {
                                                    $('.from_account').removeAttr("style");
                                                    $('.from_reporter').removeAttr("style");
                                                    $('.to_account').css('display', 'block');
                                                } else if ($('.from_reporter').css('display') == 'block') {
                                                    $('.from_reporter').removeAttr("style");
                                                    $('.from_account').removeAttr("style");
                                                    $('.to_account').css('display', 'block');
                                                } else {
                                                    $('.to_account').css('display', 'block');
                                                }
                                            });
                                            $('.from_reporter li').on('click', function () {
                                                if ($('.to_account').css('display') == 'block') {
                                                    $('.to_account').removeAttr("style");
                                                    $('.to_account').removeAttr("style");
                                                    $('.from_reporter').css('display', 'block');
                                                } else if ($('.from_account').css('display') == 'block') {
                                                    $('.to_account').removeAttr("style");
                                                    $('.from_account').removeAttr("style");
                                                    $('.from_reporter').css('display', 'block');
                                                } else {
                                                    $('.from_reporter').css('display', 'block');
                                                }
                                            });
                                            $('.filter_by').on('click', function () {
                                                $('.filter_by').removeClass('active');
                                                $('.group').css('display', 'block');
                                                $(this).addClass('active');
                                                var filter_by = $(this).attr('id');
                                                if (filter_by) {
                                                    filter_by = filter_by;
                                                } else {
                                                    filter_by = '';
                                                }
                                                var search_type = $(this).attr('search-type');
                                                if (search_type) {
                                                    search_type = '/' + search_type;
                                                } else {
                                                    search_type = '';
                                                }
                                                table_url(base_url + "admin/estimates/estimatesList/" + filter_by + search_type);
                                            });
                                            <?php }?>

                                            $('.filter_by_type').on('click', function () {
                                                $('.filter_by_type').removeClass('active');
                                                $('#showed_result').html($(this).attr('search-type'));
                                                $(this).addClass('active');
                                                var filter_by = $(this).attr('id');
                                                if (filter_by) {
                                                    filter_by = filter_by;
                                                } else {
                                                    filter_by = '';
                                                }
                                                table_url(base_url + "admin/estimates/estimatesList/" + filter_by);
                                            });
                                        });
                                    </script>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (!empty($created) || !empty($edited)) { ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                            <div class="row mb-lg invoice estimate-template">
                                <div class="col-sm-6 col-xs-12 br pv">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span
                                                        class="text-danger">*</span></label>
                                            <div class="col-lg-7">
                                                <?php $this->load->helper('string'); ?>
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($estimates_info)) {
                                                    echo $estimates_info->reference_no;
                                                } else {
                                                    if (empty(config_item('estimate_number_format'))) {
                                                        echo config_item('estimate_prefix');
                                                    }
                                                    if (config_item('increment_estimate_number') == 'FALSE') {
                                                        $this->load->helper('string');
                                                        echo random_string('nozero', 6);
                                                    } else {
                                                        echo $this->estimates_model->generate_estimate_number();
                                                    }
                                                }
                                                ?>" name="reference_no">
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <label
                                                    class="col-lg-3 control-label"><?= lang('estimate_date') ?></label>
                                            <div class="col-lg-7">
                                                <div class="input-group">
                                                    <input required type="text" name="estimate_date"
                                                           class="form-control datepicker"
                                                           value="<?php
                                                           if (!empty($estimates_info->estimate_date)) {
                                                               echo $estimates_info->estimate_date;
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
                                                    <input required type="text" name="due_date"
                                                           class="form-control datepicker"
                                                           value="<?php
                                                           if (!empty($estimates_info->due_date)) {
                                                               echo $estimates_info->due_date;
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
                                            <label class="col-lg-3 control-label"><?= lang('status') ?> </label>
                                            <div class="col-lg-7">
                                                <select name="status" class="selectpicker" data-width="100%">
                                                    <option
                                                            value="draft"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'draft' ? 'selected' : '' ?>><?= lang('draft') ?></option>
                                                    <option
                                                            value="sent"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'sent' ? 'selected' : '' ?>><?= lang('sent') ?></option>
                                                    <option
                                                            value="expired"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'expired' ? 'selected' : '' ?>><?= lang('expired') ?></option>
                                                    <option
                                                            value="declined"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'declined' ? 'selected' : '' ?>><?= lang('declined') ?></option>
                                                    <option
                                                            value="accepted"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'accepted' ? 'selected' : '' ?>><?= lang('accepted') ?></option>
                                                    <option
                                                            value="pending"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'pending' ? 'selected' : '' ?>><?= lang('pending') ?></option>
                                                    <option
                                                            value="cancelled"
                                                            <?= !empty($estimates_info) && $estimates_info->status == 'cancelled' ? 'selected' : '' ?>><?= lang('cancelled') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                                        class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="checkbox c-radio needsclick">
                                                    <label class="needsclick">
                                                        <input id="" <?php
                                                        if (!empty($estimates_info->permission) && $estimates_info->permission == 'all') {
                                                            echo 'checked';
                                                        } elseif (empty($estimates_info)) {
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
                                                        if (!empty($estimates_info->permission) && $estimates_info->permission != 'all') {
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
                                        if (!empty($estimates_info->permission) && $estimates_info->permission != 'all') {
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
                                                                    if (!empty($estimates_info->permission) && $estimates_info->permission != 'all') {
                                                                        $get_permission = json_decode($estimates_info->permission);
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

                                                        if (!empty($estimates_info->permission) && $estimates_info->permission != 'all') {
                                                            $get_permission = json_decode($estimates_info->permission);

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

                                                                    if (!empty($estimates_info->permission) && $estimates_info->permission != 'all') {
                                                                        $get_permission = json_decode($estimates_info->permission);

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

                                                                    if (!empty($estimates_info->permission) && $estimates_info->permission != 'all') {
                                                                        $get_permission = json_decode($estimates_info->permission);
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

                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12 br pv">
                                    <div class="row">
                                        <div class="f_client_id">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label"><?= lang('client') ?> <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-7">
                                                    <div class="input-group">
                                                        <select class="form-control select_box" required
                                                                style="width: 100%"
                                                                name="client_id"
                                                                onchange="get_project_by_id(this.value)">
                                                            <option
                                                                    value=""><?= lang('select') . ' ' . lang('client') ?></option>
                                                            <?php
                                                            if (!empty($all_client)) {
                                                                foreach ($all_client as $v_client) {
                                                                    if (!empty($project_info->client_id)) {
                                                                        $client_id = $project_info->client_id;
                                                                    } elseif (!empty($estimates_info->client_id)) {
                                                                        $client_id = $estimates_info->client_id;
                                                                    } elseif (!empty($c_id)) {
                                                                        $client_id = $c_id;
                                                                    }

                                                                    ?>
                                                                    <option value="<?= $v_client->client_id ?>"
                                                                        <?php
                                                                        if (!empty($client_id)) {
                                                                            echo $client_id == $v_client->client_id ? 'selected' : '';
                                                                        }
                                                                        ?>
                                                                    ><?= ucfirst($v_client->name) ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            $acreated = can_action('4', 'created');
                                                            ?>
                                                        </select>
                                                        <?php if (!empty($acreated)) { ?>
                                                            <div class="input-group-addon"
                                                                 title="<?= lang('new') . ' ' . lang('client') ?>"
                                                                 data-toggle="tooltip" data-placement="top">
                                                                <a data-toggle="modal" data-target="#myModal"
                                                                   href="<?= base_url() ?>admin/client/new_client"><i
                                                                            class="fa fa-plus"></i></a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('project') ?></label>
                                            <div class="col-lg-7">
                                                <select class="form-control " style="width: 100%" name="project_id"
                                                        id="client_project">
                                                    <option value=""><?= lang('none') ?></option>
                                                    <?php
                                                    if (!empty($client_id)) {
                                                        if (!empty($project_info->project_id)) {
                                                            $project_id = $project_info->project_id;
                                                        } elseif ($estimates_info->project_id) {
                                                            $project_id = $estimates_info->project_id;
                                                        }
                                                        $all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();
                                                        if (!empty($all_project)) {
                                                            foreach ($all_project as $v_cproject) {
                                                                ?>
                                                                <option value="<?= $v_cproject->project_id ?>" <?php
                                                                if (!empty($project_id)) {
                                                                    echo $v_cproject->project_id == $project_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_cproject->project_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('sales') . ' ' . lang('agent') ?></label>
                                            <div class="col-sm-7">
                                                <select class="form-control select_box" required style="width: 100%"
                                                        name="user_id">
                                                    <option
                                                            value=""><?= lang('select') . ' ' . lang('sales') . ' ' . lang('agent') ?></option>
                                                    <?php
                                                    $all_user = $this->db->where('role_id != ', 2)->get('tbl_users')->result();
                                                    if (!empty($all_user)) {
                                                        foreach ($all_user as $v_user) {
                                                            $profile_info = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                                                            if (!empty($profile_info)) {
                                                                ?>
                                                                <option value="<?= $v_user->user_id ?>"
                                                                    <?php
                                                                    if (!empty($estimates_info->user_id)) {
                                                                        echo $estimates_info->user_id == $v_user->user_id ? 'selected' : null;
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
                                                   class="control-label col-sm-3"><?= lang('discount_type') ?></label>
                                            <div class="col-sm-7">
                                                <select name="discount_type" class="selectpicker" data-width="100%">
                                                    <option value=""
                                                            selected><?php echo lang('no') . ' ' . lang('discount'); ?></option>
                                                    <option value="before_tax" <?php
                                                    if (isset($estimates_info)) {
                                                        if ($estimates_info->discount_type == 'before_tax') {
                                                            echo 'selected';
                                                        }
                                                    } ?>><?php echo lang('before_tax'); ?></option>
                                                    <option value="after_tax" <?php if (isset($estimates_info)) {
                                                        if ($estimates_info->discount_type == 'after_tax') {
                                                            echo 'selected';
                                                        }
                                                    } ?>><?php echo lang('after_tax'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($estimates_info)) {
                                            $estimates_id = $estimates_info->estimates_id;
                                        } else {
                                            $estimates_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(10, $estimates_id); ?>
                                        <?php if (!empty($project_id)): ?>
                                            <div class="form-group">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input data-toggle="toggle" name="client_visible"
                                                           value="Yes" <?php
                                                    if (!empty($estimates_info->client_visible) && $estimates_info->client_visible == 'Yes') {
                                                        echo 'checked';
                                                    }
                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success" data-offstyle="danger"
                                                           type="checkbox">
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group terms">
                                <label class="col-lg-1 control-label"><?= lang('notes') ?> </label>
                                <div class="col-lg-11">
                        <textarea name="notes" class="form-control textarea_"><?php
                            if (!empty($estimates_info)) {
                                echo $estimates_info->notes;
                            } else {
                                echo $this->config->item('estimate_terms');
                            }
                            ?></textarea>
                                </div>
                            </div>
                            <?php
                            if (!empty($estimates_info)) {
                                $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');
                                if (!empty($client_info)) {
                                    $client_lang = $client_info->language;
                                    $currency = $this->estimates_model->client_currency_symbol($estimates_info->client_id);
                                } else {
                                    $client_lang = 'english';
                                    $currency = $this->estimates_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                }
                            } else {
                                $client_lang = 'english';
                                $currency = $this->estimates_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            }
                            unset($this->lang->is_loaded[5]);
                            $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
                            ?>

                            <style type="text/css">
                                .dropdown-menu > li > a {
                                    white-space: normal;
                                }

                                .dragger {
                                    background: url(../assets/img/dragger.png) 10px 32px no-repeat;
                                    cursor: pointer;
                                }

                                <?php if (!empty($estimates_info)) { ?>
                                .dragger {
                                    background: url(../../../../assets/img/dragger.png) 10px 32px no-repeat;
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
                            $saved_items = $this->estimates_model->get_all_items();
                            ?>
                            <div class="row">
                                <div class="col-md-4">
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
                                                                <option
                                                                        value="<?php echo $v_item->saved_items_id; ?>"
                                                                        data-subtext="<?php echo strip_html_tags(mb_substr($v_item->item_desc, 0, 200)) . '...'; ?>">
                                                                    (<?= display_money($v_item->unit_cost, $currency->symbol); ?>
                                                                    ) <?php echo $v_item->item_name; ?></option>
                                                            <?php }
                                                        }
                                                        ?>
                                                    </optgroup>

                                                <?php } ?>
                                                <?php
                                                $item_created = can_action('39', 'created');
                                                if (!empty($item_created)) { ?>
                                                    <option data-divider="true"></option>
                                                    <option value="newitem"
                                                            data-content="<span class='text-info'><?php echo lang('new_item'); ?></span>"></option>
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
                                                    <?php if (isset($estimates_info) && $estimates_info->show_quantity_as == 'qty') {
                                                        echo 'checked';
                                                    } else if (!isset($hours_quantity) && !isset($qty_hrs_quantity)) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="fa fa-circle"></span><?php echo lang('qty'); ?>
                                            </label>
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="hours" id="<?php echo lang('hours'); ?>"
                                                       name="show_quantity_as" <?php if (isset($estimates_info) && $estimates_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                                    echo 'checked';
                                                } ?>>
                                                <span class="fa fa-circle"></span><?php echo lang('hours'); ?>
                                            </label>
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="qty_hours"
                                                       id="<?php echo lang('qty') . '/' . lang('hours'); ?>"
                                                       name="show_quantity_as"
                                                    <?php if (isset($estimates_info) && $estimates_info->show_quantity_as == 'qty_hours' || isset($qty_hrs_quantity)) {
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
                                            <th><?= $language_info['item_name'] ?></th>
                                            <th><?= $language_info['description'] ?></th>
                                            <?php
                                            $invoice_view = config_item('invoice_view');
                                            if (!empty($invoice_view) && $invoice_view == '2') {
                                                ?>
                                                <th class="col-sm-2"><?= $language_info['hsn_code'] ?></th>
                                            <?php } ?>
                                            <?php
                                            $qty_heading = $language_info['qty'];
                                            if (isset($estimates_info) && $estimates_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                                $qty_heading = lang('hours');
                                            } else if (isset($estimates_info) && $estimates_info->show_quantity_as == 'qty_hours') {
                                                $qty_heading = lang('qty') . '/' . lang('hours');
                                            }
                                            ?>
                                            <th class="qty col-sm-1"><?php echo $qty_heading; ?></th>
                                            <th class="col-sm-2"><?= $language_info['price'] ?></th>
                                            <th class="col-sm-2"><?= $language_info['tax_rate'] ?> </th>
                                            <th class="col-sm-1"><?= $language_info['total'] ?></th>
                                            <th class="col-sm-1 hidden-print"><?= $language_info['action'] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($estimates_info)) {
                                            echo form_hidden('merge_current_invoice', $estimates_info->estimates_id);
                                            echo form_hidden('isedit', $estimates_info->estimates_id);
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
                                                if (isset($estimates_info)) {
                                                    $new_item = true;
                                                }
                                                ?>
                                                <button type="button"
                                                        onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;"
                                                        class="btn-xs btn btn-info"><i class="fa fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php if (isset($estimates_info) || isset($add_items)) {
                                            $i = 1;
                                            $items_indicator = 'items';
                                            if (isset($estimates_info)) {
                                                $add_items = $this->estimates_model->ordered_items_by_id($estimates_info->estimates_id);
                                                $items_indicator = 'items';
                                            }
                                            foreach ($add_items as $item) {
                                                $manual = false;
                                                $table_row = '<tr class="sortable item">';
                                                $table_row .= '<td class="dragger">';
                                                if (!is_numeric($item->quantity)) {
                                                    $item->quantity = 1;
                                                }
                                                $invoice_item_taxes = $this->estimates_model->get_invoice_item_taxes($item->estimate_items_id, 'estimate');

                                                // passed like string
                                                if ($item->estimate_items_id == 0) {
                                                    $invoice_item_taxes = $invoice_item_taxes[0];
                                                    $manual = true;
                                                }
                                                $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][estimate_items_id]', $item->estimate_items_id);
                                                $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][saved_items_id]', $item->saved_items_id);
                                                $amount = $item->unit_cost * $item->quantity;
                                                $amount = ($amount);
                                                // order input
                                                $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]"><input type="hidden" name="estimate_items_id[]" value="' . $item->estimate_items_id . '">';
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
                                                $table_row .= '<td class="taxrate">' . $this->admin_model->get_taxes_dropdown('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item->estimate_items_id, true, $manual) . '</td>';
                                                $table_row .= '<td class="amount">' . $amount . '</td>';
                                                $table_row .= '<td><a href="#" class="btn-xs btn btn-danger pull-left" onclick="delete_item(this,' . $item->estimate_items_id . '); return false;"><i class="fa fa-trash"></i></a></td>';
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
                                                            if (isset($estimates_info)) {
                                                                if ($estimates_info->discount_percent != 0) {
                                                                    $discount_percent = $estimates_info->discount_percent;
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
                                                                <span
                                                                        class="bold"><?php echo lang('adjustment'); ?></span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" data-parsley-type="number"
                                                                   value="<?php if (isset($estimates_info)) {
                                                                       echo $estimates_info->adjustment;
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
                                    if (!empty($estimates_info)) { ?>
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
                                </div>
                            </div>

</form>
<?php } else { ?>
    </div>
<?php } ?>
</div>
<script type="text/javascript">
    function slideToggle($id) {
        $('#quick_state').attr('data-original-title', '<?= lang('view_quick_state') ?>');
        $($id).slideToggle("slow");
    }

    $(document).ready(function () {
        init_items_sortable();
    });
</script>
