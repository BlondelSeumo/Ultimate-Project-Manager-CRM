<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
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
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('transactions_report') ?></strong>


                <?php
                $all_transaction_info = $this->db->get('tbl_transactions')->result();
                if (!empty($all_transaction_info)):
                    ?>
                    <div class="pull-right hidden-print">
                        <div class="btn-group filtered">
                            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <?= lang('search_by') ?><span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-left group animated zoomIn"
                                style="width:300px;">
                                <li class="filter_by"><a href="#"><?php echo lang('all'); ?></a></li>
                                <li class="divider"></li>
                                <?php
                                $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                                if (!empty($account_info)) {
                                    foreach ($account_info as $v_account) {
                                        ?>
                                        <li class="filter_by" id="<?= $v_account->account_id ?>" >
                                            <a href="#"><?php echo $v_account->account_name; ?></a>
                                        </li>
                                    <?php }
                                    ?>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </ul>
                        </div>

                        <a href="<?php echo base_url() ?>admin/transactions/transactions_report_pdf/"
                           class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top"
                           title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                        <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger hidden-xs"
                           data-toggle="tooltip" data-placement="top"
                           title="<?= lang('print') ?>"><?= lang('print') ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th style="width: 15%"><?= lang('date') ?></th>
                        <th style="width: 15%"><?= lang('account') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('notes') ?></th>
                        <th><?= lang('amount') ?></th>
                        <th><?= lang('credit') ?></th>
                        <th><?= lang('debit') ?></th>
                        <th><?= lang('balance') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/transactions/transactions_reportList";
                            $('.filtered > .dropdown-toggle').on('click', function () {
                                if ($('.group').css('display') == 'block') {
                                    $('.group').css('display', 'none');
                                } else {
                                    $('.group').css('display', 'block')
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
                                table_url(base_url + "admin/transactions/transactions_reportList/" + filter_by);
                            });
                        });
                    </script>

                    <?php
                    $total_amount = 0;
                    $total_debit = 0;
                    $total_credit = 0;
                    $total_balance = 0;
                    $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    if (!empty($all_transaction_info)): foreach ($all_transaction_info as $v_transaction) :
                        $total_amount += $v_transaction->amount;
                        $total_debit += $v_transaction->debit;
                        $total_credit += $v_transaction->credit;
                        $total_balance += $v_transaction->total_balance;
                        ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            <strong style="width: 25%"><?= lang('total_amount') ?>:<span
                    class="label label-success"><?= display_money($total_amount, $curency->symbol) ?></span></span>
            </strong>
            <strong class="col-sm-3"><?= lang('credit') ?>:<span
                    class="label label-primary"><?= display_money($total_credit, $curency->symbol) ?></span></span>
            </strong>
            <strong class="col-sm-3"><?= lang('debit') ?>:<span
                    class="label label-danger"><?= display_money($total_debit, $curency->symbol) ?></span></span>
            </strong>
            <strong class="col-sm-3"><?= lang('balance') ?>:<span
                    class="label label-info"><?= display_money($total_credit - $total_debit, $curency->symbol) ?></span></span>
            </strong>
        </div>
    </div>
</div>
<div class="panel panel-custom ">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('transactions_report') . ' ' . lang('graph') . ' ' . date('F-Y') ?></strong>
        </div>
    </div>
    <div class="panel-body">
        <div id="morris-line"></div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

        if (typeof Morris === 'undefined') return;

        var chartdata = [
            <?php foreach ($transactions_report as $days => $v_report){
            $total_expense = 0;
            $total_income = 0;
            $total_transfer = 0;
            foreach ($v_report as $Expense) {
                if ($Expense->type == 'Expense') {
                    $total_expense += $Expense->amount;
                }
                if ($Expense->type == 'Income') {
                    $total_income += $Expense->amount;
                }
                if ($Expense->type == 'Transfer') {
                    $total_transfer += $Expense->amount / 2;
                }
            }
            ?>
            {
                y: "<?= $days ?>",
                income: <?= $total_income?>,
                expense: <?= $total_expense?>,
                transfer: <?= $total_transfer?>},
            <?php }?>


        ];
        // Line Chart
        // -----------------------------------

        new Morris.Line({
            element: 'morris-line',
            data: chartdata,
            xkey: 'y',
            ykeys: ["income", "expense", "transfer"],
            labels: ["<?= lang('Income')?>", "<?= lang('expense')?>", "<?= lang('transfer')?>"],
            lineColors: ["#27c24c", "#f05050", "#5d9cec"],
            parseTime: false,
            resize: true
        });

    });
    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>