<?= message_box('success') ?>
<style>
    .table > tbody > tr > td {
        vertical-align: baseline;
    }
</style>
<div class="row mb">

    <div class="col-sm-8">
        <?php
        $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');

        $client_lang = $client_info->language;
        unset($this->lang->is_loaded[5]);
        $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
        $currency = $this->estimates_model->client_currency_symbol($estimates_info->client_id);
        ?>

    </div>
    <div class="col-sm-4 pull-right">
        <a onclick="print_estimates('print_estimates')" href="#" data-toggle="tooltip" data-placement="top" title=""
           data-original-title="Print" class="btn btn-xs btn-danger pull-right">
            <i class="fa fa-print"></i>
        </a>

        <a href="<?= base_url() ?>frontend/pdf_estimates/<?= $estimates_info->estimates_id ?>"
           data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF"
           class="btn btn-xs btn-success pull-right mr-sm">
            <i class="fa fa-file-pdf-o"></i>
        </a>
    </div>
</div>

<?php
if (strtotime($estimates_info->due_date) < strtotime(date('Y-m-d')) && $estimates_info->status == 'accepted') {
    $start = strtotime(date('Y-m-d'));
    $end = strtotime($estimates_info->due_date);

    $days_between = ceil(abs($end - $start) / 86400);
    ?>
    <div class="alert bg-danger-light hidden-print">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-warning"></i>
        <?= lang('estimate_overdue') . ' ' . lang('by') . ' ' . $days_between . ' ' . lang('days') ?>
    </div>
    <?php
}
if (is_file(config_item('invoice_logo'))) {
    $img = base_url() . config_item('invoice_logo');
} else {
    $img = base_url() . 'uploads/default_logo.png';
}
?>
<!-- Main content -->
<div class="panel" id="print_estimates">
    <!-- info row -->
    <div class="panel-body">

        <div class="row">
            <div class="col-lg-6 hidden-xs">
                <img class="pl-lg" style="width: 233px;height: 120px;"
                     src="<?= $img ?>">
            </div>
            <div class="col-lg-6 col-xs-12 ">
                <?php

                if (!empty($client_info)) {
                    $client_name = $client_info->name;
                    $address = $client_info->address;
                    $city = $client_info->city;
                    $zipcode = $client_info->zipcode;
                    $country = $client_info->country;
                    $phone = $client_info->phone;

                } else {
                    $client_name = '-';
                    $address = '-';
                    $city = '-';
                    $zipcode = '-';
                    $country = '-';
                    $phone = '-';
                }
                ?>

                <div class="pull-right pr-lg">
                    <h4 class="mb0"><?= lang('estimates') . ' : ' . $estimates_info->reference_no ?></h4>
                    <?= $language_info['estimate_date'] ?>
                    : <?= strftime(config_item('date_format'), strtotime($estimates_info->estimate_date)); ?>
                    <br><?= $language_info['due_date'] ?>
                    : <?= strftime(config_item('date_format'), strtotime($estimates_info->due_date)); ?>
                    <?php if (!empty($estimates_info->user_id)) { ?>
                        <br><?= lang('sales') . ' ' . lang('agent') ?>:<?php echo fullname($estimates_info->user_id); ?>
                        <?php
                    }
                    if ($estimates_info->status == 'accepted') {
                        $label = 'success';
                    } else {
                        $label = 'danger';
                    }
                    ?>
                    <br><?= $language_info['estimate_status'] ?>: <span
                            class="label label-<?= $label ?>"><?= lang($estimates_info->status) ?></span>

                    <?php $show_custom_fields = custom_form_label(10, $estimates_info->estimates_id);
                    if (!empty($show_custom_fields)) {
                        foreach ($show_custom_fields as $c_label => $v_fields) {
                            if (!empty($v_fields)) {
                                ?>
                                <br><?= $c_label ?>: <?= $v_fields; ?>
                            <?php }
                        }
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="row mb-lg">
            <div class="col-lg-6 col-xs-6">
                <h5 class="p-md bg-items mr-15">
                    <?= lang('our_info') ?>:
                </h5>
                <div class="pl-sm">
                    <h4 class="mb0"><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></h4>
                    <?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?>
                    <br><?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?>
                    , <?= config_item('company_zip_code') ?>
                    <br><?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?>
                    <br/><?= $language_info['phone'] ?> : <?= config_item('company_phone') ?>
                    <br/><?= lang('vat_number') ?> : <?= config_item('company_vat') ?>
                </div>
            </div>
            <div class="col-lg-6 col-xs-6 ">
                <h5 class="p-md bg-items ml-13">
                    <?= lang('customer') ?>:
                </h5>
                <div class="pl-sm">
                    <?php

                    if (!empty($client_info)) {
                        $client_name = $client_info->name;
                        $address = $client_info->address;
                        $city = $client_info->city;
                        $zipcode = $client_info->zipcode;
                        $country = $client_info->country;
                        $phone = $client_info->phone;

                    } else {
                        $client_name = '-';
                        $address = '-';
                        $city = '-';
                        $zipcode = '-';
                        $country = '-';
                        $phone = '-';
                    }
                    ?>
                    <h4 class="mb0"><?= $client_name ?></h4>
                    <?= $address ?>
                    <br> <?= $city ?>, <?= $zipcode ?>
                    <br><?= $country ?>
                    <br><?= $language_info['phone'] ?>: <?= $phone ?>
                    <?php if (!empty($client_info->vat)) { ?>
                        <br><?= lang('vat_number') ?>: <?= $client_info->vat ?>
                    <?php } ?>
                </div>
            </div>

        </div>
        <style type="text/css">
            .dragger {
                background: url(../../../../assets/img/dragger.png) 0px 11px no-repeat;
                cursor: pointer;
            }

            .table > tbody > tr > td {
                vertical-align: initial;
            }
        </style>
        <div class="table-responsive mb-lg " style="margin-top: 25px">
            <table class="table items estimate-items-preview" page-break-inside: auto;>
                <thead class="bg-items">
                <tr>
                    <th><?= $language_info['items'] ?></th>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <th><?= $language_info['hsn_code'] ?></th>
                    <?php } ?>
                    <?php
                    $qty_heading = $language_info['qty'];
                    if (isset($estimates_info) && $estimates_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                        $qty_heading = lang('hours');
                    } else if (isset($estimates_info) && $estimates_info->show_quantity_as == 'qty_hours') {
                        $qty_heading = lang('qty') . '/' . lang('hours');
                    }
                    ?>
                    <th><?php echo $qty_heading; ?></th>
                    <th class="col-sm-1"><?= $language_info['price'] ?></th>
                    <th class="col-sm-2"><?= $language_info['tax'] ?></th>
                    <th class="col-sm-1"><?= $language_info['total'] ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $invoice_items = $this->estimates_model->ordered_items_by_id($estimates_info->estimates_id);

                if (!empty($invoice_items)) :
                    foreach ($invoice_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->estimate_items_id ?>">
                            <td><strong class="block"><?= $item_name ?></strong>
                                <?= nl2br($v_item->item_desc) ?>
                            </td>
                            <?php
                            $invoice_view = config_item('invoice_view');
                            if (!empty($invoice_view) && $invoice_view == '2') {
                                ?>
                                <td><?= $v_item->hsn_code ?></td>
                            <?php } ?>
                            <td><?= $v_item->quantity . '   &nbsp' . $v_item->unit ?></td>
                            <td><?= display_money($v_item->unit_cost) ?></td>
                            <td><?php
                                if (!empty($item_tax_name)) {
                                    foreach ($item_tax_name as $v_tax_name) {
                                        $i_tax_name = explode('|', $v_tax_name);
                                        echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                                    }
                                }
                                ?></td>
                            <td><?= display_money($v_item->total_cost) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8"><?= lang('nothing_to_display') ?></td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="row" style="margin-top: 35px">
            <div class="col-xs-8">
                <p class="well well-sm mt">
                    <?= $estimates_info->notes ?>
                </p>
            </div>
            <div class="col-sm-4 pv">
                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['sub_total'] ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->estimates_model->estimate_calculation('estimate_cost', $estimates_info->estimates_id)); ?>
                    </p>
                </div>
                <?php if ($estimates_info->discount_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['discount'] ?>
                            (<?php echo $estimates_info->discount_percent; ?>
                            %)</p>
                        <p class="pull-right mr">
                            <?= display_money($this->estimates_model->estimate_calculation('discount', $estimates_info->estimates_id)); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php
                $tax_info = json_decode($estimates_info->total_tax);
                $tax_total = 0;
                if (!empty($tax_info)) {
                    $tax_name = $tax_info->tax_name;
                    $total_tax = $tax_info->total_tax;
                    if (!empty($tax_name)) {
                        foreach ($tax_name as $t_key => $v_tax_info) {
                            $tax = explode('|', $v_tax_info);
                            $tax_total += $total_tax[$t_key];
                            ?>
                            <div class="clearfix">
                                <p class="pull-left"><?= $tax[0] . ' (' . $tax[1] . ' %)' ?></p>
                                <p class="pull-right mr">
                                    <?= display_money($total_tax[$t_key]); ?>
                                </p>
                            </div>
                        <?php }
                    }
                } ?>
                <?php if ($tax_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['total'] . ' ' . $language_info['tax'] ?></p>
                        <p class="pull-right mr">
                            <?= display_money($tax_total); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php if ($estimates_info->adjustment > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['adjustment'] ?></p>
                        <p class="pull-right mr">
                            <?= display_money($estimates_info->adjustment); ?>
                        </p>
                    </div>
                <?php endif;
                $estimate_total = $this->estimates_model->estimate_calculation('total', $estimates_info->estimates_id);
                ?>
                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['total'] ?></p>
                    <p class="pull-right mr">
                        <?= display_money($estimate_total, $currency->symbol); ?>
                    </p>
                </div>
                <?php if (config_item('amount_to_words') == 'Yes') { ?>
                    <div class="clearfix">
                        <p class="pull-right h4"><strong class="h3"><?= lang('num_word') ?>
                                : </strong> <?= number_to_word($estimates_info->client_id, $estimate_total); ?></p>
                    </div>
                <?php } ?>

            </div>
        </div>
        <?= !empty($invoice_view) && $invoice_view > 0 ? $this->gst->summary($invoice_items) : ''; ?>
    </div>
    <script type="text/javascript">
        function print_estimates(print_estimates) {
            var printContents = document.getElementById(print_estimates).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
