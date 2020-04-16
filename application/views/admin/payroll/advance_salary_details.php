<div class="panel panel-custom">
    <!-- Default panel contents -->

    <div class="panel-heading">
        <div class="panel-title">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <strong><?= lang('advance_salary_details') ?></strong>
        </div>
    </div>
    <div class="panel-body form-horizontal">
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('name') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $advance_salary_info->fullname ?></p>
            </div>
        </div>
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('advance_amount') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php
                    $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                    echo display_money($advance_salary_info->advance_amount, $curency->symbol)
                    ?></p>
            </div>
        </div>
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('deduct_month') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo date('Y M', strtotime($advance_salary_info->deduct_month)); ?></p>
            </div>
        </div>

        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('apply_on') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><span
                        class="text-danger"><?= strftime(config_item('date_format'), strtotime($advance_salary_info->request_date)) ?></span>
                </p>
            </div>
        </div>
        <?php if (!empty($advance_salary_info->approve_by) || $advance_salary_info->approve_by != 0) { ?>
            <div class="">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('action_by') ?> : </strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><span
                            ><?= $this->db->where('user_id', $advance_salary_info->approve_by)->get('tbl_account_details')->row()->fullname ?></span>
                    </p>
                </div>
            </div>
        <?php } ?>
        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('current_status') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php
                    if ($advance_salary_info->status == '0') {
                        echo '<span class="label label-warning">' . lang('pending') . '</span>';
                    } elseif ($advance_salary_info->status == '1') {
                        echo '<span class="label label-success"> ' . lang('accepted') . '</span>';
                    } elseif ($advance_salary_info->status == '2') {
                        echo '<span class="label label-danger">' . lang('rejected') . '</span>';
                    } else {
                        echo '<span class="label label-info">' . lang('paid') . '</span>';
                    }
                    ?>
                </p>
            </div>
        </div>

        <div class="">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('reason') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <blockquote
                    style="font-size: 12px; margin-top: 5px"><?= nl2br($advance_salary_info->reason) ?></blockquote>
            </div>
        </div>
        <?php if (!empty($advance_salary_info->comments)) { ?>
            <div class="">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('comments') ?> : </strong></label>
                </div>
                <div class="col-sm-8">
                    <blockquote
                        style="font-size: 12px; margin-top: 5px"><?= nl2br($advance_salary_info->comments) ?></blockquote>
                </div>
            </div>
        <?php } ?>
        <?php if ($advance_salary_info->status == '0' || $advance_salary_info->status == '2') { ?>

            <div class=" ">
                <label
                    class="control-label col-sm-4"><strong><?= lang('change') . ' ' . lang('status') ?>
                        :</strong></label>
                <div class="col-sm-8">
                    <p class="form-control-static ">
                        <?php
                        if ($advance_salary_info->status == '0') { ?>
                            <span data-toggle="tooltip" data-placment="top"
                                  title="<?= lang('approved_alert') ?>">
                                                    <a
                                                        href="<?= base_url() ?>admin/payroll/set_salary_status/1/<?= $advance_salary_info->advance_salary_id; ?>"
                                                        class="btn btn-success mr"><i
                                                            class="fa fa-thumbs-o-up"></i> <?= lang('approved') ?></a>
                                                        </span>
                            <a
                                href="<?= base_url() ?>admin/payroll/set_salary_status/2/<?= $advance_salary_info->advance_salary_id; ?>"
                                class="btn btn-danger mr"><i
                                    class="fa fa-times"></i> <?= lang('reject') ?></a>
                            <span data-toggle="tooltip" data-placment="top"
                                  title="<?= lang('paid_alert') ?>">
                            <a
                                href="<?= base_url() ?>admin/payroll/set_salary_status/3/<?= $advance_salary_info->advance_salary_id; ?>"
                                class="btn btn-info mr"><i
                                    class="fa fa-check"></i> <?= lang('paid') ?></a>
                            </span>

                        <?php } elseif ($advance_salary_info->status == '2') { ?>
                            <span data-toggle="tooltip" data-placment="top"
                                  title="<?= lang('approved_alert') ?>">
                                                    <a
                                                        href="<?= base_url() ?>admin/payroll/set_salary_status/1/<?= $advance_salary_info->advance_salary_id; ?>"
                                                        class="btn btn-success mr"><i
                                                            class="fa fa-thumbs-o-up"></i> <?= lang('approved') ?></a>
                                                        </span>
                            <a
                                href="<?= base_url() ?>admin/payroll/set_salary_status/0/<?= $advance_salary_info->advance_salary_id; ?>"
                                class="btn btn-warning mr"><i
                                    class="fa fa-times"></i> <?= lang('pending') ?></a>
                            <span data-toggle="tooltip" data-placment="top"
                                  title="<?= lang('paid_alert') ?>">
                            <a
                                href="<?= base_url() ?>admin/payroll/set_salary_status/3/<?= $advance_salary_info->advance_salary_id; ?>"
                                class="btn btn-info mr"><i
                                    class="fa fa-check"></i> <?= lang('paid') ?></a>
                            </span>
                        <?php } ?>

                    </p>
                </div>
            </div>
        <?php }
        ?>
    </div>
</div>





