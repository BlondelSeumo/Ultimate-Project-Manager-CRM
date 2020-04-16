<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('proposal') ?></title>
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
            font-family: "Source Sans Pro", sans-serif;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        .h4 {
            font-size: 18px;
        }

        .h3 {
            font-size: 24px;
        }
        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            color: #555555;
            background: #ffffff;
            font-size: 14px;
            font-family: "Source Sans Pro", sans-serif;
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
            background: #F3F3F3;
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
if ($proposals_info->module == 'client') {
    $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
    $currency = $this->proposal_model->client_currency_symbol($proposals_info->module_id);
    $client_lang = $client_info->language;
} else if ($proposals_info->module == 'leads') {
    $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
    if (!empty($client_info)) {
        $client_info->name = $client_info->lead_name;
        $client_info->zipcode = null;
    }
    $client_lang = 'english';
    $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
} else {
    $client_lang = 'english';
    $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
}
unset($this->lang->is_loaded[5]);
$language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
$img = ROOTPATH . '/' . config_item('invoice_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('invoice_logo');
}
if (!file_exists($img)) {
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<table class="clearfix">
    <tr>
        <td>
            <div id="logo" class="left">
                <img style=" width: 233px;height: 120px" src="<?= $img ?>">
            </div>
        </td>
        <td class="">


            <div class="right" style="float: right;padding-right: 20px">
                <h2 style="margin-bottom: 0"><?= $language_info['proposals'] ?>
                    : <?= $proposals_info->reference_no ?></h2>
                <div class="date"><?= $language_info['proposal_date'] ?>
                    :<?= strftime(config_item('date_format'), strtotime($proposals_info->proposal_date)); ?></div>
                <div class="date"><?= $language_info['due_date'] ?>
                    :<?= strftime(config_item('date_format'), strtotime($proposals_info->due_date)); ?></div>
                <?php if (!empty($proposals_info->user_id)) { ?>
                    <div class="date">
                        <?= lang('sales') . ' ' . lang('agent') ?><?php echo fullname($proposals_info->user_id)
                        ?>
                    </div>
                <?php }
                if ($proposals_info->status == 'accepted') {
                    $label = 'success';
                } else {
                    $label = 'danger';
                } ?>
                <div class="date"><?= lang('proposals') . '  ' . lang('status') ?>
                    : <?= lang($proposals_info->status) ?></div>
                <?php $show_custom_fields = custom_form_label(10, $proposals_info->proposals_id);
                if (!empty($show_custom_fields)) {
                    foreach ($show_custom_fields as $c_label => $v_fields) {
                        if (!empty($v_fields)) {
                            ?>
                            <div class="date"><?= $c_label ?>: <?= $v_fields ?></div>
                        <?php }
                    }
                }
                ?>
            </div>

        </td>
    </tr>
</table>

<table id="details" class="clearfix">
    <tr>
        <td style="width: 50%;overflow: hidden">
            <h4 class="p-md bg-items ">
                <?= lang('our_info') ?>
            </h4>
        </td>
        <td style="width: 50%">
            <h4 class="p-md bg-items ">
                <?= lang('customer') ?>
            </h4>
        </td>
    </tr>
    <tr style="margin-top: 0px">
        <td style="width: 50%;overflow: hidden">
            <div style="padding-left: 5px">
                <h3 style="margin: 0px"><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></h3>
                <div><?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?></div>
                <div><?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?>
                    , <?= config_item('company_zip_code') ?></div>
                <div><?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?></div>
                <div> <?= config_item('company_phone') ?></div>
                <div><a href="mailto:<?= config_item('company_email') ?>"><?= config_item('company_email') ?></a></div>
                <div><?= config_item('company_vat') ?></div>
            </div>
        </td>
        <td style="width: 50%">
            <div style="padding-left: 5px">
                <?php
                if (!empty($client_info)) {
                    $client_name = $client_info->name;
                    $address = $client_info->address;
                    $city = $client_info->city;
                    $zipcode = $client_info->zipcode;
                    $country = $client_info->country;
                    $phone = $client_info->phone;
                    $email = $client_info->email;
                } else {
                    $client_name = '-';
                    $address = '-';
                    $city = '-';
                    $zipcode = '-';
                    $country = '-';
                    $phone = '-';
                    $email = '-';
                }
                ?>
                <h3 style="margin: 0px"><?= $client_name ?></h3>
                <div class="address"><?= $address ?></div>
                <div class="address"><?= $city ?>, <?= $zipcode ?>
                    ,<?= $country ?></div>
                <div class="address"><?= $phone ?></div>
                <div class="email"><a href="mailto:<?= $email ?>"><?= $email ?></a></div>
                <?php if (!empty($client_info->vat)) { ?>
                    <div class="email"><?= lang('vat_number') ?>: <?= $client_info->vat ?></div>
                <?php } ?>
            </div>
        </td>
    </tr>
</table>

<table class="items">
    <thead class="p-md bg-items">
    <tr>
        <th><?= $language_info['items'] ?></th>
        <?php
        $colspan = 3;
        $invoice_view = config_item('invoice_view');
        if (!empty($invoice_view) && $invoice_view == '2') {
            $colspan = 4;
            ?>
            <th><?= lang('hsn_code') ?></th>
        <?php } ?>
        <th><?= $language_info['qty'] ?></th>
        <th><?= $language_info['price'] ?></th>
        <th><?= $language_info['tax'] ?></th>
        <th><?= $language_info['total'] ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $invoice_items = $this->proposal_model->ordered_items_by_id($proposals_info->proposals_id);

    if (!empty($invoice_items)) :
        foreach ($invoice_items as $key => $v_item) :
            $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
            $item_tax_name = json_decode($v_item->item_tax_name);
            ?>
            <tr>
                <td class="unit"><h3><?= $item_name ?></h3><?= nl2br($v_item->item_desc) ?></td>
                <?php
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    ?>
                    <td><?= $v_item->hsn_code ?></td>
                <?php } ?>
                <td class="unit"><?= $v_item->quantity . '   ' . $v_item->unit ?></td>
                <td class="unit"><?= display_money($v_item->unit_cost) ?></td>
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
        <td colspan="1"><?= $language_info['sub_total'] ?></td>
        <td><?= display_money($this->proposal_model->proposal_calculation('proposal_cost', $proposals_info->proposals_id)) ?></td>
    </tr>
    <?php if ($proposals_info->discount_total > 0): ?>
        <tr class="total">
            <td colspan="<?= $colspan ?>"></td>
            <td colspan="1"><?= $language_info['discount'] ?>(<?php echo $proposals_info->discount_percent; ?>%)</td>
            <td> <?= display_money($this->proposal_model->proposal_calculation('discount', $proposals_info->proposals_id)) ?></td>
        </tr>
    <?php endif;
    $tax_info = json_decode($proposals_info->total_tax);
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
            <td colspan="1"><?= $language_info['total'] . ' ' . $language_info['tax'] ?></td>
            <td><?= display_money($tax_total); ?></td>
        </tr>
    <?php endif;
    if ($proposals_info->adjustment > 0): ?>
        <tr class="total">
            <td colspan="<?= $colspan ?>"></td>
            <td colspan="1"><?= $language_info['adjustment'] ?></td>
            <td><?= display_money($proposals_info->adjustment); ?></td>
        </tr>
    <?php endif;
    $total_proposal = $this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id);
    ?>
    <tr class="total">
        <td colspan="<?= $colspan ?>"></td>
        <td colspan="1"><?= $language_info['total'] ?></td>
        <td><?= display_money($total_proposal, $currency->symbol); ?></td>
    </tr>
    </tfoot>
</table>
<?php if (config_item('amount_to_words') == 'Yes') { ?>
    <div class="clearfix">
        <p class="right h4"><strong class="h3"><?= lang('num_word') ?>
                : </strong> <?= number_to_word($proposals_info->module_id, $total_proposal); ?></p>
    </div>
<?php } ?>

<div id="thanks"><?= lang('thanks') ?>!</div>
<div id="notices">
    <div class="notice"><?= strip_html_tags($proposals_info->notes,true) ?></div>
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
                <th class="desc"><?= $language_info['items'] ?></th>
                <?php
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    ?>
                    <th><?= lang('hsn_code') ?></th>
                <?php } ?>
                <th class="unit"><?= $language_info['qty'] ?></th>
                <th class="desc"><?= $language_info['tax'] ?></th>
                <th class="unit" style="text-align: right"><?= $language_info['total_tax'] ?></th>
                <th class="total" style="text-align: right"><?= $language_info['tax_excl_amt'] ?></th>
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
                <td><?= $language_info['total'] ?></td>
                <td><?= display_money($total_tax) ?></td>
                <td><?= display_money($total_cost) ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<footer>
    <?= config_item('proposal_footer') ?>
</footer>
</body>
</html>
