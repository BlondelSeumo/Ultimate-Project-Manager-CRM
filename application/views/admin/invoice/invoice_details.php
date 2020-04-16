<?= message_box('success') ?>
<?= message_box('error');
$edited = can_action('13', 'edited');
$deleted = can_action('13', 'deleted');
$paid_amount = $this->invoice_model->calculate_to('paid_amount', $invoice_info->invoices_id);
$payment_status = $this->invoice_model->get_payment_status($invoice_info->invoices_id);
?>
<?php if ($payment_status != lang('cancelled') && $payment_status != lang('fully_paid') && !empty($total_available_credit)) { ?>
    <div class="alert text-success btn-outline-success bg-white">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-info text-warning"></i>
        <?= lang('total_credit_available', display_money($total_available_credit, client_currency($invoice_info->client_id))); ?>

        <a data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/invoice/invoices_credit/<?= $invoice_info->invoices_id ?>"
           title="<?= lang('apply_credits') ?>" class="text-info"><?= lang('apply_credits') ?></a>
    </div>
<?php } ?>
<div class="row mb">
    <div class="col-sm-12 mb">
        <div class="pull-left">
            <?= lang('copy_unique_url') ?>
        </div>
        <div class="col-sm-10">
            <input style="width: 100%"
                   value="<?= base_url() ?>frontend/view_invoice/<?= url_encode($invoice_info->invoices_id); ?>"
                   type="text" id="foo"/>
        </div>
    </div>
    <script type="text/javascript">
        var textBox = document.getElementById("foo");
        textBox.onfocus = function () {
            textBox.select();
            // Work around Chrome's little problem
            textBox.onmouseup = function () {
                // Prevent further mouseup intervention
                textBox.onmouseup = null;
                return false;
            };
        };
    </script>
    <div class="col-sm-10">
        <?php
        $where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $invoice_info->invoices_id, 'module_name' => 'invoice');
        $check_existing = $this->invoice_model->check_by($where, 'tbl_pinaction');
        if (!empty($check_existing)) {
            $url = 'remove_todo/' . $check_existing->pinaction_id;
            $btn = 'danger';
            $title = lang('remove_todo');
        } else {
            $url = 'add_todo_list/invoice/' . $invoice_info->invoices_id;
            $btn = 'warning';
            $title = lang('add_todo_list');
        }

        $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
        if (!empty($client_info)) {
            $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
            $client_lang = $client_info->language;
        } else {
            $client_lang = 'english';
            $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        }
        //        unset($this->lang->is_loaded[5]);
        $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
        ?>
        <?php $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $invoice_info->invoices_id));
        if (!empty($can_edit) && !empty($edited)) { ?>
            <span data-toggle="tooltip" data-placement="top" title="<?= lang('from_items') ?>">
            <a data-toggle="modal" data-target="#myModal_lg"
               href="<?= base_url() ?>admin/invoice/insert_items/<?= $invoice_info->invoices_id ?>"
               title="<?= lang('item_quick_add') ?>" class="btn btn-xs btn-primary">
                <i class="fa fa-pencil text-white"></i> <?= lang('add_items') ?></a>
                </span>
        <?php }
        ?>
        <?php
        if (!empty($can_edit) && !empty($edited)) { ?>
            <?php if ($invoice_info->show_client == 'Yes') { ?>
            <a class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top"
               href="<?= base_url() ?>admin/invoice/change_status/hide/<?= $invoice_info->invoices_id ?>"
               title="<?= lang('hide_to_client') ?>"><i class="fa fa-eye-slash"></i> <?= lang('hide_to_client') ?>
                </a><?php } else { ?>
            <a class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="top"
               href="<?= base_url() ?>admin/invoice/change_status/show/<?= $invoice_info->invoices_id ?>"
               title="<?= lang('show_to_client') ?>"><i class="fa fa-eye"></i> <?= lang('show_to_client') ?>
                </a><?php }
        } ?>

        <?php
        if (!empty($can_edit) && !empty($edited)) { ?>
            <?php if ($this->invoice_model->get_invoice_cost($invoice_info->invoices_id) > 0) {
                ?>
                <?php if ($invoice_info->status == 'Cancelled') {
                    $disable = 'disabled';
                    $p_url = '';
                } else {
                    $disable = false;
                    $p_url = base_url() . 'admin/invoice/manage_invoice/payment/' . $invoice_info->invoices_id;
                } ?>
                <a class="btn btn-xs btn-danger <?= $disable ?>" data-toggle="tooltip" data-placement="top"
                   href="<?= $p_url ?>"
                   title="<?= lang('add_payment') ?>"><i class="fa fa-credit-card"></i> <?= lang('pay_invoice') ?>
                </a>
                <?php
            }
            if (!empty($all_payments_history)) {
                ?>
                <a class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top"
                   href="<?= base_url('admin/invoice/manage_invoice/payment_history/' . $invoice_info->invoices_id) ?>"
                   title="<?= lang('payment_history_for_this_invoice') ?>"><i
                        class="fa fa fa-money"></i> <?= lang('histories') ?>
                </a>
            <?php }
        }
        ?>
        <?php
        if (!empty($can_edit) && !empty($edited)) { ?>
            <span data-toggle="tooltip" data-placement="top" title="<?= lang('clone') . ' ' . lang('invoice') ?>">
            <a data-toggle="modal" data-target="#myModal" title="<?= lang('clone') . ' ' . lang('invoice') ?>"
               href="<?= base_url() ?>admin/invoice/clone_invoice/<?= $invoice_info->invoices_id ?>"
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
                <?php if ($this->invoice_model->get_invoice_cost($invoice_info->invoices_id) > 0) { ?>
                    <li>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/email_invoice/<?= $invoice_info->invoices_id ?>"
                           title="<?= lang('email_invoice') ?>"><?= lang('email_invoice') ?></a>
                    </li>

                    <li>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/send_reminder/<?= $invoice_info->invoices_id ?>"
                           title="<?= lang('send_reminder') ?>"><?= lang('send_reminder') ?></a>
                    </li>
                    <?php if (strtotime($invoice_info->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/send_overdue/<?= $invoice_info->invoices_id ?>"
                               title="<?= lang('send_invoice_overdue') ?>"><?= lang('send_invoice_overdue') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($invoice_info->emailed != 'Yes') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/invoice/change_invoice_status/mark_as_sent/<?= $invoice_info->invoices_id ?>"
                               title="<?= lang('mark_as_sent') ?>"><?= lang('mark_as_sent') ?></a>
                        </li>
                    <?php }
                    if ($paid_amount <= 0) {
                        ?>
                        <?php if ($invoice_info->status != 'Cancelled') { ?>
                            <li>
                                <a href="<?= base_url() ?>admin/invoice/change_invoice_status/mark_as_cancelled/<?= $invoice_info->invoices_id ?>"
                                   title="<?= lang('mark_as_cancelled') ?>"><?= lang('mark_as_cancelled') ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($invoice_info->status == 'Cancelled') { ?>
                            <li>
                                <a href="<?= base_url() ?>admin/invoice/change_invoice_status/unmark_as_cancelled/<?= $invoice_info->invoices_id ?>"
                                   title="<?= lang('unmark_as_cancelled') ?>"><?= lang('unmark_as_cancelled') ?></a>
                            </li>
                        <?php }
                    }
                    ?>
                    <li>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_history/<?= $invoice_info->invoices_id ?>"><?= lang('invoice_history') ?></a>
                    </li>
                <?php } ?>

                <?php
                if (!empty($can_edit) && !empty($edited)) { ?>
                    <li class="divider"></li>
                    <li>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/create_invoice/<?= $invoice_info->invoices_id ?>"><?= lang('edit_invoice') ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?php
        if (!empty($can_edit) && !empty($edited)) { ?>
            <?php if ($invoice_info->recurring == 'Yes') { ?>
                <a onclick="return confirm('<?= lang('stop_recurring_alert') ?>')" class="btn btn-xs btn-warning"
                   href="<?= base_url() ?>admin/invoice/stop_recurring/<?= $invoice_info->invoices_id ?>"
                   title="<?= lang('stop_recurring') ?>"><i class="fa fa-retweet"></i> <?= lang('stop_recurring') ?>
                </a>
            <?php }
        } ?>
        <?php
        if (!empty($invoice_info->project_id)) {
            $project_info = $this->db->where('project_id', $invoice_info->project_id)->get('tbl_project')->row();
            ?>
            <strong><?= lang('project') ?>:</strong>
            <a
                href="<?= base_url() ?>admin/projects/project_details/<?= $invoice_info->project_id ?>"
                class="">
                <?= $project_info->project_name ?>
            </a>
        <?php }
        ?>

        <?php
        $notified_reminder = count($this->db->where(array('module' => 'invoice', 'module_id' => $invoice_info->invoices_id, 'notified' => 'No'))->get('tbl_reminders')->result());
        ?>
        <a class="btn btn-xs btn-green" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/invoice/reminder/invoice/<?= $invoice_info->invoices_id ?>"><?= lang('reminder') ?>
            <?= !empty($notified_reminder) ? '<span class="badge ml-sm" style="border-radius: 50%">' . $notified_reminder . '</span>' : '' ?>
        </a>
        <?php
        $credit_used = count(get_result('tbl_credit_used', array('invoices_id' => $invoice_info->invoices_id)));
        ?>
        <a class="btn btn-xs btn-info" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/invoice/applied_credits/<?= $invoice_info->invoices_id ?>"><?= lang('applied_credits') ?>
            <?= !empty($credit_used) ? '<span class="badge ml-sm" style="border-radius: 50%">' . $credit_used . '</span>' : '' ?>
        </a>
    </div>
    <div class="col-sm-2 pull-right">
        <a
            href="<?= base_url() ?>admin/invoice/send_invoice_email/<?= $invoice_info->invoices_id . '/' . true ?>"
            data-toggle="tooltip" data-placement="top" title="<?= lang('send_email') ?>"
            class="btn btn-xs btn-primary pull-right">
            <i class="fa fa-envelope-o"></i>
        </a>
        <a onclick="print_invoice('print_invoice')" href="#" data-toggle="tooltip" data-placement="top" title=""
           data-original-title="Print" class="mr-sm btn btn-xs btn-danger pull-right">
            <i class="fa fa-print"></i>
        </a>
        <a href="<?= base_url() ?>admin/invoice/pdf_invoice/<?= $invoice_info->invoices_id ?>"
           data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF"
           class="btn btn-xs btn-success pull-right mr-sm">
            <i class="fa fa-file-pdf-o"></i>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
           href="<?= base_url() ?>admin/projects/<?= $url ?>"
           class="mr-sm btn pull-right  btn-xs  btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
    </div>
</div>
<?php if (strtotime($invoice_info->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
    $start = strtotime(date('Y-m-d'));
    $end = strtotime($invoice_info->due_date);

    $days_between = ceil(abs($end - $start) / 86400);
    ?>
    <div class="alert bg-danger-light hidden-print">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-warning"></i>
        <?= lang('invoice_overdue') . ' ' . lang('by') . ' ' . $days_between . ' ' . lang('days') ?>
    </div>
    <?php
}
if (is_file(config_item('invoice_logo'))) {
    $img = base_url() . config_item('invoice_logo');
} else {
    $img = base_url() . 'uploads/default_logo.png';
}
?>

<div class="panel" id="print_invoice">
    <div class="panel-body mt-lg">
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
                    <br><?= lang('status') ?>: <span
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
            .dragger {
                background: url(../../../../assets/img/dragger.png) 0px 11px no-repeat;
                cursor: pointer;
            }

            .table > tbody > tr > td {
                vertical-align: initial;
            }
        </style>

        <div class="table-responsive mb-lg">
            <table class="table items invoice-items-preview" page-break-inside: auto;>
                <thead class="bg-items">
                <tr>
                    <th>#</th>
                    <th><?= $language_info['items'] ?></th>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <th><?= $language_info['hsn_code'] ?></th>
                    <?php } ?>
                    <?php
                    $qty_heading = $language_info['qty'];
                    if (isset($invoice_info) && $invoice_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                        $qty_heading = lang('hours');
                    } else if (isset($invoice_info) && $invoice_info->show_quantity_as == 'qty_hours') {
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
                $invoice_items = $this->invoice_model->ordered_items_by_id($invoice_info->invoices_id);

                if (!empty($invoice_items)) :
                    foreach ($invoice_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->items_id ?>">
                            <td class="item_no dragger pl-lg"><?= $key + 1 ?></td>
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
                    <?= $invoice_info->notes ?>
                </p>
            </div>
            <div class="col-sm-4 pv">
                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['sub_total'] ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->invoice_model->calculate_to('invoice_cost', $invoice_info->invoices_id)); ?>
                    </p>
                </div>
                <?php if ($invoice_info->discount_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['discount'] ?>
                            (<?php echo $invoice_info->discount_percent; ?>
                            %)</p>
                        <p class="pull-right mr">
                            <?= display_money($this->invoice_model->calculate_to('discount', $invoice_info->invoices_id)); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php
                $tax_info = json_decode($invoice_info->total_tax);
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
                <?php if ($invoice_info->adjustment > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['adjustment'] ?></p>
                        <p class="pull-right mr">
                            <?= display_money($invoice_info->adjustment); ?>
                        </p>
                    </div>
                <?php endif ?>

                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['total'] ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->invoice_model->calculate_to('total', $invoice_info->invoices_id), $currency->symbol); ?>
                    </p>
                </div>

                <?php
                $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
                
                if ($paid_amount > 0) {
                    $total = $language_info['total_due'];
                    if ($paid_amount > 0) {
                        $text = 'text-danger';
                        ?>
                        <div class="clearfix">
                            <p class="pull-left"><?= $language_info['paid_amount'] ?> </p>
                            <p class="pull-right mr">
                                <?= display_money($paid_amount, $currency->symbol); ?>
                            </p>
                        </div>
                    <?php } else {
                        $text = '';
                    } ?>
                    <div class="clearfix">
                        <p class="pull-left h3 <?= $text ?>"><?= $total ?></p>
                        <p class="pull-right mr h3"><?= display_money(($invoice_due), $currency->symbol); ?></p>
                    </div>
                <?php } ?>
                <?php if (config_item('amount_to_words') == 'Yes') { ?>
                    <div class="clearfix">
                        <p class="pull-right h4"><strong class="h3"><?= lang('num_word') ?>
                                : </strong> <?= number_to_word($invoice_info->client_id, $invoice_due); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?= !empty($invoice_view) && $invoice_view > 0 ? $this->gst->summary($invoice_items) : ''; ?>
</div>
<?php $all_payment_info = $this->db->where('invoices_id', $invoice_info->invoices_id)->get('tbl_payments')->result();
if (!empty($all_payment_info)) { ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="panel-title"> <?= lang('payment') . ' ' . lang('details') ?></div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped " cellspacing="0" width="100%">
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
                    $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');
                    ?>
                    <tr>
                        <td>
                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payments_info->payments_id ?>"> <?= $v_payments_info->trans_id; ?></a>
                        </td>
                        <td>
                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payments_info->payments_id ?>"><?= strftime(config_item('date_format'), strtotime($v_payments_info->payment_date)); ?></a>
                        </td>
                        <td><?= display_money($v_payments_info->amount, $currency->symbol) ?></td>
                        <td><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-'; ?></td>
                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                            <td>
                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                    <?= btn_edit('admin/invoice/all_payments/' . $v_payments_info->payments_id) ?>
                                <?php }
                                if (!empty($can_delete) && !empty($deleted)) {
                                    ?>
                                    <?= btn_delete('admin/invoice/delete/delete_payment/' . $v_payments_info->payments_id) ?>
                                <?php } ?>
                                <a data-toggle="tooltip" data-placement="top"
                                   href="<?= base_url() ?>admin/invoice/send_payment/<?= $v_payments_info->payments_id . '/' . $v_payments_info->amount ?>"
                                   title="<?= lang('send_email') ?>"
                                   class="btn btn-xs btn-success">
                                    <i class="fa fa-envelope"></i> </a>
                                <a data-toggle="tooltip" data-placement="top"
                                   href="<?= base_url() ?>admin/invoice/payments_pdf/<?= $v_payments_info->payments_id ?>"
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
    function print_invoice(print_invoice) {
        var printContents = document.getElementById(print_invoice).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>