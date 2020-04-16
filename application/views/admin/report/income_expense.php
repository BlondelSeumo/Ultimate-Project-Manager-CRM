
<div id="printReport">
    <div class="show_print">
        <div style="width: 100%; border-bottom: 2px solid black;">
            <table style="width: 100%; vertical-align: middle;">
                <tr>
                    <td style="width: 50px; border: 0px;">
                        <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                             src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                    </td>

                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                    </td>

                </tr>
            </table>
        </div>
        <br/>
    </div>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('income_expense_report') ?></strong>
                <div class="pull-right hidden-print">
                    <a href="<?php echo base_url() ?>admin/report/income_expense_pdf/" class="btn btn-xs btn-success"
                       data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                    <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger hidden-xs" data-toggle="tooltip"
                       data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
                </div>
            </div>
        </div>
        <div class="panel-body">


            <h5><strong><?= lang('income_expense') ?></strong></h5>
            <?php
            $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            $mdate = date('Y-m-d');
            //first day of month
            $first_day_month = date('Y-m-01');
            //first day of Weeks
            $this_week_start = date('Y-m-d', strtotime('previous sunday'));
            // 30 days before
            $before_30_days = date('Y-m-d', strtotime('today - 30 days'));

            $total_income = $this->db->select_sum('credit')->get('tbl_transactions')->row();
            $total_expense = $this->db->select_sum('debit')->get('tbl_transactions')->row();

            $income_this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
            $income_this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
            $income_this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();

            $expense_this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
            $expense_this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
            $expense_this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();

            $this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->get('tbl_transactions')->result();
            $last_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->get('tbl_transactions')->result();
            ?>
            <strong>

                <hr>
                <p><?= lang('total_income') ?>
                    : <?= display_money($total_income->credit, $curency->symbol) ?></p>
                <p><?= lang('total_expense') ?>
                    : <?= display_money($total_expense->debit, $curency->symbol) ?></p>

                <hr>
                <p><strong><?= lang('Income') ?>
                        - <?= lang('Expense') ?> </strong>: <?= display_money($total_income->credit - $total_expense->debit, $curency->symbol); ?>
                </p>

                <hr>
                <p><?= lang('total_income_this_month') ?>
                    : <?= display_money($income_this_month->credit, $curency->symbol) ?>
                <div class="hidden-print" data-sparkline="" data-bar-color="#27c24c" data-height="30"
                     data-bar-width="5" data-bar-spacing="2"
                     data-values="
                     <?php foreach ($transactions_report as $days => $v_report) {
                         foreach ($v_report as $Expense) {
                             if ($Expense->credit != '0.00') {
                                 echo $Expense->amount . ',';
                                 ?><?php }
                         }

                     } ?>"></div>
                </p>
                <p><?= lang('total_expense_this_month') ?>
                    : <?= display_money($expense_this_month->debit, $curency->symbol) ?>
                <div class=" hidden-print
                " data-sparkline="" data-bar-color="#f05050" data-height="30"
                     data-bar-width="5" data-bar-spacing="2"
                     data-values="
                     <?php foreach ($transactions_report as $days => $v_report) {
                         foreach ($v_report as $Expense) {
                             if ($Expense->debit != '0.00') {
                                 echo $Expense->amount . ',';
                                 ?><?php }
                         }

                     } ?>"></div>
                </p>
                <p>
                    <strong><?= lang('total') ?></strong>:
                    <?= display_money($income_this_month->credit - $expense_this_month->debit, $curency->symbol) ?>
                </p>
                <hr>
                <p><?= lang('total_income_this_week') ?>
                    : <?= display_money($income_this_week->credit, $curency->symbol) ?>

                <div class="hidden-print" data-sparkline="" data-bar-color="#27c24c" data-height="30"
                     data-bar-width="5" data-bar-spacing="2"
                     data-values="
                     <?php foreach ($this_week as $v_weeks) {
                         if ($v_weeks->credit != '0.00') {
                             echo $v_weeks->amount . ',';
                             ?><?php }

                     } ?>"></div>
                </p>
                <p><?= lang('total_expense_this_week') ?>
                    : <?= display_money($expense_this_week->debit, $curency->symbol) ?>
                <div class=" hidden-print
                " data-sparkline="" data-bar-color="#f05050" data-height="30"
                     data-bar-width="5" data-bar-spacing="2"
                     data-values="
                     <?php foreach ($this_week as $v_weeks) {
                         if ($v_weeks->debit != '0.00') {
                             echo $v_weeks->amount . ',';
                             ?><?php }

                     } ?>"></div>
                </p>
                <p>
                    <strong><?= lang('total') ?></strong>:
                    <?= display_money($income_this_week->credit - $expense_this_week->debit, $curency->symbol) ?>
                </p>
                <hr>
                <p><?= lang('total_income_last_30') ?>
                    : <?= display_money($income_this_30_days->credit, $curency->symbol) ?>
                <div class="hidden-print" data-sparkline="" data-bar-color="#27c24c" data-height="30"
                     data-bar-width="5" data-bar-spacing="2"
                     data-values="
                         <?php foreach ($last_30_days as $v_30Days) {
                         if ($v_30Days->credit != '0.00') {
                             echo $v_30Days->amount . ',';
                             ?><?php }
                     } ?>"></div>
                </p>
                <p><?= lang('total_expense_last_30') ?>
                    : <?= display_money($expense_this_30_days->debit, $curency->symbol) ?>
                <div class=" hidden-print
                " data-sparkline="" data-bar-color="#f05050" data-height="30"
                     data-bar-width="5" data-bar-spacing="2"
                     data-values="
                     <?php foreach ($last_30_days as $v_30Days) {
                         if ($v_30Days->debit != '0.00') {
                             echo $v_30Days->amount . ',';
                             ?><?php }
                     } ?>"></div>
                </p>
                <p>
                    <strong><?= lang('total') ?></strong>:
                    <?= display_money($income_this_30_days->credit - $expense_this_30_days->debit, $curency->symbol) ?>
                </p>

                <hr>
            </strong>
        </div>
    </div>
</div>

<script type="text/javascript">
    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
