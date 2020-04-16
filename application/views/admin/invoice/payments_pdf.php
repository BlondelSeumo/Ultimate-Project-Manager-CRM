<!DOCTYPE html>
<html>
<head>
    <title>Payments PDF</title>
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
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
        }

        td {
            padding: 5px 0px 0px 5px;
            font-size: 13px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
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
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<?php
if (!empty($all_invoices_info)) {
    foreach ($all_invoices_info as $v_invoice) {
        if (!empty($v_invoice)) {
            $all_payment_info = $this->db->where('invoices_id', $v_invoice->invoices_id)->get('tbl_payments')->result();

            if (!empty($all_payment_info)):foreach ($all_payment_info as $v_payments_info):
                $client_info = $this->invoice_model->check_by(array('client_id' => $v_payments_info->paid_by), 'tbl_client');
                ?>

                <?php
            endforeach;
            endif;
        }

    }
}
?>
<?php
$invoice_info = $this->invoice_model->check_by(array('invoices_id' => $payments_info->invoices_id), 'tbl_invoices');
if (empty($invoice_info)) {
    $invoice_info = new stdClass();
    $invoice_info->adjustment = 0;
    $invoice_info->client_id = 0;
    $invoice_info->date_saved = 0;
    $invoice_info->invoices_id = 0;
    $invoice_info->reference_no = '-';

}
$client_info = $this->invoice_model->check_by(array('client_id' => $payments_info->paid_by), 'tbl_client');
$payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $payments_info->payment_method), 'tbl_payment_methods');
$img = ROOTPATH . '/' . config_item('invoice_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('invoice_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<br/>
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
<div style="padding:35px 0 50px;text-align:center">
                                        <span
                                            style="text-transform: uppercase; border-bottom:1px solid #eee;font-size:13pt;"><?= lang('payments_received') ?></span>
    <div style="text-align:center;color:white;float:right;background:#1B9BA0;width: 25%;overflow: hidden;
                                         padding: 20px 5px;">
        <span> <?= lang('amount_received') ?></span><br>
        <?php
        $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
        ?>

        <span
            style="font-size:16pt;"><?= display_money($payments_info->amount, $currency->symbol); ?></span>
    </div>
</div>

<table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
       cellspacing="0" border="0">
    <tr>
        <td class="headcol"><?= lang('payment_date') ?></td>
        <td><?= strftime(config_item('date_format'), strtotime($payments_info->payment_date)); ?></td>
    </tr>
    <?php if (config_item('amount_to_words') == 'Yes') { ?>
        <tr>
            <td class="headcol"><?= lang('num_word') ?></td>
            <td><?= number_to_word($invoice_info->client_id, $payments_info->amount); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td style="" class="headcol"><?= lang('transaction_id') ?></td>
        <td><?= $payments_info->trans_id ?></td>
    </tr>


    <tr>
        <td class="headcol"><?= lang('received_from') ?></td>
        <td><strong><?= ucfirst($client_info->name) ?></strong>
        </td>
    </tr>
    <?php
    $role = $this->session->userdata('user_type');
    if ($role == 1 && $payments_info->account_id != 0) {
        $account_info = $this->invoice_model->check_by(array('account_id' => $payments_info->account_id), 'tbl_accounts');
        if (!empty($account_info)) {
            ?>
            <tr>
                <td class="headcol"><?= lang('received_account') ?></td>
                <td><strong><?= $account_info->account_name ?></strong>
                </td>
            </tr>
        <?php }
    } ?>
    <tr>
        <td class="headcol"><?= lang('payment_mode') ?></td>
        <td><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-' ?></td>
    </tr>
    <tr>
        <td class="headcol"><?= lang('notes') ?></td>
        <td><?= strip_html_tags($payments_info->notes,true) ?></td>
    </tr>

</table>
<br/>
<?php $invoice_due = $this->invoice_model->calculate_to('invoice_due', $payments_info->invoices_id);?>
<div style="width:100%">
    <div style="width:50%;float:left"><h4><?= lang('payment_for') ?></h4></div>
    <div style="clear:both;"></div>
</div>
<table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
       cellspacing="0" border="0">
    <thead>
    <tr style="height:40px;background:#f5f5f5">
        <td style="padding:5px 10px 5px 10px;word-wrap: break-word;">
            <?= lang('invoice_code') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('invoice_date') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('invoice_amount') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('paid_amount') ?>
        </td>
        <?php if ($invoice_due > 0) { ?>
            <td style="padding:5px 10px 5px 5px;color:red;word-wrap: break-word;"
                align="right">
                <?= lang('due_amount') ?>
            </td>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <tr style="border-bottom:1px solid #ededed">
        <td style="padding: 10px 0px 10px 10px;"
            valign="top"><?= $invoice_info->reference_no ?></td>
        <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
            valign="top">
            <?= strftime(config_item('date_format'), strtotime($invoice_info->date_saved)) ?>
        </td>
        <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
            valign="top"><span>
                <?= display_money($this->invoice_model->get_invoice_cost($payments_info->invoices_id), $currency->symbol); ?>
                (- <?= lang('tax') ?>) </span>
        </td>
        <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
            valign="top">
            <span><?= display_money($payments_info->amount, $currency->symbol); ?></span>
        </td>
        <?php if ($invoice_due > 0) { ?>
            <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;color: red"
                valign="top">
                <span><?= display_money($invoice_due, $currency->symbol); ?></span>
            </td>
        <?php } ?>
    </tr>
    </tbody>
</table>
</body>
</html>
