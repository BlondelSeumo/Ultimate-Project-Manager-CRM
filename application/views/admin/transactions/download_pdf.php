<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="width: 100%;">
<br/>
<?php
$img = ROOTPATH . '/' . config_item('company_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('company_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= $img ?>" alt="" class="img-circle"/>
            </td>
            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<?php
$account_info = $this->db->where(array('account_id' => $expense_info->account_id))->get('tbl_accounts')->row();
$currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
$category_info = $this->db->where('expense_category_id', $expense_info->category_id)->get('tbl_expense_category')->row();
if (!empty($category_info)) {
    $category = $category_info->expense_category;
} else {
    $category = lang('undefined_category');
}
$client_name = $this->db->where('client_id', $expense_info->paid_by)->get('tbl_client')->row();
?>
<div style="width: 100%;">
    <div>
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?php echo $title;
                    if (!empty($expense_info->reference)) {
                        echo '#' . $expense_info->reference;
                    }
                    ?></strong></p>
        </div>

        <table style="width: 100%; font-size: 13px;margin-top: 20px">
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
                }
                $paid_amount = $this->invoice_model->calculate_to('paid_amount', $expense_info->invoices_id);
                ?>
                <tr>
                    <td style="width: 30%;text-align: right"><strong><?= lang('reference_no') ?> :</strong>
                    </td>

                    <td style="">&nbsp; <?php
                        echo $invoice_info->reference_no;
                        ?></td>
                </tr>
                <tr>
                    <td style="width: 30%;text-align: right"><strong><?= lang('payment_status') ?> :</strong>
                    </td>

                    <td style="">&nbsp; <?php
                        echo $payment_status;
                        ?></td>
                </tr>
                <?php if ($paid_amount > 0) { ?>
                    <tr>
                        <td style="width: 30%;text-align: right"><strong><?= lang('paid_amount') ?> :</strong>
                        </td>

                        <td style="">&nbsp; <?= display_money($paid_amount, $currency->symbol); ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <?php
            if ($expense_info->project_id != 0) {
                $project = $this->db->where('project_id', $expense_info->project_id)->get('tbl_project')->row();
                ?>
                <tr>
                    <td style="width: 30%;text-align: right"><strong><?= lang('project_name') ?> :</strong>
                    </td>

                    <td style="">&nbsp; <?php
                        echo($project->project_name ? $project->project_name : '-');
                        ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('name') . '/' . lang('title') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo(!empty($expense_info->name) ? $expense_info->name : '-');
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('date') ?> :</strong></td>

                <td style="">&nbsp; <?php
                    echo strftime(config_item('date_format'), strtotime($expense_info->date));
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('account') ?> :</strong></td>

                <td style="">&nbsp; <?php
                    if (!empty($account_info->account_name)) {
                        echo $account_info->account_name;
                    } else {
                        echo '-';
                    }
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('amount') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo display_money($expense_info->amount, $currency->symbol);
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('categories') ?> :</strong>
                </td>
                <td style="">&nbsp; <?php
                    echo $category;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('paid_by') ?> :</strong>
                </td>
                <td style="">&nbsp; <?php
                    echo(!empty($client_name->name) ? $client_name->name : '-');
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('payment_method') ?> :</strong>
                </td>
                <td style="">&nbsp; <?php
                    if (!empty($expense_info->payment_methods_id)) {
                        $payment_methods = $this->db->where('payment_methods_id', $expense_info->payment_methods_id)->get('tbl_payment_methods')->row();
                    }
                    if (!empty($payment_methods)) {
                        echo $payment_methods->method_name;
                    } else {
                        echo '-';
                    }
                    ?></td>
            </tr>
            <?php if ($expense_info->type == 'Expense') { ?>
                <tr>
                    <td style="width: 30%;text-align: right"><strong><?= lang('status') ?> :</strong>
                    </td>

                    <td style="">&nbsp; <?php
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
                        } ?>
                        <span class="label label-<?= $label ?>"><?= lang($status); ?></span>

                    </td>
                </tr>
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
                        <tr>
                            <td style="width: 30%;text-align: right"><strong><?= $c_label ?> :</strong>
                            </td>
                            <td style="">&nbsp;
                                <span style="word-wrap: break-word;"><?= $v_fields ?></span>
                            </td>
                        </tr>
                    <?php }
                }
            }
            ?>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('notes') ?> :</strong>
                </td>
                <td style="">&nbsp;
                    <span style="word-wrap: break-word;"><?php echo strip_html_tags($expense_info->notes,true); ?></span>
                </td>
            </tr>

        </table>

    </div>
</div><!-- ***************** Salary Details  Ends *********************-->

</body>
</html>