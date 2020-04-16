<?php include_once 'assets/admin-ajax.php'; ?>
<?php include_once 'assets/js/sales.php'; ?>
<form name="myform" role="form" data-parsley-validate="" novalidate=""
      enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>client/invoice/submit_refund_items/<?php
if (!empty($invoice_info)) {
    echo $invoice_info->invoices_id;
}
?>" method="post" class="form-horizontal  ">

    <div class="panel panel-custom">
        <header class="panel-heading ">
            <div class="panel-title"><strong><?= lang('refund_items') . ' ' . $invoice_info->reference_no ?></strong>
            </div>
        </header>
        <?php
        if (is_file(config_item('invoice_logo'))) {
            $img = base_url() . config_item('invoice_logo');
        } else {
            $img = base_url() . 'uploads/default_logo.png';
        }
        ?>
        <div class="row">
            <div class="col-lg-6 hidden-xs">
                <img class="pl-lg" style="width: 233px;height: 120px;"
                     src="<?= $img ?>">
            </div>
            <div class="col-lg-6 col-xs-12 ">
                <?php
                $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
                if (!empty($client_info)) {
                    $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
                    $client_lang = $client_info->language;
                } else {
                    $client_lang = 'english';
                    $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                }
                unset($this->lang->is_loaded[5]);
                $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
                $payment_status = $this->invoice_model->get_payment_status($invoice_info->invoices_id);
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
                    <h4 class="mb0"><?= lang('invoice') . ' : ' . $invoice_info->reference_no ?></h4>
                    <?= $language_info['invoice_date'] ?>
                    : <?= strftime(config_item('date_format'), strtotime($invoice_info->invoice_date)); ?>
                    <br><?= $language_info['due_date'] ?>
                    : <?= strftime(config_item('date_format'), strtotime($invoice_info->due_date)); ?>
                    <?php if (!empty($invoice_info->user_id)) { ?>
                        <br><?= lang('sales') . ' ' . lang('agent') ?>:<?php echo fullname($invoice_info->user_id); ?>
                    <?php }
                    if ($payment_status == lang('fully_paid')) {
                        $label = "success";
                    } elseif ($payment_status == lang('draft')) {
                        $label = "default";
                        $text = lang('status_as_draft');
                    } elseif ($payment_status == lang('cancelled')) {
                        $label = "danger";
                    } elseif ($payment_status == lang('partially_paid')) {
                        $label = "warning";
                    } elseif ($invoice_info->emailed == 'Yes') {
                        $label = "info";
                        $payment_status = lang('sent');
                    } else {
                        $label = "danger";
                    }
                    ?>
                    <br><?= $language_info['payment_status'] ?>: <span
                            class="label label-<?= $label ?>"><?= $payment_status ?></span>
                    <?php if (!empty($text)) { ?>
                        <br><p
                                style="padding: 15px;margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;;background: color: #8a6d3b;background-color: #fcf8e3;border-color: #faebcc;"><?= $text ?></p>
                    <?php } ?>
                    <?php $show_custom_fields = custom_form_label(9, $invoice_info->invoices_id);

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
            .dropdown-menu > li > a {
                white-space: normal;
            }

            .dragger {
                background: url(../../assets/img/dragger.png) 10px 32px no-repeat;
                cursor: pointer;
            }

            <?php if (!empty($invoice_info)) { ?>
            .dragger {
                background: url(../../../../assets/img/dragger.png) 10px 32px no-repeat;
                cursor: pointer;
            }

            <?php }?>
            .input-transparent {
                box-shadow: none;
                outline: 0;
                border: 0 !important;
                background: 0 0;
                padding: 3px;
            }

        </style>
        <?php
        $saved_items = $this->invoice_model->get_all_items();
        ?>
        <div class="">
            <div class="table-responsive s_table">
                <table class="table invoice-items-table items">
                    <thead style="background: #e8e8e8">
                    <tr>
                        <th></th>
                        <th><?= $language_info['item_name'] ?></th>
                        <th><?= $language_info['description'] ?></th>
                        <?php
                        $invoice_view = config_item('invoice_view');
                        if (!empty($invoice_view) && $invoice_view == '2') {
                            ?>
                            <th class="col-sm-2"><?= $language_info['hsn_code'] ?></th>
                        <?php } ?>
                        <?php
                        $qty_heading = $language_info['qty'];
                        if (isset($invoice_info) && $invoice_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                            $qty_heading = lang('hours');
                        } else if (isset($invoice_info) && $invoice_info->show_quantity_as == 'qty_hours') {
                            $qty_heading = lang('qty') . '/' . lang('hours');
                        }
                        ?>
                        <th class="qty col-sm-1"><?php echo $qty_heading; ?></th>
                        <th class="col-sm-2"><?= $language_info['price'] ?></th>
                        <th class="col-sm-2"><?= $language_info['tax_rate'] ?> </th>
                        <th class="col-sm-1"><?= $language_info['total'] ?></th>
                        <th class="hidden-print"><?= $language_info['action'] ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($invoice_info)) {
                        echo form_hidden('merge_current_invoice', $invoice_info->invoices_id);
                        echo form_hidden('isedit', $invoice_info->invoices_id);
                    }
                    ?>
                    <tr class="main">

                    </tr>
                    <?php if (isset($invoice_info) || isset($add_items)) {
                        $i = 1;
                        $items_indicator = 'items';
                        if (isset($invoice_info)) {
                            $add_items = $this->invoice_model->ordered_items_by_id($invoice_info->invoices_id);
                            $items_indicator = 'items';
                        }

                        foreach ($add_items as $item) {
                            $manual = false;
                            $table_row = '<tr class="sortable item">';
                            $table_row .= '<td class="dragger">';
                            if (!is_numeric($item->quantity)) {
                                $item->quantity = 1;
                            }
                            $invoice_item_taxes = $this->invoice_model->get_invoice_item_taxes($item->items_id);
                            $item_tax_name = json_decode($item->item_tax_name);
                            // passed like string
                            if ($item->items_id == 0) {
                                $invoice_item_taxes = $invoice_item_taxes[0];
                                $manual = true;
                            }
                            $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][items_id]', $item->items_id);
                            $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][saved_items_id]', $item->saved_items_id);
                            $amount = $item->unit_cost * $item->quantity;
                            $amount = ($amount);
                            // order input
                            $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]"><input type="hidden" name="items_id[]" value="' . $item->items_id . '"><input type="hidden" name="saved_items_id[]" value="' . $item->saved_items_id . '">';
                            $table_row .= '</td>';
                            $table_row .= '<td class="bold item_name">' . $item->item_name . '</td>';
                            $table_row .= '<td>' . $item->item_desc . '</td>';
                            $invoice_view = config_item('invoice_view');
                            if (!empty($invoice_view) && $invoice_view == '2') {
                                $table_row .= '<td>' . $item->hsn_code . '</td>';
                            }
                            $table_row .= '<td><input type="text" data-parsley-type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][quantity]" value="' . $item->quantity . '" class="form-control">';
                            $unit_placeholder = '';
                            if (!$item->unit) {
                                $unit_placeholder = lang('unit');
                                $item->unit = '';
                            }
                            $table_row .= '' . $item->unit . '';
                            $table_row .= '</td>';
                            $table_row .= '<td class="rate">' . $item->unit_cost . '<input type="hidden" data-parsley-type="number" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][unit_cost]" value="' . $item->unit_cost . '" class="form-control"></td>';
                            $tax_info = null;
                            if (!empty($item_tax_name)) {
                                foreach ($item_tax_name as $v_tax_name) {
                                    $i_tax_name = explode('|', $v_tax_name);
                                    $tax_info .= '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                                }
                            }
                            $table_row .= '<td class="taxrate">' . '<span class="hidden">' . $this->admin_model->get_taxes_dropdown('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item->items_id, true, $manual) . '</span>' . $tax_info . '</td>';
                            $table_row .= '<td class="amount">' . $amount . '</td>';
                            $table_row .= '<td><a href="#" class="btn-xs btn btn-danger pull-left" onclick="delete_item(this,' . $item->items_id . '); return false;"><i class="fa fa-trash"></i></a></td>';
                            $table_row .= '</tr>';
                            echo $table_row;
                            $i++;
                        }
                    }
                    ?>

                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-sm-6 ">
                    <div class="form-group ">
                        <label class="col-lg-2 control-label"><?= lang('notes') ?> </label>
                        <div class="col-lg-10 row">
                            <textarea name="notes" class="textarea"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 pull-right">
                    <table class="table text-right">
                        <tbody>
                        <tr id="subtotal">
                            <td><span class="bold"><?php echo lang('sub_total'); ?> :</span>
                            </td>
                            <td class="subtotal">
                            </td>
                        </tr>
                        <tr id="discount_percent">
                            <td>
                                <div class="row">
                                    <div class="col-md-7 pull-right"><span
                                                class="bold"><?php echo lang('discount'); ?>
                                                                (%)</span>
                                    </div>
                                    <div class="col-md-5">
                                        <?php
                                        $discount_percent = 0;
                                        if (isset($invoice_info)) {
                                            if ($invoice_info->discount_percent != 0) {
                                                $discount_percent = $invoice_info->discount_percent;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td class="discount_percent"></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-7 pull-right">
                                        <span class="bold"><?php echo lang('adjustment'); ?></span>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="hidden" data-parsley-type="number"
                                               value="<?php if (isset($invoice_info)) {
                                                   echo $invoice_info->adjustment;
                                               } else {
                                                   echo 0;
                                               } ?>" class="form-control pull-left"
                                               name="adjustment">
                                    </div>
                                </div>
                            </td>
                            <td class="adjustment"></td>
                        </tr>
                        <tr>
                            <td><span class="bold"><?php echo lang('total'); ?> :</span>
                            </td>
                            <td class="total">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="removed-items"></div>
            <div class="btn-bottom-toolbar text-right">
                <?php
                if (!empty($invoice_info)) { ?>
                    <button type="submit"
                            class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                    <button type="button" onclick="goBack()"
                            class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                <?php } else {
                    ?>
                    <input type="submit" value="<?= lang('save_as_draft') ?>" name="save_as_draft"
                           class="btn btn-primary">
                    <input type="submit" value="<?= lang('update') ?>" name="update"
                           class="btn btn-success">
                <?php }
                ?>
            </div>
        </div>
</form>


<script type="text/javascript">
    function slideToggle($id) {
        $('#quick_state').attr('data-original-title', '<?= lang('view_quick_state') ?>');
        $($id).slideToggle("slow");
    }

    $(document).ready(function () {
        $("#select_all_tasks").click(function () {
            $(".tasks_list").prop('checked', $(this).prop('checked'));
        });
        $("#select_all_expense").click(function () {
            $(".expense_list").prop('checked', $(this).prop('checked'));
        });
        $('[data-toggle="popover"]').popover();

        $('#start_recurring').click(function () {
            if ($('#show_recurring').is(":visible")) {
                $('#recuring_frequency').prop('disabled', true);
            } else {
                $('#recuring_frequency').prop('disabled', false);
            }
            $('#show_recurring').slideToggle("fast");
            $('#show_recurring').removeClass("hide");
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        init_items_sortable();

    });
</script>