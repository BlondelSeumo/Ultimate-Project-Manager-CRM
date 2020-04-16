<?= message_box('success') ?>
<?= message_box('error');
$edited = can_action('140', 'edited');
$deleted = can_action('140', 'deleted');
?>
<div class="row mb">
    <div class="col-sm-12 mb">
        <div class="pull-left">
            <?= lang('copy_unique_url') ?>
        </div>
        <div class="col-sm-10">
            <input style="width: 100%"
                   value="<?= base_url() ?>frontend/proposals/<?= url_encode($proposals_info->proposals_id); ?>"
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

    <div class="col-sm-8">
        <?php
        $client_id = null;
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $proposals_info->proposals_id));
        $can_delete = $this->proposal_model->can_action('tbl_proposals', 'delete', array('proposals_id' => $proposals_info->proposals_id));
        if ($proposals_info->module == 'client') {
            $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
            $currency = $this->proposal_model->client_currency_symbol($proposals_info->module_id);
            $client_lang = $client_info->language;
            $client_id = $proposals_info->module_id;
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
        ?>

        <?php if (!empty($can_edit) && !empty($edited)) { ?>

            <a data-toggle="modal" data-target="#myModal_lg"
               href="<?= base_url() ?>admin/proposals/insert_items/<?= $proposals_info->proposals_id ?>"
               title="<?= lang('item_quick_add') ?>" class="btn btn-xs btn-primary">
                <i class="fa fa-pencil text-white"></i> <?= lang('add_items') ?></a>

            <?php if ($proposals_info->show_client == 'Yes') { ?>
            <a class="btn btn-xs btn-success"
               href="<?= base_url() ?>admin/proposals/change_status/hide/<?= $proposals_info->proposals_id ?>"
               title="<?= lang('hide_to_client') ?>"><i class="fa fa-eye-slash"></i> <?= lang('hide_to_client') ?>
                </a><?php } else { ?>
            <a class="btn btn-xs btn-warning"
               href="<?= base_url() ?>admin/proposals/change_status/show/<?= $proposals_info->proposals_id ?>"
               title="<?= lang('show_to_client') ?>"><i class="fa fa-eye"></i> <?= lang('show_to_client') ?>
                </a><?php }
            if ($proposals_info->convert != 'Yes') {
                ?>
                <div class="btn-group">
                    <button class="btn btn-xs btn-purple dropdown-toggle" data-toggle="dropdown">
                        <?= lang('convert') . ' ' . lang('TO') ?>
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu animated zoomIn">
                        <li>
                            <a data-toggle="modal" data-target="#myModal_large"
                               href="<?= base_url() ?>admin/proposals/convert_to/invoice/<?= $proposals_info->proposals_id ?>"
                               title="<?= lang('invoice') ?>"><?= lang('invoice') ?></a>
                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#myModal_large"
                               href="<?= base_url() ?>admin/proposals/convert_to/estimate/<?= $proposals_info->proposals_id ?>"><?= lang('estimate') ?></a>
                        </li>
                    </ul>
                </div>
            <?php } else {
                if ($proposals_info->convert_module == 'invoice') {
                    $convert_info = $this->proposal_model->check_by(array('invoices_id' => $proposals_info->convert_module_id), 'tbl_invoices');
                    $c_url = base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $proposals_info->convert_module_id;
                } else {
                    $convert_info = $this->proposal_model->check_by(array('estimates_id' => $proposals_info->convert_module_id), 'tbl_estimates');
                    $c_url = base_url() . 'admin/estimates/index/estimates_details/' . $proposals_info->convert_module_id;
                } ?>
            <?php } ?>
            <span data-toggle="tooltip" data-placement="top" title="<?= lang('clone') . ' ' . lang('proposal') ?>">
            <a data-toggle="modal" data-target="#myModal" title="<?= lang('clone') . ' ' . lang('proposal') ?>"
               href="<?= base_url() ?>admin/proposals/clone_proposal/<?= $proposals_info->proposals_id ?>"
               class="btn btn-xs btn-green">
                <i class="fa fa-copy"></i> <?= lang('clone') ?></a>
            </span>

            <div class="btn-group">
                <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                    <?= lang('more_actions') ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu animated zoomIn">
                    <li>
                        <a href="<?= base_url() ?>admin/proposals/index/email_proposals/<?= $proposals_info->proposals_id ?>"
                           data-toggle="ajaxModal"><?= lang('email_proposal') ?></a></li>
                    <li>
                        <a href="<?= base_url() ?>admin/proposals/index/proposals_history/<?= $proposals_info->proposals_id ?>"><?= lang('proposal_history') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url() ?>admin/proposals/change_status/draft/<?= $proposals_info->proposals_id ?>"
                           title="<?= lang('unmark_as_draft') ?>"><?= lang('mark_as_draft') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url() ?>admin/proposals/change_status/sent/<?= $proposals_info->proposals_id ?>"
                           title="<?= lang('mark_as_sent') ?>"><?= lang('mark_as_sent') ?></a>
                    </li>

                    <li>
                        <a href="<?= base_url() ?>admin/proposals/change_status/revised/<?= $proposals_info->proposals_id ?>"
                           title="<?= lang('mark_as_revised') ?>"><?= lang('mark_as_revised') ?></a>
                    </li>

                    <li>
                        <a href="<?= base_url() ?>admin/proposals/change_status/open/<?= $proposals_info->proposals_id ?>"
                           title="<?= lang('mark_as_open') ?>"><?= lang('mark_as_open') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url() ?>admin/proposals/change_status/declined/<?= $proposals_info->proposals_id ?>"><?= lang('declined') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url() ?>admin/proposals/change_status/accepted/<?= $proposals_info->proposals_id ?>"><?= lang('accepted') ?></a>
                    </li>
                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= base_url() ?>admin/proposals/index/edit_proposals/<?= $proposals_info->proposals_id ?>"><?= lang('edit') . ' ' . lang('proposals') ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php if (!empty($c_url)) { ?>
                <a class="btn btn-purple btn-xs" href="<?= $c_url ?>"><i
                        class="fa fa-hand-o-right"></i> <?= $convert_info->reference_no ?></a>
            <?php } ?>
        <?php } ?>
        <?php
        $notified_reminder = count($this->db->where(array('module' => 'proposal', 'module_id' => $proposals_info->proposals_id, 'notified' => 'No'))->get('tbl_reminders')->result());
        ?>
        <a class="btn btn-xs btn-green" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/invoice/reminder/proposal/<?= $proposals_info->proposals_id ?>"><?= lang('reminder') ?>
            <?= !empty($notified_reminder) ? '<span class="badge ml-sm" style="border-radius: 50%">' . $notified_reminder . '</span>' : '' ?>
        </a>


    </div>
    <div class="col-sm-4 pull-right">
        <a
            href="<?= base_url() ?>admin/proposals/send_proposals_email/<?= $proposals_info->proposals_id . '/' . true ?>"
            data-toggle="tooltip" data-placement="top" title="<?= lang('send_email') ?>"
            class="btn btn-xs btn-primary pull-right">
            <i class="fa fa-envelope-o"></i>
        </a>
        <a onclick="print_proposals('print_proposals')" href="#" data-toggle="tooltip" data-placement="top" title=""
           data-original-title="Print" class="mr-sm btn btn-xs btn-danger pull-right">
            <i class="fa fa-print"></i>
        </a>

        <a
            href="<?= base_url() ?>admin/proposals/pdf_proposals/<?= $proposals_info->proposals_id ?>"
            data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF"
            class="btn btn-xs btn-success pull-right mr-sm">
            <i class="fa fa-file-pdf-o"></i>
        </a>

    </div>
</div>
<!-- Start Display Details -->
<?php
if (strtotime($proposals_info->due_date) < strtotime(date('Y-m-d')) && $proposals_info->status == 'pending' || strtotime($proposals_info->due_date) < strtotime(date('Y-m-d')) && $proposals_info->status == ('draft')) {
    $start = strtotime(date('Y-m-d'));
    $end = strtotime($proposals_info->due_date);
    $days_between = ceil(abs($end - $start) / 86400);
    ?>
    <div class="alert bg-danger-light hidden-print">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-warning"></i>
        <?= lang('proposal_overdue') . ' ' . lang('by') . ' ' . $days_between . ' ' . lang('days') ?>
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
<div class="panel" id="print_proposals">

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
                    <h4 class="mb0"><?= lang('proposals') . ' : ' . $proposals_info->reference_no ?></h4>
                    <?= $language_info['proposal_date'] ?>
                    : <?= strftime(config_item('date_format'), strtotime($proposals_info->proposal_date)); ?>
                    <br><?= $language_info['due_date'] ?>
                    : <?= strftime(config_item('date_format'), strtotime($proposals_info->due_date)); ?>
                    <?php if (!empty($proposals_info->user_id)) { ?>
                        <br><?= lang('sales') . ' ' . lang('agent') ?>:<?php echo fullname($proposals_info->user_id); ?>
                        <?php
                    }
                    if ($proposals_info->status == 'accepted') {
                        $label = 'success';
                    } else {
                        $label = 'danger';
                    }
                    ?>
                    <br><?= lang('proposals') . '  ' . lang('status') ?>: <span
                        class="label label-<?= $label ?>"><?= lang($proposals_info->status) ?></span>

                    <?php $show_custom_fields = custom_form_label(11, $proposals_info->proposals_id);
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
            <table class="table items proposal-items-preview" page-break-inside: auto;>
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
                    if (isset($proposals_info) && $proposals_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                        $qty_heading = lang('hours');
                    } else if (isset($proposals_info) && $proposals_info->show_quantity_as == 'qty_hours') {
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
                $invoice_items = $this->proposal_model->ordered_items_by_id($proposals_info->proposals_id);
                if (!empty($invoice_items)) :
                    foreach ($invoice_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->proposals_items_id ?>">
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
                    <?= $proposals_info->notes ?>
                </p>
            </div>
            <div class="col-sm-4 pv">
                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['sub_total'] ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->proposal_model->proposal_calculation('proposal_cost', $proposals_info->proposals_id)); ?>
                    </p>
                </div>
                <?php if ($proposals_info->discount_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['discount'] ?>
                            (<?php echo $proposals_info->discount_percent; ?>
                            %)</p>
                        <p class="pull-right mr">
                            <?= display_money($this->proposal_model->proposal_calculation('discount', $proposals_info->proposals_id)); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php
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
                <?php if ($proposals_info->adjustment > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['adjustment'] ?></p>
                        <p class="pull-right mr">
                            <?= display_money($proposals_info->adjustment); ?>
                        </p>
                    </div>
                <?php endif ?>

                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['total'] ?></p>
                    <p class="pull-right mr">
                        <?php
                        $proposal_total = $this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id);
                        echo display_money($proposal_total, $currency->symbol); ?>
                    </p>
                </div>
                <?php if (config_item('amount_to_words') == 'Yes') { ?>
                    <div class="clearfix">
                        <p class="pull-right h4"><strong class="h3"><?= lang('num_word') ?>
                                : </strong> <?= number_to_word($client_id, $proposal_total); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?= !empty($invoice_view) && $invoice_view > 0 ? $this->gst->summary($invoice_items) : ''; ?>
</div>
<?php include_once 'assets/js/sales.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        init_items_sortable(true);
    });
    function print_proposals(print_proposals) {
        var printContents = document.getElementById(print_proposals).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>