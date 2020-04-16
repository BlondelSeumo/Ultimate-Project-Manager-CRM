<?= message_box('success') ?>
<?= message_box('error');
$edited = can_action('152', 'edited');
$deleted = can_action('152', 'deleted');
$paid_amount = $this->return_stock_model->calculate_to('paid_amount', $return_stock_info->return_stock_id);
$payment_status = $this->return_stock_model->get_payment_status($return_stock_info->return_stock_id);
$currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
?>

<div class="row mb">
    <div class="col-sm-10">
        <?php $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $return_stock_info->return_stock_id));
        if (!empty($can_edit) && !empty($edited)) { ?>
            <?php if ($this->return_stock_model->get_return_stock_cost($return_stock_info->return_stock_id) > 0) {
                ?>
                <?php if ($return_stock_info->status == 'cancelled') {
                    $disable = 'disabled';
                    $p_url = '';
                } else {
                    $disable = false;
                    $p_url = base_url() . 'admin/return_stock/payment/' . $return_stock_info->return_stock_id;
                } ?>
                <a class="btn btn-xs btn-danger <?= $disable ?>" data-toggle="tooltip" data-placement="top"
                   href="<?= $p_url ?>"
                   title="<?= lang('received') . ' ' . lang('return_stock') ?>"><i
                            class="fa fa-credit-card"></i> <?= lang('received') . ' ' . lang('return_stock') ?>
                </a>
                <?php
            }
            ?>
            <?php
        }
        ?>
        <?php
        if (!empty($can_edit) && !empty($edited)) { ?>
            <span data-toggle="tooltip" data-placement="top" title="<?= lang('clone') . ' ' . lang('return_stock') ?>">
            <a data-toggle="modal" data-target="#myModal" title="<?= lang('clone') . ' ' . lang('return_stock') ?>"
               href="<?= base_url() ?>admin/return_stock/clone_return_stock/<?= $return_stock_info->return_stock_id ?>"
               class="btn btn-xs btn-purple">
                <i class="fa fa-copy"></i> <?= lang('clone') ?></a>
            </span>
            <?php
        }
        ?>


        <div class="btn-group">
            <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                <?= lang('more_actions') ?>
                <span class="caret"></span></button>
            <ul class="dropdown-menu animated zoomIn">
                <?php if ($this->return_stock_model->get_return_stock_cost($return_stock_info->return_stock_id) > 0) { ?>
                    <li>
                        <a href="<?= base_url() ?>admin/return_stock/send_return_stock_email/<?= $return_stock_info->return_stock_id ?>"
                           title="<?= lang('send') . ' ' . lang('return_stock') . ' ' . lang('email') ?>"><?= lang('send') . ' ' . lang('return_stock') . ' ' . lang('email') ?></a>
                    </li>
                    <?php if ($return_stock_info->emailed != 'Yes') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/return_stock/change_status/mark_as_sent/<?= $return_stock_info->return_stock_id ?>"
                               title="<?= lang('mark_as_sent') ?>"><?= lang('mark_as_sent') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($return_stock_info->status == 'cancelled' || $return_stock_info->status == 'draft') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/return_stock/change_status/pending/<?= $return_stock_info->return_stock_id ?>"
                               title="<?= lang('mark_as_pending') ?>"><?= lang('mark_as_pending') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($return_stock_info->update_stock != 'Yes') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/return_stock/change_status/draft/<?= $return_stock_info->return_stock_id ?>"
                               title="<?= lang('mark_as_draft') ?>"><?= lang('mark_as_draft') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($paid_amount <= 0) {
                        ?>
                        <?php if ($return_stock_info->status != 'cancelled') { ?>
                            <li>
                                <a href="<?= base_url() ?>admin/return_stock/change_status/cancelled/<?= $return_stock_info->return_stock_id ?>"
                                   title="<?= lang('mark_as_cancelled') ?>"><?= lang('mark_as_cancelled') ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($return_stock_info->status == 'cancelled') { ?>
                            <li>
                                <a href="<?= base_url() ?>admin/return_stock/change_status/unmark_as_cancelled/<?= $return_stock_info->return_stock_id ?>"
                                   title="<?= lang('unmark_as_cancelled') ?>"><?= lang('unmark_as_cancelled') ?></a>
                            </li>
                        <?php }
                    }
                    ?>
                    <?php
                    if ($payment_status != 'fully_paid') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/return_stock/change_status/accepted/<?= $return_stock_info->return_stock_id ?>"
                               title="<?= lang('mark_as') . ' ' . lang('accepted') ?>"><?= lang('mark_as') . ' ' . lang('accepted') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/return_stock/change_status/declined/<?= $return_stock_info->return_stock_id ?>"
                               title="<?= lang('mark_as') . ' ' . lang('declined') ?>"><?= lang('mark_as') . ' ' . lang('declined') ?></a>
                        </li>
                    <?php } ?>
                <?php } ?>

                <?php
                if (!empty($can_edit) && !empty($edited)) { ?>
                    <li class="divider"></li>
                    <li>
                        <a href="<?= base_url() ?>admin/return_stock/index/<?= $return_stock_info->return_stock_id ?>"><?= lang('edit') . ' ' . lang('return_stock') ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-sm-2 pull-right">
        <a
                href="<?= base_url() ?>admin/return_stock/send_return_stock_email/<?= $return_stock_info->return_stock_id ?>"
                data-toggle="tooltip" data-placement="top" title="<?= lang('send_email') ?>"
                class="btn btn-xs btn-primary pull-right">
            <i class="fa fa-envelope-o"></i>
        </a>
        <a onclick="print_return_stock('print_return_stock')" href="#" data-toggle="tooltip" data-placement="top"
           title=""
           data-original-title="Print" class="mr-sm btn btn-xs btn-danger pull-right">
            <i class="fa fa-print"></i>
        </a>
        <a href="<?= base_url() ?>admin/return_stock/pdf_return_stock/<?= $return_stock_info->return_stock_id ?>"
           data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF"
           class="btn btn-xs btn-success pull-right mr-sm">
            <i class="fa fa-file-pdf-o"></i>
        </a>
        <a href="<?= base_url() ?>admin/return_stock/index/<?= $return_stock_info->return_stock_id ?>"
           data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= lang('edit') ?>"
           class="btn btn-xs btn-primary pull-right mr-sm">
            <i class="fa fa-pencil-square-o"></i>
        </a>
    </div>
</div>
<?php if (strtotime($return_stock_info->due_date) < time() AND $payment_status != lang('fully_paid')) {
    $start = strtotime(date('Y-m-d H:i'));
    $end = strtotime($return_stock_info->due_date);

    $days_between = ceil(abs($end - $start) / 86400);
    ?>
    <div class="alert bg-danger-light hidden-print">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-warning"></i>
        <?= lang('invoice_overdue') . ' ' . lang('by') . ' ' . $days_between . ' ' . lang('days') ?>
    </div>
    <?php
}
?>
<div class="panel" id="print_return_stock">
    <div class="panel-body mt-lg">
        <div class="row">
            <div class="col-lg-6 col-xs-6">
                <img class="pl-lg" style="width: auto;height: 64px;"
                     src="<?= base_url() . config_item('invoice_logo') ?>">
            </div>
            <div class="col-lg-6 col-xs-6 ">
                <div class="pull-right pr-lg">
                    <h4 class="mb0"><?= lang('return_stock') . ' : ' . $return_stock_info->reference_no ?></h4>
                    <?php if (!empty($return_stock_info->invoices_id)) {
                        if ($return_stock_info->module == 'supplier') {
                            $inv_info = get_any_field('tbl_purchases', array('purchase_id' => $return_stock_info->invoices_id), 'reference_no');
                            $text = lang('purchase') . ' ' . lang('no');
                            $p_url = base_url("admin/purchase/purchase_details/$return_stock_info->invoices_id");
                        } else {
                            $inv_info = get_any_field('tbl_invoices', array('invoices_id' => $return_stock_info->invoices_id), 'reference_no');
                            $text = lang('invoice') . ' ' . lang('no');
                            $p_url = base_url("admin/invoice/manage_invoice/invoice_details/$return_stock_info->invoices_id");
                        }
                        ?>
                        <strong class="mb0 block"><?= $text . ' : <a href="' . $p_url . '">' . $inv_info ?></a></strong>
                    <?php } ?>
                    <?= lang('return_stock') . ' ' . lang('date') ?>
                    : <?= display_date($return_stock_info->return_stock_date); ?>
                    <br><?= lang('due_date') ?>
                    : <?= display_date($return_stock_info->due_date); ?>
                    <?php if (!empty($return_stock_info->user_id)) { ?>
                        <br><?= lang('sales') . ' ' . lang('agent') ?>: <?php echo fullname($return_stock_info->user_id); ?>
                    <?php }
                    if ($payment_status == ('fully_paid')) {
                        $label = "success";
                    } elseif ($payment_status == ('draft')) {
                        $label = "default";
                    } elseif ($payment_status == ('cancelled')) {
                        $label = "danger";
                    } elseif ($payment_status == ('not_paid')) {
                        $label = "danger";
                    } elseif ($payment_status == ('partially_paid')) {
                        $label = "warning";
                    } elseif ($return_stock_info->emailed == 'Yes') {
                        $label = "info";
                    } else {
                        $label = "danger";
                    }

                    if ($return_stock_info->main_status == 'Pending') {
                        $m_label = "warning";
                    } elseif ($return_stock_info->main_status == 'accepted') {
                        $m_label = "success";
                    } else {
                        $m_label = "danger";

                    }
                    ?>
                    <br><?= lang('status') ?>: <span
                            class="label label-<?= $m_label ?>"><?= lang($return_stock_info->main_status) ?></span>
                    <br><?= lang('payment_status') ?>: <span
                            class="label label-<?= $label ?>"><?= lang($payment_status) ?></span>
                </div>
            </div>

        </div>

        <div class="row mb-lg">
            <div class="col-lg-6 col-xs-6">
                <h5 class="p-md bg-items mr-15">
                    <?= lang('our_info') ?>:
                </h5>
                <div class="pl-sm">
                    <h4 class="mb0"><?= config_item('company_legal_name') ?></h4>
                    <?= config_item('company_address') ?>
                    <br><?= config_item('company_city') ?>
                    , <?= config_item('company_zip_code') ?>
                    <br><?= config_item('company_country') ?>
                    <br/><?= lang('phone') ?> : <?= config_item('company_phone') ?>
                    <br/><?= lang('vat_number') ?> : <?= config_item('company_vat') ?>
                </div>
            </div>
            <div class="col-lg-6 col-xs-6 ">
                <h5 class="p-md bg-items ml-13">
                    <?= lang($return_stock_info->module) . ' ' . lang('info') ?>:
                </h5>
                <div class="pl-sm">
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
                        $zipcode = $supplier_info->email;

                    } else {
                        $client_name = '-';
                        $address = '-';
                        $mobile = '-';
                        $zipcode = '-';
                        $country = '-';
                        $phone = '-';
                    }
                    ?>
                    <h4 class="mb0"><?= $client_name ?></h4>
                    <?= $address ?>
                    <br> <?= $zipcode ?>
                    <br><?= lang('phone') ?>: <?= $phone ?>
                    <br><?= lang('mobile') ?>: <?= $mobile ?>
                    <?php if (!empty($supplier_info->tax)) { ?>
                        <br><?= lang('tax') ?>: <?= $supplier_info->tax ?>
                    <?php } ?>
                </div>
            </div>

        </div>
        <style type="text/css">
            .dragger {
                background: url(<?= base_url()?>assets/img/dragger.png) 0px 11px no-repeat;
                cursor: pointer;
            }

            .table > tbody > tr > td {
                vertical-align: initial;
            }
        </style>

        <div class="table-responsive mb-lg">
            <table class="table items return_stock-items-preview" page-break-inside: auto;>
                <thead class="bg-items">
                <tr>
                    <th>#</th>
                    <th><?= lang('items') ?></th>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <th><?= lang('hsn_code') ?></th>
                    <?php } ?>
                    <?php
                    $qty_heading = lang('qty');
                    if (isset($return_stock_info) && $return_stock_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                        $qty_heading = lang('hours');
                    } else if (isset($return_stock_info) && $return_stock_info->show_quantity_as == 'qty_hours') {
                        $qty_heading = lang('qty') . '/' . lang('hours');
                    }
                    ?>
                    <th><?php echo $qty_heading; ?></th>
                    <th class="col-sm-1"><?= lang('price') ?></th>
                    <th class="col-sm-2"><?= lang('tax') ?></th>
                    <th class="col-sm-1"><?= lang('total') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $invoice_items = $this->return_stock_model->ordered_items_by_id($return_stock_info->return_stock_id);
                if (!empty($invoice_items)) :
                    foreach ($invoice_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : strip_html_tags($v_item->item_desc);
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->items_id ?>">
                            <td class="item_no dragger pl-lg"><?= $key + 1 ?></td>
                            <td><strong class="block"><?= $item_name ?></strong>
                                <?= strip_html_tags($v_item->item_desc) ?>
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
                    <?= $return_stock_info->notes ?>
                </p>
            </div>
            <div class="col-sm-4 pv">
                <div class="clearfix">
                    <p class="pull-left"><?= lang('sub_total') ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->return_stock_model->calculate_to('return_stock_cost', $return_stock_info->return_stock_id)); ?>
                    </p>
                </div>
                <?php if ($return_stock_info->discount_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('discount') ?>
                            (<?php echo $return_stock_info->discount_percent; ?>
                            %)</p>
                        <p class="pull-right mr">
                            <?= display_money($this->return_stock_model->calculate_to('discount', $return_stock_info->return_stock_id)); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php
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
                        <p class="pull-left"><?= lang('total') . ' ' . lang('tax') ?></p>
                        <p class="pull-right mr">
                            <?= display_money($tax_total); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php if ($return_stock_info->adjustment > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('adjustment') ?></p>
                        <p class="pull-right mr">
                            <?= display_money($return_stock_info->adjustment); ?>
                        </p>
                    </div>
                <?php endif ?>

                <div class="clearfix">
                    <p class="pull-left"><?= lang('total') ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->return_stock_model->calculate_to('total', $return_stock_info->return_stock_id), $currency->symbol); ?>
                    </p>
                </div>
                <?php
                $return_stock_due = $this->return_stock_model->calculate_to('return_stock_due', $return_stock_info->return_stock_id);
                if ($paid_amount > 0) {
                    $total = lang('total_due');
                    if ($paid_amount > 0) {
                        $text = 'text-danger';
                        ?>
                        <div class="clearfix">
                            <p class="pull-left"><?= lang('paid_amount') ?> </p>
                            <p class="pull-right mr">
                                <?= display_money($paid_amount, $currency->symbol); ?>
                            </p>
                        </div>
                    <?php } else {
                        $text = '';
                    } ?>
                    <div class="clearfix">
                        <p class="pull-left h3 <?= $text ?>"><?= $total ?></p>
                        <p class="pull-right mr h3"><?= display_money($return_stock_due, $currency->symbol); ?></p>
                    </div>
                <?php } ?>
                <?php if (config_item('amount_to_words') == 'Yes') { ?>
                    <div class="clearfix">
                        <p class="pull-right h4"><strong class="h3"><?= lang('num_word') ?>
                                : </strong> <?= number_to_word('', $return_stock_due); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?= !empty($invoice_view) && $invoice_view > 0 ? $this->gst->summary($invoice_items) : ''; ?>
</div>
<?php $all_payment_info = $this->db->where('return_stock_id', $return_stock_info->return_stock_id)->get('tbl_return_stock_payments')->result();
if (!empty($all_payment_info)) { ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="panel-title"> <?= lang('payment') . ' ' . lang('details') ?></div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th><?= lang('transaction_id') ?></th>
                    <th><?= lang('payment_date') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('payment_mode') ?></th>
                    <th><?= lang('action') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($all_payment_info as $v_payments_info) {
                    $payment_methods = $this->return_stock_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');
                    ?>
                    <tr>
                        <td>
                            <a href="<?= base_url() ?>admin/return_stock/payments_details/<?= $v_payments_info->payments_id ?>"> <?= $v_payments_info->trans_id; ?></a>
                        </td>
                        <td>
                            <a href="<?= base_url() ?>admin/return_stock/payments_details/<?= $v_payments_info->payments_id ?>"><?= display_date($v_payments_info->payment_date); ?></a>
                        </td>
                        <td><?= display_money($v_payments_info->amount, $currency->symbol) ?></td>
                        <td><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-'; ?></td>
                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                            <td>
                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                    <?= btn_edit('admin/return_stock/all_payments/' . $v_payments_info->payments_id) ?>
                                <?php }
                                if (!empty($can_delete) && !empty($deleted)) {
                                    ?>
                                    <?= btn_delete('admin/return_stock/delete_payment/' . $v_payments_info->payments_id) ?>
                                <?php } ?>
                                <a data-toggle="tooltip" data-placement="top"
                                   href="<?= base_url() ?>admin/return_stock/send_payment/<?= $v_payments_info->payments_id . '/' . $v_payments_info->amount ?>"
                                   title="<?= lang('send_email') ?>"
                                   class="btn btn-xs btn-success">
                                    <i class="fa fa-envelope"></i> </a>
                                <a data-toggle="tooltip" data-placement="top"
                                   href="<?= base_url() ?>admin/return_stock/payments_pdf/<?= $v_payments_info->payments_id ?>"
                                   title="<?= lang('pdf') ?>"
                                   class="btn btn-xs btn-warning">
                                    <i class="fa fa-file-pdf-o"></i></a>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php
                } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>
<?php include_once 'assets/js/sales.php'; ?>

<script type="text/javascript">
    $(document).ready(function () {
        init_items_sortable(true);
    });

    function print_return_stock(print_return_stock) {
        var printContents = document.getElementById(print_return_stock).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
