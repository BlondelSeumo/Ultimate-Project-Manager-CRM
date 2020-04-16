<div id="payment_receipt">
    <style type="text/css">
        .bd {
            width: 100%;
        }

        .banner {
            border-bottom: 2px solid black;
        }

        .banner td {
            border: 0px;
        }

        .banner td p {
            font-size: 16px;
            font-weight: bold;
            margin-left: 10px;
        }

        table {
            font-family: Arial, Helvetica, sans-serif;
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 8px 0 8px 5px;
            text-align: left;
            font-size: 13px;
            border: 1px solid black;
            background-color: #F2F2F2;
        }

        td {
            padding: 10px 0 8px 8px;
            text-align: left;
            font-size: 13px;
            color: black;
            border: 1px solid black;
        }

        .head {
            background-color: #F2F2F2;
            font-size: 14px;
            padding: 15px 5px 8px 15px;
            border-radius: 5px;
        }

        .head tr td {
            text-align: left;
            font-size: 15px;
            border: 0px;
            padding-left: 20px;
        }

        .tbl1 {
            width: 49%;
            float: left;
        }

        .tbl2 {
            width: 49%;
            float: right;
        }

        .tbl_total {
            width: 49%;
            float: right;
        }

        .tbl_total tr td {
            border: 0px;
        }

        .tbl_total td {
            padding-left: 25px;
        }

        .bg td {
            background-color: #F2F2F2;
        }
    </style>
    <div class="bd">
        <div style="text-align: right" class="hidden-print">

            <a href="<?= base_url() ?>admin/payroll/send_payslip/<?= $employee_salary_info->salary_payment_id ?>"
               class="btn btn-danger btn-xs" data-toggle="tooltip"
               data-placement="top" title="" data-original-title="<?= lang('send_email') ?>"><span <i
                    class="fa fa-envelope-o"></i></span></a>
            <?php echo btn_pdf('admin/payroll/salary_payment_details_pdf/' . $employee_salary_info->salary_payment_id); ?>

        </div>

        <div style="width: 100%; border-bottom: 2px solid black;">
            <table style="width: 100%; vertical-align: middle;">
                <tr>
                    <td style="width: 50px; border: 0px;">
                        <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                             src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                    </td>

                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                    </td>
                </tr>
            </table>
        </div>
        <br/>
        <div style="width: 100%;">
            <div align="center">
                <table class="head">
                    <tr>
                        <td colspan="3" style="text-align: center; font-size: 18px; padding-bottom: 18px;">
                            <strong><?= lang('payslip') ?>
                                <br/><?= lang('salary_month') ?>
                                : <?php echo date('F  Y', strtotime($employee_salary_info->payment_month)) ?>
                            </strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('employment_id') ?>
                                :</strong> <?php echo $employee_salary_info->employment_id; ?></td>
                        <td>
                            <strong><?= lang('name') ?> :</strong> <?php echo $employee_salary_info->fullname; ?>
                        </td>
                        <td><strong><?= lang('payslip_no') ?> :</strong> <?php echo $payslip_number; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('mobile') ?> :</strong> <?php echo $employee_salary_info->mobile; ?></td>
                        <?php if (!empty($employee_salary_info->bank_name)): ?>
                            <td><strong><?= lang('bank') ?> :</strong> <?php echo $employee_salary_info->bank_name; ?>
                            </td>
                        <?php else:
                            $user_info = $this->db->where('user_id', $employee_salary_info->user_id)->get('tbl_users')->row();
                            ?>
                            <td><strong><?= lang('email') ?> :</strong> <?php echo $user_info->email; ?></td>
                        <?php endif; ?>
                        <?php if (!empty($employee_salary_info->account_number)): ?>
                            <td><strong><?= lang('A_C_no') ?>
                                    :</strong> <?php echo $employee_salary_info->account_number; ?></td>
                        <?php else: ?>
                            <td><strong><?= lang('address') ?>
                                    :</strong> <?php echo $employee_salary_info->present_address; ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td><strong><?= lang('departments') ?> :</strong> <?php echo $employee_salary_info->deptname; ?>
                        </td>
                        <td><strong><?= lang('designation') ?>
                                :</strong> <?php echo $employee_salary_info->designations; ?></td>
                        <td><strong><?= lang('joining_date') ?>
                                :</strong> <?= strftime(config_item('date_format'), strtotime($employee_salary_info->joining_date)) ?>
                        </td>
                    </tr>
                </table>
                <br/><br/>
            </div>
            <div align="center">
                <div class="tbl1">
                    <table>
                        <tr>
                            <th colspan="2"
                                style="border: 0px; font-size: 20px;padding-left:0px;background: none;color: #000">
                                <?= lang('earning') ?></th>
                        </tr>
                        <tr>
                            <th><?= lang('type_of_pay') ?></th>
                            <th><?= lang('amount') ?></th>
                        </tr>
                        <?php
                        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                        $total_hours_amount = 0;
                        foreach ($salary_payment_details_info as $v_payment_details) :
                            ?>
                            <tr>
                                <td style="text-align: right">
                                    <strong> <?php
                                        if ($v_payment_details->salary_payment_details_label == 'overtime_salary' || $v_payment_details->salary_payment_details_label == 'hourly_rates') {
                                            $small = ($v_payment_details->salary_payment_details_label == 'overtime_salary' ? ' <small>( ' . lang('per_hour') . ')</small>' : '');
                                            $label = lang($v_payment_details->salary_payment_details_label) . $small;
                                        } else {
                                            $label = $v_payment_details->salary_payment_details_label;
                                        }
                                        echo $label; ?>
                                        :&nbsp;&nbsp; </strong>
                                </td>
                                <td> <?php
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
                        <?php
                        $total_allowance = 0;
                        if (!empty($allowance_info)):foreach ($allowance_info as $v_allowance) :
                            ?>
                            <tr>
                                <td style="text-align: right">
                                    <strong> <?php echo $v_allowance->salary_payment_allowance_label ?>
                                        :&nbsp;&nbsp; </strong></td>
                                <td><?php echo display_money($v_allowance->salary_payment_allowance_value, $curency->symbol); ?></td>
                            </tr>
                            <?php
                            $total_allowance += $v_allowance->salary_payment_allowance_value;
                        endforeach;
                            ?>
                        <?php endif; ?>
                    </table>
                </div>
                <?php
                $deduction = 0;
                if (!empty($deduction_info)):
                    ?>
                    <div class="tbl2">
                        <table>
                            <tr>
                                <th colspan="2"
                                    style="border: 0px; font-size: 20px;padding-left:0px;background: none;color: #000">
                                    <strong><?= lang('deductions') ?></strong></th>
                            </tr>
                            <tr>
                                <th><?= lang('type_of_pay') ?></th>
                                <th><?= lang('amount') ?></th>
                            </tr>
                            <?php foreach ($deduction_info as $v_deduction): ?>
                                <tr>
                                    <td style="text-align: right">
                                        <strong><?php echo $v_deduction->salary_payment_deduction_label; ?> :&nbsp;&nbsp;</strong>
                                    </td>

                                    <td>&nbsp; <?php
                                        echo display_money($v_deduction->salary_payment_deduction_value, $curency->symbol);
                                        ?></td>
                                </tr>
                                <?php
                                $deduction += $v_deduction->salary_payment_deduction_value;
                            endforeach;
                            ?>
                        </table>
                    </div>
                <?php endif; ?>
                <table class="tbl_total">
                    <tr>
                        <th colspan="2"
                            style="border: 0px; font-size: 20px;padding-left:0px;background: none;color: #000">
                            <strong><?= lang('total_details') ?></strong></th>
                    </tr>
                    <?php if (!empty($employee_salary_info)): ?>
                        <tr>
                            <td style="text-align: right;"><strong> <?= lang('gross_salary') ?> :&nbsp;&nbsp;</strong>
                            </td>
                            <td>&nbsp; <?php
                                if (!empty($rate)) {
                                    $rate = $rate;
                                } else {
                                    $rate = 0;
                                }
                                $gross = $total_hours_amount + $total_allowance - $rate;
                                echo display_money($gross, $curency->symbol);
                                ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right"><strong><?= lang('total_deduction') ?> :&nbsp;&nbsp;</strong>
                            </td>

                            <td> &nbsp; <?php
                                $total_deduction = $deduction;
                                echo display_money($total_deduction, $curency->symbol);
                                ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($employee_salary_info)): ?>
                        <tr>
                            <td style="text-align: right"><strong><?= lang('net_salary') ?> :&nbsp;&nbsp;</strong></td>

                            <td>&nbsp; <?php
                                $net_salary = $gross - $deduction;
                                echo display_money($net_salary, $curency->symbol);
                                ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($employee_salary_info->fine_deduction)): ?>
                        <tr>
                            <td style="text-align: right"><strong><?= lang('fine_deduction') ?> :&nbsp;&nbsp;</strong>
                            </td>

                            <td>&nbsp; <?php
                                $net_salary = $gross - $deduction;
                                echo display_money($employee_salary_info->fine_deduction, $curency->symbol);
                                ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="bg">
                        <td style="text-align: right;font-weight: bold"><strong><?= lang('paid_amount') ?>
                                :&nbsp;&nbsp;</strong></td>

                        <td style="font-weight: bold;">&nbsp; <?php
                            if (!empty($employee_salary_info->fine_deduction)) {
                                $paid_amount = $net_salary - $employee_salary_info->fine_deduction;
                            } else {
                                $paid_amount = $net_salary;
                            }
                            echo display_money($paid_amount, $curency->symbol);
                            ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function payment_receipt(payment_receipt) {
        var printContents = document.getElementById(payment_receipt).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>