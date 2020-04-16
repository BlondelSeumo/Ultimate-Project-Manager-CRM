<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?php
                if ($this->session->userdata('user_type') == '1') {
                    ?>
                    <a style="margin-top: -5px;" href="<?= base_url() ?>admin/invoice/manage_invoice/create_invoice"
                       data-original-title="<?= lang('new_invoice') ?>" data-toggle="tooltip" data-placement="top"
                       class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                            class="fa fa-plus"></i></a>
                <?php } ?>
                <?= lang('all_invoices') ?>
            </div>

            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php

                            if (!empty($all_invoices_info)) {
                                $all_invoices_info = array_reverse($all_invoices_info);
                                foreach ($all_invoices_info as $v_invoices) {
                                    if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                                        $invoice_status = lang('fully_paid');
                                        $label = "success";
                                    } elseif ($v_invoices->emailed == 'Yes') {
                                        $invoice_status = lang('sent');
                                        $label = "info";
                                    } else {
                                        $invoice_status = lang('draft');
                                        $label = "default";
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_invoices->invoices_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <?php
                                        $client_info = $this->invoice_model->check_by(array('client_id' => $v_invoices->client_id), 'tbl_client');
                                        if (!empty($client_info)) {
                                            $client_name = $client_info->name;
                                        } else {
                                            $client_name = '-';
                                        }
                                        ?>
                                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/payment_history/<?= $v_invoices->invoices_id ?>">
                                            <?= $client_name ?>
                                            <div class="pull-right">
                                                <?php $currency = $this->invoice_model->client_currency_symbol($v_invoices->client_id);
                                                if (empty($currency)) {
                                                    $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                                }
                                                ?>
                                                <?= display_money($this->invoice_model->get_invoice_cost($v_invoices->invoices_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_invoices->reference_no ?> <span
                                                    class="label label-<?= $label ?>"><?= $invoice_status ?></span>
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
    <section class="col-sm-9">
        <?= message_box('error') ?>
        <!-- Start create invoice -->
        <?php
        if (!empty($all_payments_history)) {
            $reference = ": <a href='" . base_url('admin/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id) . "' >" . $invoice_info->reference_no . "</a>";
            $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
            ?>
            <section class="panel panel-custom ">
                <header class="panel-heading pb-sm"><?= lang('payment_history_for_this_invoice', $reference) ?>
                    <?php if ($invoice_due != 0) { ?>
                        <div class="pull-right">
                            <a class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top"
                               href="<?= base_url('admin/invoice/manage_invoice/payment/' . $invoice_info->invoices_id) ?>"
                               title="<?= lang('add_payment') ?>"><i
                                    class="fa fa-credit-card"></i> <?= lang('pay_invoice') ?>
                            </a>
                        </div>
                    <?php } ?>
                </header>
                <div class="panel-body table-responsive">
                    <table class="table table-striped" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('trans_id') ?></th>
                            <th><?= lang('payment_date') ?></th>
                            <th><?= lang('paid_amount') ?></th>
                            <th><?= lang('payment_method') ?></th>
                            <th><?= lang('account') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($all_payments_history as $v_payment_history) {
                            $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
                            if (empty($currency)) {
                                $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            }
                            $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $v_payment_history->payment_method), 'tbl_payment_methods');
                            $account = get_row('tbl_accounts', array('account_id' => $v_payment_history->account_id), 'account_name')
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payment_history->payments_id ?>"> <?= $v_payment_history->trans_id ?></a>
                                </td>
                                <td>
                                    <?= strftime(config_item('date_format'), strtotime($v_payment_history->payment_date)) ?>
                                </td>
                                <td>
                                    <span><?= display_money($v_payment_history->amount, $currency->symbol); ?></span>
                                </td>
                                <td>
                                    <span><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-';; ?></span>
                                </td>
                                <td>
                                    <span><?= !empty($account) ? $account : '-'; ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>
                <footer class="panel-footer no-padder">
                    <div class="row text-center no-gutter">
                        <div class="col-xs-4 b-r b-light">
                            <span
                                class="h4 font-bold m-t block"><?= display_money($this->invoice_model->calculate_to('total', $invoice_info->invoices_id), $currency->symbol) ?></span>
                            <small
                                class="text-muted m-b block"><?= lang('total') . ' ' . lang('invoice_amount') ?></small>
                        </div>
                        <div class="col-xs-4 b-r b-light">
                            <span
                                class="h4 font-bold m-t block"><?= display_money($this->invoice_model->calculate_to('paid_amount', $invoice_info->invoices_id), $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('paid_amount') ?></small>
                        </div>
                        <div class="col-xs-4">
                            <span
                                class="h4 font-bold m-t block"><?= display_money($invoice_due, $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('due_amount') ?></small>

                        </div>
                    </div>
                </footer>
            </section>

        <?php } ?>
    </section>
</div>

<!-- end -->






