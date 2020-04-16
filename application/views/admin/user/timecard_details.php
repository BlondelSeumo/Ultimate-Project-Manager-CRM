<div class="form-horizontal">
    <!-- ********************************* Salary Details Panel ***********************-->
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('timecard_details') ?></strong>
                <?php if (!empty($attendace_info)) { ?>
                    <div class="pull-right ">
                        <span><?php echo btn_pdf('admin/user/timecard_details_pdf/' . $profile_info->user_id . '/' . date('Y-n', strtotime($date))); ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body">
            <form id="attendance-form" role="form" enctype="multipart/form-data"
                  action="<?php echo base_url(); ?>admin/user/user_details/<?= $profile_info->user_id ?>/6"
                  method="post"
                  class="form-horizontal form-groups-bordered">
                <div class="form-group">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('month') ?><span
                            class="required"> *</span></label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" class="form-control monthyear" value="<?php
                            if (!empty($date)) {
                                echo date('Y-n', strtotime($date));
                            }
                            ?>" name="date">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 ">
                        <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('go') ?></button>
                    </div>
                </div>
            </form>
            <?php if (!empty($attendace_info)): ?>
                <div class="row">
                    <div class="panel panel-custom ">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <strong><?= lang('works_hours_deatils').' ' ?><?php echo date('F-Y', strtotime($date));; ?></strong>
                            </h4>
                        </div>
                        <?php

                        foreach ($attendace_info as $week => $v_attndc_info):

                            ?>
                            <div class="box-header" style="border-bottom: 1px solid red">
                                <h4 class="box-title" style="font-size: 15px">
                                    <strong><?= lang('week') ?> : <?php echo $week; ?> </strong>
                                </h4>
                            </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <?php
                                    if (!empty($v_attndc_info)): foreach ($v_attndc_info as $date => $attendace):
                                        $total_hour = 0;
                                        $total_minutes = 0;
                                        ?>
                                        <th>

                                            <?= strftime(config_item('date_format'), strtotime($date)) ?></th>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <?php
                                    if (!empty($v_attndc_info)):foreach ($v_attndc_info as $date => $v_attendace):

                                        $total_hh = 0;
                                        $total_mm = 0;

                                        ?>
                                        <?php
                                        if (!empty($v_attendace)) {
                                            foreach ($v_attendace as $v_attandc) {
                                                if ($v_attandc->attendance_status == 1) {

                                                    // calculate the start timestamp
                                                    $startdatetime = strtotime($v_attandc->date_in . " " . $v_attandc->clockin_time);
                                                    // calculate the end timestamp
                                                    $enddatetime = strtotime($v_attandc->date_out . " " . $v_attandc->clockout_time);
                                                    // calulate the difference in seconds
                                                    $difference = $enddatetime - $startdatetime;
                                                    $years = abs(floor($difference / 31536000));
                                                    $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                                                    $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                                                    $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                                                    $total_mm += $mins;
                                                    $total_hh += $hours;
                                                    // output the result
                                                    //echo round($hoursDiff) . " : " . round($minutesDiffRemainder) . " m";
                                                } elseif ($v_attandc->attendance_status == 'H') {
                                                    $holiday = 1;
                                                } elseif ($v_attandc->attendance_status == '3') {
                                                    $leave = 1;
                                                } elseif ($v_attandc->attendance_status == '0') {
                                                    $absent = 1;
                                                }
                                            }
                                        }

                                        ?>
                                        <td>

                                            <?php
                                            if ($total_mm > 59) {
                                                $total_hh += intval($total_mm / 60);
                                                $total_mm = intval($total_mm % 60);
                                            }
                                            $total_hour += $total_hh;
                                            $total_minutes += $total_mm;
                                            if ($total_hh != 0 || $total_mm != 0) {
                                                echo $total_hh . " : " . $total_mm . " m";
                                            } elseif (!empty($holiday)) {
                                                echo '<span style="font-size: 12px;" class="label label-info std_p">' . lang('holiday') . '</span>';
                                            } elseif (!empty($leave)) {
                                                echo '<span style="font-size: 12px;" class="label label-warning std_p">' . lang('on_leave') . '</span>';
                                            } elseif (!empty ($absent)) {
                                                echo '<span style="font-size: 12px;" class="label label-danger std_p">' . lang('absent') . '</span>';
                                            } else {
                                                echo $total_hh . " : " . $total_mm . " m";
                                            }
                                            ?>
                                        </td>
                                        <?php
                                        $holiday = NULL;
                                        $leave = NULL;
                                        $absent = NULL;
                                    endforeach;
                                    endif;
                                    ?>
                                </tr>
                                <table>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong
                                                    style="margin-right: 10px; "><?= lang('total_working_hour') ?>
                                                : </strong>
                                        </td>
                                        <td>
                                            <?php
                                            if ($total_minutes >= 60) {
                                                $total_hour += intval($total_minutes / 60);
                                                $total_minutes = intval($total_minutes % 60);
                                            }
                                            echo $total_hour . " : " . $total_minutes . " m";
                                            ?>
                                        </td>
                                    </tr>
                                </table>

                                </tbody>
                            </table>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div><!-- ****************** Total Salary Details End  *******************-->
    </div>
</div>

