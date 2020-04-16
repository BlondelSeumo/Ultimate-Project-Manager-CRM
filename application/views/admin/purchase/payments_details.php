<?php
$edited = can_action('152', 'edited');
$can_edit = $this->purchase_model->can_action('tbl_purchases', 'edit', array('purchase_id' => $payments_info->purchase_id));
$purchase_info = $this->purchase_model->check_by(array('purchase_id' => $payments_info->purchase_id), 'tbl_purchases');
$supplier_info = $this->purchase_model->check_by(array('supplier_id' => $payments_info->paid_to), 'tbl_suppliers');
$payment_method = $this->purchase_model->check_by(array('payment_methods_id' => $payments_info->payment_method), 'tbl_payment_methods');
$currency = $this->purchase_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
?>
<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?= lang('all_payments') ?>
            </div>

            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php

                            if (!empty($all_purchases)) {
                                $all_purchases = array_reverse($all_purchases);
                                foreach ($all_purchases as $v_purchase) {
                                    $payment_status = $this->purchase_model->get_payment_status($v_purchase->purchase_id);
                                    if ($payment_status == ('fully_paid')) {
                                        $label = "success";
                                    } elseif ($payment_status == ('draft')) {
                                        $label = "default";
                                    } elseif ($payment_status == ('cancelled')) {
                                        $label = "danger";
                                    } elseif ($payment_status == ('partially_paid')) {
                                        $label = "warning";
                                    } elseif ($v_purchase->emailed == 'Yes') {
                                        $label = "info";
                                        $payment_status = ('sent');
                                    } else {
                                        $label = "danger";
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_purchase->purchase_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <?php
                                        $client_info = $this->purchase_model->check_by(array('supplier_id' => $v_purchase->supplier_id), 'tbl_suppliers');
                                        if (!empty($client_info)) {
                                            $client_name = $client_info->name;
                                        } else {
                                            $client_name = '-';
                                        }
                                        ?>
                                        <a href="<?= base_url() ?>admin/purchase/payments_details/<?= $v_purchase->purchase_id ?>">
                                            <?= $client_name ?>
                                            <div class="pull-right">
                                                <?= display_money($this->purchase_model->get_purchase_cost($v_purchase->purchase_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_purchase->reference_no ?> <span
                                                        class="label label-<?= $label ?>"><?= lang($payment_status) ?></span>
                                            </small>
                                        </a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>

                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php
    $p_client_info = $this->purchase_model->check_by(array('supplier_id' => $payments_info->paid_to), 'tbl_suppliers');
    if (!empty($p_client_info)) {
        $p_client_name = $p_client_info->name;
    } else {
        $p_client_name = '-';
    }
    ?>
    <section class="col-sm-9">
        <div class="row">
            <section class="panel panel-custom">
                <div class="panel-body">
                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                        <div class="btn-group">
                            <a data-toggle="tooltip" data-placement="top"
                               href="<?= base_url() ?>admin/purchase/all_payments/<?= $payments_info->payments_id ?>"
                               title="<?= lang('edit_payment') ?>"
                               class="btn btn-sm btn-primary">
                                <i class="fa fa-pencil"></i> <?= lang('edit_payment') ?></a>
                        </div>

                        <a data-toggle="tooltip" data-placement="top"
                           href="<?= base_url() ?>admin/purchase/send_payment/<?= $payments_info->payments_id . '/' . $payments_info->amount ?>"
                           title="<?= lang('send_email') ?>"
                           class="btn btn-sm btn-danger pull-right ">
                            <i class="fa fa-envelope"></i> <?= lang('send_email') ?></a>


                        <a data-toggle="tooltip" data-placement="top"
                           href="<?= base_url() ?>admin/purchase/payments_pdf/<?= $payments_info->payments_id ?>"
                           title="<?= lang('pdf') ?>"
                           class="btn btn-sm btn-success pull-right mr">
                            <i class="fa fa-file-pdf-o"></i> <?= lang('pdf') ?></a>
                    <?php } ?>


                    <div class="details-page" style="margin:45px 25px 25px 8px">
                        <div class="details-container clearfix" style="margin-bottom:20px">
                            <div style="font-size:10pt;">

                                <div style="padding:5px;">
                                    <div style="padding-bottom:25px;border-bottom:1px solid #eee;width:100%;">
                                        <div>
                                            <div style="text-transform: uppercase;font-weight: bold;">
                                                <div class="pull-left">
                                                    <img
                                                            style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                                                            src="<?= base_url() . config_item('invoice_logo') ?>">
                                                </div>
                                                <div class="pull-left">
                                                    <?= config_item('company_name') ?>
                                                    <p style="color:#999"><?= $this->config->item('company_address') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                    <div style="padding:15px 0 50px;text-align:center">
                                        <span
                                                style="text-transform: uppercase; border-bottom:1px solid #eee;font-size:13pt;"><?= lang('payments_received') ?></span>
                                    </div>
                                    <div style="width: 70%;float: left;">
                                        <div style="width: 100%;padding: 11px 0;">
                                            <div
                                                    style="color:#999;width:35%;float:left;"><?= lang('payment_date') ?></div>
                                            <div
                                                    style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;"><?= display_date($payments_info->payment_date); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <?php if (config_item('amount_to_words') == 'Yes') { ?>
                                            <div style="width: 100%;padding: 11px 0;">
                                                <div
                                                        style="color:#999;width:35%;float:left;"><?= lang('num_word') ?></div>
                                                <div
                                                        style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;"><?= number_to_word('', $payments_info->amount); ?></div>
                                                <div style="clear:both;"></div>
                                            </div>
                                        <?php } ?>
                                        <div style="width: 100%;padding: 10px 0;">
                                            <div
                                                    style="color:#999;width:35%;float:left;"><?= lang('transaction_id') ?></div>
                                            <div
                                                    style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;min-height:22px"><?= $payments_info->trans_id ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                    <div style="text-align:center;color:white;float:right;background:#1B9BA0;width: 25%;
                                         padding: 20px 5px;">
                                        <span> <?= lang('amount_received') ?></span><br>
                                        <span
                                                style="font-size:16pt;"><?= display_money($payments_info->amount, $currency->symbol); ?></span>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div style="padding-top:10px">
                                        <div style="width:75%;border-bottom:1px solid #eee;float:right">
                                            <strong><?= $p_client_name ?></strong>
                                        </div>
                                        <div style="color:#999;width:25%"><?= lang('paid') . ' ' . lang('TO') ?></div>
                                    </div>
                                    <?php
                                    $role = $this->session->userdata('user_type');
                                    if ($role == 1 && $payments_info->account_id != 0) {
                                        $account_info = $this->purchase_model->check_by(array('account_id' => $payments_info->account_id), 'tbl_accounts');
                                        if (!empty($account_info)) {
                                            ?>
                                            <div style="padding-top:25px">
                                                <div
                                                        style="width:75%;border-bottom:1px solid #eee;float:right">
                                                    <a
                                                            href="<?= base_url() ?>admin/account/manage_account"><?= $account_info->account_name ?></a>
                                                </div>
                                                <div style="color:#999;width:25%"><?= lang('received_account') ?></div>
                                            </div>
                                        <?php }
                                    } ?>
                                    <div style="padding-top:25px">
                                        <div
                                                style="width:75%;border-bottom:1px solid #eee;float:right"><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-' ?></div>
                                        <div style="color:#999;width:25%"><?= lang('payment_mode') ?></div>
                                    </div>

                                    <div style="padding-top:25px">
                                        <div
                                                style="width:75%;border-bottom:1px solid #eee;float:right"><?= $payments_info->notes ?></div>
                                        <div style="color:#999;width:25%"><?= lang('notes') ?></div>
                                    </div>
                                    <?php $purchase_due = $this->purchase_model->calculate_to('purchase_due', $payments_info->purchase_id); ?>

                                    <div style="margin-top:50px">
                                        <div style="width:100%">
                                            <div style="width:50%;float:left"><h4><?= lang('payment_for') ?></h4></div>
                                            <div style="clear:both;"></div>
                                        </div>

                                        <table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
                                               cellspacing="0" border="0">
                                            <thead>
                                            <tr style="height:40px;background:#f5f5f5">
                                                <td style="padding:5px 10px 5px 10px;word-wrap: break-word;">
                                                    <?= lang('reference_no') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('purchase_date') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('purchase').' '.lang('amount') ?>
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
                                                    valign="top"><a
                                                            href="<?= base_url() ?>admin/purchase/purchase_details/<?= $payments_info->purchase_id ?>"> <?= $purchase_info->reference_no ?></a>
                                                </td>
                                                <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                                                    valign="top">
                                                    <?= display_date($purchase_info->purchase_date) ?>
                                                </td>
                                                <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                                                    valign="top">
                                                    <span><?= display_money($this->purchase_model->calculate_to('total', $payments_info->purchase_id), $currency->symbol); ?></span>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Payment -->
            </section>
        </div>
    </section>
</div>
<!-- end -->