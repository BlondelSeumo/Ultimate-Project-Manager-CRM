<div id="printableArea">
    <div class="modal-header ">
        <h4 class="modal-title" id="myModalLabel"><?= lang('payment_details') ?>
            <div class="pull-right ">
                <button class="btn btn-xs btn-danger" type="button" data-toggle="tooltip" title="Print"
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
                        <?php if ($employee_info->avatar): ?>
                            <img src="<?php echo base_url() . $employee_info->avatar; ?>"
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
                            <h3><?php echo $employee_info->fullname; ?></h3>
                            <hr class="mt0"/>
                            <table class="table-hover">
                                <tr>
                                    <td><strong><?= lang('emp_id') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$employee_info->employment_id"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('departments') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$employee_info->deptname"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('designation') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$employee_info->designations"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('joining_date') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?= strftime(config_item('date_format'), strtotime($employee_info->joining_date)) ?></td>
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
                        <p class="form-control-static"><?php echo date('F Y', strtotime($payment_month)); ?></p>
                    </div>
                    <?php
                    $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();

                    if (!empty($total_hours)) :
                        ?>
                        <div class="">
                            <label for="field-1"
                                   class="col-sm-5 control-label"><strong><?php echo lang('hourly_grade'); ?>
                                    :</strong> </label>
                            <p class="form-control-static"><?php
                                echo $employee_info->hourly_grade;
                                ?></p>
                        </div>
                        <div class="">
                            <label for="field-1"
                                   class="col-sm-5 control-label"><strong><?php echo lang('hourly_rates'); ?>
                                    :</strong> </label>
                            <p class="form-control-static"><?php
                                echo display_money($employee_info->hourly_rate, $curency->symbol);
                                ?></p>
                        </div>
                        <div class="">
                            <label for="field-1"
                                   class="col-sm-5 control-label"><strong><?php echo lang('total_hour'); ?>
                                    :</strong> </label>
                            <p class="form-control-static">
                                <strong><?php echo $total_hours['total_hours'] . ' : ' . $total_hours['total_minutes'] . ' ' . lang('m'); ?></strong>
                            </p>
                        </div>

                        <?php
                        $total_hour = $total_hours['total_hours'];
                        $total_minutes = $total_hours['total_minutes'];
                        if ($total_hour > 0) {
                            $hours_ammount = $total_hour * $employee_info->hourly_rate;
                        } else {
                            $hours_ammount = 0;
                        }
                        if ($total_minutes > 0) {
                            $amount = round($employee_info->hourly_rate / 60, 2);
                            $minutes_ammount = $total_minutes * $amount;
                        } else {
                            $minutes_ammount = 0;
                        }
                        $total_hours_amount = round($hours_ammount + $minutes_ammount,2);
                        ?>
                    <?php else: ?>
                        <div class="">
                            <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('salary_grade') ?>
                                    :</strong>
                            </label>
                            <p class="form-control-static"><?php
                                echo $employee_info->salary_grade;
                                ?></p>
                        </div>
                        <div class="">
                            <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('basic_salary') ?>
                                    :</strong>
                            </label>
                            <p class="form-control-static"><?php
                                echo display_money($employee_info->basic_salary, $curency->symbol);
                                ?></p>
                        </div>
                        <?php if (!empty($employee_info->overtime_salary)): ?>
                            <div class="">
                                <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('overtime') ?>
                                        <small> (<?= lang('per_hour') ?>)</small>
                                        :</strong> </label>
                                <p class="form-control-static"><?php
                                    echo display_money($employee_info->overtime_salary, $curency->symbol);
                                    ?></p>
                            </div>
                            <div class="">
                                <label for="field-1"
                                       class="col-sm-5 control-label"><strong><?php echo lang('overtime_hour'); ?>
                                        :</strong> </label>
                                <p class="form-control-static">
                                    <strong><?php echo $overtime_info['overtime_hours'] . ' : ' . $overtime_info['overtime_minutes'] . ' ' . lang('m'); ?></strong>
                                </p>
                            </div>
                            <div class="">
                                <label for="field-1"
                                       class="col-sm-5 control-label"><strong><?= lang('overtime_amount') ?>
                                        :</strong> </label>
                                <p class="form-control-static"><?php
                                    if (!empty($overtime_info)) {
                                        $overtime_hour = $overtime_info['overtime_hours'];
                                        $overtime_minutes = $overtime_info['overtime_minutes'];
                                        if ($overtime_hour > 0) {
                                            $ov_hours_ammount = $overtime_minutes * $employee_info->overtime_salary;
                                        } else {
                                            $ov_hours_ammount = 0;
                                        }
                                        if ($overtime_minutes > 0) {
                                            $ov_amount = round($employee_info->overtime_salary / 60, 2);
                                            $ov_minutes_ammount = $overtime_minutes * $ov_amount;
                                        } else {
                                            $ov_minutes_ammount = 0;
                                        }
                                        $overtime_amount = $ov_hours_ammount + $ov_minutes_ammount;
                                    }
                                    echo display_money($overtime_amount, $curency->symbol);
                                    ?></p>

                            </div>
                            <?php
                        endif;
                        ?>
                        <?php
                        if (!empty($overtime_amount)) {
                            $overtime_amount = $overtime_amount;
                        } else {
                            $overtime_amount = 0;
                        }
                        $total_hours_amount = $employee_info->basic_salary + $overtime_amount;
                    endif;
                    ?>

                    <?php
                    $total_award = 0;
                    if (!empty($award_info)) : foreach ($award_info as $v_award_info) :
                        ?>
                        <div class="">
                            <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('award_name') ?>
                                    <small>( <?php echo $v_award_info->award_name; ?> )</small>
                                    :</strong> </label>
                            <p class="form-control-static"><?php
                                echo display_money($v_award_info->award_amount, $curency->symbol);
                                ?></p>
                        </div>
                        <?php
                        $total_award += $v_award_info->award_amount;
                    endforeach;
                        ?>
                    <?php endif; ?>

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
                                                class="col-sm-6 control-label"><strong><?php echo $v_allowance->allowance_label ?>
                                                    : </strong></label>
                                            <p class="form-control-static">
                                                <?php echo display_money($v_allowance->allowance_value, $curency->symbol) ?></p>
                                        </div>
                                        <?php
                                        $total_allowance += $v_allowance->allowance_value;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div><!-- ********************Allowance End ******************-->
                    <?php endif; ?>

                    <!-- ************** Deduction Panel Column  **************-->
                    <?php
                    $deduction = 0;
                    $advance_amount = 0;
                    if (!empty($advance_salary['advance_amount']) || !empty($deduction_info)):
                        ?>
                        <div class="col-sm-6">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <strong><?= lang('deductions') ?></strong>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php if (!empty($advance_salary['advance_amount'])) : ?>
                                        <div class="">
                                            <label for="field-1"
                                                   class="col-sm-6 control-label"><strong><?= lang('advance_salary') ?>
                                                    :</strong> </label>
                                            <p class="form-control-static"><?php
                                                echo display_money($advance_salary['advance_amount'], $curency->symbol);
                                                ?></p>
                                        </div>
                                    <?php endif;
                                    ?>
                                    <?php
                                    if (!empty($advance_salary['advance_amount'])) {
                                        $advance_amount = $advance_salary['advance_amount'];
                                    } else {
                                        $advance_amount = 0;
                                    }
                                    ?>
                                    <?php
                                    if (!empty($deduction_info)):foreach ($deduction_info as $v_deduction):
                                        ?>
                                        <div class="">
                                            <label
                                                class="col-sm-6 control-label"><strong><?php echo $v_deduction->deduction_label; ?>
                                                    : </strong></label>
                                            <p class="form-control-static"><?php
                                                echo display_money($v_deduction->deduction_value, $curency->symbol)
                                                ?></p>
                                        </div>
                                        <?php
                                        $deduction += $v_deduction->deduction_value;
                                    endforeach;
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!-- ****************** Deduction End  *******************-->
                    <?php endif; ?>
                </div>
            </div><!-- ***************** Salary Details  Ends *********************-->
            <!-- ************** Total Salary Details Start  **************-->
            <div class="form-horizontal col-sm-8 pull-right">
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
                                $gross = $total_hours_amount + $total_award + $total_allowance;
                                echo display_money($gross, $curency->symbol);
                                ?></p>
                        </div>
                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('total_deduction') ?>
                                    : </strong></label>
                            <p class="form-control-static"><?php
                                $total_deduction = $advance_amount + $deduction;
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
                                <label class="col-sm-6 control-label"><strong> <?= lang('fine_deduction') ?> : </strong></label>
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
                    </div>
                </div>
            </div><!-- ****************** Total Salary Details End  *******************-->
        </div>
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

