<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php include_once 'assets/admin-ajax.php'; ?>
<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->bugs_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->bugs_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 9) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->bugs_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->bugs_model->send_goal_mail('goal_not_achieve', $v_goal_track);
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
    $where = array('update_time >=' => $date . " 00:00:00", 'update_time <=' => $date . " 23:59:59", 'bug_status' => 'resolved');

    $invoice_result[$date] = count($this->db->where($where)->get('tbl_bug')->result());
}

$terget_achievement = $this->db->where(array('goal_type_id' => 9, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->bugs_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
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
                        <?= $pending_goal ?></p>
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
    $unconfirmed = 0;
    $confirmed = 0;
    $in_progress = 0;
    $resolved = 0;
    $verified = 0;
    if (!empty($all_bugs_info)):foreach ($all_bugs_info as $v_bugs):
        if ($v_bugs->bug_status == 'unconfirmed') {
            $unconfirmed += count($v_bugs->bug_id);
        }
        if ($v_bugs->bug_status == 'confirmed') {
            $confirmed += count($v_bugs->bug_id);
        }
        if ($v_bugs->bug_status == 'in_progress') {
            $in_progress += count($v_bugs->bug_id);
        }
        if ($v_bugs->bug_status == 'resolved') {
            $resolved += count($v_bugs->bug_id);
        }
        if ($v_bugs->bug_status == 'verified') {
            $verified += count($v_bugs->bug_id);
        }
    endforeach;
    endif;
    if (!empty($all_bugs_info)) {
        $unconfirmed_width = ($unconfirmed / count($all_bugs_info)) * 100;
        $confirmed_width = ($confirmed / count($all_bugs_info)) * 100;
        $in_progress_width = ($in_progress / count($all_bugs_info)) * 100;
        $resolved_width = ($resolved / count($all_bugs_info)) * 100;
        $verified_width = ($verified / count($all_bugs_info)) * 100;
    } else {
        $unconfirmed_width = 0;
        $confirmed_width = 0;
        $in_progress_width = 0;
        $resolved_width = 0;
        $verified_width = 0;
    }
    ?>
    <div class="row">
        <div class="col-lg-5ths pl-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="b_status" search-type="<?= ('unconfirmed') ?>"
                               id="unconfirmed"
                               href="#"><?= lang('unconfirmed') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $unconfirmed ?>
                            / <?= count($all_bugs_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= $unconfirmed_width ?>%"
                             style="width: <?= $unconfirmed_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="b_status" search-type="<?= ('confirmed') ?>"
                               id="confirmed"
                               href="#"><?= lang('confirmed') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $confirmed ?>
                            / <?= count($all_bugs_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-info " data-toggle="tooltip"
                             data-original-title="<?= $confirmed_width ?>%"
                             style="width: <?= $confirmed_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="b_status" search-type="<?= ('in_progress') ?>"
                               id="in_progress"
                               href="#"><?= lang('in_progress') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $in_progress ?>
                            / <?= count($all_bugs_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                             data-original-title="<?= $in_progress_width ?>%"
                             style="width: <?= $in_progress_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>

        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="b_status" search-type="<?= ('resolved') ?>"
                               id="resolved"
                               href="#"><?= lang('resolved') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $resolved ?>
                            / <?= count($all_bugs_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-danger " data-toggle="tooltip"
                             data-original-title="<?= $resolved_width ?>%"
                             style="width: <?= $resolved_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths pr-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="b_status" search-type="<?= ('verified') ?>" id="verified"
                               href="#"><?= lang('verified') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $verified ?>
                            / <?= count($all_bugs_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-success " data-toggle="tooltip"
                             data-original-title="<?= $verified_width ?>%"
                             style="width: <?= $verified_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    </div>
<?php }
$created = can_action('58', 'created');
$edited = can_action('58', 'edited');
$deleted = can_action('58', 'deleted');
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

                    <li class="b_status" id="assigned_to_me"><a href="#"><?php echo lang('assigned_to_me'); ?></a></li>
                    <?php if (admin()) { ?>
                        <li class="filter_by" id="everyone"
                            search-type="by_staff">
                            <a href="#"><?php echo lang('assigned_to') . ' ' . lang('everyone'); ?></a>
                        </li>
                    <?php } ?>
                    <li class="dropdown-submenu pull-left  " id="from_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('project'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left from_account"
                            style="">
                            <?php
                            $project_info = $this->items_model->get_permission('tbl_project');
                            if (!empty($project_info)) {
                                foreach ($project_info as $v_project) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_project->project_id ?>" search-type="by_project">
                                        <a href="#"><?php echo $v_project->project_name; ?></a>
                                    </li>
                                <?php }
                            }
                            ?>
                        </ul>
                    </li>
                    <div class="clearfix"></div>
                    <li class="dropdown-submenu pull-left  " id="from_reporter">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('reporter'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left from_reporter"
                            style="">
                            <?php
                            $reporter_info = $this->db->get('tbl_users')->result();;
                            if (!empty($reporter_info)) {
                                foreach ($reporter_info as $v_reporter) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_reporter->user_id ?>" search-type="from_reporter">
                                        <a href="#"><?php echo fullname($v_reporter->user_id); ?></a>
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
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"
                                                                   data-toggle="tab"><?= lang('all_bugs') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"
                                                                   data-toggle="tab"><?= lang('new_bugs') ?></a></li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_bugs') ?></strong></div>
                        </header>
                        <?php } ?>
                        <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                            <div class="box-body">
                                <!-- Table -->
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('bug_title') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('severity') ?></th>
                                        <?php if ($this->session->userdata('user_type') == '1') { ?>
                                            <th><?= lang('reporter') ?></th>
                                        <?php } ?>
                                        <th><?= lang('assigned_to') ?></th>
                                        <?php $show_custom_fields = custom_form_table(6, null);
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
                                            <th><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        list = base_url + "admin/bugs/bugsList";
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
                                            table_url(base_url + "admin/bugs/bugsList/" + filter_by + search_type);
                                        });
                                        <?php }?>
                                        $('.b_status').on('click', function () {
                                            var result = $(this).attr('id');
                                            table_url(base_url + "admin/bugs/bugsList/" + result);
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <!-- Add Stock Category tab Starts -->
                        <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task"
                             style="position: relative;">
                            <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                                <div class="panel-body">
                                    <form data-parsley-validate="" novalidate=""
                                          action="<?php echo base_url() ?>admin/bugs/save_bug/<?php if (!empty($bug_info->bug_id)) echo $bug_info->bug_id; ?>"
                                          method="post" class="form-horizontal">


                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('issue_#') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" style="width:260px" value="<?php
                                                $this->load->helper('string');
                                                if (!empty($bug_info)) {
                                                    echo $bug_info->issue_no;
                                                } else {
                                                    echo strtoupper(random_string('alnum', 7));
                                                }
                                                ?>" name="issue_no">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('bug_title') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="bug_title" required class="form-control"
                                                       value="<?php if (!empty($bug_info->bug_title)) echo $bug_info->bug_title; ?>"/>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($bug_info->project_id)) {
                                            $project_id = $bug_info->project_id;
                                        } elseif (!empty($project_id)) {
                                            $project_id = $project_id; ?>
                                            <input type="hidden" name="un_project_id" required class="form-control"
                                                   value="<?php echo $project_id ?>"/>
                                        <?php }
                                        if (!empty($bug_info->opportunities_id)) {
                                            $opportunities_id = $bug_info->opportunities_id;
                                        } elseif (!empty($opportunities_id)) {
                                            $opportunities_id = $opportunities_id; ?>
                                            <input type="hidden" name="un_opportunities_id" required
                                                   class="form-control"
                                                   value="<?php echo $opportunities_id ?>"/>
                                        <?php }
                                        ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
                                            <div class="col-sm-5">
                                                <select name="related_to" class="form-control" id="check_related"
                                                        onchange="get_related_moduleName(this.value)">
                                                    <option
                                                        value="0"> <?= lang('none') ?> </option>
                                                    <option
                                                        value="project" <?= (!empty($project_id) ? 'selected' : '') ?>> <?= lang('project') ?> </option>
                                                    <option
                                                        value="opportunities" <?= (!empty($opportunities_id) ? 'selected' : '') ?>> <?= lang('opportunities') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="related_to">
                                        </div>
                                        <?php
                                        if (!empty($project_id)):?>
                                            <div class="form-group <?= !empty($project_id) ? '' : 'company' ?>">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('project') ?>
                                                    <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="project_id" style="width: 100%"
                                                            class="select_box <?= !empty($project_id) ? '' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_project = $this->bugs_model->get_permission('tbl_project');
                                                        if (!empty($all_project)) {
                                                            foreach ($all_project as $v_project) {
                                                                ?>
                                                                <option value="<?= $v_project->project_id ?>" <?php
                                                                if (!empty($project_id)) {
                                                                    echo $v_project->project_id == $project_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_project->project_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div id="milestone"></div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($opportunities_id)): ?>
                                            <div class="form-group <?= !empty($opportunities_id) ? '' : 'company' ?>">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('opportunities') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="opportunities_id" style="width: 100%"
                                                            class="select_box <?= !empty($opportunities_id) ? '' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        if (!empty($all_opportunities_info)) {
                                                            foreach ($all_opportunities_info as $v_opportunities) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_opportunities->opportunities_id ?>" <?php
                                                                if (!empty($opportunities_id)) {
                                                                    echo $v_opportunities->opportunities_id == $opportunities_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_opportunities->opportunity_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1" class="col-sm-3 control-label"><?= lang('reporter') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <select name="reporter" style="width: 100%" class="select_box"
                                                        required="">
                                                    <?php
                                                    $type = $this->uri->segment(4);
                                                    if (!empty($type) && !is_numeric($type)) {
                                                        $ex = explode('_', $type);
                                                        if ($ex[0] == 'c') {
                                                            $primary_contact = $ex[1];
                                                        }
                                                    }
                                                    $reporter_info = $this->db->get('tbl_users')->result();
                                                    if (!empty($reporter_info)) {
                                                        foreach ($reporter_info as $key => $v_reporter) {
                                                            $users_info = $this->db->where(array("user_id" => $v_reporter->user_id))->get('tbl_account_details')->row();
                                                            if (!empty($users_info)) {
                                                                if ($v_reporter->role_id == 1) {
                                                                    $role = lang('admin');
                                                                } elseif ($v_reporter->role_id == 2) {
                                                                    $role = lang('client');
                                                                } else {
                                                                    $role = lang('staff');
                                                                }
                                                                ?>
                                                                <option value="<?= $users_info->user_id ?>" <?php
                                                                if (!empty($bug_info->reporter)) {
                                                                    echo $v_reporter->user_id == $bug_info->reporter ? 'selected' : '';
                                                                } else if (!empty($primary_contact) && $primary_contact == $users_info->user_id) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?= $users_info->fullname . ' (' . $role . ')'; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('priority') ?> <span
                                                    class="text-danger">*</span> </label>
                                            <div class="col-lg-5">
                                                <div class=" ">
                                                    <select name="priority" class="form-control">
                                                        <?php
                                                        $priorities = $this->db->get('tbl_priority')->result();
                                                        if (!empty($priorities)) {
                                                            foreach ($priorities as $v_priorities):
                                                                ?>
                                                                <option value="<?= $v_priorities->priority ?>" <?php
                                                                if (!empty($bug_info) && $bug_info->priority == $bug_info->priority) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?= ($v_priorities->priority) ?></option>
                                                                <?php
                                                            endforeach;
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('severity') ?> <span
                                                    class="text-danger">*</span> </label>
                                            <div class="col-lg-5">
                                                <div class=" ">
                                                    <select name="severity" class="form-control">
                                                        <?php
                                                        $severity = array('minor', 'major', 'show_stopper', 'must_be_fixed');
                                                        if (!empty($severity)) {
                                                            foreach ($severity as $v_severity):
                                                                ?>
                                                                <option value="<?= $v_severity ?>" <?php
                                                                if (!empty($bug_info) && $bug_info->severity == $v_severity) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?= lang($v_severity) ?></option>
                                                                <?php
                                                            endforeach;
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('description') ?> </label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control textarea_" name="bug_description"><?php if (!empty($bug_info->bug_description)) echo $bug_info->bug_description; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('reproducibility') ?> </label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control textarea"
                                                          name="reproducibility"><?php if (!empty($bug_info->reproducibility)) echo $bug_info->reproducibility; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('bug_status') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">

                                                <select name="bug_status" class="form-control" required>
                                                    <option
                                                        value="unconfirmed" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'unconfirmed' ? 'selected' : '' ?>> <?= lang('unconfirmed') ?> </option>
                                                    <option
                                                        value="confirmed" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'confirmed' ? 'selected' : '' ?>> <?= lang('confirmed') ?> </option>
                                                    <option
                                                        value="in_progress" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'in_progress' ? 'selected' : '' ?>> <?= lang('in_progress') ?> </option>
                                                    <option
                                                        value="resolved" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'resolved' ? 'selected' : '' ?>> <?= lang('resolved') ?> </option>
                                                    <option
                                                        value="verified" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'verified' ? 'selected' : '' ?>> <?= lang('verified') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if (!empty($project_id)): ?>
                                            <div class="form-group">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                                    if (!empty($bug_info) && $bug_info->client_visible == 'Yes') {
                                                        echo 'checked';
                                                    }
                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success" data-offstyle="danger"
                                                           type="checkbox">
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php
                                        if (!empty($bug_info)) {
                                            $bug_id = $bug_info->bug_id;
                                        } else {
                                            $bug_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(6, $bug_id); ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('assined_to') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="checkbox c-radio needsclick">
                                                    <label class="needsclick">
                                                        <input id="" <?php
                                                        if (!empty($bug_info->permission) && $bug_info->permission == 'all') {
                                                            echo 'checked';
                                                        } elseif (empty($bug_info)) {
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
                                                        if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
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
                                        if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                            echo 'show';
                                        }
                                        ?>" id="permission_user_1">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
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
                                                                    if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                                        $get_permission = json_decode($bug_info->permission);
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

                                                        if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                            $get_permission = json_decode($bug_info->permission);

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
                                                                <input <?php if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                } ?> id="<?= $v_user->user_id ?>"
                                                                    <?php

                                                                    if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                                        $get_permission = json_decode($bug_info->permission);

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
                                                                <input <?php if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                } ?> id="<?= $v_user->user_id ?>"
                                                                    <?php

                                                                    if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                                        $get_permission = json_decode($bug_info->permission);
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


                                        <div class="btn-bottom-toolbar text-right">
                                            <?php
                                            if (!empty($bug_info)) { ?>
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
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

