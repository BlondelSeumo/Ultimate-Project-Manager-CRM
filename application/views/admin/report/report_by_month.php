<?= message_box('success'); ?>
<?= message_box('error'); ?>
<!-- ************ Expense Report List start ************-->
<div class="row">
    <div class="col-sm-3">
        <form action="<?php echo base_url() ?>admin/report/report_by_month" method="post">
            <label for="field-1" class="control-label pull-left holiday-vertical"><strong><?= lang('year') ?>:</strong></label>
            <div class="col-sm-8">
                <input type="text" name="year" class="form-control years" value="<?php
                if (!empty($year)) {
                    echo $year;
                }
                ?>" data-format="yyyy">
            </div>
            <button type="submit" id="search_product" data-toggle="tooltip" data-placement="top" title="Search"
                    class="btn btn-custom pull-right">
                <i class="fa fa-search"></i></button>
        </form>
    </div>
</div>
<br/>
<div id="expense_report">
    <div class="show_print" style="width: 100%; border-bottom: 2px solid black;">
        <table style="width: 100%; vertical-align: middle;">
            <tr>

                <td style="width: 50px;">
                    <img style="width: 50px;height: 50px" src="<?= base_url() . config_item('company_logo') ?>" alt=""
                         class="img-circle"/>
                </td>
                <td>
                    <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <div class="col-md-3 hidden-print"><!-- ************ Expense Report Month Start ************-->
            <ul class="nav nav-pills nav-stacked navbar-custom-nav">
                <?php
                foreach ($report_by_month as $key => $v_month):
                    $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                    ?>
                    <li class="<?php
                    if ($current_month == $key) {
                        echo 'active';
                    }
                    ?>">
                        <a aria-expanded="<?php
                        if ($current_month == $key) {
                            echo 'true';
                        } else {
                            echo 'false';
                        }
                        ?>" data-toggle="tab" href="#<?php echo $month_name ?>">
                            <i class="fa fa-fw fa-calendar"></i> <?php echo $month_name; ?> </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div><!-- ************ Expense Report Month End ************-->
        <div class="col-md-9"><!-- ************ Expense Report Content Start ************-->
            <div class="tab-content">
                <?php
                foreach ($report_by_month as $key => $v_monthly_report):
                    $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                    ?>
                    <div id="<?php echo $month_name ?>" class="tab-pane <?php
                    if ($current_month == $key) {
                        echo 'active';
                    }
                    ?>">
                        <div class="wrap-fpanel">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <strong><i class="fa fa-calendar"></i> <?php echo $month_name . ' ' . $year; ?>
                                        </strong>
                                        <div class="pull-right hidden-print hidden-xs">
                                            <span><?php echo btn_pdf('admin/report/report_by_month_pdf/' . $year . '/' . $key); ?></span>
                                            <button class="btn btn-danger btn-xs"
                                                    type="button" data-toggle="tooltip" title="Print"
                                                    onclick="expense_report('expense_report')"><i
                                                    class="fa fa-print"></i></button>
                                        </div>
                                    </div>

                                </div>
                                <!-- Table -->
                                <div class="panel-body table-responsive">
                                    <table class="table table-hover">
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
                                        <?php
                                        $total_amount = 0;
                                        $total_debit = 0;
                                        $total_credit = 0;
                                        $total_balance = 0;
                                        $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                        if (!empty($v_monthly_report)): foreach ($v_monthly_report as $v_month) :
                                            $account_info = $this->report_model->check_by(array('account_id' => $v_month->account_id), 'tbl_accounts');
                                            ?>
                                            <tr class="custom-tr custom-font-print">
                                                <td><?= strftime(config_item('date_format'), strtotime($v_month->date)); ?></td>
                                                <td class="vertical-td"><?= $account_info->account_name ?></td>
                                                <td class="vertical-td"><?= lang($v_month->type) ?> </td>
                                                <td class="vertical-td"><?= $v_month->notes ?></td>
                                                <td><?= display_money($v_month->amount, $curency->symbol) ?></td>
                                                <td><?= display_money($v_month->credit, $curency->symbol) ?></td>
                                                <td><?= display_money($v_month->debit, $curency->symbol) ?></td>
                                                <td><?= display_money($v_month->total_balance, $curency->symbol) ?></td>
                                            </tr>
                                            <?php
                                            $total_amount += $v_month->amount;
                                            $total_debit += $v_month->debit;
                                            $total_credit += $v_month->credit;
                                            $total_balance += $v_month->total_balance;
                                            ?>
                                        <?php endforeach; ?>
                                            <tr class="custom-color-with-td">
                                                <td style="text-align: right;" colspan="4"><strong><?= lang('total') ?>
                                                        :</strong></td>
                                                <td>
                                                    <strong><?= display_money($total_amount, $curency->symbol) ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?= display_money($total_credit, $curency->symbol) ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?= display_money($total_debit, $curency->symbol) ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?= display_money($total_credit - $total_debit, $curency->symbol) ?></strong>
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
            </div>
        </div><!-- ************ Expense Report Content Start ************-->
    </div><!-- ************ Expense Report List End ************-->
</div>
<script type="text/javascript">
    function expense_report(expense_report) {
        var printContents = document.getElementById(expense_report).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
