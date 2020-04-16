<div class="panel panel-custom">
    <!-- Default panel contents -->

    <div class="panel-heading">
        <div class="panel-title">
            <a class="pull-right" href="<?= base_url() ?>admin/utilities/overtime"> <span aria-hidden="true">&times;</span><span
                    class="sr-only"><?= lang('close') ?></span></a>
            <strong><?= $title ?></strong>
        </div>
    </div>
    <?php
    $profile = $this->db->where('user_id', $overtime_info->user_id)->get('tbl_account_details')->row();
    ?>
    <div class="panel-body form-horizontal">
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('name') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $profile->fullname ?></p>
            </div>
        </div>
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('date') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($overtime_info->overtime_date)) ?></p>
            </div>
        </div>
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('overtime_hour') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $overtime_info->overtime_hours; ?></p>
            </div>
        </div>
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('status') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php
                    if ($overtime_info->status == 'pending') {
                        $status = '<strong class="label label-warning">' . lang($overtime_info->status) . '</strong>';
                    } elseif ($overtime_info->status == 'approved') {
                        $status = '<strong class="label label-success">' . lang($overtime_info->status) . '</strong>';
                    } else {
                        $status = '<strong class="label label-danger">' . lang($overtime_info->status) . '</strong>';
                    }
                    echo $status;
                    ?>
                    <?php
                    if ($this->session->userdata('user_type') == 1) {
                        if ($overtime_info->status == 'pending') { ?>
                            <a data-toggle="tooltip" data-placment="top"
                               title="<?= lang('approved') ?>"
                               href="<?= base_url() ?>admin/utilities/change_overtime_status/approved/<?= $overtime_info->overtime_id; ?>"
                               class="btn btn-xs btn-success ml"><i
                                    class="fa fa-check"></i> </a>
                            <a data-toggle="tooltip" data-placment="top"
                               title="<?= lang('reject') ?>"
                               href="<?= base_url() ?>admin/utilities/change_overtime_status/rejected/<?= $overtime_info->overtime_id; ?>"
                               class="btn btn-xs btn-danger ml"><i
                                    class="fa fa-times"></i></a>
                        <?php } elseif ($overtime_info->status == 'rejected') { ?>
                            <a data-toggle="tooltip" data-placment="top"
                               title="<?= lang('approved') ?>"
                               href="<?= base_url() ?>admin/utilities/change_overtime_status/approved/<?= $overtime_info->overtime_id; ?>"
                               class="btn btn-xs btn-success ml"><i
                                    class="fa fa-check"></i> </a>
                        <?php } elseif ($overtime_info->status == 'approved') { ?>
                            <a data-toggle="tooltip" data-placment="top"
                               title="<?= lang('reject') ?>"
                               href="<?= base_url() ?>admin/utilities/change_overtime_status/rejected/<?= $overtime_info->overtime_id; ?>"
                               class="btn btn-xs btn-danger ml"><i
                                    class="fa fa-times"></i></a>
                        <?php }
                    }
                    ?>
                </p>
            </div>
        </div>

        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('notes') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <blockquote
                    style="font-size: 12px; margin-top: 5px"><?= nl2br($overtime_info->notes) ?></blockquote>
            </div>
        </div>
    </div>
</div>





