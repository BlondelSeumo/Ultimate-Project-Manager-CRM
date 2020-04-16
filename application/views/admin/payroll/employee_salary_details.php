<div id="printableArea">
    <div class="modal-header ">
        <h4 class="modal-title" id="myModalLabel"><?= lang('employee_salary_details') ?>
            <div class="pull-right ">
                <span><?php echo btn_pdf('admin/payroll/make_pdf/' . $emp_salary_info->user_id); ?></span>
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
                        <?php if ($emp_salary_info->avatar): ?>
                            <img src="<?php echo base_url() . $emp_salary_info->avatar; ?>"
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
                            <h3><?php echo $emp_salary_info->fullname; ?></h3>
                            <hr class="mt0"/>
                            <table class="table-hover">
                                <tr>
                                    <td><strong><?= lang('emp_id') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$emp_salary_info->employment_id"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('departments') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$emp_salary_info->deptname"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('designation') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?php echo "$emp_salary_info->designations"; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?= lang('joining_date') ?></strong> :</td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><?= strftime(config_item('date_format'), strtotime($emp_salary_info->joining_date)) ?></td>
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
                        <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('salary_grade') ?>
                                :</strong></label>
                        <p class="form-control-static"><?php echo $emp_salary_info->salary_grade; ?></p>
                    </div>
                    <div class="">
                        <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('basic_salary') ?>
                                :</strong>
                        </label>
                        <p class="form-control-static"><?php
                            $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                            echo display_money($emp_salary_info->basic_salary, $curency->symbol);
                            ?></p>
                    </div>
                    <div class="">
                        <label for="field-1" class="col-sm-5 control-label"><strong><?= lang('overtime') ?>
                                <small>(<?= lang('per_hour') ?>)</small>
                                :</strong> </label>
                        <p class="form-control-static">
                            <?php
                            if (!empty($emp_salary_info->overtime_salary)) {
                                echo display_money($emp_salary_info->overtime_salary, $curency->symbol);
                            }
                            ?>
                        </p>
                    </div>

                    <!-- ***************** Salary Details  Ends *********************-->

                    <!-- ******************-- Allowance Panel Start **************************-->
                    <div class="col-sm-6">
                        <div class="panel panel-custom">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <strong><?= lang('allowances') ?></strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php
                                $total_salary = 0;
                                if (!empty($salary_allowance_info)):foreach ($salary_allowance_info as $v_allowance_info):
                                    ?>
                                    <div class="">
                                        <label
                                            class="col-sm-6 control-label"><strong><?php echo $v_allowance_info->allowance_label; ?>
                                                : </strong></label>
                                        <p class="form-control-static"><?php echo display_money($v_allowance_info->allowance_value, $curency->symbol) ?></p>
                                    </div>
                                    <?php $total_salary += $v_allowance_info->allowance_value; ?>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <h2> <?= lang('nothing_to_display') ?></h2>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div><!-- ********************Allowance End ******************-->

                    <!-- ************** Deduction Panel Column  **************-->
                    <div class="col-sm-6">
                        <div class="panel panel-custom">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <strong><?= lang('deductions') ?></strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php
                                $total_deduction = 0;
                                if (!empty($salary_deduction_info)):foreach ($salary_deduction_info as $v_deduction_info):
                                    ?>
                                    <div class="">
                                        <label
                                            class="col-sm-6 control-label"><strong><?php echo $v_deduction_info->deduction_label; ?>
                                                : </strong></label>
                                        <p class="form-control-static"><?php
                                            echo display_money($v_deduction_info->deduction_value, $curency->symbol);
                                            ?></p>
                                    </div>
                                    <?php $total_deduction += $v_deduction_info->deduction_value ?>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <h2> <?= lang('nothing_to_display') ?></h2>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div><!-- ****************** Deduction End  *******************-->
                </div>
            </div>
        </div>

        <!-- ************** Total Salary Details Start  **************-->
        <div class="row">
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
                                if (!empty($total_salary) || !empty($emp_salary_info->basic_salary)) {
                                    $total = $total_salary + $emp_salary_info->basic_salary;
                                    echo display_money($total, $curency->symbol);
                                }
                                ?></p>
                        </div>
                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('total_deduction') ?>
                                    : </strong></label>
                            <p class="form-control-static"><?php
                                if (!empty($total_deduction)) {
                                    echo display_money($total_deduction, $curency->symbol);
                                }
                                ?></p>
                        </div>
                        <div class="">
                            <label class="col-sm-6 control-label"><strong><?= lang('net_salary') ?>
                                    : </strong></label>
                            <p class="form-control-static"><?php
                                $net_salary = $total - $total_deduction;
                                echo display_money($net_salary, $curency->symbol);
                                ?></p>
                        </div>
                    </div>
                </div>
            </div><!-- ****************** Total Salary Details End  *******************-->
        </div><!-- ****************** Total Salary Details End  *******************-->
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

