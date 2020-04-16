<?php
$edited = can_action('152', 'edited');
$can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $payments_info->return_stock_id));
$supplier_info = $this->return_stock_model->check_by(array('supplier_id' => $payments_info->paid_to), 'tbl_suppliers');
$payment_method = $this->return_stock_model->check_by(array('payment_methods_id' => $payments_info->payment_method), 'tbl_payment_methods');
$currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if (!empty($supplier_info)) {
    $supplier_name = $supplier_info->name;
} else {
    $supplier_name = '-';
}
if (!empty($payments_info)) {
    $payments_id = $payments_info->payments_id;
} else {
    $payments_id = null;
}
?>
<div class="row">
    <div class="col-sm-5">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?= lang('edit_payment') ?>
            </div>
            <div class="panel-body">
                <?php echo form_open(base_url('admin/return_stock/update_payemnt/' . $payments_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
                <div class="form-group">
                    <label class="col-lg-12"><?= lang('amount') ?> <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-12">
                        <input type="text" data-parsley-type="number" required="" class="form-control"
                               value="<?= $payments_info->amount ?>"
                               name="amount">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-12"><?= lang('payment_method') ?> <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-12">
                        <div class="input-group">
                            <select class="form-control select_box" style="width: 100%"
                                    name="payment_methods_id">
                                <option value="0"><?= lang('select_payment_method') ?></option>
                                <?php
                                $payment_methods = $this->db->order_by('payment_methods_id', 'DESC')->get('tbl_payment_methods')->result();
                                if (!empty($payment_methods)) {
                                    foreach ($payment_methods as $p_method) {
                                        ?>
                                        <option value="<?= $p_method->payment_methods_id ?>" <?php
                                        if (!empty($payments_info->payment_method)) {
                                            echo $payments_info->payment_method == $p_method->payment_methods_id ? 'selected' : '';
                                        }
                                        ?>><?= $p_method->method_name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="input-group-addon"
                                 title="<?= lang('new') . ' ' . lang('payment_method') ?>"
                                 data-toggle="tooltip" data-placement="top">
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/settings/inline_payment_method"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-12"><?= lang('payment_date') ?></label>
                    <div class="col-lg-12">
                        <div class="input-group">
                            <input type="text" required="" name="payment_date" class="form-control datepicker"
                                   value="<?php
                                   if (!empty($payments_info->payment_date)) {
                                       echo $payments_info->payment_date;
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
                    <label class="col-lg-12"><?= lang('notes') ?> </label>
                    <div class="col-lg-12">
                        <textarea name="notes" class=" form-control"><?= $payments_info->notes ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-12"></label>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"> <?= lang('save_changes') ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <section class="col-sm-7">
        <?= message_box('error') ?>
        <div class="row">
            <section class="panel panel-custom">
                <div class="panel-heading">
                    <?= lang('payment_details') . '- ' . $payments_info->trans_id ?>
                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                        <a data-toggle="tooltip" data-placement="top"
                           href="<?= base_url() ?>admin/return_stock/send_payment/<?= $payments_info->payments_id . '/' . $payments_info->amount ?>"
                           title="<?= lang('send_email') ?>"
                           class="btn btn-xs btn-danger pull-right ">
                            <i class="fa fa-envelope"></i> <?= lang('send_email') ?></a>
                        <a data-toggle="tooltip" data-placement="top"
                           href="<?= base_url() ?>admin/return_stock/payments_pdf/<?= $payments_info->payments_id ?>"
                           title="<?= lang('pdf') ?>"
                           class="btn btn-xs btn-success pull-right mr">
                            <i class="fa fa-file-pdf-o"></i> <?= lang('pdf') ?></a>
                    <?php } ?>
                </div>
                <div class="panel-body">
                    <div class="details-page" style="margin:45px 25px 25px 8px">
                        <div class="details-container clearfix" style="margin-bottom:20px">
                            <div style="font-size:10pt;">

                                <div>

                                    <div style="padding:5px 0 36px;text-align:center">
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
                                        <div style="width: 100%;padding: 10px 0;">
                                            <div
                                                style="color:#999;width:35%;float:left;"><?= lang('transaction_id') ?></div>
                                            <div
                                                style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;min-height:22px"><?= $payments_info->trans_id ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                    <div style="text-align:center;color:white;float:right;background:#1B9BA0;width: 27%;
                                         padding: 20px 5px;">
                                        <span> <?= lang('amount_received') ?></span><br>
                                        <span
                                            style="font-size:16pt;"><?= display_money($payments_info->amount, $currency->symbol); ?></span>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div style="padding-top:10px">
                                        <div style="width:75%;border-bottom:1px solid #eee;float:right">
                                            <strong><?= $supplier_name ?></strong>
                                        </div>
                                        <div style="color:#999;width:25%"><?= lang('paid') . ' ' . lang('TO') ?></div>
                                    </div>
                                    <?php
                                    $role = $this->session->userdata('user_type');
                                    if ($role == 1 && $payments_info->account_id != 0) {
                                        $account_info = $this->return_stock_model->check_by(array('account_id' => $payments_info->account_id), 'tbl_accounts');
                                        if (!empty($account_info)) {
                                            ?>
                                            <div style="padding-top:25px">
                                                <div
                                                    style="width:75%;border-bottom:1px solid #eee;float:right">
                                                    <a
                                                        href="<?= base_url() ?>admin/account/manage_account"><?= $account_info->account_name ?></a>
                                                </div>
                                                <div style="color:#999;width:25%"><?= lang('deduct_from') ?></div>
                                            </div>
                                        <?php }
                                    } ?>
                                    <div style="padding-top:25px">
                                        <div
                                            style="width:75%;border-bottom:1px solid #eee;float:right"><?= !empty($payment_method->method_name) ? $payment_method->method_name : '-' ?></div>
                                        <div style="color:#999;width:25%"><?= lang('payment_mode') ?></div>
                                    </div>
                                    <div style="padding-top:25px">
                                        <div
                                            style="width:75%;border-bottom:1px solid #eee;float:right"><?= $payments_info->notes ?></div>
                                        <div style="color:#999;width:25%"><?= lang('notes') ?></div>
                                    </div>
                                    <?php $return_stock_due = $this->return_stock_model->calculate_to('return_stock_due', $payments_info->return_stock_id); ?>
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
                                                    <?= lang('return_stock') . ' ' . lang('date') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('return_stock') . ' ' . lang('amount') ?>
                                                </td>
                                                <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
                                                    align="right">
                                                    <?= lang('paid_amount') ?>
                                                </td>
                                                <?php if ($return_stock_due > 0) { ?>
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
                                                        href="<?= base_url() ?>admin/return_stock/return_stock_details/<?= $payments_info->return_stock_id ?>"> <?= $return_stock_info->reference_no ?></a>
                                                </td>
                                                <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                                                    valign="top">
                                                    <?= display_date($return_stock_info->return_stock_date) ?>
                                                </td>
                                                <td style="padding: 10px 10px 5px 10px;text-align:right;word-wrap: break-word;"
                                                    valign="top">
                                                    <span><?= display_money($this->return_stock_model->calculate_to('total', $return_stock_info->return_stock_id), $currency->symbol); ?></span>
                                                </td>
                                                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                                                    valign="top">
                                                    <span><?= display_money($payments_info->amount, $currency->symbol); ?></span>
                                                </td>
                                                <?php if ($return_stock_due > 0) { ?>
                                                    <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;color: red"
                                                        valign="top">
                                                        <span><?= display_money($return_stock_due, $currency->symbol); ?></span>
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