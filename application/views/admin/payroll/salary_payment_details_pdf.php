<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="width: 100%;">
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
<div style="padding: 5px 0; width: 100%;">
    <div>
        <table style="width: 100%; border-radius: 3px;">
            <tr>
                <td style="width: 150px;">
                    <table style="border: 1px solid grey;">
                        <tr>
                            <td style="background-color: lightgray; border-radius: 2px;">
                                <img src="<?php echo staffImage($emp_salary_info->user_id); ?>"
                                     style="width: 132px; height: 138px; border-radius: 3px;">
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="width: 300px; margin-left: 10px; margin-bottom: 10px; font-size: 13px;">
                        <tr>
                            <td colspan="2">
                                <h2><?php echo "$salary_payment_info->fullname "; ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 120px"><strong><?= lang('emp_id') ?> : </strong></td>
                            <td>&nbsp; <?php echo "$salary_payment_info->employment_id"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 120px"><strong><?= lang('departments') ?> : </strong></td>
                            <td>&nbsp; <?php echo "$salary_payment_info->deptname"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 120px"><strong><?= lang('designation') ?> :</strong></td>
                            <td>&nbsp; <?php echo "$salary_payment_info->designations"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 120px"><strong><?= lang('joining_date') ?> : </strong></td>
                            <td>
                                &nbsp; <?= strftime(config_item('date_format'), strtotime($salary_payment_info->joining_date)) ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<br/>
<div style="width: 100%;">
    <div>
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('salary_details') ?></strong></p>
        </div>
        <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0;">
            <tr>
                <td>
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="width: 30%;text-align: right"><strong><?= lang('salary_month') ?> :</strong></td>
                            <td style="">
                                &nbsp; <?php echo date('F Y', strtotime($salary_payment_info->payment_month)); ?></td>
                        </tr>
                        <?php
                        $rate = 0;
                        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                        $total_hours_amount = 0;
                        foreach ($salary_payment_details_info as $v_payment_details) :
                            ?>
                            <tr>
                                <td style="text-align: right">
                                    <strong><?php
                                        if ($v_payment_details->salary_payment_details_label == 'overtime_salary' || $v_payment_details->salary_payment_details_label == 'hourly_rates') {
                                            $small = ($v_payment_details->salary_payment_details_label == 'overtime_salary' ? ' <small>( ' . lang('per_hour') . ')</small>' : '');
                                            $label = lang($v_payment_details->salary_payment_details_label) . $small;
                                        } else {
                                            $label = $v_payment_details->salary_payment_details_label;
                                        }
                                        echo $label; ?>:</strong>
                                </td>

                                <td style="width: 220px;">&nbsp; <?php
                                    if (is_numeric($v_payment_details->salary_payment_details_value)) {
                                        if ($v_payment_details->salary_payment_details_label == 'overtime_salary') {
                                            $rate = $v_payment_details->salary_payment_details_value;
                                        } elseif ($v_payment_details->salary_payment_details_label == 'hourly_rates') {
                                            $rate = $v_payment_details->salary_payment_details_value;
                                        }
                                        $total_hours_amount += $v_payment_details->salary_payment_details_value;
                                        echo display_money($v_payment_details->salary_payment_details_value, $curency->symbol);
                                    } else {
                                        echo $v_payment_details->salary_payment_details_value;
                                    }
                                    ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div><!-- ***************** Salary Details  Ends *********************-->

<!-- ******************-- Allowance Panel Start **************************-->
<?php
$total_allowance = 0;
if (!empty($allowance_info)):
    ?>
    <div style="width: 100%;">
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('allowances') ?></strong></p>
        </div>
        <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0;">
            <tr>
                <td>
                    <table style="width: 100%; font-size: 13px;">
                        <?php
                        foreach ($allowance_info as $v_allowance) :
                            ?>
                            <tr>
                                <td style="width: 30%;text-align: right">
                                    <strong><?php echo $v_allowance->salary_payment_allowance_label ?> :</strong></td>

                                <td style="width: 220px;">&nbsp;<?php
                                    echo display_money($v_allowance->salary_payment_allowance_value, $curency->symbol);
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $total_allowance += $v_allowance->salary_payment_allowance_value;
                        endforeach;
                        ?>
                    </table>
                </td>
            </tr>
        </table>
    </div><!-- ********************Allowance End ******************-->
<?php endif; ?>

<!-- ************** Deduction Panel Column  **************-->
<?php
$deduction = 0;
if (!empty($deduction_info)):
    ?>
    <div style="width: 100%;">
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('deductions') ?></strong></p>
        </div>
        <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0;">
            <tr>
                <td>
                    <table style="width: 100%; font-size: 13px;">
                        <?php
                        if (!empty($deduction_info)):foreach ($deduction_info as $v_deduction):
                            ?>
                            <tr>
                                <td style="width: 30%;text-align: right">
                                    <strong><?php echo $v_deduction->salary_payment_deduction_label; ?> :</strong></td>

                                <td style="width: 220px;">&nbsp; <?php
                                    echo display_money($v_deduction->salary_payment_deduction_value, $curency->symbol);
                                    ?></td>
                            </tr>
                            <?php
                            $deduction += $v_deduction->salary_payment_deduction_value;
                        endforeach;
                            ?>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
        </table>
    </div><!-- ****************** Deduction End  *******************-->
<?php endif; ?>
<!-- ************** Total Salary Details Start  **************-->
<div style="width: 100%;">
    <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
        <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
            <strong><?= lang('total_salary_details') ?></strong></p>
    </div>
    <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0;">
        <tr>
            <td>
                <table style="width: 100%; font-size: 13px;">
                    <tr>
                        <td style="width: 30%;text-align: right"><strong><?= lang('gross_salary') ?> :</strong></td>

                        <td style="width: 220px;">&nbsp; <?php
                            $gross = $total_hours_amount + $total_allowance - $rate;
                            echo display_money($gross, $curency->symbol);
                            ?></td>
                    </tr>

                    <tr>
                        <td style="text-align: right"><strong><?= lang('total_deduction') ?> :</strong></td>

                        <td style="width: 220px;">&nbsp; <?php
                            $total_deduction = $deduction;
                            echo display_money($total_deduction, $curency->symbol);
                            ?></td>
                    </tr>

                    <tr>
                        <td style="text-align: right"><strong><?= lang('net_salary') ?> :</strong></td>

                        <td style="width: 220px;">&nbsp;<?php
                            $net_salary = $gross - $total_deduction;
                            echo display_money($net_salary, $curency->symbol);
                            ?></td>
                    </tr>
                    <?php if (!empty($salary_payment_info->fine_deduction)): ?>
                        <tr>
                            <td style="text-align: right"><strong><?= lang('fine_deduction') ?> :</strong></td>

                            <td style="width: 220px;">&nbsp; <?php
                                echo display_money($salary_payment_info->fine_deduction, $curency->symbol);
                                ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="text-align: right"><strong><?= lang('paid_amount') ?> :</strong></td>
                        <td style="width: 220px;">&nbsp; <?php
                            if (!empty($salary_payment_info->fine_deduction)) {
                                $paid_amount = $net_salary - $salary_payment_info->fine_deduction;
                            } else {
                                $paid_amount = $net_salary;
                            }
                            echo display_money($paid_amount, $curency->symbol);
                            ?></td>
                    </tr>
                    <?php if (!empty($salary_payment_info->payment_type)): ?>
                        <tr>
                            <td style="text-align: right"><strong><?= lang('payment_method') ?> :</strong></td>

                            <td style="width: 220px;">&nbsp; <?php
                                $payment_method = $this->db->where('payment_methods_id', $salary_payment_info->payment_type)->get('tbl_payment_methods')->row();
                                if (!empty($payment_method->method_name)) {
                                    echo $payment_method->method_name;
                                }
                                ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($salary_payment_info->comments)): ?>
                        <tr>
                            <td style="text-align: right"><strong><?= lang('comments') ?> :</strong></td>

                            <td style="width: 220px;">&nbsp; <?php
                                echo $salary_payment_info->comments;
                                ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php
                    $role = $this->session->userdata('user_type');
                    if ($role == 1 && $salary_payment_info->deduct_from != 0) {
                        $account_info = $this->payroll_model->check_by(array('account_id' => $salary_payment_info->deduct_from), 'tbl_accounts');
                        if (!empty($account_info)) {
                            ?>
                            <tr>
                                <td style="text-align: right"><strong><?= lang('deduct_from') ?> :</strong></td>

                                <td style="width: 220px;">&nbsp; <?php
                                    echo $account_info->account_name;
                                    ?></td>
                            </tr>
                        <?php }
                    } ?>
                </table>
            </td>
        </tr>
    </table>
</div><!-- ****************** Total Salary Details End  *******************-->
</body>
</html>