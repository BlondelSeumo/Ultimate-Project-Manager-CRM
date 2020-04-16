<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$user_type = $this->session->userdata('user_type');
if ($user_type == '1') { ?>
    <div class="row">
        <div class="col-sm-12" data-offset="0">
            <div class="panel panel-custom" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?= lang('view_time_history') ?></strong>
                    </div>
                </div>
                <div class="panel-body">
                    <form id="attendance-form" role="form" enctype="multipart/form-data"
                          action="<?php echo base_url(); ?>admin/attendance/time_history" method="post"
                          class="form-horizontal form-groups-bordered">
                        <div class="form-group" id="border-none">
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('employee') ?> <span
                                    class="required">*</span></label>
                            <div class="col-sm-5">
                                <select name="user_id" style="width: 100%" id="employee"
                                        class="form-control select_box">
                                    <option value=""><?= lang('select_employee') ?>...</option>
                                    <?php if (!empty($all_employee)): ?>
                                        <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                            <optgroup label="<?php echo $dept_name; ?>">
                                                <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                                    <option value="<?php echo $v_employee->user_id; ?>"
                                                        <?php
                                                        if (!empty($user_id)) {
                                                            $user_id = $user_id;
                                                        } else {
                                                            $user_id = $this->session->userdata('user_id');
                                                        }
                                                        if (!empty($user_id)) {
                                                            echo $v_employee->user_id == $user_id ? 'selected' : '';
                                                        }
                                                        ?>><?php echo $v_employee->employment_id . '- ' . $v_employee->fullname . ' ( ' . $v_employee->designations . ' )' ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-sm-2 ">
                                <button type="submit" name="search" value="1"
                                        class="btn btn-primary"><?= lang('go') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php }
if (!empty($edit)) {
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $name = $user_info->fullname;
} else {
    $name = lang('my');
}
if (!empty($user_id)) {
    $user_id = $user_id;
} else {
    $user_id = $this->session->userdata('user_id');
}
?>

<div class="row" id="printableArea">
    <div class="col-sm-12">
        <div class="panel panel-custom">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= $name . ' ' . lang('time_logs') ?></strong>
                    <div class="pull-right hidden-print">
                        <a href="<?= base_url() ?>admin/attendance/time_history_pdf/<?= $user_id ?>"
                           class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top"
                           title="<?= lang('pdf') ?>"><span><i class="fa fa-file-pdf-o"></i></span></a>
                        <a href="" onclick="printEmp_report('printableArea')" class="btn btn-danger btn-xs"
                           data-toggle="tooltip" data-placement="top" title="<?= lang('print') ?>"><span><i
                                    class="fa fa-print"></i></span></a>
                    </div>
                </div>
            </div>
            <div class="custom-tabs" role="tabpanel">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    if (!empty($mytime_info)):foreach ($mytime_info as $year => $v_time_info):
                        ?>
                        <?php if (!empty($v_time_info)): ?>
                        <li role="presentation" class="<?php
                        if ($year == $active) {
                            echo 'active';
                        }
                        ?>"><a href="#<?php echo $year ?>" aria-controls="home" role="tab"
                               data-toggle="tab"><?php echo $year ?></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="custom-tab-content">
                    <?php if (!empty($mytime_info)) :
                    foreach ($mytime_info as $year => $v_time_info):
                        ?>
                    <div role="tabpanel" class="tab-pane <?php
                    if ($year == $active) {
                        echo 'active';
                    }
                    ?>" id="<?php echo $year ?>">
                        <?php if (!empty($v_time_info)): foreach ($v_time_info as $week => $time_info): ?>
                        <?php if (!empty($time_info)): ?>
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-custom">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title time-color">
                                            <a data-toggle="collapse" data-parent="#accordion"
                                               href="#<?php echo $week; ?>" aria-expanded="true"
                                               aria-controls="collapseOne">
                                                Week : <?php echo $week; ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="<?php echo $week; ?>" class="panel-collapse collapse" role="tabpanel"
                                         aria-labelledby="headingOne">
                                        <div class="panel-body table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th><?= lang('clock_in_time') ?></th>
                                                    <th><?= lang('clock_out_time') ?></th>
                                                    <th><?= lang('ip_address') ?></th>
                                                    <th><?= lang('hours') ?></th>
                                                    <th class="hidden-print"><?= lang('action') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $total_hh = 0;
                                                $total_mm = 0;

                                                if (!empty($time_info)):foreach ($time_info as $key => $v_mytime):
                                                    ?>
                                                    <td colspan="5"
                                                        style="background: rgba(233, 237, 228, 0.73);font-weight: bold"><?php echo $key; ?></td>

                                                    <?php
                                                    $vtimeinfo = $v_mytime[0];
                                                    if ($vtimeinfo->attendance_status == 1) {
                                                        $timeinfo = get_result('tbl_clock', array('attendance_id' => $vtimeinfo->attendance_id));

                                                        foreach ($timeinfo as $mytime):?>
                                                            <tr id="table_<?= strtotime($key) ?>">
                                                                <td><?php echo display_time($mytime->clockin_time); ?></td>
                                                                <td><?php
                                                                    if (empty($mytime->clockout_time)) {
                                                                        echo '<span class="text-danger">' . lang("currently_clock_in") . '<span>';
                                                                    } else {
                                                                        echo display_time($mytime->clockout_time);
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?= $mytime->ip_address ?>
                                                                </td>
                                                                <td><?php
                                                                    if (!empty($mytime->clockout_time)) {
                                                                        // calculate the start timestamp
                                                                        $startdatetime = strtotime($vtimeinfo->date_in . " " . $mytime->clockin_time);
                                                                        // calculate the end timestamp
                                                                        $enddatetime = strtotime($vtimeinfo->date_out . " " . $mytime->clockout_time);
                                                                        // calulate the difference in seconds
                                                                        $difference = $enddatetime - $startdatetime;
                                                                        $years = abs(floor($difference / 31536000));
                                                                        $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                                                                        $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                                                                        $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                                                                        $total_mm += $mins;
                                                                        $total_hh += $hours;
                                                                        echo $hours . " : " . $mins . " m";
                                                                    }
                                                                    ?></td>
                                                                <?php if (!empty($mytime->clock_id)) { ?>
                                                                    <td class="hidden-print"><?php echo btn_edit_modal('admin/attendance/edit_mytime/' . $mytime->clock_id) ?>
                                                                        <?php $admin = admin();
                                                                        if (!empty($admin)) { ?>
                                                                            <?php echo ajax_anchor(base_url('admin/attendance/delete_mytime/' . $mytime->clock_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . strtotime($key))) ?>
                                                                        <?php } ?>
                                                                    </td>
                                                                <?php } ?>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php } else if ($vtimeinfo->attendance_status == 0) {
                                                        ?>
                                                        <tr>
                                                            <td colspan="3"><span
                                                                    class="label label-danger"><?= lang('absent') ?></span>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    } elseif ($vtimeinfo->attendance_status == 3) { ?>
                                                        <tr>
                                                            <td colspan="3"><span
                                                                    class="label label-danger"><?= lang('on_leave') ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>

                                                <?php endforeach; ?>
                                                    <table>
                                                        <tr>
                                                            <td colspan="2" class="text-right">
                                                                <strong
                                                                    style="margin-right: 10px; "><?= lang('total_working_hour') ?>
                                                                    : </strong>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if ($total_mm > 59) {
                                                                    $total_hh += intval($total_mm / 60);
                                                                    $total_mm = intval($total_mm % 60);
                                                                }
                                                                echo $total_hh . " : " . $total_mm . " m";
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6">
                                                            <?= lang('nothing_to_display') ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <?= lang('nothing_to_display') ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    function printEmp_report(printableArea) {
        $('div.wrapper').find('.collapse').css('display', 'block');
        var printContents = document.getElementById(printableArea).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>