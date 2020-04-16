<!-- Include Required Prerequisites -->
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

<?php
$cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if (!empty($range[0])) {
    $start_date = date('F d, Y', strtotime($range[0]));
    $end_date = date('F d, Y', strtotime($range[1]));
}
$status = (isset($status)) ? $status : 'all';
?>


<div class="">
    <div class="hidden-print">
        <div class="criteria-band">
            <address class="row">
                <?php echo form_open(base_url() . 'admin/report/sales_report/' . $filterBy);
                ?>
                <div class="col-md-3">
                    <label><?= lang('select_client') ?></label>
                    <select class="form-control" name="client_id">
                        <option value="all" ><?= lang('all')?></option>
                        <?php
                        $all_client = get_result('tbl_client');
                        if (!empty($all_client)) {
                            foreach ($all_client as $v_client) {
                                ?>
                                <option value="<?= $v_client->client_id ?>"
                                    <?php
                                    if (!empty($status)) {
                                        echo $status == $v_client->client_id ? 'selected' : null;
                                    }
                                    ?>
                                ><?= ucfirst($v_client->name) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label><?= lang('date_range') ?></label>
                    <input type="text" name="range" id="reportrange"
                           class="pull-right form-control">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <b class="caret"></b>
                </div>


                <div class="col-md-2">
                    <label style="visibility: hidden"><?= lang('run_report') ?></label>
                    <button class="btn btn-purple" type="submit">
                        <?= lang('run_report') ?>
                    </button>
                </div>
            </address>
        </div>
        </form>
    </div>


    <div class="rep-container">
        <div class="page-header text-center">
            <h3 class="reports-headerspacing"><?= lang($filterBy) ?></h3>
            <?php if (!empty($start_date)) { ?>
                <h5><span><?= lang('FROM') ?></span>&nbsp;<?= $start_date ?>
                    &nbsp;<span><?= lang('TO') ?></span>&nbsp;<?= $end_date ?></h5>
            <?php } ?>
        </div>


        <table class="table zi-table table-hover norow-action small">
            <thead>
            <tr>
                <th class="text-left">
                    <div class="pull-left "><?= lang('payment_date') ?></div>
                </th>
                <th class="text-left">
                    <div class="pull-left "><?= lang('invoice_date') ?></div>
                </th>
                <th class="text-left">
                    <div class="pull-left "><?= lang('invoice') ?></div>
                </th>
                <th class="text-left">
                    <div class="pull-left "> <?= lang('client') ?></div>
                    <!---->
                </th>
                <th class="text-left">
                    <div class=""> <?= lang('payment_method') ?></div>
                </th>

                <th class="text-right">
                    <div class="pull-right "> <?= lang('amount') ?></div>
                </th>

            </tr>
            </thead>

            <tbody>

            <?php

            $total_amount = 0;

            if (!empty($all_payments)) {
                foreach ($all_payments as $key => $payments) {
                    $total_amount += $payments->amount;
                    ?>
                    <tr>
                        <?php
                        $client_info = $this->invoice_model->check_by(array('client_id' => $payments->paid_by), 'tbl_client');
                        $v_invoice = $this->invoice_model->check_by(array('invoices_id' => $payments->invoices_id), 'tbl_invoices');
                        if (!empty($client_info)) {
                            $c_name = $client_info->name;
                            $currency = $this->invoice_model->client_currency_symbol($payments->paid_by);
                        } else {
                            $c_name = '-';
                            $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                        }
                        $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $payments->payment_method), 'tbl_payment_methods');
                        ?>

                        <td>
                            <a class="hidden-print"
                               href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $payments->payments_id ?>"> <?= strftime(config_item('date_format'), strtotime($payments->payment_date)); ?></a>
                            <span
                                class="show_print"><?= strftime(config_item('date_format'), strtotime($payments->payment_date)) ?></span>
                        </td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_invoice->invoice_date)) ?></td>
                        <td><a class="text-info hidden-print"
                               href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $payments->invoices_id ?>"><?= $v_invoice->reference_no; ?></a>
                            <span
                                class="show_print"><?= $v_invoice->reference_no ?></span>
                        </td>
                        <td><?= $c_name; ?></td>
                        <td><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-'; ?></td>
                        <td class="text-right"><?= display_money($payments->amount, $currency->symbol); ?> </td>
                    </tr>
                <?php } ?>

                <tr class="hover-muted bt">
                    <td colspan="5"><?= lang('total') ?></td>
                    <td class="text-right"><?= display_money($total_amount, $cur->symbol) ?></td>
                </tr>

            <?php } ?>

            <!----></tbody>
        </table>
    </div>


</div>


<script type="text/javascript">
    $('#reportrange').daterangepicker({
        autoUpdateInput: <?= !empty($start_date) ? 'true' : 'false'?>,
        locale: {
            format: 'MMMM D, YYYY'
        },
        <?php if(!empty($start_date)){?>
        startDate: '<?=$start_date?>',
        endDate: '<?=$end_date?>',
        <?php }?>
        "opens": "right",
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
    });

    $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
</script>
