<?= message_box('success') ?>
<?= message_box('error');
$edited = can_action('156', 'edited');
$deleted = can_action('156', 'deleted');
?>
<div class="row mb">
    <div class="col-sm-8">
        <?php
        $where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $credit_note_info->credit_note_id, 'module_name' => 'credit_note');
        $check_existing = $this->credit_note_model->check_by($where, 'tbl_pinaction');
        if (!empty($check_existing)) {
            $url = 'remove_todo/' . $check_existing->pinaction_id;
            $btn = 'danger';
            $title = lang('remove_todo');
        } else {
            $url = 'add_todo_list/credit_note/' . $credit_note_info->credit_note_id;
            $btn = 'warning';
            $title = lang('add_todo_list');
        }

        $can_edit = $this->credit_note_model->can_action('tbl_credit_note', 'edit', array('credit_note_id' => $credit_note_info->credit_note_id));
        $can_delete = $this->credit_note_model->can_action('tbl_credit_note', 'delete', array('credit_note_id' => $credit_note_info->credit_note_id));
        $client_info = $this->credit_note_model->check_by(array('client_id' => $credit_note_info->client_id), 'tbl_client');
        if (!empty($client_info)) {
            $currency = $this->credit_note_model->client_currency_symbol($credit_note_info->client_id);
            $client_lang = $client_info->language;
        } else {
            $client_lang = 'english';
            $currency = $this->credit_note_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        }
        unset($this->lang->is_loaded[5]);
        $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
        ?>

        <?php if (!empty($can_edit) && !empty($edited)) { ?>

            <a data-toggle="modal" data-target="#myModal_lg"
               href="<?= base_url() ?>admin/credit_note/insert_items/<?= $credit_note_info->credit_note_id ?>"
               title="<?= lang('item_quick_add') ?>" class="btn btn-xs btn-primary">
                <i class="fa fa-pencil text-white"></i> <?= lang('add_items') ?></a>

            <span data-toggle="tooltip" data-placement="top" title="<?= lang('clone') . ' ' . lang('credit_note') ?>">
            <a data-toggle="modal" data-target="#myModal" title="<?= lang('clone') . ' ' . lang('credit_note') ?>"
               href="<?= base_url() ?>admin/credit_note/clone_credit_note/<?= $credit_note_info->credit_note_id ?>"
               class="btn btn-xs btn-purple">
                <i class="fa fa-copy"></i> <?= lang('clone') ?></a>
            </span>
            <?php
        }
        ?>

        <?php if (!empty($can_edit) && !empty($edited)) { ?>
            <div class="btn-group">
                <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                    <?= lang('more_actions') ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu animated zoomIn">
                    <li>
                        <a href="<?= base_url() ?>admin/credit_note/index/email_credit_note/<?= $credit_note_info->credit_note_id ?>"
                           data-toggle="ajaxModal"><?= lang('email_credit_note') ?></a></li>
                    <li>
                        <a href="<?= base_url() ?>admin/credit_note/index/credit_note_history/<?= $credit_note_info->credit_note_id ?>"><?= lang('credit_note_history') ?></a>
                    </li>
                    <li class="<?= $credit_note_info->status == 'open' ? 'hide' : '' ?>">
                        <a href="<?= base_url() ?>admin/credit_note/change_status/open/<?= $credit_note_info->credit_note_id ?>"><?= lang('open') ?></a>
                    </li>
                    <li class="<?= $credit_note_info->status == 'refund' ? 'hide' : '' ?>">
                        <a href="<?= base_url() ?>admin/credit_note/change_status/refund/<?= $credit_note_info->credit_note_id ?>"><?= lang('refund') ?></a>
                    </li>
                    <li class="<?= $credit_note_info->status == 'void' ? 'hide' : '' ?>">
                        <a href="<?= base_url() ?>admin/credit_note/change_status/void/<?= $credit_note_info->credit_note_id ?>"><?= lang('void') ?></a>
                    </li>
                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= base_url() ?>admin/credit_note/index/edit_credit_note/<?= $credit_note_info->credit_note_id ?>"><?= lang('edit_credit_note') ?></a>
                        </li>
                    <?php } ?>

                </ul>
            </div>
        <?php } ?>
        <?php
        $notified_reminder = count(get_result('tbl_reminders', array('module' => 'credit_note', 'module_id' => $credit_note_info->credit_note_id, 'notified' => 'No')));
        ?>
        <a class="btn btn-xs btn-green" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/invoice/reminder/credit_note/<?= $credit_note_info->credit_note_id ?>"><?= lang('reminder') ?>
            <?= !empty($notified_reminder) ? '<span class="badge ml-sm" style="border-radius: 50%">' . $notified_reminder . '</span>' : '' ?>
        </a>
        <?php
        $credit_used = count(get_result('tbl_credit_used', array('credit_note_id' => $credit_note_info->credit_note_id)));
        ?>
        <a class="btn btn-xs btn-info" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/credit_note/invoice_credited/<?= $credit_note_info->credit_note_id ?>"><?= lang('invoice_credited') ?>
            <?= !empty($credit_used) ? '<span class="badge ml-sm" style="border-radius: 50%">' . $credit_used . '</span>' : '' ?>
        </a>
        <a class="btn btn-xs btn-success" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/credit_note/credit_invoices/<?= $credit_note_info->credit_note_id ?>"><?= lang('apply') . ' ' . lang('TO') . ' ' . lang('invoice') ?>
        </a>

    </div>
    <div class="col-sm-4 pull-right">
        <a
                href="<?= base_url() ?>admin/credit_note/send_credit_note_email/<?= $credit_note_info->credit_note_id . '/' . true ?>"
                data-toggle="tooltip" data-placement="top" title="<?= lang('send_email') ?>"
                class="btn btn-xs btn-primary pull-right">
            <i class="fa fa-envelope-o"></i>
        </a>
        <a onclick="print_credit_note('print_credit_note')" href="#" data-toggle="tooltip" data-placement="top" title=""
           data-original-title="Print" class="mr-sm btn btn-xs btn-danger pull-right">
            <i class="fa fa-print"></i>
        </a>

        <a href="<?= base_url() ?>admin/credit_note/pdf_credit_note/<?= $credit_note_info->credit_note_id ?>"
           data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF"
           class="btn btn-xs btn-success pull-right mr-sm">
            <i class="fa fa-file-pdf-o"></i>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
           href="<?= base_url() ?>admin/projects/<?= $url ?>"
           class="mr-sm btn pull-right  btn-xs  btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
    </div>
</div>
<!-- Start Display Details -->
<?php
if (is_file(config_item('invoice_logo'))) {
    $img = base_url() . config_item('invoice_logo');
} else {
    $img = base_url() . 'uploads/default_logo.png';
}
?>
<!-- Main content -->
<div class="panel" id="print_credit_note">
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
                    <h4 class="mb0"><?= lang('credit_note') . ' : ' . $credit_note_info->reference_no ?></h4>
                    <?= lang('credit_note_date') ?>
                    : <?= strftime(config_item('date_format'), strtotime($credit_note_info->credit_note_date)); ?>
                    <?php
                    if ($credit_note_info->status == 'open') {
                        $label = 'success';
                    } else {
                        $label = 'danger';
                    }
                    ?>
                    <br><?= lang('status') ?>: <span
                            class="label label-<?= $label ?>"><?= lang($credit_note_info->status) ?></span>

                    <?php
                    if (!empty($credit_note_info->project_id)) {
                        $project_info = $this->db->where('project_id', $credit_note_info->project_id)->get('tbl_project')->row();
                        ?>
                        <br><strong><?= lang('project') ?>:</strong>: <a
                                href="<?= base_url() ?>admin/projects/project_details/<?= $credit_note_info->project_id ?>"
                                class="">
                            <?= $project_info->project_name ?>
                        </a>
                    <?php } ?>
                    <?php $show_custom_fields = custom_form_label(22, $credit_note_info->credit_note_id);
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
            <table class="table items credit_note-items-preview" page-break-inside: auto;>
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
                    if (isset($credit_note_info) && $credit_note_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                        $qty_heading = lang('hours');
                    } else if (isset($credit_note_info) && $credit_note_info->show_quantity_as == 'qty_hours') {
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
                $invoice_items = $this->credit_note_model->ordered_items_by_id($credit_note_info->credit_note_id);

                if (!empty($invoice_items)) :
                    foreach ($invoice_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->credit_note_items_id ?>">
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
                    <?= $credit_note_info->notes ?>
                </p>
            </div>
            <div class="col-sm-4 pv">
                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['sub_total'] ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->credit_note_model->credit_note_calculation('credit_note_cost', $credit_note_info->credit_note_id)); ?>
                    </p>
                </div>
                <?php if ($credit_note_info->discount_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['discount'] ?>
                            (<?php echo $credit_note_info->discount_percent; ?>
                            %)</p>
                        <p class="pull-right mr">
                            <?= display_money($this->credit_note_model->credit_note_calculation('discount', $credit_note_info->credit_note_id)); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php
                $tax_info = json_decode($credit_note_info->total_tax);
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
                <?php if ($credit_note_info->adjustment > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= $language_info['adjustment'] ?></p>
                        <p class="pull-right mr">
                            <?= display_money($credit_note_info->adjustment); ?>
                        </p>
                    </div>
                <?php endif ?>

                <div class="clearfix">
                    <p class="pull-left"><?= $language_info['total'] ?></p>
                    <p class="pull-right mr">
                        <?php
                        $total_amount = $this->credit_note_model->credit_note_calculation('total', $credit_note_info->credit_note_id);
                        echo display_money($total_amount, $currency->symbol);
                        ?>
                    </p>
                </div>
                <?php
                $credit_used = $this->credit_note_model->credit_note_calculation('credit_used', $credit_note_info->credit_note_id);
                if ($credit_used > 0) { ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('credit_used') ?></p>
                        <p class="pull-right mr">
                            <?php
                            echo display_money($credit_used, $currency->symbol);
                            ?>
                        </p>
                    </div>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('credit_remaining') ?></p>
                        <p class="pull-right mr">
                            <?php
                            $credit_remaining = $this->credit_note_model->credit_note_calculation('credit_remaining', $credit_note_info->credit_note_id);
                            echo display_money($credit_remaining, $currency->symbol);
                            ?>
                        </p>
                    </div>
                <?php } ?>
                <?php if (config_item('amount_to_words') == 'Yes') { ?>
                    <div class="clearfix">
                        <p class="pull-right h4"><strong class="h3"><?= lang('num_word') ?>
                                : </strong> <?= number_to_word($credit_note_info->client_id, $total_amount); ?></p>
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

    function print_credit_note(print_credit_note) {
        var printContents = document.getElementById(print_credit_note).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>