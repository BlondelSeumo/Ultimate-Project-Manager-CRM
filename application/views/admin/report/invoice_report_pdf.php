<!DOCTYPE html>
<html>
<head>
    <title><?= lang('invoices_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>
    <style>
        th {
            padding: 10px 0px 5px 5px;
            font-size: 13px;;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>
        }

        td {
            padding: 5px 0px 0px 5px;
            font-size: 13px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>
        }

        .notes {
            color: #777;
            min-height: 20px;
            padding: 19px;
            margin-bottom: 20px;
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">

<br/>
<?php
$img = ROOTPATH . '/' . config_item('invoice_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('invoice_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>

            <td style="width: 35px; border: 0px;padding-bottom: 10px;">
                <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                     src="<?= $img ?>">
            </td>
            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 22px lighter;"><?= config_item('company_name') ?></p>
                <p style="color:#999;"><?= $this->config->item('company_address') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="padding:35px 0 50px;text-align:center"><span
        style="text-transform: uppercase; border-bottom:1px solid #eee;font-size:13pt;"><?= lang('invoices_report') ?></span>
    <?php
    if (!empty($range[0])) {
        $start_date = display_date($range[0]);
        $end_date = display_date($range[1]);
    }
    if (!empty($start_date)) { ?>
        <span style="margin-top: 10px;display: block;font-size: 15px;text-align: center"><?= lang('FROM') ?>
            &nbsp;<?= $start_date ?>
            &nbsp;<?= lang('TO') ?>&nbsp;<?= $end_date ?>
            </span>
    <?php } ?>
</div>

<table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
       cellspacing="0" border="0">
    <thead>
    <tr style="height:40px;background:#d6d6d6">
        <td style="padding:5px 10px 5px 10px;word-wrap: break-word;">
            <?= lang('status') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('invoice_date') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('due_date') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('reference_no') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('client_name') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;text-align:right"
            align="right">
            <?= lang('invoice_amount') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;text-align:right"
            align="right">
            <?= lang('tax') . ' ' . lang('amount') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;text-align:right"
            align="right">
            <?= lang('balance_due') ?>
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    $due_total = 0;
    $invoice_total = 0;
    $total_tax = 0;

    if (!empty($all_invoices)) {
        foreach ($all_invoices as $key => $invoice) {
            $status = $this->invoice_model->get_payment_status($invoice->invoices_id);
            $text_color = '#23b7e5';
            switch ($status) {
                case lang('fully_paid'):
                    $text_color = '#27c24c';
                    break;
                case lang('partially_paid'):
                    $text_color = '#ff902b';
                    break;
                case lang('not_paid'):
                    $text_color = '#f05050';
                    break;

            }
            $cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            $invoice_payable = $this->invoice_model->invoice_payable($invoice->invoices_id);
            $invoice_total += $invoice_payable;
            $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice->invoices_id);
            $due_total += $invoice_due;
            $tax_amount = $this->invoice_model->get_invoice_tax_amount($invoice->invoices_id);
            $total_tax += $tax_amount;
            ?>
            <tr style="border-bottom:1px solid #ededed">
                <td style="padding: 10px 0px 10px 10px;color: <?= $text_color ?>"
                    valign="top"><?= $status ?></td>
                <td style="padding: 10px 10px 5px 10px;word-wrap: break-word;"
                    valign="top">
                    <?= display_date($invoice->invoice_date) ?>
                </td>
                <td style="padding: 10px 10px 5px 10px;word-wrap: break-word;"
                    valign="top">
                    <?= display_date($invoice->due_date) ?>
                </td>
                <td style="padding: 10px 10px 5px 10px;word-wrap: break-word;"
                    valign="top"><span><?= $invoice->reference_no; ?></span>
                </td>
                <td style="padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span><?= client_name($invoice->client_id); ?></span>
                </td>
                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span><?= display_money($invoice_payable, $cur->symbol); ?></span>
                </td>
                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span><?= display_money($tax_amount, $cur->symbol); ?></span>
                </td>
                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span><?= display_money($invoice_due, $cur->symbol); ?></span>
                </td>
            </tr>
        <?php } ?>
        <tr style="height:50px;background:#d6d6d6">
            <td style="height:50px;padding-left: 15px" colspan="5"><?= lang('total') ?></td>
            <td style="text-align: right;height:50px"><?= display_money($invoice_total, $cur->symbol) ?></td>
            <td style="text-align: right;height:50px"><?= display_money($total_tax, $cur->symbol) ?></td>
            <td style="text-align: right;height:50px;padding-right: 15px"><?= display_money($due_total, $cur->symbol) ?></td>
        </tr>

    <?php } ?>
    </tbody>
</table>
</body>
</html>
