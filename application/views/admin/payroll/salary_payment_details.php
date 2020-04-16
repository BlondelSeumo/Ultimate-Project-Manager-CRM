<div id="printableArea">
    <div class="modal-header ">
        <h4 class="modal-title" id="myModalLabel"><?= lang('salary_payment_details') ?>
            <div class="pull-right">
                <a href="<?= base_url() ?>admin/payroll/send_payslip/<?= $salary_payment_info->salary_payment_id ?>"
                   class="btn btn-success btn-xs" data-toggle="tooltip"
                   data-placement="top" title="" data-original-title="<?= lang('send_email') ?>"><span <i
                        class="fa fa-envelope-o"></i></span></a>

                <span><?php echo btn_pdf('admin/payroll/salary_payment_details_pdf/' . $salary_payment_info->salary_payment_id); ?></span>
                <button class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" data-placement="top"
                        title="<?= lang('print') ?>"
                        onclick="printDiv('printableArea')"><i class="fa fa-print"></i></button>
            </div>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="show_print" style="width: 100%; border-bottom: 2px solid black;margin-bottom: 30px">
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
        </div><!-- show when print start-->
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-2 col-sm-2">
                    <div class="fileinput-new thumbnail"
                         style="width: 144px; height: 158px; margin-top: 14px; margin-left: 16px; background-color: #EBEBEB;">
                        <?php if ($salary_payment_info->avatar): ?>
                            <img src="<?php echo base_url() . $salary_payment_info->avatar; ?>"
                                 style="width: 142px; height: 148px; border-radius: 3px;">
                        <?php else: ?>
                            <img src="<?php echo base_url() ?>/img/user.png" alt="Employee_Image">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-1 col-sm-1">
                    &nbsp;
                </div>
                <div class="col-lg-8 col-sm-8 ">
                    <div>
                        <div style="margin-left: 20px;">
                            <h3><?php echo $salary_payment_info->fullname; ?></h3>
                            <hr class="mt0"/>
                            <table class="table-hover">
                                <tr>
                                    <td><strong><?= lang('emp_id') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$salary_payment_info->employment_id"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('departments') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$salary_payment_info->deptname"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('designation') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$salary_payment_info->designations"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('joining_date') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?= strftime(config_item('date_format'), strtotime($salary_payment_info->joining_date)) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row form-horizontal">
            <!-- ********************************* Salary Details Panel ***********************-->
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?= lang('salary_details') ?></strong>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="">
                        <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('salary_month') ?>
                                :</strong></label>
                        <p class="form-control-static"><?php echo date('F Y', strtotime($salary_payment_info->payment_month)); ?></p>
                    </div>
                    <?php
                    $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                    $total_hours_amount = 0;
                    $rate = 0;
                    foreach ($salary_payment_details_info as $v_payment_details) :
                        ?>
                        <div class="">
                            <label for="field-1"
                                   class="col-sm-5 control-label"><strong><?php
                                    if ($v_payment_details->salary_payment_details_label == 'overtime_salary' || $v_payment_details->salary_payment_details_label == 'hourly_rates') {
                                        $small = ($v_payment_details->salary_payment_details_label == 'overtime_salary' ? ' <small>( ' . lang('per_hour') . ')</small>' : '');
                                        $label = lang($v_payment_details->salary_payment_details_label) . $small;
                                    } else {
                                        $label = $v_payment_details->salary_payment_details_label;
                                    }
                                    echo $label; ?>
                                    :</strong> </label>

                            <p class="form-control-static"><?php
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
                                ?></p>
                        </div>
                        <?php
                    endforeach;
                    ?>
                    <!-- ***************** Salary Details  Ends *********************-->

                    <!-- ******************-- Allowance Panel Start **************************-->
                    <?php
                    $total_allowance = 0;
                    if (!empty($allowance_info)):
                        ?>
                        <div class="col-sm-6">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <strong><?= lang('allowances') ?></strong>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    foreach ($allowance_info as $v_allowance) :
                                        ?>
                                        <div class="">
                                            <label
                                                class="col-sm-6 control-label"><strong><?php echo $v_allowance->salary_payment_allowance_label ?>
                                                    : </strong></label>
                                            <p class="form-control-static"><?php
                                                echo display_money($v_allowance->salary_payment_allowance_value, $curency->symbol);
                                                ?></p>
                                        </div>
                                        <?php
                                        $total_allowance += $v_allowance->salary_payment_allowance_value;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div><!-- ********************Allowance End ******************-->
                    <?php endif; ?>

                    <!-- ************** Deduction Panel Column  **************-->
                    <?php
                    $deduction = 0;
                    if (!empty($deduction_info)):
                        ?>
                        <div class="col-sm-6">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <strong><?= lang('deductions') ?></strong>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    if (!empty($deduction_info)):foreach ($deduction_info as $v_deduction):
                                        ?>
                                        <div class="">
                                            <label
                                                class="col-sm-6 control-label"><strong><?php echo $v_deduction->salary_payment_deduction_label; ?>
                                                    : </strong></label>
                                            <p class="form-control-static"><?php
                                                echo display_money($v_deduction->salary_payment_deduction_value, $curency->symbol);
                                                ?></p>
                                        </div>
                                        <?php
                                        $deduction += $v_deduction->salary_payment_deduction_value;
                                    endforeach;
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!-- ****************** Deduction End  *******************-->
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-sm-8 pull-right">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><?= lang('total_salary_details') ?></strong>
                        </div>
                    </div>
                    <div class="panel-body">

                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('gross_salary') ?>
                                    : </strong></label>
                            <p class="form-control-static"><?php
                                if (!empty($rate)) {
                                    $rate = $rate;
                                } else {
                                    $rate = 0;
                                }
                                $gross = $total_hours_amount + $total_allowance - $rate;
                                echo display_money($gross, $curency->symbol);
                                ?></p>
                        </div>
                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('total_deduction') ?>
                                    : </strong></label>
                            <p class="form-control-static"><?php
                                $total_deduction = $deduction;
                                echo display_money($total_deduction, $curency->symbol);
                                ?></p>
                        </div>
                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('net_salary') ?> : </strong></label>
                            <p class="form-control-static"><?php
                                $net_salary = $gross - $total_deduction;
                                echo display_money($net_salary, $curency->symbol);
                                ?></p>
                        </div>
                        <?php if (!empty($salary_payment_info->fine_deduction)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label"><strong><?= lang('fine_deduction') ?> : </strong></label>
                                <p class="form-control-static"><?php
                                    echo display_money($salary_payment_info->fine_deduction, $curency->symbol);
                                    ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('paid_amount') ?> : </strong></label>
                            <p class="form-control-static"><?php
                                if (!empty($salary_payment_info->fine_deduction)) {
                                    $paid_amount = $net_salary - $salary_payment_info->fine_deduction;
                                } else {
                                    $paid_amount = $net_salary;
                                }
                                echo display_money($paid_amount, $curency->symbol);
                                ?></p>
                        </div>
                        <?php if (!empty($salary_payment_info->payment_type)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label"><strong><?= lang('payment_method') ?> : </strong></label>
                                <p class="form-control-static"><?php
                                    $payment_method = $this->db->where('payment_methods_id', $salary_payment_info->payment_type)->get('tbl_payment_methods')->row();
                                    if (!empty($payment_method->method_name)) {
                                        echo $payment_method->method_name;
                                    }
                                    ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($salary_payment_info->comments)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label"><strong><?= lang('comments') ?>
                                        : </strong></label>
                                <p class="form-control-static"><?php
                                    echo $salary_payment_info->comments;
                                    ?></p>
                            </div>
                        <?php endif; ?>
                        <?php
                        $role = $this->session->userdata('user_type');
                        if ($role == 1 && $salary_payment_info->deduct_from != 0) {
                            $account_info = $this->payroll_model->check_by(array('account_id' => $salary_payment_info->deduct_from), 'tbl_accounts');
                            if (!empty($account_info)) {
                                ?>
                                <div class="">
                                    <label class="col-sm-6 control-label"><strong><?= lang('deduct_from') ?>
                                            : </strong></label>
                                    <p class="form-control-static"><a
                                            href="<?= base_url() ?>admin/account/manage_account"><?php echo $account_info->account_name; ?></a>
                                    </p>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div><!-- ****************** Total Salary Details End  *******************-->
        </div><!-- ************** Total Salary Details Start  **************-->
    </div>
</div>
<div class="modal-footer hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-right col-sm-8">
                <div class="col-sm-2 pull-right" style="margin-right: -31px;">
                    <button type="button" class="btn col-sm-12 pull-right btn-default btn-block"
                            data-dismiss="modal"><?= lang('close') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function printDiv(printableArea) {
        var printContents = document.getElementById(printableArea).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

