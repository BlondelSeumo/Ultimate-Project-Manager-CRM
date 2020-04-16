<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><i class="fa fa-calendar"></i> <?php echo lang('Overtime_details') . ' ' . $year; ?>
            </strong>
            <div class="pull-right hidden-print">
                                <span
                                    class="hidden-print"><?php echo btn_pdf('admin/user/overtime_report_pdf/' . $year . '/' . $profile_info->user_id); ?></span>
            </div>
        </div>

    </div>
    <form id="attendance-form" role="form" enctype="multipart/form-data"
          action="<?php echo base_url(); ?>admin/user/user_details/<?= $profile_info->user_id ?>/9"
          method="post"
          class="form-horizontal form-groups-bordered">
        <div class="form-group">
            <label for="field-1" class="col-sm-3 control-label"><?= lang('year') ?><span
                    class="required"> *</span></label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" name="overtime_year" class="form-control years" value="<?php
                    if (!empty($overtime_year)) {
                        echo $overtime_year;
                    }
                    ?>" data-format="yyyy">
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
    <!-- Table -->
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th><?= lang('overtime_date') ?></th>
            <th><?= lang('overtime_hour') ?></th>
            <th><?= lang('status') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $key = 1;
        $hh = 0;
        $mm = 0;
        if (!empty($all_overtime_info)) {
            foreach ($all_overtime_info as $key => $v_overtime_info) {
                if (!empty($v_overtime_info)) {
                    foreach ($v_overtime_info as $v_overtime) {
                        if ($v_overtime->status == 'pending') {
                            $status = '<strong class="label label-warning">' . lang($v_overtime->status) . '</strong>';
                        } elseif ($v_overtime->status == 'approved') {
                            $status = '<strong class="label label-success">' . lang($v_overtime->status) . '</strong>';
                        } else {
                            $status = '<strong class="label label-danger">' . lang($v_overtime->status) . '</strong>';
                        }
                        ?>
                        <tr>
                            <td><?= strftime(config_item('date_format'), strtotime($v_overtime->overtime_date)) ?></td>
                            <td><?php echo $v_overtime->overtime_hours; ?></td>
                            <td><?= $status ?>

                                <?php
                                if ($this->session->userdata('user_type') == 1) {
                                    if ($v_overtime->status == 'pending') { ?>
                                        <a data-toggle="tooltip" data-placment="top"
                                           title="<?= lang('approved') ?>"
                                           href="<?= base_url() ?>admin/utilities/change_overtime_status/approved/<?= $v_overtime->overtime_id; ?>"
                                           class="btn btn-xs btn-success ml"><i
                                                class="fa fa-check"></i> </a>
                                        <a data-toggle="tooltip" data-placment="top"
                                           title="<?= lang('reject') ?>"
                                           href="<?= base_url() ?>admin/utilities/change_overtime_status/rejected/<?= $v_overtime->overtime_id; ?>"
                                           class="btn btn-xs btn-danger ml"><i
                                                class="fa fa-times"></i></a>
                                    <?php } elseif ($v_overtime->status == 'rejected') { ?>
                                        <a data-toggle="tooltip" data-placment="top"
                                           title="<?= lang('approved') ?>"
                                           href="<?= base_url() ?>admin/utilities/change_overtime_status/approved/<?= $v_overtime->overtime_id; ?>"
                                           class="btn btn-xs btn-success ml"><i
                                                class="fa fa-check"></i> </a>
                                    <?php } elseif ($v_overtime->status == 'approved') { ?>
                                        <a data-toggle="tooltip" data-placment="top"
                                           title="<?= lang('reject') ?>"
                                           href="<?= base_url() ?>admin/utilities/change_overtime_status/rejected/<?= $v_overtime->overtime_id; ?>"
                                           class="btn btn-xs btn-danger ml"><i
                                                class="fa fa-times"></i></a>
                                    <?php }
                                }
                                ?>
                            </td>
                            <?php $hh += $v_overtime->overtime_hours; ?>
                            <?php $mm += date('i', strtotime($v_overtime->overtime_hours)); ?>

                        </tr>
                        <?php
                        $key++;
                    };
                };
            };
        };
        ?>
        <tr class="total_amount">
            <td colspan="1" style="text-align: right;">
                <strong><?= lang('total_overtime_hour') ?> : </strong></td>
            <td colspan="2" style="padding-left: 8px;"><strong><?php
                    if ($hh > 1 && $hh < 10 || $mm > 1 && $mm < 10) {
                        $total_mm = '0' . $mm;
                        $total_hh = '0' . $hh;
                    } else {
                        $total_mm = $mm;
                        $total_hh = $hh;
                    }
                    if ($total_mm > 59) {
                        $total_hh += intval($total_mm / 60);
                        $total_mm = intval($total_mm % 60);
                    }
                    echo $total_hh . " : " . $total_mm . " m";

                    ?></strong></td>
        </tr>
        </tbody>
    </table>
</div>