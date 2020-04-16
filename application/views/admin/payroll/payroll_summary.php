<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="panel panel-custom"><!-- *********     Employee Search Panel ***************** -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('payroll_summary') ?></strong>
                </div>
            </div>
            <form id="form" role="form" enctype="multipart/form-data"
                  action="<?php echo base_url() ?>admin/payroll/payroll_summary" method="post"
                  class="form-horizontal form-groups-bordered">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('search_type') ?> <span
                                class="required"> *</span></label>

                        <div class="col-sm-5">
                            <select required name="search_type" id="search_type" class="form-control ">
                                <option value=""><?= lang('select') . ' ' . lang('search_type') ?></option>
                                <option value="employee" <?php if (!empty($search_type)) {
                                    echo $search_type == 'employee' ? 'selected' : '';
                                } ?>><?php echo lang('by') . ' ' . lang('employee') ?></option>

                                <option value="month" <?php if (!empty($search_type)) {
                                    echo $search_type == 'month' ? 'selected' : '';
                                } ?>><?php echo lang('by') . ' ' . lang('month') ?></option>

                                <option value="period" <?php if (!empty($search_type)) {
                                    echo $search_type == 'period' ? 'selected' : '';
                                } ?>><?php echo lang('by') . ' ' . lang('period') ?></option>

                                <option value="activities" <?php if (!empty($search_type)) {
                                    echo $search_type == 'activities' ? 'selected' : '';
                                } ?>><?php echo lang('all') . ' ' . lang('activities') ?></option>

                            </select>
                        </div>
                    </div>

                    <div class="by_employee"
                         style="display: <?= !empty($search_type) && $search_type == 'employee' ? 'block' : 'none' ?>">
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('employee') . ' ' . lang('name') ?>
                                <span
                                    class="required"> *</span></label>

                            <div class="col-sm-5">
                                <select class="by_employee form-control select_box" style="width: 100%" name="user_id">
                                    <option value=""><?= lang('select_employee') ?>...</option>
                                    <?php
                                    $all_employee = $this->payroll_model->get_all_employee();
                                    if (!empty($all_employee)): ?>
                                        <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                            <optgroup label="<?php echo $dept_name; ?>">
                                                <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                                    <option value="<?php echo $v_employee->user_id; ?>"
                                                        <?php
                                                        if (!empty($user_id)) {
                                                            echo $v_employee->user_id == $user_id ? 'selected' : '';
                                                        }
                                                        ?>><?php echo $v_employee->fullname . ' ( ' . $v_employee->designations . ' )' ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="by_month"
                         style="display: <?= !empty($search_type) && $search_type == 'month' ? 'block' : 'none' ?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('month') ?> <span
                                    class="required"> *</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" value="<?php
                                    if (!empty($by_month)) {
                                        echo $by_month;
                                    }
                                    ?>" class="form-control monthyear by_month" name="by_month"
                                           data-format="yyyy/mm/dd">

                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="by_period"
                         style="display: <?= !empty($search_type) && $search_type == 'period' ? 'block' : 'none' ?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= lang('start') . ' ' . lang('month') ?> <span
                                    class="required"> *</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" value="<?php
                                    if (!empty($start_month)) {
                                        echo $start_month;
                                    }
                                    ?>" class="by_period form-control monthyear" name="start_month"
                                           data-format="yyyy/mm/dd">

                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= lang('end') . ' ' . lang('month') ?> <span
                                    class="required"> *</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" value="<?php
                                    if (!empty($end_month)) {
                                        echo $end_month;
                                    }
                                    ?>" class="by_period form-control monthyear" name="end_month"
                                           data-format="yyyy/mm/dd">

                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="border-none">
                        <label for="field-1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-5">
                            <button id="submit" type="submit" name="flag" value="1"
                                    class="btn btn-primary btn-block"><?= lang('go') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div><!-- ******************** Employee Search Panel Ends ******************** -->
    </div>
</div>
<?php if (!empty($search_type) && $search_type != 'activities') {
if ($search_type == 'period') {
    $by = ' - ' . ' ' . date('F-Y', strtotime($start_month)) . ' ' . lang('TO') . ' ' . date('F-Y', strtotime($end_month));
    $pdf = $start_month . 'n' . $end_month;
}
if ($search_type == 'month') {
    $by = ' - ' . ' ' . date('F-Y', strtotime($by_month));
    $pdf = $by_month;
}
if ($search_type == 'employee') {
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $by = ' - ' . ' ' . ' ' . $user_info->fullname;
    $pdf = $user_id;
}
?>
<div id="payment_history" class="all_payment_history">
    <div class="show_print" style="width: 100%; border-bottom: 2px solid black;margin-bottom: 30px">
        <!-- show when print start-->
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
    <!--  **************** show when print End ********************* -->
    <div class="panel panel-custom">
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('payroll_summary') . ' ' . $by ?></strong>
                <div class="pull-right"><!-- set pdf,Excel start action -->
                    <label class="hidden-print control-label pull-left hidden-xs">
                        <button class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top"
                                title="<?= lang('print') ?>" type="button"
                                onclick="payment_history('payment_history')"><i class="fa fa-print"></i>
                        </button>
                        <span><?php echo btn_pdf('admin/payroll/payroll_summary_pdf/' . $search_type . '/' . $pdf); ?></span>
                    </label>
                </div><!-- set pdf,Excel start action -->
            </div>
        </div>


        <!-- Table -->
        <table class="table table-striped " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('month') ?></th>
                <th><?= lang('date') ?></th>
                <th><?= lang('gross_salary') ?></th>
                <th><?= lang('total_deduction') ?></th>
                <th><?= lang('net_salary') ?></th>
                <th><?= lang('fine_deduction') ?></th>
                <th><?= lang('amount') ?></th>
                <th class="hidden-print"><?= lang('details') ?></th>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                $(document).ready(function () {
                    <?php if($search_type == 'employee'){?>
                    list = base_url + "admin/payroll/payment_historyList/<?= $pdf ?>";
                    <?php }?>

                    <?php if($search_type == 'month'){?>
                    list = base_url + "admin/payroll/payment_historyMonth/<?= $pdf ?>";
                    <?php }?>

                    <?php if($search_type == 'period'){?>
                    list = base_url + "admin/payroll/payment_historyPeriod/<?= $pdf ?>";
                    <?php }?>
                });
            </script>

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
                    <tr>
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
                        <td class="hidden-print">
                            <a href="<?php echo base_url() ?>admin/payroll/salary_payment_details/<?php echo $v_payroll->salary_payment_id ?>"
                               class="btn btn-info btn-xs" title="View" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-list-alt"></span></a></td>
                    </tr>
                <?php }; ?>
            <?php } else { ?>
                <tr>
                    <td colspan="8"><?= lang('nothing_to_display') ?></td>
                </tr>
            <?php }; ?>
            </tbody>
        </table>
    </div><!--************ Payment History End***********-->
    <script type="text/javascript">
        function payment_history(payment_history) {
            var printContents = document.getElementById(payment_history).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
    <?php } ?>
</div>
<div class="by_activities"
     style="display: <?= !empty($search_type) && $search_type == 'activities' ? 'block' : 'none' ?>">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('payroll_summary'); ?>
                    <a onclick="return confirm('<?= lang('delete_alert') ?>')"
                       href="<?= base_url() ?>admin/payroll/clear_activities"
                       class="btn btn-xs btn-primary pull-right"><?= lang('clear') ?></a>
                </div>
            </div>
            <table class="table table-striped" id="Transation_DataTables">
                <thead>
                <tr>
                    <th class="col-xs-2"><?= lang('activity_date') ?></th>
                    <th class="col-xs-3"><?= lang('user') ?></th>
                    <th class="col-xs-1"><?= lang('module') ?></th>

                    <th><?= lang('activity') ?></th>

                </tr>
                </thead>
                <tbody>
                <?php
                $activities_info = $this->db->where('module', 'payroll')->get('tbl_activities')->result();
                if (!empty($activities_info)) {
                    foreach ($activities_info as $v_activity) {

                        ?>
                        <tr>
                            <td><?= display_datetime($v_activity->activity_date); ?></td>
                            <td><?= $this->db->where('user_id', $v_activity->user)->get('tbl_account_details')->row()->fullname; ?></td>
                            <td><?= $v_activity->module ?></td>
                            <td>
                                <?= lang($v_activity->activity) ?>
                                <strong> <?= $v_activity->value1 . ' ' . $v_activity->value2 ?></strong>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#Transation_DataTables').dataTable({
                paging: false,
                "bSort": false
            });
        });
    </script>
</div>
