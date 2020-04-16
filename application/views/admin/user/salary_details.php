<div class="form-horizontal">

    <!-- ********************************* Salary Details Panel ***********************-->

    <div class="panel panel-custom">

        <div class="panel-heading">

            <div class="panel-title">

                <strong><?= lang('salary_details') ?></strong>

                <?php

                // get all employee salary info   by id

                $emp_salary_info = $this->payroll_model->get_emp_salary_list($id);

                if (!empty($emp_salary_info)) {


                    // get salary allowance info by  salary template id

                    $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name

                    $this->payroll_model->_order_by = "salary_allowance_id"; // $id

                    $salary_allowance_info = $this->payroll_model->get_by(array('salary_template_id' => $emp_salary_info->salary_template_id), FALSE);


                    // get salary deduction info by salary template id

                    $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name

                    $this->payroll_model->_order_by = "salary_deduction_id"; // $id

                    $salary_deduction_info = $this->payroll_model->get_by(array('salary_template_id' => $emp_salary_info->salary_template_id), FALSE);

                    ?>

                    <div class="pull-right ">

                        <span><?php echo btn_pdf('admin/payroll/make_pdf/' . $emp_salary_info->user_id); ?></span>

                    </div>

                <?php } ?>

            </div>

        </div>

        <?php

        if (!empty($emp_salary_info)) {

            ?>

            <div class="panel-body">

                <div class="">

                    <label for="field-1"

                           class="col-sm-5 control-label"><strong><?= lang('salary_grade') ?>

                            :</strong></label>

                    <p class="form-control-static"><?php echo((!empty($emp_salary_info->salary_template_id)) ? $emp_salary_info->salary_grade : $emp_salary_info->hourly_grade); ?></p>

                </div>

                <div class="">

                    <label for="field-1"

                           class="col-sm-5 control-label"><strong><?= lang('basic_salary') ?>

                            :</strong>

                    </label>
                    <?php if (!empty($emp_salary_info->salary_template_id)) {
                        $basic_salary = $emp_salary_info->basic_salary;
                    } else {
                        $basic_salary = $emp_salary_info->hourly_rate;
                    }
                    if (empty($basic_salary)) {
                        $basic_salary = 0;
                    }
                    ?>

                    <p class="form-control-static"><?php

                        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();

                        echo display_money($basic_salary, $curency->symbol);

                        ?></p>

                </div>
                <?php if (!empty($emp_salary_info->overtime_salary)) { ?>
                    <div class="">

                        <label for="field-1"

                               class="col-sm-5 control-label"><strong><?= lang('overtime') ?>

                                <small>(<?= lang('per_hour') ?>)</small>

                                :</strong> </label>

                        <p class="form-control-static">

                            <?php echo display_money($emp_salary_info->overtime_salary, $curency->symbol); ?>

                        </p>

                    </div>
                <?php } ?>


                <!-- ***************** Salary Details  Ends *********************-->


                <!-- ******************-- Allowance Panel Start **************************-->
                <?php
                $total_salary = 0;
                $total_deduction = 0;
                if (!empty($emp_salary_info->salary_template_id)) { ?>
                    <div class="col-sm-6">

                        <div class="row panel panel-custom" style="margin-left: -30px">

                            <div class="panel-heading">

                                <div class="panel-title">

                                    <strong><?= lang('allowances') ?></strong>

                                </div>

                            </div>

                            <div class="panel-body">

                                <?php

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

                        <div class=" panel panel-custom" style="margin-right: -30px">

                            <div class="panel-heading">

                                <div class="panel-title">

                                    <strong><?= lang('deductions') ?></strong>

                                </div>

                            </div>

                            <div class="panel-body">

                                <?php

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
                <?php } ?>
                <div class="form-horizontal col-sm-8 pull-right">

                    <div class="panel panel-info" style="margin-right: -30px">

                        <div class="panel-heading">

                            <div class="panel-title">

                                <strong><?= lang('total_salary_details') ?></strong>

                            </div>

                        </div>

                        <div class="panel-body">

                            <div class="">

                                <label

                                    class="col-sm-6 control-label"><strong><?= lang('gross_salary') ?>

                                        : </strong></label>

                                <p class="form-control-static"><?php

                                    if (!empty($total_salary) || !empty($basic_salary)) {

                                        $total = $total_salary + $basic_salary;
                                        if (!empty($total)) {
                                            $total = $total;
                                        } else {
                                            $total = 0;
                                        }

                                        echo display_money($total, $curency->symbol);

                                    }

                                    ?></p>

                            </div>

                            <div class="">

                                <label

                                    class="col-sm-6 control-label"><strong><?= lang('total_deduction') ?>

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
                                    if (!empty($total)) {
                                        $total = $total;
                                    } else {
                                        $total = 0;
                                    }

                                    $net_salary = $total - $total_deduction;

                                    echo display_money($net_salary, $curency->symbol);

                                    ?></p>

                            </div>

                        </div>

                    </div>

                </div><!-- ****************** Total Salary Details End  *******************-->

            </div>

        <?php } else {

            echo lang('no_data');

        } ?>

    </div>

</div>