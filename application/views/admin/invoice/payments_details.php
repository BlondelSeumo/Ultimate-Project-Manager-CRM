<?php
$edited = can_action('13', 'edited');
$invoice_info = $this->invoice_model->check_by(array('invoices_id' => $payments_info->invoices_id), 'tbl_invoices');
if (empty($invoice_info)) {
    $invoice_info = new stdClass();
    $invoice_info->adjustment = 0;
    $invoice_info->client_id = 0;
    $invoice_info->date_saved = 0;
    $invoice_info->invoices_id = 0;
    $invoice_info->reference_no = '-';

}
$client_info = $this->invoice_model->check_by(array('client_id' => $payments_info->paid_by), 'tbl_client');
$payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $payments_info->payment_method), 'tbl_payment_methods');
$can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $payments_info->invoices_id));
$currency = $this->invoice_model->client_currency_symbol($client_info->client_id);
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
                            if (!empty($all_invoices_info)) {
                                foreach ($all_invoices_info as $v_invoice) {
                                    if (!empty($v_invoice)) {
                                        $all_payment_info = $this->db->where('invoices_id', $v_invoice->invoices_id)->get('tbl_payments')->result();
                                        if (!empty($all_payment_info)):foreach ($all_payment_info as $v_payments_info):
                                            $client_info = $this->invoice_model->check_by(array('client_id' => $v_payments_info->paid_by), 'tbl_client');
                                            if (!empty($client_info)) {
                                                $client_name = $client_info->name;
                                                $currency = $this->invoice_model->client_currency_symbol($v_invoice->client_id);
                                            } else {
                                                $client_name = '-';
                                                $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                            }
                                            ?>
                                            <li class="<?= ($v_payments_info->payments_id == $this->uri->segment(5) ? 'active' : '') ?>">
                                                <a href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payments_info->payments_id ?>">
                                                    <?= $client_name ?>
                                                    <div class="pull-right">
                                                        <?=
                                                        display_money($v_payments_info->amount, $currency->symbol)
                                                        ?>
                                                    </div>
                                                    <br>
                                                    <small
                                                        class="block small text-info"><?= $v_payments_info->trans_id ?>
                                                        | <?= strftime(config_item('date_format'), strtotime($v_payments_info->created_date)) . ' ' . display_time($v_payments_info->created_date); ?> </small>

                                                </a>
                                            </li>
                                            <?php
                                        endforeach;
                                        endif;
                                    }

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
    $p_client_info = $this->invoice_model->check_by(array('client_id' => $payments_info->paid_by), 'tbl_client');
    if (!empty($p_client_info)) {
        $p_client_name = $p_client_info->name;
        $currency = $this->invoice_model->client_currency_symbol($payments_info->paid_by);
    } else {
        $p_client_name = '-';
        $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
    }
    ?>
    <section class="col-sm-9">
        <div class="row">
            <section class="panel panel-custom">
                <div class="panel-body">
                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                        <div class="btn-group">
                            <a data-toggle="tooltip" data-placement="top"
                               href="<?= base_url() ?>admin/invoice/all_payments/<?= $payments_info->payments_id ?>"
                               title="<?= lang('edit_payment') ?>"
                               class="btn btn-sm btn-primary">
                                <i class="fa fa-pencil"></i> <?= lang('edit_payment') ?></a>
                        </div>

                        <a data-toggle="tooltip" data-placement="top"
                           href="<?= base_url() ?>admin/invoice/send_payment/<?= $payments_info->payments_id . '/' . $payments_info->amount ?>"
                           title="<?= lang('send_email') ?>"
                           class="btn btn-sm btn-danger pull-right ">
                            <i class="fa fa-envelope"></i> <?= lang('send_email') ?></a>


                        <a data-toggle="tooltip" data-placement="top"
                           href="<?= base_url() ?>admin/invoice/payments_pdf/<?= $payments_info->payments_id ?>"
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
                                                style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;"><?= strftime(config_item('date_format'), strtotime($payments_info->payment_date)); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <?php if (config_item('amount_to_words') == 'Yes') { ?>
                                            <div style="width: 100%;padding: 10px 0;">
                                                <div
                                                        style="color:#999;width:35%;float:left;"><?= lang('num_word') ?></div>
                                                <div
                                                        style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;min-height:22px"><?= number_to_word($invoice_info->client_id, $payments_info->amount) ?></div>
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
                                        <div style="width:75%;border-bottom:1px solid #eee;float:right"><strong><a
                                                    href="<?= base_url() ?>admin/client/client_details/<?= $payments_info->paid_by ?>"><?= $p_client_name ?></a></strong>
                                        </div>
                                        <div style="color:#999;width:25%"><?= lang('received_from') ?></div>
                                    </div>
                                    <?php
                                    $role = $this->session->userdata('user_type');
                                    if ($role == 1 && $payments_info->account_id != 0) {
                                        $account_info = $this->invoice_model->check_by(array('account_id' => $payments_info->account_id), 'tbl_accounts');
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
                                    <?php $invoice_due = $this->invoice_model->calculate_to('invoice_due', $payments_info->invoices_id);?>

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
                                                    <?= lang('invoice_code') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('invoice_date') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('invoice_amount') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('paid_amount') ?>
                                                </td>
                                                <?php if ($invoice_due > 0) { ?>
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
                                                        href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $payments_info->invoices_id ?>"> <?= $invoice_info->reference_no ?></a>
                                                </td>
                                                <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                                                    valign="top">
                                                    <?= strftime(config_item('date_format'), strtotime($invoice_info->date_saved)) ?>
                                                </td>
                                                <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                                                    valign="top">
                                                    <span><?= display_money($this->invoice_model->calculate_to('total', $invoice_info->invoices_id), $currency->symbol); ?></span>
                                                </td>
                                                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                                                    valign="top">
                                                    <span><?= display_money($payments_info->amount, $currency->symbol); ?></span>
                                                </td>
                                                <?php if ($invoice_due > 0) { ?>
                                                    <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;color: red"
                                                        valign="top">
                                                        <span><?= display_money($invoice_due, $currency->symbol); ?></span>
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