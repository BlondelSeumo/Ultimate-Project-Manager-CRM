<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><i class="fa fa-calendar"></i> <?php echo lang('provident_found') . ' ' . $year; ?>
            </strong>
            <div class="pull-right hidden-print">
                                <span
                                    class="hidden-print"><?php echo btn_pdf('admin/user/provident_fund_pdf/' . $year . '/' . $profile_info->user_id); ?></span>
            </div>
        </div>
    </div>
    <form data-parsley-validate="" novalidate="" role="form" enctype="multipart/form-data"
          action="<?php echo base_url(); ?>admin/user/user_details/<?= $profile_info->user_id ?>/8"
          method="post"
          class="form-horizontal form-groups-bordered">
        <div class="form-group">
            <label for="field-1" class="col-sm-3 control-label"><?= lang('year') ?><span
                    class="required"> *</span></label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" required name="year" class="form-control years" value="<?php
                    if (!empty($year)) {
                        echo $year;
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
            <th><?= lang('payment_month') ?></th>
            <th><?= lang('payment_date') ?></th>
            <th><?= lang('amount') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_amount = 0;
        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
        ?>
        <?php if (!empty($provident_fund_info)) {
            foreach ($provident_fund_info as $key => $v_provident_fund) {
                $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query

                $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                if (!empty($v_provident_fund)) {
                    foreach ($v_provident_fund as $provident_fund) { ?>
                        <tr>
                            <td><?php echo $month_name ?></td>
                            <td><?= strftime(config_item('date_format'), strtotime($provident_fund->paid_date)) ?></td>
                            <td><?php echo display_money($provident_fund->salary_payment_deduction_value, $curency->symbol);
                                $total_amount += $provident_fund->salary_payment_deduction_value;
                                ?></td>

                        </tr>
                        <?php
                        $key++;
                    };
                    $total_amount = $total_amount;
                };

            }
        }; ?>
        <tr class="total_amount">
            <td colspan="2" style="text-align: right;">
                <strong><?= lang('total') . ' ' . lang('provident_fund') ?>
                    : </strong></td>
            <td colspan="3" style="padding-left: 8px;"><strong><?php
                    echo display_money($total_amount, $curency->symbol);
                    ?></strong></td>
        </tr>

        </tbody>
    </table>
</div>