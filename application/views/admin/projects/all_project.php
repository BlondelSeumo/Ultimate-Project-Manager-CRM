<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->items_model->get_permission('tbl_goal_tracking');
$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->items_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 12) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->items_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->items_model->send_goal_mail('goal_not_achieve', $v_goal_track);
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
    $where = array('created_time >=' => $date . " 00:00:00", 'created_time <=' => $date . " 23:59:59", 'project_status' => 'completed');
    $invoice_result[$date] = count($this->db->where($where)->get('tbl_project')->result());
}

$terget_achievement = $this->db->where(array('goal_type_id' => 12, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->items_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if (!empty(admin())) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($tolal_goal) ?></p>
                    <p class="m0">
                        <small data-toggle="tooltip" data-placement="top"
                               title="<?= lang('achievement') ?>"><?= lang('achievement') ?></small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead" data-toggle="tooltip" data-placement="top"
                       title="<?= lang('completed') . ' ' . lang('achievements') ?>"><?= ($complete_achivement) ?></p>
                    <p class="m0" data-toggle="tooltip" data-placement="top"
                       title="<?= lang('completed') . ' ' . lang('achievements') ?>">
                        <small><?= lang('completed') ?></small>
                    </p>
                </div>


            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead" data-toggle="tooltip" data-placement="top"
                       title="<?= lang('pending') . ' ' . lang('achievements') ?>">
                        <?php
                        if ($tolal_goal < $complete_achivement) {
                            $pending_goal = 0;
                        } else {
                            $pending_goal = $tolal_goal - $complete_achivement;
                        } ?>
                        <?= $pending_goal ?></p>
                    <p class="m0" data-toggle="tooltip" data-placement="top"
                       title="<?= lang('pending') . ' ' . lang('achievements') ?>">
                        <small><?= lang('pending') ?></small>
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
                <div class="col-xs-6 text-center pt" data-toggle="tooltip" data-placement="top"
                     title="<?= lang('done') . ' ' . lang('percentage') ?>">
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
                            <span class="easypie-text"><strong> <?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= ($total_terget) ?></p>
                    <p class="m0">
                        <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
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
    </div>
    <?php
    $complete = 0;
    $cancel = 0;
    $in_progress = 0;
    $started = 0;
    $on_hold = 0;
    $overdue = 0;
    $all_project = $this->items_model->get_permission('tbl_project');

    if (!empty($all_project)):foreach ($all_project as $v_project):
        $aprogress = $this->items_model->get_project_progress($v_project->project_id);
        if ($v_project->project_status == 'completed') {
            $complete += count($v_project->project_id);
        }
        if ($v_project->project_status == 'cancel') {
            $cancel += count($v_project->project_id);
        }
        if ($v_project->project_status == 'in_progress') {
            $in_progress += count($v_project->project_id);
        }
        if ($v_project->project_status == 'on_hold') {
            $on_hold += count($v_project->project_id);
        }
        if ($v_project->project_status == 'started') {
            $started += count($v_project->project_id);
        }
        if (strtotime(date('Y-m-d')) > strtotime($v_project->end_date) && $aprogress < 100) {
            $overdue += count($v_project->project_id);

        }
    endforeach;
    endif;
    if (!empty($all_project)) {
        $overdue_width = ($overdue / count($all_project)) * 100;
        $started_width = ($started / count($all_project)) * 100;
        $on_hold_width = ($on_hold / count($all_project)) * 100;
        $in_progress_width = ($in_progress / count($all_project)) * 100;
        $cancel_width = ($cancel / count($all_project)) * 100;
        $complete_width = ($complete / count($all_project)) * 100;
    } else {
        $overdue_width = 0;
        $started_width = 0;
        $on_hold_width = 0;
        $in_progress_width = 0;
        $cancel_width = 0;
        $complete_width = 0;
    }
    ?>
    <div class="row">
        <div class="col-lg-2 pl-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="p_status" search-type="<?= ('overdue') ?>" id="overdue"
                               href="#"><?= lang('overdue') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $overdue ?>
                            / <?= count($all_project) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-danger " data-toggle="tooltip"
                             data-original-title="<?= $overdue_width ?>%"
                             style="width: <?= $overdue_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>

        <div class="col-lg-2">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="p_status" search-type="<?= ('started') ?>" id="started"
                               href="#"><?= lang('started') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $started ?>
                            / <?= count($all_project) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= $overdue_width ?>%"
                             style="width: <?= $overdue_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-2">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="p_status" search-type="<?= ('on_hold') ?>" id="on_hold"
                               href="#"><?= lang('on_hold') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $on_hold ?>
                            / <?= count($all_project) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                             data-original-title="<?= $on_hold_width ?>%"
                             style="width: <?= $on_hold_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-2">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="p_status" search-type="<?= ('in_progress') ?>"
                               id="in_progress"
                               href="#"><?= lang('in_progress') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $in_progress ?>
                            / <?= count($all_project) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-info " data-toggle="tooltip"
                             data-original-title="<?= $in_progress_width ?>%"
                             style="width: <?= $in_progress_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-2">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="p_status" search-type="<?= ('cancel') ?>" id="cancel"
                               href="#"><?= lang('cancel') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $cancel ?>
                            / <?= count($all_project) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-danger " data-toggle="tooltip"
                             data-original-title="<?= $cancel_width ?>%"
                             style="width: <?= $cancel_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-2 pr-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="p_status" search-type="<?= ('complete') ?>" id="completed"
                               href="#"><?= lang('complete') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $complete ?>
                            / <?= count($all_project) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-success " data-toggle="tooltip"
                             data-original-title="<?= $complete_width ?>%"
                             style="width: <?= $complete_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    </div>
<?php } ?>
<?= message_box('success'); ?>
<?= message_box('error');
$all_client = $this->db->get('tbl_client')->result();
$created = can_action('57', 'created');
$edited = can_action('57', 'edited');
$deleted = can_action('57', 'deleted');
if (!empty($created) || !empty($edited)){
?>
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
                    <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                    <li class="divider"></li>

                    <li class="dropdown-submenu pull-left  " id="from_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('client'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left from_account"
                            style="">
                            <?php
                            if (!empty($all_client)) {
                                foreach ($all_client as $v_client) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_client->client_id ?>" search-type="by_client">
                                        <a href="#"><?php echo $v_client->name; ?></a>
                                    </li>
                                <?php }
                            }
                            ?>
                        </ul>
                    </li>
                    <div class="clearfix"></div>
                    <li class="dropdown-submenu pull-left " id="to_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('staff'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left to_account"
                            style="">
                            <?php if (admin()) { ?>
                                <li class="filter_by" id="everyone"
                                    search-type="by_staff">
                                    <a href="#"><?php echo lang('everyone'); ?></a>
                                </li>
                            <?php } ?>
                            <?php
                            if (count($assign_user) > 0) { ?>
                                <?php foreach ($assign_user as $v_staff) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_staff->user_id ?>"
                                        search-type="by_staff">
                                        <a href="#"><?php echo fullname($v_staff->user_id); ?></a>
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
                                                                    data-toggle="tab"><?= lang('all') . ' ' . lang($tab) ?></a>
                </li>

                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                    data-toggle="tab"><?= lang('new_project') ?></a>
                </li>
                <li><a style="background-color: #1797be;color: #ffffff"
                       href="<?= base_url() ?>admin/projects/import"><?= lang('import') . ' ' . lang('project') ?></a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- ************** general *************-->
                <div
                        class="tab-pane <?= $active == 1 || $active == 'overdue' || $active == 'started' || $active == 'on_hold' || $active == 'in_progress' || $active == 'cancel' || $active == 'completed' ? 'active' : ''; ?>"
                        id="manage">
                    <?php } else { ?>
                    <style type="text/css">
                        .pull-right a {
                            font-size: 14px;
                            border: 1px solid #e8e8e8;
                            padding: 4px;
                            margin-left: 10px;
                            color: #656565;
                        }
                    </style>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all') . ' ' . lang($tab) ?></strong></div>
                        </header>
                        <?php } ?>

                        <style type="text/css">
                            .custom-bulk-button {
                                display: initial;
                            }
                        </style>
                        <div class="table-responsive">
                            <table class="table table-striped bulk_table" id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (!empty($deleted)) { ?>
                                        <th data-orderable="false">
                                            <div class="checkbox c-checkbox">
                                                <label class="needsclick">
                                                    <input id="select_all" type="checkbox">
                                                    <span class="fa fa-check"></span></label>
                                            </div>
                                        </th>
                                    <?php } ?>
                                    <th><?= lang('project_name') ?></th>
                                    <th><?= lang('client') ?></th>
                                    <th><?= lang('end_date') ?></th>
                                    <th><?= lang('assigned_to') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <?php $show_custom_fields = custom_form_table(4, null);
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
                                    $(document).ready(function () {
                                        list = base_url + "admin/projects/projectList";
                                        bulk_url = base_url + "admin/projects/bulk_delete";
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
                                        });
                                        $('.from_account li').on('click', function () {
                                            if ($('.to_account').css('display') == 'block') {
                                                $('.to_account').removeAttr("style");
                                                $('.from_account').css('display', 'block');
                                            } else {
                                                $('.from_account').css('display', 'block')
                                            }
                                        });

                                        $('.to_account li').on('click', function () {
                                            if ($('.from_account').css('display') == 'block') {
                                                $('.from_account').removeAttr("style");
                                                $('.to_account').css('display', 'block');
                                            } else {
                                                $('.to_account').css('display', 'block');
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
                                            table_url(base_url + "admin/projects/projectList/" + filter_by + search_type);
                                        });
                                        <?php }?>

                                        $('.p_status').on('click', function () {
                                            var result = $(this).attr('id');
                                            table_url(base_url + "admin/projects/projectList/" + result);
                                        });
                                    });
                                </script>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) {
                        $client_project = $this->uri->segment(4);
                        if ($client_project == 'client_project') {
                            $client_id = $this->uri->segment(5);
                        }
                        if (!empty($project_info)) {
                            $projects_id = $project_info->project_id;
                        } else {
                            $projects_id = null;
                        }
                        ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                            <?php echo form_open(base_url('admin/projects/saved_project/' . $projects_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
                            <div class="panel-body">
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('project_no') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($project_info)) {
                                                echo $project_info->project_no;
                                            } else {
                                                if (empty(config_item('projects_number_format'))) {
                                                    echo config_item('projects_prefix');
                                                }
                                                echo $this->items_model->generate_projects_number();
                                            }
                                            ?>" name="project_no">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('project_name') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($project_info)) {
                                                echo $project_info->project_name;
                                            }
                                            ?>" name="project_name" required="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('select_client') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <select name="client_id" class="form-control select_box"
                                                        style="width: 100%"
                                                        required="">
                                                    <option value=""><?= lang('select_client') ?></option>
                                                    <?php
                                                    $all_client = $this->db->get('tbl_client')->result();
                                                    if (!empty($all_client)) {
                                                        foreach ($all_client as $v_client) {
                                                            ?>
                                                            <option value="<?= $v_client->client_id ?>" <?php
                                                            if (!empty($project_info) && $project_info->client_id == $v_client->client_id) {
                                                                echo 'selected';
                                                            } else if (!empty($client_id) && $client_id == $v_client->client_id) {
                                                                echo 'selected';
                                                            }
                                                            ?>><?= $v_client->name ?></option>
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
                                    <script src="<?= base_url() ?>assets/js/jquery-ui.js"></script>
                                    <?php $direction = $this->session->userdata('direction');
                                    if (!empty($direction) && $direction == 'rtl') {
                                        $RTL = 'on';
                                    } else {
                                        $RTL = config_item('RTL');
                                    }
                                    ?>
                                    <?php
                                    if (!empty($RTL)) { ?>
                                        <!-- bootstrap-editable -->
                                        <script type="text/javascript"
                                                src="<?= base_url() ?>assets/plugins/jquery-ui/jquery.ui.slider-rtl.js"></script>
                                    <?php }
                                    ?>
                                    <style>

                                        .ui-widget.ui-widget-content {
                                            border: 1px solid #dde6e9;
                                        }

                                        .ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl {
                                            border: 7px solid #28a9f1;
                                        }

                                        .ui-widget-content {
                                            border: 1px solid #dddddd;
                                            /*background: #E1E4E9;*/
                                            color: #333333;
                                        }

                                        .ui-slider {
                                            position: relative;
                                            text-align: left;
                                        }

                                        .ui-slider-horizontal {
                                            height: 1em;
                                        }

                                        .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
                                            border: 1px solid #1797be;
                                            background: #1797be;
                                            font-weight: normal;
                                            color: #454545;
                                        }

                                        .ui-slider-horizontal .ui-slider-handle {
                                            top: -.3em;
                                            margin-left: -.1em;;
                                            margin-right: -.1em;;
                                        }

                                        .ui-slider .ui-slider-handle:hover {
                                            background: #1797be;
                                        }

                                        .ui-slider .ui-slider-handle {
                                            position: absolute;
                                            z-index: 2;
                                            width: 1.2em;;
                                            height: 1.5em;
                                            cursor: default;
                                            -ms-touch-action: none;
                                            touch-action: none;

                                        }

                                        .ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled {
                                            opacity: .35;
                                            filter: Alpha(Opacity=35);
                                            background-image: none;
                                        }

                                        .ui-state-disabled {
                                            cursor: default !important;
                                            pointer-events: none;
                                        }

                                        .ui-slider.ui-state-disabled .ui-slider-handle, .ui-slider.ui-state-disabled .ui-slider-range {
                                            filter: inherit;
                                        }

                                        .ui-slider-range, .ui-widget-header, .ui-slider-handle:before, .list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus, .icon-frame {
                                            background-image: none;
                                            background: #28a9f1;
                                        }

                                    </style>
                                    <?php
                                    if (!empty($project_info)) {
                                        $value = $this->items_model->get_project_progress($project_info->project_id);
                                    } else {
                                        $value = 0;
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label
                                                class="col-lg-3 control-label"><?php echo lang('progress'); ?> </label>
                                        <div class="col-lg-8">
                                            <?php echo form_hidden('progress', $value); ?>
                                            <div
                                                    class="project_progress_slider project_progress_slider_horizontal mbot15"></div>

                                            <div class="input-group">
                                <span class="input-group-addon">
                                     <div class="">
                                         <div class="pull-left mt">
                                             <?php echo lang('progress'); ?>
                                             <span class="label_progress "><?php echo $value; ?>%</span>
                                         </div>
                                         <div class="checkbox c-checkbox pull-right" data-toggle="tooltip"
                                              data-placement="top"
                                              title="<?php echo lang('calculate_progress_through_tasks'); ?>">
                                             <label class="needsclick">
                                                 <input class="select_one"
                                                        type="checkbox" <?php if ((!empty($project_info) && $project_info->calculate_progress == 'through_tasks')) {
                                                     echo 'checked';
                                                 } ?> name="calculate_progress" value="through_tasks"
                                                        id="progress_from_tasks">
                                                 <span class="fa fa-check"></span>
                                                 <small><?php echo lang('through_tasks'); ?></small>
                                             </label>
                                         </div>
                                         <div class="checkbox c-checkbox pull-right" data-toggle="tooltip"
                                              data-placement="top"
                                              title="<?php echo lang('calculate_progress_through_project_hours'); ?>">
                                             <label class="needsclick">
                                                 <input class="select_one"
                                                        type="checkbox" <?php if ((!empty($project_info) && $project_info->calculate_progress == 'through_project_hours')) {
                                                     echo 'checked';
                                                 } ?> name="calculate_progress" value="through_project_hours"
                                                        id="through_project_hours">
                                                 <span class="fa fa-check"></span>
                                                 <small><?php echo lang('through_project_hours'); ?></small>
                                             </label>
                                         </div>
                                     </div>
                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ready(function () {
                                            var progress_input = $('input[name="progress"]');
                                            <?php if ((!empty($project_info) && $project_info->calculate_progress == 'through_project_hours')) {?>
                                            var progress_from_tasks = $('#through_project_hours');
                                            <?php }elseif ((!empty($project_info) && $project_info->calculate_progress == 'through_tasks')){?>
                                            var progress_from_tasks = $('#progress_from_tasks');
                                            <?php }else{?>
                                            var progress_from_tasks = $('.select_one');
                                            <?php } ?>

                                            var progress = progress_input.val();
                                            $('.project_progress_slider').slider({
                                                range: "min",
                                                <?php
                                                if (!empty($RTL)) { ?>
                                                isRTL: true,
                                                <?php }
                                                ?>
                                                min: 0,
                                                max: 100,
                                                value: progress,
                                                disabled: progress_from_tasks.prop('checked'),
                                                slide: function (event, ui) {
                                                    progress_input.val(ui.value);
                                                    $('.label_progress').html(ui.value + '%');
                                                }
                                            });
                                            progress_from_tasks.on('change', function () {
                                                var _checked = $(this).prop('checked');
                                                $('.project_progress_slider').slider({
                                                    disabled: _checked,
                                                });
                                            });
                                        })
                                        ;
                                    </script>

                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('start_date') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <input required type="text" name="start_date"
                                                       class="form-control start_date"
                                                       value="<?php
                                                       if (!empty($project_info->start_date)) {
                                                           echo date('Y-m-d', strtotime($project_info->start_date));
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
                                        <label class="col-lg-3 control-label"><?= lang('end_date') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <input required type="text" name="end_date"
                                                       class="form-control end_date"
                                                       value="<?php
                                                       if (!empty($project_info->end_date)) {
                                                           echo date('Y-m-d', strtotime($project_info->end_date));
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
                                        <label class="col-lg-3 control-label"><?= lang('billing_type') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select name="billing_type" onchange="get_billing_value(this.value)"
                                                    class="form-control select_box" style="width: 100%" required="">
                                                <option
                                                    <?php
                                                    if (!empty($project_info->billing_type)) {
                                                        echo $project_info->billing_type == 'fixed_rate' ? 'selected' : null;
                                                    } ?>
                                                        value="fixed_rate"><?= lang('fixed_rate') ?></option>
                                                <option
                                                    <?php
                                                    if (!empty($project_info->billing_type)) {
                                                        echo $project_info->billing_type == 'project_hours' ? 'selected' : null;
                                                    } ?>
                                                        value="project_hours"><?= lang('only') . ' ' . lang('project_hours') ?></option>
                                                <option
                                                    <?php
                                                    if (!empty($project_info->billing_type)) {
                                                        echo $project_info->billing_type == 'tasks_hours' ? 'selected' : null;
                                                    } ?>
                                                        value="tasks_hours"><?= lang('only') . ' ' . lang('tasks_hours') ?></option>
                                                <option
                                                    <?php
                                                    if (!empty($project_info->billing_type)) {
                                                        echo $project_info->billing_type == 'tasks_and_project_hours' ? 'selected' : null;
                                                    } ?>
                                                        value="tasks_and_project_hours"><?= lang('tasks_and_project_hours') ?></option>
                                            </select>
                                            <small class="based_on_tasks_hour" <?php
                                            if (!empty($project_info) && $project_info->billing_type == 'tasks_hours' || !empty($project_info) && $project_info->billing_type == 'tasks_and_project_hours') {
                                                echo 'style="display: block;"';
                                            } else {
                                                echo 'style="display: none;"';
                                            } ?> ><?php echo lang('based_on_hourly_rate') ?></small>
                                        </div>
                                    </div>
                                    <div class="form-group fixed_rate " <?php
                                    if (!empty($project_info) && $project_info->billing_type == 'fixed_rate') {
                                        echo 'style="display: block;"';
                                    } elseif (!empty($project_info) && $project_info->billing_type != 'fixed_rate') {
                                        echo 'style="display: none;"';
                                    }
                                    ?>>
                                        <label class="col-lg-3 control-label"><?= lang('fixed_price') ?></label>
                                        <div class="col-lg-8">
                                            <input data-parsley-type="number" type="text"
                                                   class="form-control fixed_rate"
                                                   value="<?php
                                                   if (!empty($project_info->project_cost)) {
                                                       echo $project_info->project_cost;
                                                   }
                                                   ?>" placeholder="50" name="project_cost">
                                        </div>
                                    </div>

                                    <div class="form-group hourly_rate " <?php
                                    if (!empty($project_info) && $project_info->billing_type == 'project_hours' || !empty($project_info) && $project_info->billing_type == 'tasks_and_project_hours') {
                                        echo 'style="display: block;"';
                                    } elseif (!empty($project_info) && $project_info->billing_type == 'fixed_rate' || !empty($project_info) && $project_info->billing_type == 'tasks_hours') {
                                        echo 'style="display: none;"';
                                    }
                                    ?>>
                                        <label
                                                class="col-lg-3 control-label"><?= lang('project_hourly_rate') ?></label>
                                        <div class="col-lg-8">
                                            <input data-parsley-type="number" type="text"
                                                   class="form-control hourly_rate"
                                                   value="<?php
                                                   if (!empty($project_info->hourly_rate)) {
                                                       echo $project_info->hourly_rate;
                                                   }
                                                   ?>" placeholder="50" name="hourly_rate">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('estimate_hours') ?></label>
                                        <div class="col-lg-8">
                                            <input type="number" step="0.01" value="<?php
                                            if (!empty($project_info->estimate_hours)) {
                                                $result = explode(':', $project_info->estimate_hours);
                                                echo $result[0] . '.' . $result[1];
                                            }
                                            ?>" class="form-control" name="estimate_hours">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('status') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select name="project_status" class="form-control select_box"
                                                    style="width: 100%"
                                                    required="">
                                                <option <?php
                                                if (!empty($project_info->project_status)) {
                                                    echo $project_info->project_status == 'started' ? 'selected' : null;
                                                } ?>
                                                        value="started"><?= lang('started') ?></option>
                                                <option <?php
                                                if (!empty($project_info->project_status)) {
                                                    echo $project_info->project_status == 'in_progress' ? 'selected' : null;
                                                } ?>
                                                        value="in_progress"><?= lang('in_progress') ?></option>
                                                <option <?php
                                                if (!empty($project_info->project_status)) {
                                                    echo $project_info->project_status == 'on_hold' ? 'selected' : null;
                                                } ?>
                                                        value="on_hold"><?= lang('on_hold') ?></option>
                                                <option <?php
                                                if (!empty($project_info->project_status)) {
                                                    echo $project_info->project_status == 'cancel' ? 'selected' : null;
                                                } ?>
                                                        value="cancel"><?= lang('cancel') ?></option>
                                                <option <?php
                                                if (!empty($project_info->project_status)) {
                                                    echo $project_info->project_status == 'completed' ? 'selected' : null;
                                                } ?>
                                                        value="completed"><?= lang('completed') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('demo_url') ?></label>
                                        <div class="col-lg-8">
                                            <input type="text" value="<?php
                                            if (!empty($project_info->demo_url)) {
                                                echo $project_info->demo_url;
                                            }
                                            ?>" class="form-control" placeholder="http://www.demourl.com"
                                                   name="demo_url">
                                        </div>
                                    </div>
                                    <?php
                                    if (!empty($project_info)) {
                                        $project_id = $project_info->project_id;
                                    } else {
                                        $project_id = null;
                                    }
                                    ?>
                                    <?= custom_form_Fields(4, $project_id); ?>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1"
                                               class="col-lg-3 control-label"><?= lang('assined_to') ?> <span
                                                    class="required">*</span></label>
                                        <div class="col-lg-8">
                                            <div class="checkbox c-radio needsclick">
                                                <label class="needsclick">
                                                    <input id="" <?php
                                                    if (!empty($project_info->permission) && $project_info->permission == 'all') {
                                                        echo 'checked';
                                                    } elseif (empty($project_info)) {
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
                                                    if (!empty($project_info->permission) && $project_info->permission != 'all') {
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
                                    if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                        echo 'show';
                                    }
                                    ?>" id="permission_user_1">
                                        <label for="field-1"
                                               class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                            <span
                                                    class="required">*</span></label>
                                        <div class="col-lg-8">
                                            <?php
                                            if (!empty($assign_user)) {
                                                foreach ($assign_user as $key => $v_user) {

                                                    if ($v_user->role_id == 1) {
                                                        $disable = true;
                                                        $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                                    } else {
                                                        $disable = false;
                                                        $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                                    }

                                                    ?>
                                                    <div class="checkbox c-checkbox needsclick">
                                                        <label class="needsclick">
                                                            <input type="checkbox"
                                                                <?php
                                                                if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                                    $get_permission = json_decode($project_info->permission);
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

                                                    if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                        $get_permission = json_decode($project_info->permission);

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
                                                                    class="fa fa-check"></span><?= lang('view') ?>
                                                        </label>
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input <?php if (!empty($disable)) {
                                                                echo 'disabled' . ' ' . 'checked';
                                                            } ?> id="<?= $v_user->user_id ?>"
                                                                <?php

                                                                if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                                    $get_permission = json_decode($project_info->permission);

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
                                                                    class="fa fa-check"></span><?= lang('edit') ?>
                                                        </label>
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input <?php if (!empty($disable)) {
                                                                echo 'disabled' . ' ' . 'checked';
                                                            } ?> id="<?= $v_user->user_id ?>"
                                                                <?php

                                                                if (!empty($project_info->permission) && $project_info->permission != 'all') {
                                                                    $get_permission = json_decode($project_info->permission);
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
                                                                    class="fa fa-check"></span><?= lang('delete') ?>
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
                                </div>
                                <div class="col-sm-5">
                                    <!-- checkbox -->
                                    <?php
                                    $project_permissions = $this->db->get('tbl_project_settings')->result();
                                    if (!empty($project_info->project_settings)) {
                                        $current_permissions = $project_info->project_settings;
                                        if ($current_permissions == NULL) {
                                            $current_permissions = '{"settings":"on"}';
                                        }
                                        $get_permissions = json_decode($current_permissions);
                                    }

                                    foreach ($project_permissions as $v_permissions) {
                                        ?>
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick">
                                                <input name="<?= $v_permissions->settings_id ?>"
                                                       value="<?= $v_permissions->settings ?>" <?php
                                                if (!empty($project_info->project_settings)) {
                                                    if (in_array($v_permissions->settings, $get_permissions)) {
                                                        echo "checked=\"checked\"";
                                                    }
                                                } else {
                                                    echo "checked=\"checked\"";
                                                }
                                                ?> type="checkbox">
                                                <span class="fa fa-check"></span>
                                                <?= lang($v_permissions->settings) ?>
                                            </label>
                                        </div>
                                        <hr class="mt-sm mb-sm"/>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label"><?= lang('description') ?> <span
                                                    class="text-danger">*</span></label>
                                        <div class="col-lg-10">

                            <textarea style="" name="description" class="form-control textarea_"
                                      placeholder="<?= lang('description') ?>"><?php
                                if (!empty($project_info->description)) {
                                    echo $project_info->description;
                                }
                                ?></textarea>
                                        </div>
                                    </div>
                                    <div class="btn-bottom-toolbar text-right">
                                        <?php
                                        if (!empty($project_info)) { ?>
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


                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    <?php } else { ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    <?php if(empty($project_info)){?>
    $('.hourly_rate').hide();
    <?php }?>
    function get_billing_value(val) {

        if (val == 'fixed_rate') {
            $('.fixed_rate').show();
            $(".fixed_rate").removeAttr('disabled');
            $('.hourly_rate').hide();
            $(".hourly_rate").attr('disabled', 'disabled');
            $('.based_on_tasks_hour').hide();
        } else if (val == 'tasks_hours') {
            $('.hourly_rate').hide();
            $(".hourly_rate").attr('disabled', 'disabled');
            $('.fixed_rate').hide();
            $(".fixed_rate").attr('disabled', 'disabled');
            $('.based_on_tasks_hour').show();
        } else {
            $('.hourly_rate').show();
            $(".hourly_rate").removeAttr('disabled');
            $('.fixed_rate').hide();
            $(".fixed_rate").attr('disabled', 'disabled');
            $('.based_on_tasks_hour').show();
        }
        if (val == 'project_hours') {
            $('.based_on_tasks_hour').hide();
        }
    }
</script>