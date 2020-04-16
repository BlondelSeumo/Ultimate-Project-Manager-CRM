<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= lang('invoice') ?></title>
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }

    ?>
    <style type="text/css">
        @font-face {
            font-family: latha;
            font-style: normal;
            font-weight: 400;
            src: url('<?= ROOTPATH ?>/assets/latha.ttf') format('truetype');
            /*src: url(http://eclecticgeek.com/dompdf/fonts/latha.ttf) format('true-type');*/
        }

        * {
            font-family: 'tamil-latha', sans-serif;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            color: #555555;
            background: #ffffff;
            font-size: 14px;
            font-family: 'tamil-latha', sans-serif;
            width: 100%;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #aaaaaa;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        #logo {
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        #company {
        <?php if(!empty($RTL)){?> text-align: left;
        <?php }else{?> text-align: right;
        <?php }?>
        }

        #details {
            margin-bottom: 20px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        #client {
            padding-left: 6px;
            /*border-left: 6px solid #0087C3;*/
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1em;
            font-weight: normal;
            margin: 0;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        #invoice {
        <?php if(!empty($RTL)){?> text-align: left;
        <?php }else{?> text-align: right;
        <?php }?>
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 1.5em;
            line-height: 1em;
            font-weight: normal;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        table {
            width: 100%;
            border-spacing: 0;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 10px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        table.items th,
        table.items td {
            padding: 8px;
            /*background: #EEEEEE;*/
            border-bottom: 1px solid #FFFFFF;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>

        }

        table.items th {
            white-space: nowrap;
            font-weight: normal;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        table.items td {
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>
        }

        table.items td h3 {
            color: #57B223;
            font-size: 1em;
            font-weight: normal;
            margin-top: 5px;
            margin-bottom: 5px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        table.items .no {
            background: #dddddd;
        }

        table.items .desc {
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>
        }

        table.items .unit {
            background: #dddddd;
        }

        table.items .qty {
        }

        table.items td.unit,
        table.items td.qty,
        table.items td.total {
            font-size: 1em;
        }

        table.items tbody tr:last-child td {
            border: none;

        }

        table.items tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }

        table.items tfoot tr:first-child td {
            border-top: none;
        }

        table.items tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table.items tfoot tr td:first-child {
            border: none;
        <?php if(!empty($RTL)){?> text-align: left;
        <?php }else{?> text-align: right;
        <?php }?>
        }

        #thanks {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;

        }

        #notices .notice {
            font-size: 1em;
            color: #777;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #aaaaaa;
            padding: 8px 0;
            text-align: center;
        }

        tr.total td, tr th.total, tr td.total {
        <?php if(!empty($RTL)){?> text-align: left;
        <?php }else{?> text-align: right;
        <?php }?>
        }

        .bg-items {
            background: #515151 !important;
            color: #FFFFFF
        }

        .p-md {
            padding: 12px !important;
        }

    </style>
</head>
<body>

<?php
$paid_amount = $this->return_stock_model->calculate_to('paid_amount', $return_stock_info->return_stock_id);
$currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

$payment_status = $this->return_stock_model->get_payment_status($return_stock_info->return_stock_id);
$uri = $this->uri->segment(3);
if ($uri == 'invoice_email') {
    $img = base_url() . config_item('invoice_logo');
} else {
    $img = ROOTPATH . '/' . config_item('invoice_logo');
    $a = file_exists($img);
    if (empty($a)) {
        $img = base_url() . config_item('invoice_logo');
    }
}
?>

<table class="clearfix">
    <tr>
        <td>
            <div id="logo" class="left">
                <img style=" width: auto;height: 64px" src="<?= $img ?>">
            </div>
        </td>
        <td class="">

            <div class="right" style="float: right;padding-right: 20px">
                <h2 style="margin-bottom: 0"><?= lang('return_stock') ?>: <?= $return_stock_info->reference_no ?></h2>
                <div class="date"><?= lang('return_stock') . ' ' . lang('date') ?>
                    :<?= display_date($return_stock_info->return_stock_date); ?></div>
                <div class="date"><?= lang('due_date') ?>
                    :<?= display_date($return_stock_info->due_date); ?></div>
                <?php if (!empty($return_stock_info->user_id)) { ?>
                    <div class="date">
                        <?= lang('sales') . ' ' . lang('agent') ?><?php
                        $profile_info = $this->db->where('user_id', $return_stock_info->user_id)->get('tbl_account_details')->row();
                        if (!empty($profile_info)) {
                            echo $profile_info->fullname;
                        }
                        ?>
                    </div>
                <?php } ?>
                <div class="date"><?= lang('status') ?>
                    : <?= lang($return_stock_info->main_status) ?></div>
                <div class="date"><?= lang('payment_status') ?>: <?= lang($payment_status) ?></div>
            </div>

        </td>
    </tr>
</table>
<?php
if ($return_stock_info->module == 'client') {
    $supplier_info = $this->return_stock_model->check_by(array('client_id' => $return_stock_info->module_id), 'tbl_client');
} else if ($return_stock_info->module == 'supplier') {
    $supplier_info = $this->return_stock_model->check_by(array('supplier_id' => $return_stock_info->module_id), 'tbl_suppliers');
}
if (!empty($supplier_info)) {
    $client_name = $supplier_info->name;
    $address = $supplier_info->address;
    $mobile = $supplier_info->mobile;
    $phone = $supplier_info->phone;
    $email = $supplier_info->email;
} else {
    $client_name = '-';
    $address = '-';
    $city = '-';
    $zipcode = '-';
    $mobile = '-';
    $phone = '-';
    $email = '-';
}
?>
<table id="details" class="clearfix">
    <tr>
        <td style="width: 50%;overflow: hidden">
            <h4 class="p-md bg-items ">
                <?= lang('our_info') ?>
            </h4>
        </td>
        <td style="width: 50%">
            <h4 class="p-md bg-items ">
                <?= lang($return_stock_info->module) . ' ' . lang('info') ?>
            </h4>
        </td>
    </tr>
    <tr style="margin-top: 0px">
        <td style="width: 50%;overflow: hidden">
            <div style="padding-left: 5px">
                <h3 style="margin: 0px"><?= config_item('company_legal_name') ?></h3>
                <div><?= config_item('company_address') ?></div>
                <div><?= config_item('company_city') ?>
                    , <?= config_item('company_zip_code') ?></div>
                <div><?= config_item('company_country') ?></div>
                <div> <?= config_item('company_phone') ?></div>
                <div><a href="mailto:<?= config_item('company_email') ?>"><?= config_item('company_email') ?></a></div>
                <div><?= config_item('company_vat') ?></div>
            </div>
        </td>
        <td style="width: 50%;overflow: hidden">
            <div style="padding-left: 5px">
                <h3 style="margin: 0px"><?= $client_name ?></h3>
                <div class="address"><?= $address ?></div>
                <div class="address"><?= $mobile ?></div>
                <div class="address"><?= $phone ?></div>
                <div class="email"><a href="mailto:<?= $email ?>"><?= $email ?></a></div>
                <?php if (!empty($supplier_info->tax)) { ?>
                    <div class="email"><?= lang('tax') ?>: <?= $supplier_info->tax ?></div>
                <?php } ?>
            </div>
        </td>
    </tr>
</table>

<table class="items">
    <thead class="p-md bg-items">
    <tr>
        <th><?= lang('description') ?></th>
        <?php
        $colspan = 3;
        $invoice_view = config_item('invoice_view');
        if (!empty($invoice_view) && $invoice_view == '2') {
            $colspan = 4;
            ?>
            <th><?= lang('hsn_code') ?></th>
        <?php } ?>
        <th><?= lang('price') ?></th>
        <th><?= lang('qty') ?></th>
        <th><?= lang('tax') ?></th>
        <th><?= lang('total') ?></th>
    </tr>
    </thead>
    <tbody style="border-bottom: 1px solid #aaaaaa">
    <?php
    $invoice_items = $this->return_stock_model->ordered_items_by_id($return_stock_info->return_stock_id);

    if (!empty($invoice_items)) :
        foreach ($invoice_items as $key => $v_item) :
            $item_name = $v_item->item_name ? $v_item->item_name : strip_html_tags($v_item->item_desc);
            $item_tax_name = json_decode($v_item->item_tax_name);
            ?>
            <tr>
                <td class="unit"><h3><?= $item_name ?></h3><?= strip_html_tags($v_item->item_desc) ?></td>
                <?php
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    ?>
                    <td><?= $v_item->hsn_code ?></td>
                <?php } ?>
                <td class="unit"><?= display_money($v_item->unit_cost) ?></td>
                <td class="unit"><?= $v_item->quantity . '   ' . $v_item->unit ?></td>
                <td class="unit"><?php
                    if (!empty($item_tax_name)) {
                        foreach ($item_tax_name as $v_tax_name) {
                            $i_tax_name = explode('|', $v_tax_name);
                            echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                        }
                    }
                    ?></td>
                <td class="unit"><?= display_money($v_item->total_cost) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif ?>

    </tbody>
    <tfoot>
    <tr class="total">
        <td colspan="<?= $colspan ?>"></td>
        <td colspan="1"><?= lang('sub_total') ?></td>
        <td><?= display_money($this->return_stock_model->calculate_to('return_stock_cost', $return_stock_info->return_stock_id)) ?></td>
    </tr>
    <?php if ($return_stock_info->discount_total > 0): ?>
        <tr class="total">
            <td colspan="<?= $colspan ?>"></td>
            <td colspan="1"><?= lang('discount') ?>(<?php echo $return_stock_info->discount_percent; ?>%)</td>
            <td> <?= display_money($this->return_stock_model->calculate_to('discount', $return_stock_info->return_stock_id)) ?></td>
        </tr>
    <?php endif;
    $tax_info = json_decode($return_stock_info->total_tax);
    $tax_total = 0;
    if (!empty($tax_info)) {
        $tax_name = $tax_info->tax_name;
        $total_tax = $tax_info->total_tax;
        if (!empty($tax_name)) {
            foreach ($tax_name as $t_key => $v_tax_info) {
                $tax = explode('|', $v_tax_info);
                $tax_total += $total_tax[$t_key];
                ?>
                <tr class="total">
                    <td colspan="<?= $colspan ?>"></td>
                    <td colspan="1"><?= $tax[0] . ' (' . $tax[1] . ' %)' ?></td>
                    <td> <?= display_money($total_tax[$t_key]); ?></td>
                </tr>
            <?php }
        }
    } ?>
    <?php if ($tax_total > 0): ?>
        <tr class="total">
            <td colspan="<?= $colspan ?>"></td>
            <td colspan="1"><?= lang('total') . ' ' . lang('tax') ?></td>
            <td><?= display_money($tax_total); ?></td>
        </tr>
    <?php endif;
    if ($return_stock_info->adjustment > 0): ?>
        <tr class="total">
            <td colspan="<?= $colspan ?>"></td>
            <td colspan="1"><?= lang('adjustment') ?></td>
            <td><?= display_money($return_stock_info->adjustment); ?></td>
        </tr>
    <?php endif ?>
    <tr class="total">
        <td colspan="<?= $colspan ?>"></td>
        <td colspan="1"><?= lang('total') ?></td>
        <td><?= display_money($this->return_stock_model->calculate_to('total', $return_stock_info->return_stock_id), $currency->symbol); ?></td>
    </tr>
    <?php
    $paid_amount = $this->return_stock_model->calculate_to('paid_amount', $return_stock_info->return_stock_id);
    if ($paid_amount > 0) {
        $total = lang('total_due');
        if ($paid_amount > 0) {
            $text = 'style="color:red"';
            ?>
            <tr class="total">
                <td colspan="<?= $colspan ?>"></td>
                <td colspan="1"><?= lang('paid_amount') ?></td>
                <td><?= $paid_amount ?></td>
            </tr>
        <?php } else {
            $text = '';
        } ?>
        <tr class="total">
            <td colspan="<?= $colspan ?>"></td>
            <td colspan="1"><span <?= $text ?>><?= $total ?></span></td>
            <td><?= display_money($this->return_stock_model->calculate_to('return_stock_due', $return_stock_info->return_stock_id), $currency->symbol); ?></td>
        </tr>
    <?php } ?>
    </tfoot>
</table>
<div id="thanks"><?= lang('thanks') ?>!</div>
<div id="notices">
    <div class="notice"><?= strip_tags($return_stock_info->notes) ?></div>
</div>
<?php

$invoice_view = config_item('invoice_view');
if (!empty($invoice_view) && $invoice_view > 0) {
    ?>
    <style type="text/css">
        .panel {
            margin-bottom: 21px;
            background-color: #ffffff;
            border: 1px solid transparent;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .panel-custom .panel-heading {
            border-bottom: 2px solid #2b957a;
        }

        .panel .panel-heading {
            border-bottom: 0;
            font-size: 14px;
        }

        .panel-heading {
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
        }

        .panel-title {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 16px;
        }
    </style>
    <div class="panel panel-custom" style="margin-top: 20px">
        <div class="panel-heading" style="border:1px solid #dde6e9;border-bottom: 2px solid #57B223;">
            <div class="panel-title"><?= lang('tax_summary') ?></div>
        </div>
        <table class="items" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th class="desc"><?= lang('items') ?></th>
                <?php
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    ?>
                    <th><?= lang('hsn_code') ?></th>
                <?php } ?>
                <th class="unit"><?= lang('qty') ?></th>
                <th class="desc"><?= lang('tax') ?></th>
                <th class="unit" style="text-align: right"><?= lang('total_tax') ?></th>
                <th class="total" style="text-align: right"><?= lang('tax_excl_amt') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_tax = 0;
            $total_cost = 0;
            if (!empty($invoice_items)) :
                foreach ($invoice_items as $key => $v_item) :
                    $item_tax_name = json_decode($v_item->item_tax_name);
                    $tax_amount = 0;
                    ?>
                    <tr>
                        <td class="desc"><?= $v_item->item_name ?></td>
                        <?php
                        $invoice_view = config_item('invoice_view');
                        if (!empty($invoice_view) && $invoice_view == '2') {
                            ?>
                            <td><?= $v_item->hsn_code ?></td>
                        <?php } ?>
                        <td class="unit"><?= $v_item->quantity . '   ' . $v_item->unit ?></td>
                        <td class="desc"><?php
                            if (!empty($item_tax_name)) {
                                foreach ($item_tax_name as $v_tax_name) {
                                    $i_tax_name = explode('|', $v_tax_name);
                                    $tax_amount += $v_item->total_cost / 100 * $i_tax_name[1];
                                    echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                                }
                            }
                            $total_cost += $v_item->total_cost;
                            $total_tax += $tax_amount;
                            ?></td>
                        <td class="unit" style="text-align: right"><?= display_money($tax_amount) ?></td>
                        <td class="total" style="text-align: right"><?= display_money($v_item->total_cost) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif ?>

            </tbody>
            <tfoot>
            <tr class="total">
                <td colspan="<?= $colspan ?>"></td>
                <td><?= lang('total') ?></td>
                <td><?= display_money($total_tax) ?></td>
                <td><?= display_money($total_cost) ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php $all_payment_info = $this->db->where('return_stock_id', $return_stock_info->return_stock_id)->get('tbl_return_stock_payments')->result();
if (!empty($all_payment_info)) { ?>

    <div style="margin-top:20px">
        <div style="width:100%">
            <div style="width:50%;float:left"><h4><?= lang('payment') . ' ' . lang('details') ?></h4></div>
            <div style="clear:both;"></div>
        </div>

        <table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
               cellspacing="0" border="0">
            <thead>
            <tr style="height:40px;background:#f5f5f5">
                <td style="padding:5px 10px 5px 10px;word-wrap: break-word;">
                    <?= lang('transaction_id') ?>
                </td>
                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                    align="right">
                    <?= lang('payment_date') ?>
                </td>
                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                    align="right">
                    <?= lang('amount') ?>
                </td>
                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                    align="right">
                    <?= lang('payment_mode') ?>
                </td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($all_payment_info as $v_payments_info) {
                $payment_methods = $this->return_stock_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');
                ?>
                <tr style="border-bottom:1px solid #ededed">
                    <td style="padding: 10px 0px 10px 10px;"
                        valign="top"><?= $v_payments_info->trans_id; ?>
                    </td>
                    <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                        valign="top">
                        <?= display_date($v_payments_info->payment_date); ?>
                    </td>
                    <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                        valign="top"><?= display_money($v_payments_info->amount, $currency->symbol) ?>
                    </td>
                    <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                        valign="top">
                        <?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-'; ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
<footer>
    <?= config_item('invoice_footer') ?>
</footer>
</body>
</html>
