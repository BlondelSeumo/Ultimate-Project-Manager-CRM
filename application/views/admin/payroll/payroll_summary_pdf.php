<!DOCTYPE html>
<html>
<head>
    <title><?= lang('payroll_summary') . ' ' . lang('report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>
    <style>
        th {
            padding: 10px 0px 5px 5px;
            font-size: 13px;
            border: 1px solid black;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
        }

        td {
            padding: 5px 0px 0px 5px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
            border: 1px solid black;
            font-size: 13px;
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
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
                <p style="margin-left: 10px; font: 22px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<?php if (!empty($search_type)) {

    ?>
    <div style="width: 100%;">
        <div style="background: #E0E5E8;padding: 5px;">
            <!-- Default panel contents -->
            <div style="font-size: 15px;padding: 0px 0px 0px 0px">
                <strong><?= lang('payroll_summary') ?><?= $by ?> </strong></div>
        </div>
        <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
            <tr>
                <th><?= lang('month') ?></th>
                <th><?= lang('date') ?></th>
                <th><?= lang('gross_salary') ?></th>
                <th><?= lang('total_deduction') ?></th>
                <th><?= lang('net_salary') ?></th>
                <th><?= lang('fine_deduction') ?></th>
                <th><?= lang('amount') ?></th>
            </tr>
            <?php
            $currency = $this->payroll_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            if (!empty($employee_payroll)) {
                foreach ($employee_payroll as $index => $v_payroll) {
                    $salary_payment_history = $this->db->where('salary_payment_id', $v_payroll->salary_payment_id)->get('tbl_salary_payment_details')->result();
                    $total_salary_amount = 0;
                    if (!empty($salary_payment_history)) {
                        foreach ($salary_payment_history as $v_payment_history) {
                            if (is_numeric($v_payment_history->salary_payment_details_value)) {
                                if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                }
                                $total_salary_amount += $v_payment_history->salary_payment_details_value;
                            }
                        }
                    }
                    $salary_allowance_info = $this->db->where('salary_payment_id', $v_payroll->salary_payment_id)->get('tbl_salary_payment_allowance')->result();
                    $total_allowance = 0;
                    if (!empty($salary_allowance_info)) {
                        foreach ($salary_allowance_info as $v_salary_allowance_info) {
                            $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                        }
                    }
                    if (empty($rate)) {
                        $rate = 0;
                    }
                    $salary_deduction_info = $this->db->where('salary_payment_id', $v_payroll->salary_payment_id)->get('tbl_salary_payment_deduction')->result();
                    $total_deduction = 0;
                    if (!empty($salary_deduction_info)) {
                        foreach ($salary_deduction_info as $v_salary_deduction_info) {
                            $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                        }
                    }

                    $total_paid_amount = $total_salary_amount + $total_allowance - $rate;
                    $gross = 0;
                    $deduction = 0;
                    ?>

                    <tr style="width: 100%;">
                        <td><?php echo date('F-Y', strtotime($v_payroll->payment_month)); ?></td>
                        <td><?php echo strftime(config_item('date_format'), strtotime($v_payroll->paid_date)); ?></td>
                        <td><?php echo display_money($total_paid_amount, $currency->symbol); ?></td>
                        <td><?php echo display_money($total_deduction, $currency->symbol); ?></td>
                        <td><?php echo display_money($net_salary = $total_paid_amount - $total_deduction, $currency->symbol); ?></td>
                        <td><?php
                            if (!empty($v_payroll->fine_deduction)) {
                                echo display_money($fine_deduction = $v_payroll->fine_deduction, $currency->symbol);
                            } else {
                                $fine_deduction = 0;
                            }
                            ?></td>
                        <td><?php echo display_money($net_salary - $fine_deduction, $currency->symbol); ?></td>
                    </tr>
                <?php }; ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5">
                        <strong><?= lang('nothing_to_display') ?></strong>
                    </td>
                </tr>
            <?php }; ?>
        </table>
    </div>
<?php } ?>
</body>
</html>
