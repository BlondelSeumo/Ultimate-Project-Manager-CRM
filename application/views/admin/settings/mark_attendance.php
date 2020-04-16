<script type="text/javascript">
    $(document).ready(function () {
        $('#select_all').change(function () {
            var c = this.checked;
            $(':checkbox').prop('checked', c);
        });
        // select select_all_view
        $(".clock_in").change(function () {
            $('.clock_in').prop("checked", this.checked);
        });
        // select select_all_view
        $(".clock_out").change(function () {
            $('.clock_out').prop("checked", this.checked);
        });
    });
</script>
<form action="<?php echo base_url() ?>admin/dashboard/update_clock"
      method="post" class="form-horizontal form-groups-bordered">
    <div class="row">
        <div class="col-sm-12" data-spy="scroll" data-offset="0">
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <div class="panel-title"><strong><?= lang('mark_attendance'); ?></strong>
                        <div class="pull-right" style="font-size: 14px">
                            <div class="checkbox c-checkbox pull-left mt0 mr-lg hidden-xs" data-toggle="tooltip"
                                 data-placement="top"
                                 data-original-title="<?= lang('mark_as_clock_in_help') ?>">
                                <label class="needsclick clock_in">
                                    <input id="select_all_clock_in" class="clock_in" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <?= lang('mark_as_clock_in') ?>
                                </label>
                            </div>
                            <div class="checkbox c-checkbox pull-left mt0 mr-lg hidden-xs" data-toggle="tooltip"
                                 data-placement="top"
                                 data-original-title="<?= lang('mark_as_clock_out_help') ?>">
                                <label class="needsclick">
                                    <input id="select_all_clock_out" class="clock_out" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <?= lang('mark_as_clock_out') ?>
                                </label>
                            </div>
                            <div class="checkbox c-checkbox pull-left mt0 mr-lg hidden-xs" data-toggle="tooltip"
                                 data-placement="top"
                                 data-original-title="<?= lang('mark_all_help') ?>">
                                <label class="needsclick">
                                    <input id="select_all" type="checkbox">
                                    <span class="fa fa-check"></span>
                                    <?= lang('mark_all') ?>
                                </label>
                            </div>
                            <button type="submit"
                                    class="btn btn-primary"><?= lang('update') ?></button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped" id="Transation_DataTables">
                            <thead>
                            <tr>
                                <th></th>
                                <th><?= lang('emp_id') ?></th>
                                <th><?= lang('name') ?></th>
                                <th><?= lang('clocking_hours') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_hour = 0;
                            $total_minutes = 0;

                            if ((!empty($date)) && !empty($attendace_info)) { ?>
                                <?php
                                if (!empty($attendace_info)) {
                                    foreach ($attendace_info as $nkey => $v_attendace_info) {

                                        $already_clock = null;
                                        $currently_clock_in = null;
                                        $clock_id = null;
                                        $holiday = null;
                                        $name = 'clock_in';
                                        $total_hh = 0;
                                        $total_mm = 0;

                                        if (!empty($v_attendace_info)) {
                                            foreach ($v_attendace_info as $v_mytime) {
                                                if ($v_mytime->attendance_status == 1) {
                                                    if (!empty($v_mytime->clockout_time)) {
                                                        // calculate the start timestamp
                                                        $startdatetime = strtotime($v_mytime->date_in . " " . $v_mytime->clockin_time);
                                                        // calculate the end timestamp
                                                        $enddatetime = strtotime($v_mytime->date_out . " " . $v_mytime->clockout_time);
                                                        // calulate the difference in seconds
                                                        $difference = $enddatetime - $startdatetime;

                                                        $years = abs(floor($difference / 31536000));
                                                        $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                                                        $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                                                        $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                                                        $total_mm += $mins;
                                                        $total_hh += $hours;
                                                        $already_clock = true;
                                                        // output the result
                                                    } else {
                                                        $currently_clock_in = '<span style="padding:5px 75px; font-size: 12px;" class="label label-purple std_p">' . lang('currently_clock_in') . '</span>';
                                                        $clock_id = $v_mytime->clock_id;
                                                        $name = 'clock_out';

                                                    }
                                                    ?>
                                                    <?php
                                                } elseif ($v_mytime->attendance_status == 'H') {
                                                    $holiday = '<span style="padding:5px 109px; font-size: 12px;" class="label label-info std_p">' . lang('holiday') . '</span>';
                                                } elseif ($v_mytime->attendance_status == '3') {
                                                    $holiday = '<span style="padding:5px 109px; font-size: 12px;" class="label label-warning std_p">' . lang('on_leave') . '</span>';
                                                } elseif ($v_mytime->attendance_status == '0') {
                                                    $holiday = '<span style="padding:5px 109px; font-size: 12px;" class="label label-danger std_p">' . lang('absent') . '</span>';
                                                }
                                            }

                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="checkbox c-checkbox">
                                                    <label class="needsclick">
                                                        <input type="checkbox" class="<?= $name ?>"
                                                               name="<?= $name; ?>[]"
                                                               value="<?= $users[$nkey]->user_id; ?>">
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                                <input type="hidden" name="<?= $users[$nkey]->user_id; ?>"
                                                       value="<?= $clock_id; ?>">

                                            </td>
                                            <td><?= $users[$nkey]->employment_id; ?></td>
                                            <td><?= $users[$nkey]->fullname; ?></td>
                                            <td>
                                                <?php
                                                if (!empty($already_clock)) {
                                                    if ($total_mm > 59) {
                                                        $total_hh += intval($total_mm / 60);
                                                        $total_mm = intval($total_mm % 60);
                                                    }
                                                    echo $total_hh . " : " . $total_mm . " m" . '<br/>';
                                                }
                                                if (!empty($currently_clock_in)) {
                                                    echo $currently_clock_in . ' ' . '<a style="padding:5px 10px; font-size: 12px;" class="label label-danger clock_in_button" href="' . base_url() . 'admin/dashboard/set_clocking/' . $clock_id . '/' . $users[$nkey]->user_id . '"' . '>' . lang('clock_out') . '</a>';
                                                } else {
                                                    echo $holiday . ' ' . '<a style="padding:5px 10px; font-size: 12px;" class="label label-success clock_in_button" href="' . base_url() . 'admin/dashboard/set_clocking/0/' . $users[$nkey]->user_id . '/1"' . '>' . lang('clock_in') . '</a>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $(".clock_in_button").click(function () {
            var ubtn = $(this);
            ubtn.html('<?= lang('please_wait')?>' + '...');
            ubtn.addClass('disabled');
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#Transation_DataTables').dataTable({
            paging: false,
            "bSort": false
        });
    });
</script>
<!-- end -->