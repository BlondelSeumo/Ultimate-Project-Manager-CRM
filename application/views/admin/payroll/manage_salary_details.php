<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('manage_salary_details') ?></strong>
        </div>
    </div>
    <form id="form" role="form" enctype="multipart/form-data"
          action="<?php echo base_url() ?>admin/payroll/manage_salary_details" method="post"
          class="form-horizontal form-groups-bordered">
        <div class="panel-body">
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('select_department') ?> <span
                        class="required"> *</span></label>

                <div class="col-sm-5">
                    <select name="departments_id" class="form-control select_box" required>
                        <option value=""><?= lang('select_department') ?></option>
                        <?php if (!empty($all_department_info)): foreach ($all_department_info as $v_department_info) :
                            if (!empty($v_department_info->deptname)) {
                                $deptname = $v_department_info->deptname;
                            } else {
                                $deptname = lang('undefined_department');
                            }
                            ?>
                            <option value="<?php echo $v_department_info->departments_id; ?>"
                                <?php
                                if (!empty($departments_id)) {
                                    echo $v_department_info->departments_id == $departments_id ? 'selected' : '';
                                }
                                ?>><?php echo $deptname ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="sbtn" value="1" name="flag" class="btn btn-primary">Go
                    </button>
                </div>
            </div>
        </div>
    </form>
    <form id="form_validation" role="form" enctype="multipart/form-data"
          action="<?php echo base_url() ?>admin/payroll/save_salary_details" method="post"
          class="form-horizontal form-groups-bordered">
        <?php if (!empty($flag)): ?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?= lang('employee') . ' ' . lang('name') ?></th>
                    <th><?= lang('designation') ?></th>
                    <th><?= lang('hourly') ?></th>
                    <th><?= lang('monthly') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($employee_info)):foreach ($employee_info as $key => $v_emp_info): ?>
                    <?php if (!empty($v_emp_info)):foreach ($v_emp_info as $v_employee): ?>
                        <tr>
                            <td><input type="hidden" name="user_id[]"
                                       value="<?php echo $v_employee->user_id ?>"> <?php echo $v_employee->fullname; ?>
                            </td>
                            <td><?php echo $v_employee->designations ?></td>
                            <td style="width: 25%">
                                <div class="pull-left"><!-- /****** Hourly Payment Details  *********/ -->
                                    <div class="checkbox-inline c-checkbox">
                                        <label class="needsclick">
                                            <input name="hourly_status[]" id="<?php echo $v_employee->user_id ?>"
                                                   value="<?php echo $v_employee->user_id ?>" type="checkbox"
                                                <?php
                                                foreach ($salary_grade_info as $v_grade_salary) {
                                                    foreach ($v_grade_salary as $v_gsalary) {
                                                        if (!empty($v_gsalary)) {
                                                            if ($v_employee->user_id == $v_gsalary->user_id) {
                                                                echo $v_gsalary->hourly_rate_id ? 'checked ' : '';
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                   style="margin-right: 8px;" class="child_absent">
                                            <span class="fa fa-check"></span>
                                        </label>
                                    </div>
                                    <div id="l_category" class="pull-right">
                                        <select name="hourly_rate_id[]"
                                                class="form-control select_box">
                                            <option value=""><?= lang('select_hourly_grade') ?></option>
                                            <?php if (!empty($hourly_grade)) : foreach ($hourly_grade as $v_hourly_grade) : ?>
                                                <option value="<?php echo $v_hourly_grade->hourly_rate_id ?>"
                                                    <?php
                                                    foreach ($salary_grade_info as $v_grade_salary) {
                                                        foreach ($v_grade_salary as $v_gsalary) {
                                                            if (!empty($v_gsalary)) {
                                                                if ($v_employee->user_id == $v_gsalary->user_id) {
                                                                    echo $v_hourly_grade->hourly_rate_id == $v_gsalary->hourly_rate_id ? 'selected ' : '';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?> >
                                                    <?php echo $v_hourly_grade->hourly_grade ?></option>;
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div><!-- /****** Hourly Payment Details  *********/ -->
                            </td>
                            <td style="width: 25%">

                                <!-- /****** Monthly Payment Details  *********/ -->
                                <div class="pull-left">
                                    <div class="checkbox-inline c-checkbox">
                                        <label class="needsclick">
                                            <input name="monthly_status[]" id="<?php echo $v_employee->user_id ?>"
                                                   type="checkbox"
                                                <?php
                                                foreach ($salary_grade_info as $v_grade_salary_info) {
                                                    foreach ($v_grade_salary_info as $v_gsalary_info) {
                                                        if (!empty($v_gsalary_info)) {
                                                            if ($v_employee->user_id == $v_gsalary_info->user_id) {
                                                                echo $v_gsalary_info->salary_template_id ? 'checked ' : '';
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                   value="<?php echo $v_employee->user_id ?>" style="margin-left: 8px;"
                                                   class="child_absent">
                                            <span class="fa fa-check"></span>
                                        </label>
                                    </div>
                                    <div id="l_category" class="pull-right">
                                        <select name="salary_template_id[]"
                                                class="form-control select_box">
                                            <option value=""><?= lang('select_monthly_grade') ?></option>
                                            <?php if (!empty($salary_grade)) : foreach ($salary_grade as $v_salary_info) : ?>
                                                <option value="<?php echo $v_salary_info->salary_template_id ?>"
                                                    <?php
                                                    foreach ($salary_grade_info as $v_grade_salary_info) {
                                                        foreach ($v_grade_salary_info as $v_gsalary_info) {
                                                            if (!empty($v_gsalary_info)) {
                                                                if ($v_employee->user_id == $v_gsalary_info->user_id) {
                                                                    echo $v_salary_info->salary_template_id == $v_gsalary_info->salary_template_id ? 'selected ' : '';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?> >
                                                    <?php echo $v_salary_info->salary_grade ?></option>;
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div><!-- /****** Monthly Payment Details  *********/ -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php endif; ?>
                <?php if (empty($employee_info[0])) { ?>
                    <tr>
                        <td>
                            <?= lang('nothing_to_display') ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if (!empty($employee_info[0])) { ?>
                <div class="col-sm-8"></div>
                <div class="col-sm-4 row mt-lg pull-right">
                    <button id="salery_btn" type="submit"
                            class="btn btn-primary btn-block"><?= lang('update') ?></button>
                </div>
            <?php } ?>


            <!-- Hidden value when update  Start-->
            <input type="hidden" name="departments_id" value="<?php echo $departments_id ?>"/>
            <?php
            if (!empty($salary_grade_info)) {
                foreach ($salary_grade_info as $v_grade_salary_info) {
                    foreach ($v_grade_salary_info as $v_gsalary_info) {

                        if (!empty($v_gsalary_info)) {
                            ?>
                            <input type="hidden" name="payroll_id[]" value="<?php echo $v_gsalary_info->payroll_id ?>"/>
                            <?php
                        }
                    }
                }
            }
            ?>
        <?php endif; ?>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(':checkbox').on('change', function () {
            var th = $(this), id = th.prop('id');
            if (th.is(':checked')) {
                $(':checkbox[id="' + id + '"]').not($(this)).prop('checked', false);
            }
        });
    });
</script>
