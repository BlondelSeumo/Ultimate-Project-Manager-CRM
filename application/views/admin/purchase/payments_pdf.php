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

<?php
$purchase_info = $this->purchase_model->check_by(array('purchase_id' => $payments_info->purchase_id), 'tbl_purchases');
$supplier_info = $this->purchase_model->check_by(array('supplier_id' => $payments_info->paid_to), 'tbl_suppliers');
$payment_methods = $this->purchase_model->check_by(array('payment_methods_id' => $payments_info->payment_method), 'tbl_payment_methods');

?>
<br/>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>

            <td style="width: 35px; border: 0px;padding-bottom: 10px;">
                <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                     src="<?= base_url() . config_item('invoice_logo') ?>">
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
        $currency = $this->purchase_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        ?>

        <span
            style="font-size:16pt;"><?= display_money($payments_info->amount, $currency->symbol); ?></span>
    </div>
</div>

<table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
       cellspacing="0" border="0">
    <tr>
        <td class="headcol"><?= lang('payment_date') ?></td>
        <td><?= display_date($payments_info->payment_date); ?></td>
    </tr>
    <?php if (config_item('amount_to_words') == 'Yes') { ?>
        <tr>
            <td class="headcol"><?= lang('num_word') ?></td>
            <td><?= number_to_word('', $payments_info->amount); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td style="" class="headcol"><?= lang('transaction_id') ?></td>
        <td><?= $payments_info->trans_id ?></td>
    </tr>


    <tr>
        <td class="headcol"><?= lang('received_from') ?></td>
        <td><strong><?= ($supplier_info->name) ?></strong>
        </td>
    </tr>
    <?php
    $role = $this->session->userdata('user_type');
    if ($role == 1 && $payments_info->account_id != 0) {
        $account_info = $this->purchase_model->check_by(array('account_id' => $payments_info->account_id), 'tbl_accounts');
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
        <td><?= strip_tags($payments_info->notes) ?></td>
    </tr>

</table>
<br/>
<?php $purchase_due = $this->purchase_model->calculate_to('purchase_due', $payments_info->purchase_id); ?>
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
            <?= lang('purchase') . ' ' . lang('date') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('purchase') . ' ' . lang('amount') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('paid_amount') ?>
        </td>
        <?php if ($purchase_due > 0) { ?>
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
            valign="top"><?= $purchase_info->reference_no ?></td>
        <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
            valign="top">
            <?= display_datetime($purchase_info->created) ?>
        </td>
        <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
            valign="top"><span>
                <?= display_money($this->purchase_model->get_purchase_cost($payments_info->purchase_id), $currency->symbol); ?>
                (- <?= lang('tax') ?>) </span>
        </td>
        <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
            valign="top">
            <span><?= display_money($payments_info->amount, $currency->symbol); ?></span>
        </td>
        <?php if ($purchase_due > 0) { ?>
            <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;color: red"
                valign="top">
                <span><?= display_money($purchase_due, $currency->symbol); ?></span>
            </td>
        <?php } ?>
    </tr>
    </tbody>
</table>
</body>
</html>
