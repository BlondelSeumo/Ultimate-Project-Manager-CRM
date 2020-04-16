<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<!-- ************ Expense Report List start ************-->
<?= message_box('success'); ?>
<?= message_box('error'); ?>

<div class="row">
    <div class="col-sm-3">
        <form id="existing_customer" action="<?php echo base_url() ?>admin/payroll/provident_fund" method="post">
            <label for="field-1" class="control-label pull-left holiday-vertical"><strong><?= lang('year') ?>:</strong></label>
            <div class="col-sm-8">
                <input type="text" name="year" class="form-control years" value="<?php
                if (!empty($year)) {
                    echo $year;
                }
                ?>" data-format="yyyy">
            </div>
            <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                    class="btn btn-purple pull-right">
                <i class="fa fa-search"></i></button>
        </form>
    </div>
</div>

<div id="advance_salary">
    <div class="show_print" style="width: 100%; border-bottom: 2px solid black;margin-bottom: 20px;">
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
    </div><!--            show when print start-->
    <div class="row">
        <div class="col-md-3 hidden-print"><!-- ************ Expense Report Month Start ************-->
            <ul class="mt nav nav-pills nav-stacked navbar-custom-nav">
                <?php
                foreach ($provident_fund_info as $key => $v_provident_fund):
                    $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                    ?>
                    <li class="<?php
                    if ($current_month == $key) {
                        echo 'active';
                    }
                    ?>">
                        <a aria-expanded="<?php
                        if ($current_month == $key) {
                            echo 'true';
                        } else {
                            echo 'false';
                        }
                        ?>" data-toggle="tab" href="#<?php echo $month_name ?>">
                            <i class="fa fa-fw fa-calendar"></i> <?php echo $month_name; ?> </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div><!-- ************ Expense Report Month End ************-->
        <div class="col-md-9"><!-- ************ Expense Report Content Start ************-->
            <div class="tab-content pl0">
                <?php
                foreach ($provident_fund_info as $key => $v_provident_fund):

                    $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                    ?>
                    <div id="<?php echo $month_name ?>" class="tab-pane <?php
                    if ($current_month == $key) {
                        echo 'active';
                    }
                    ?>">
                        <div class="panel panel-custom">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <strong><i class="fa fa-calendar"></i> <?php echo $month_name . ' ' . $year; ?>
                                    </strong>
                                    <div class="pull-right hidden-print">
                                            <span class="hidden-print"><?php echo btn_pdf('admin/payroll/provident_fund_pdf/' . $year . '/' . $key); ?></span>
                                    </div>
                                </div>

                            </div>
                            <!-- Table -->
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th><?= lang('emp_id') ?></th>
                                    <th><?= lang('name') ?></th>
                                    <th><?= lang('payment_date') ?></th>
                                    <th><?= lang('amount') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $total_amount = 0;
                                $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                ?>
                                <?php if (!empty($v_provident_fund)): foreach ($v_provident_fund as $provident_fund) : ?>
                                    <tr>
                                        <td><?php echo $provident_fund->employment_id ?></td>
                                        <td><?php echo $provident_fund->fullname ?></td>
                                        <td><?= strftime(config_item('date_format'), strtotime($provident_fund->paid_date)) ?></td>
                                        <td><?php echo display_money($provident_fund->salary_payment_deduction_value, $curency->symbol);
                                            $total_amount += $provident_fund->salary_payment_deduction_value;
                                            ?></td>

                                    </tr>
                                    <?php
                                    $key++;
                                endforeach;
                                    ?>
                                    <tr class="total_amount">
                                        <td colspan="3" style="text-align: right;">
                                            <strong><?= lang('total') . ' ' . lang('provident_fund') ?>
                                                : </strong></td>
                                        <td colspan="3" style="padding-left: 8px;"><strong><?php
                                                echo display_money($total_amount, $curency->symbol);
                                                ?></strong></td>
                                    </tr>
                                <?php else : ?>
                                    <td colspan="6">
                                        <strong><?= lang('nothing_to_display') ?></strong>
                                    </td>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div><!-- ************ Expense Report Content Start ************-->
    </div><!-- ************ Expense Report List End ************-->

</div>
<script type="text/javascript">
    function advance_salary(advance_salary) {
        var printContents = document.getElementById(advance_salary).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
