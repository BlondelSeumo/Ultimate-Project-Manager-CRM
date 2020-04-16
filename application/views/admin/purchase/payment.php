<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?php
                $currency = $this->purchase_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                if (empty($purchase_info)) {
                    redirect('admin/purchase/manage_purchase');
                }
                if ($this->session->userdata('user_type') == '1') {
                    ?>
                    <a style="margin-top: -5px;" href="<?= base_url() ?>admin/purchase/index/new"
                       data-original-title="<?= lang('new') . ' ' . lang('purchase') ?>" data-toggle="tooltip"
                       data-placement="top"
                       class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                                class="fa fa-plus"></i></a>
                <?php } ?>
                <?= lang('all') . ' ' . lang('purchase') ?>
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
                                        <a href="<?= base_url() ?>admin/purchase/payment/<?= $v_purchase->purchase_id ?>">
                                            <?= $client_name ?>
                                            <div class="pull-right">
                                                <?= display_money($this->purchase_model->calculate_to('purchase_due', $v_purchase->purchase_id), $currency->symbol); ?>
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
    <section class="col-sm-9">
        <?= message_box('error'); ?>
        <!-- Start create invoice -->
        <section class="panel panel-custom">
            <header class="panel-heading"><?= lang('pay') . ' ' . lang('purchase') ?></header>
            <div class="panel-body">
                <form method="post" data-parsley-validate="" novalidate="" id="purchase_payment"
                      action="<?= base_url() ?>admin/purchase/get_payment/<?= $purchase_info->purchase_id ?>"
                      class="form-horizontal">
                    <input type="hidden" name="currency" value="<?= $currency->symbol ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('trans_id') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <?php $this->load->helper('string'); ?>
                            <input type="text" class="form-control" value="<?= random_string('nozero', 6); ?>"
                                   name="trans_id" readonly>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('amount') ?> (<?= $currency->symbol ?>) <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" required="" class="form-control"
                                   value="<?= round($this->purchase_model->calculate_to('purchase_due', $purchase_info->purchase_id), 2) ?>"
                                   name="amount">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('payment_date') ?></label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" required="" name="payment_date" class="form-control datepicker"
                                       value="<?php
                                       if (!empty($payment_info->payment_date)) {
                                           echo $payment_info->payment_date;
                                       } else {
                                           echo date('Y-m-d');
                                       }
                                       ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('payment_method') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select class="form-control select_box" style="width: 100%"
                                    name="payment_methods_id">
                                <option value="0"><?= lang('select_payment_method') ?></option>
                                <?php
                                $payment_methods = $this->db->order_by('payment_methods_id', 'DESC')->get('tbl_payment_methods')->result();
                                if (!empty($payment_methods)) {
                                    foreach ($payment_methods as $p_method) {
                                        ?>
                                        <option value="<?= $p_method->payment_methods_id ?>" <?php
                                        if (!empty($payment_info->payment_method)) {
                                            echo $payment_info->payment_method == $p_method->payment_methods_id ? 'selected' : '';
                                        }
                                        ?>><?= $p_method->method_name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('notes') ?></label>
                        <div class="col-lg-6">
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('send_email') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox" class="custom-checkbox" name="send_thank_you">
                                    <span class="fa fa-check"></span></label>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('send') . ' ' . lang('sms') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox" class="custom-checkbox" name="send_sms">
                                    <span class="fa fa-check"></span></label>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('deduct_from_default_account') ?>
                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"
                               title="<?= lang('will_be_deduct_into_account') ?>"></i>
                        </label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox" checked class="custom-checkbox" id="use_postmark"
                                           name="deduct_from_account">
                                    <span class="fa fa-check"></span></label>
                            </div>

                        </div>
                    </div>
                    <div
                            id="postmark_config" <?php echo (empty($payment_info->account_id)) ? 'style="display:block"' : '' ?>>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('account') ?></label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <select name="account_id" style="width:100%;" class="form-control select_box">
                                        <?php
                                        $account_info = get_order_by('tbl_accounts', null, 'account_id');
                                        if (!empty($account_info)) {
                                            foreach ($account_info as $v_account) : ?>
                                                <option
                                                        value="<?= $v_account->account_id ?>"<?= (config_item('default_account') == $v_account->account_id ? ' selected="selected"' : '') ?>><?= $v_account->account_name ?></option>
                                            <?php endforeach;
                                        }
                                        ?>
                                    </select>
                                    <div class="input-group-addon"
                                         title="<?= lang('new') . ' ' . lang('account') ?>"
                                         data-toggle="tooltip" data-placement="top">
                                        <a data-toggle="modal" data-target="#myModal"
                                           href="<?= base_url() ?>admin/account/new_account"><i
                                                    class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3"></label>
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-primary"><?= lang('add_payment') ?></button>
                            </div>
                        </div>
                </form>
            </div>
        </section>
        <?php
        if (!empty($all_payments_history)) {
            $reference = ": <a href='" . base_url('admin/purchase/purchase_details/' . $purchase_info->purchase_id) . "' >" . $purchase_info->reference_no . "</a>";
            $purchase_due = $this->purchase_model->calculate_to('purchase_due', $purchase_info->purchase_id);
            ?>
            <section class="panel panel-custom ">
                <header class="panel-heading pb-sm"><?= lang('payment_history_for_this_purchase', $reference) ?></header>
                <div class="panel-body">
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
                            $currency = $this->purchase_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            $payment_methods = $this->purchase_model->check_by(array('payment_methods_id' => $v_payment_history->payment_method), 'tbl_payment_methods');
                            $account = get_row('tbl_accounts', array('account_id' => $v_payment_history->account_id), 'account_name')
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url() ?>admin/purchase/payments_details/<?= $v_payment_history->payments_id ?>"> <?= $v_payment_history->trans_id ?></a>
                                </td>
                                <td>
                                    <?= display_date($v_payment_history->payment_date) ?>
                                </td>
                                <td>
                                    <span><?= display_money($v_payment_history->amount, $currency->symbol); ?></span>
                                </td>
                                <td>
                                    <span><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-'; ?></span>
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
                                    class="h4 font-bold m-t block"><?= display_money($this->purchase_model->calculate_to('total', $purchase_info->purchase_id), $currency->symbol) ?></span>
                            <small
                                    class="text-muted m-b block"><?= lang('total') . ' ' . lang('invoice_amount') ?></small>
                        </div>
                        <div class="col-xs-4 b-r b-light">
                            <span
                                    class="h4 font-bold m-t block"><?= display_money($this->purchase_model->calculate_to('paid_amount', $purchase_info->purchase_id), $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('paid_amount') ?></small>
                        </div>
                        <div class="col-xs-4">
                            <span
                                    class="h4 font-bold m-t block"><?= display_money($purchase_due, $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('due_amount') ?></small>

                        </div>
                    </div>
                </footer>
            </section>

        <?php } ?>
    </section>
</div>

<!-- end -->






