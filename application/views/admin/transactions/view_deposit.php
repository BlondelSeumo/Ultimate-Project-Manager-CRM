<?php
$account_info = $this->transactions_model->check_by(array('account_id' => $expense_info->account_id), 'tbl_accounts');
$currency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

$category = lang('undefined_category');

if ($expense_info->type == 'Income') {
    $category_info = $this->db->where('income_category_id', $expense_info->category_id)->get('tbl_income_category')->row();
    if (!empty($category_info)) {
        $category = $category_info->income_category;
    }
} else {
    $category_info = $this->db->where('expense_category_id', $expense_info->category_id)->get('tbl_expense_category')->row();
    if (!empty($category_info)) {
        $category = $category_info->expense_category;
    }
}

$client_name = $this->db->where('client_id', $expense_info->paid_by)->get('tbl_client')->row();
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <h4 class="modal-title"
            id="myModalLabel"><?php echo $title;
            if (!empty($expense_info->reference)) {
                echo '#' . $expense_info->reference;
            }
            ?>
            <div class="pull-right">
                <?php

                if ($expense_info->billable == 'Yes' && $expense_info->project_id != 0 && $expense_info->invoices_id == 0) { ?>
                    <a onclick="return confirm('Are you sure to <?= lang('convert') ?> This <?= (!empty($expense_info->name) ? $expense_info->name : '-') ?> ?')"
                       href="<?= base_url() ?>admin/transactions/convert/<?= $expense_info->transactions_id ?>"
                       class="btn btn-purple btn-xs"><i
                                class="fa fa-copy"></i> <?= lang('convert_to_invoice') ?></a>

                <?php } ?>
                <?= btn_pdf('admin/transactions/download_pdf/' . $expense_info->transactions_id) ?>
            </div>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="panel-body form-horizontal">
            <?php
            if ($expense_info->invoices_id != 0) {
                $payment_status = $this->invoice_model->get_payment_status($expense_info->invoices_id);
                $invoice_info = $this->db->where('invoices_id', $expense_info->invoices_id)->get('tbl_invoices')->row();
                if ($payment_status == lang('fully_paid')) {
                    $p_label = "success";
                } elseif ($payment_status == lang('draft')) {
                    $p_label = "default";
                    $text = lang('status_as_draft');
                } elseif ($payment_status == lang('cancelled')) {
                    $p_label = "danger";
                } elseif ($payment_status == lang('partially_paid')) {
                    $p_label = "warning";
                } else {
                    $p_label = "primary";
                }
                $paid_amount = $this->invoice_model->calculate_to('paid_amount', $expense_info->invoices_id);
                ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('reference_no') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><a class="label label-success"
                                                          href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $expense_info->invoices_id ?>"> <?= $invoice_info->reference_no ?>
                            </a>
                        </p>
                    </div>
                </div>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('payment_status') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><span class="label label-<?= $p_label ?>">
                            <?= $payment_status ?>
                            </span>
                        </p>
                    </div>
                </div>
                <?php if ($paid_amount > 0) { ?>
                    <div class="col-md-12 notice-details-margin">
                        <div class="col-sm-4 text-right">
                            <label class="control-label"><strong><?= lang('paid_amount') ?> :</strong></label>
                        </div>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= display_money($paid_amount, $currency->symbol); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php
            if ($expense_info->project_id != 0) {
                $project = $this->db->where('project_id', $expense_info->project_id)->get('tbl_project')->row();
                ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('project_name') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ($project->project_name ? $project->project_name : '-') ?></p>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('name') . '/' . lang('title') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= (!empty($expense_info->name) ? $expense_info->name : '-') ?></p>
                </div>
            </div>

            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('date') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($expense_info->date)); ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('account') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if (!empty($account_info->account_name)) {
                            echo $account_info->account_name;
                        } else {
                            echo '-';
                        }
                        ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('amount') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= display_money($expense_info->amount, $currency->symbol) ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('categories') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $category ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('paid_by') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= (!empty($client_name->name) ? $client_name->name : '-'); ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('payment_method') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if (!empty($expense_info->payment_methods_id)) {
                            $payment_methods = $this->db->where('payment_methods_id', $expense_info->payment_methods_id)->get('tbl_payment_methods')->row();
                        }
                        if (!empty($payment_methods)) {
                            echo $payment_methods->method_name;
                        } else {
                            echo '-';
                        }
                        ?></p>
                </div>
            </div>
            <?php if ($expense_info->type == 'Expense') { ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('status') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?php
                            $status = $expense_info->status;
                            if ($expense_info->project_id != 0) {
                                if ($expense_info->billable == 'No') {
                                    $status = 'not_billable';
                                    $label = 'primary';
                                    $title = lang('not_billable');
                                    $action = '';
                                } else {
                                    $status = 'billable';
                                    $label = 'success';
                                    $title = lang('billable');
                                    $action = '';
                                }
                            } else {
                                if ($expense_info->status == 'non_approved') {
                                    $label = 'danger';
                                    $title = lang('get_approved');
                                    $action = 'approved';
                                } elseif ($expense_info->status == 'unpaid') {
                                    $label = 'warning';
                                    $title = lang('get_paid');
                                    $action = 'paid';
                                } else {
                                    $label = 'success';
                                    $title = '';
                                    $action = '';
                                }
                            }
                            $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
                            $role = $this->session->userdata('user_type');
                            if ($role == 1 || !empty($check_head)) {
                                ?>
                                <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
                                   class="label label-<?= $label ?>"
                                   href="
                                               <?php
                                   if (!empty($action)) {
                                       echo base_url() ?>admin/transactions/set_status/<?= $action . '/' . $expense_info->transactions_id;
                                   } else {
                                       echo '#';
                                   }
                                   ?>">

                                    <?= lang($status) ?>

                                </a>
                            <?php } else { ?>
                                <span class="label label-<?= $label ?>">
                                                <?= lang($status); ?>
                                            </span>

                            <?php } ?></p>
                    </div>
                </div>
            <?php } ?>
            <?php
            if ($expense_info->type == 'Income') {
                $view = 1;
            } else {
                $view = 2;
            }
            $show_custom_fields = custom_form_label($view, $expense_info->transactions_id);

            if (!empty($show_custom_fields)) {
                foreach ($show_custom_fields as $c_label => $v_fields) {
                    if (!empty($v_fields)) {
                        ?>
                        <div class="col-md-12 notice-details-margin">
                            <div class="col-sm-4 text-right">
                                <label class="control-label"><strong><?= $c_label ?> :</strong></label>
                            </div>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?= $v_fields ?></p>
                            </div>
                        </div>
                    <?php }
                }
            }
            ?>
            <?php
            $uploaded_file = json_decode($expense_info->attachement);
            ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('attachment') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <ul class="mailbox-attachments clearfix mt">
                        <?php
                        if (!empty($uploaded_file)):
                            foreach ($uploaded_file as $v_files):

                                if (!empty($v_files)):

                                    ?>
                                    <li>
                                        <?php if ($v_files->is_image == 1) : ?>
                                            <span class="mailbox-attachment-icon has-img"><img
                                                        src="<?= base_url() . $v_files->path ?>"
                                                        alt="Attachment"></span>
                                        <?php else : ?>
                                            <span class="mailbox-attachment-icon"><i
                                                        class="fa fa-file-pdf-o"></i></span>
                                        <?php endif; ?>
                                        <div class="mailbox-attachment-info">
                                            <a href="<?= base_url() ?>admin/transactions/download/<?= $expense_info->transactions_id . '/' . $v_files->fileName ?>"
                                               class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                                <?= $v_files->fileName ?></a>
                                            <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> <?= lang('kb') ?>
                            <a href="<?= base_url() ?>admin/transactions/download/<?= $expense_info->transactions_id . '/' . $v_files->fileName ?>"
                               class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                                        </div>
                                    </li>
                                <?php
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('notes') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <blockquote style="font-size: 12px"><?php echo $expense_info->notes; ?></blockquote>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
    </div>
</div>
