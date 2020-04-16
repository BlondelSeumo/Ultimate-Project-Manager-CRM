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
                <strong><?= lang('balance_sheet') ?></strong>
                <?php
                $all_transaction_info = $this->db->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                if (!empty($all_transaction_info)):
                    ?>
                    <div class="pull-right hidden-print">
                        <a href="<?php echo base_url() ?>admin/transactions/balance_sheet_pdf/"
                           class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top"
                           title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                        <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger"
                           data-toggle="tooltip"
                           data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped DataTables " cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th><?= lang('account') ?></th>
                    <th><?= lang('balance') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                $total_amount = 0;
                $all_account = $this->db->get('tbl_accounts')->result();
                foreach ($all_account as $v_account):
                    ?>
                    <tr>
                        <td class="vertical-td"><?php
                            if (!empty($v_account->account_name)) {
                                echo $v_account->account_name;
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?= display_money($v_account->balance, $curency->symbol) ?></td>
                    </tr>
                    <?php
                    $total_amount += $v_account->balance;
                endforeach;
                ?>
                <tr class="custom-color-with-td">
                    <th style="text-align: right;" colspan="1"><strong><?= lang('total') ?>:</strong></th>
                    <td><strong><?= display_money($total_amount, $curency->symbol) ?></strong></td>
                <tr>
                </tbody>
            </table>
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
