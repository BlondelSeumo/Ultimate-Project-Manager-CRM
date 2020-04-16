<style type="text/css" media="print">
    @media print {
        .accordion {
            overflow: visible !important;
        }

        .accordionPanelContent {
            display: block !important;
            overflow: visible !important;
            height: auto !important;
        }

        @page {
            size: landscape
        }

        .panel-collapse,
        .panel-collapse .collapse {
            display: block !important;
            overflow: visible !important;
            height: auto !important;
        }

        .collapse {
            display: block !important;
            overflow: visible !important;
            height: auto !important;
        }
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('attendance_report') ?></strong>
                </div>
            </div>
            <div class="panel-body">
                <form id="attendance-form" role="form" enctype="multipart/form-data"
                      action="<?php echo base_url(); ?>admin/attendance/get_report_3" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('department') ?><span
                                class="required"> *</span></label>

                        <div class="col-sm-5">
                            <select required name="departments_id" class="form-control select_box">
                                <option value=""><?= lang('select') . ' ' . lang('department') ?></option>
                                <?php if (!empty($all_department)): foreach ($all_department as $department):
                                    if (!empty($department->deptname)) {
                                        $deptname = $department->deptname;
                                    } else {
                                        $deptname = lang('undefined_department');
                                    }
                                    ?>
                                    <option value="<?php echo $department->departments_id; ?>"
                                        <?php if (!empty($departments_id)): ?>
                                            <?php echo $department->departments_id == $departments_id ? 'selected ' : '' ?>
                                        <?php endif; ?>>
                                        <?php echo $deptname ?>
                                    </option>
                                    <?php
                                endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('month') ?><span
                                class="required"> *</span></label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <input required type="text" class="form-control monthyear" value="<?php
                                if (!empty($date)) {
                                    echo date('Y-n', strtotime($date));
                                }
                                ?>" name="date">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-5 ">
                            <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('search') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
if ((!empty($date)) && !empty($attendace_info)): ?>
<div class="row" id="printableArea">
    <div class="col-sm-12 std_print">
        <div class="panel panel-custom ">
            <div class="panel-heading" style="">
                <h4 class="panel-title"><strong><?= lang('works_hours_deatils') . ' ' ?><?php echo $month; ?></strong>
                    <div class="show_print">
                        <?= lang('department') . ' : ' . $dept_name->deptname ?>
                    </div>
                    <div class="pull-right hidden-print">
                        <a href="<?= base_url() ?>admin/attendance/attendance_pdf/3/<?= $departments_id . '/' . $date; ?>"
                           class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top"
                           title="<?= lang('pdf') ?>"><span><i class="fa fa-file-pdf-o"></i></span></a>
                        <a href="" onclick="printEmp_report('printableArea')" class="btn btn-danger btn-xs"
                           data-toggle="tooltip" data-placement="top" title="<?= lang('print') ?>"><span><i
                                    class="fa fa-print"></i></span></a>
                    </div>
                </h4>
            </div>
            <div class="panel-group" id="accordion" style="margin:8px 5px" role="tablist" aria-multiselectable="true">
                <?php
                foreach ($attendace_info as $name => $v_attendace_info):
                    ?>
                    <div class="panel panel-default" style="border-radius: 0px ">
                        <div class="panel-heading" style="border-radius: 0px;border: none" role="tab">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $name ?>"
                                   aria-expanded="true" aria-controls="collapseOne">
                                    <?php echo $employee[$name]->fullname ?>
                                </a>
                            </h4>
                        </div>
                        <div id="<?php echo $name ?>" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <?php
                                if (!empty($v_attendace_info)): foreach ($v_attendace_info as $week => $v_attendace):
                                    $total_hour = 0;
                                    $total_minutes = 0;
                                    ?>
                                    <div class="panel-group" style="margin:8px 5px" id="accordion" role="tablist"
                                         aria-multiselectable="true">
                                        <div class="panel panel-default" style="border-radius: 0px ">
                                            <div class="panel-heading" style="border-radius: 0px;border: none"
                                                 role="tab"
                                                 id="headingOne">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#<?php echo $name ?>"
                                                       href="#<?php echo $name . $week; ?>" aria-expanded="true"
                                                       aria-controls="collapseOne">
                                                        <strong><?= lang('week') ?> : <?php echo $week; ?> </strong>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="<?php echo $name . $week; ?>" class="panel-collapse collapse "
                                                 role="tabpanel" aria-labelledby="headingOne">
                                                <div class="panel-body table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th><?= lang('clock_in_time') ?></th>
                                                            <th><?= lang('clock_out_time') ?></th>
                                                            <th><?= lang('ip_address') ?></th>
                                                            <th><?= lang('hours') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $total_hh = 0;
                                                        $total_mm = 0;
                                                        if (!empty($v_attendace)):
                                                            $hourly_leave = null;

                                                            foreach ($v_attendace as $date => $v_mytime):
                                                                foreach ($v_mytime as $hmytime) {
                                                                    if ($hmytime->attendance_status == 1) {
                                                                        if (!empty($hmytime->leave_application_id)) { // check leave type is hours
                                                                            $is_hours = get_row('tbl_leave_application', array('leave_application_id' => $hmytime->leave_application_id));
                                                                            if (!empty($is_hours) && $is_hours->leave_type == 'hours') {
                                                                                $hourly_leave = "<small class='label label-pink text-sm' data-toggle='tooltip' data-placement='top'  title='" . lang('hourly') . ' ' . lang('leave') . ": " . $is_hours->hours . ":00" . ' ' . lang('hour') . "'>" . lang('hourly') . ' ' . lang('leave') . "</small>";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <td colspan="4"
                                                                    style="background: rgba(233, 237, 228, 0.73);font-weight: bold"><?php echo $date; ?>
                                                                    <span class="pull-right"><?= $hourly_leave ?></span>
                                                                </td>

                                                                <?php
                                                                foreach ($v_mytime as $mytime) {
                                                                    if ($mytime->attendance_status == 1) {
                                                                        ?>
                                                                        <tr>
                                                                        <td><?php echo display_time($mytime->clockin_time); ?></td>
                                                                        <td><?php
                                                                            if (empty($mytime->clockout_time)) {
                                                                                echo '<span class="text-danger">' . lang('currently_clock_in') . '<span>';
                                                                            } else {
                                                                                if (!empty($mytime->comments)) {
                                                                                    $comments = ' <small> (' . $mytime->comments . ')</small>';
                                                                                } else {
                                                                                    $comments = '';
                                                                                }
                                                                                echo display_time($mytime->clockout_time) . $comments;
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td><?= $mytime->ip_address ?></td>
                                                                        <td><?php
                                                                            if (!empty($mytime->clockout_time)) {
                                                                                // calculate the start timestamp
                                                                                $startdatetime = strtotime($mytime->date_in . " " . $mytime->clockin_time);
                                                                                // calculate the end timestamp
                                                                                $enddatetime = strtotime($mytime->date_out . " " . $mytime->clockout_time);
                                                                                // calulate the difference in seconds
                                                                                $difference = $enddatetime - $startdatetime;

                                                                                $years = abs(floor($difference / 31536000));
                                                                                $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                                                                                $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                                                                                $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                                                                                $total_mm += $mins;
                                                                                $total_hh += $hours;
                                                                                echo $hours . " : " . $mins . " m";

                                                                                // output the result
                                                                            }
                                                                            ?></td>
                                                                    <?php } elseif ($mytime->attendance_status == 'H') { ?>
                                                                        <tr>
                                                                            <td colspan="4" style="text-align: center">
                                                                            <span
                                                                                style="padding:5px 109px; font-size: 12px;"
                                                                                class="label label-info std_p"><?= lang('holiday') ?></span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } elseif ($mytime->attendance_status == '3') { ?>
                                                                        <tr>
                                                                            <td colspan="4" style="text-align: center">
                                                                            <span
                                                                                style="padding:5px 109px; font-size: 12px;"
                                                                                class="label label-warning std_p"><?= lang('on_leave') ?></span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } elseif ($mytime->attendance_status == '0') { ?>
                                                                        <tr style="">
                                                                            <td colspan="4" style="text-align: center">
                                                                            <span
                                                                                style="padding:5px 109px; font-size: 12px;"
                                                                                class="label label-danger std_p"><?= lang('absent') ?></span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } else { ?>
                                                                        <tr>
                                                                            <td colspan="4" style="text-align: center">
                                                                            <span style=" font-size: 12px;"
                                                                                  class=" std_p"><?= lang('no_data_available') ?> </span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                    </tr>
                                                                <?php }; ?>
                                                                <?php
                                                                $hourly_leave = null;
                                                            endforeach; ?>
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
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <?php endif; ?>
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
