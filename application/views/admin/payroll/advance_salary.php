<?php echo message_box('success'); ?>
<?php echo message_box('error');

?>
    <!-- ************ Expense Report List start ************-->
<?php if (empty($switch)) { ?>
    <div class="row">
        <div class="col-sm-3">
            <form action="<?php echo base_url() ?>admin/payroll/advance_salary" method="post">
                <label for="field-1" class="control-label pull-left holiday-vertical"><strong><?= lang('year') ?>
                        :</strong></label>
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
        <div class="col-sm-5 mt">
            <a href="<?= base_url() ?>admin/payroll/add_advance_salary" class="text-danger" data-toggle="modal"
               data-placement="top" data-target="#myModal">
            <span class="fa fa-plus ">
                <?php if ($this->session->userdata('user_type') == 1) {
                    $request = lang('new');
                } else {
                    $request = lang('apply');
                } ?>
                <?= $request . ' ' . lang('advance_salary') ?>
            </span></a>
        </div>
        <div class="col-sm-4 mt">
            <a href="<?= base_url() ?>admin/payroll/advance_salary/true" style="margin-right: 21px"
               class="btn btn-xs btn-info pull-right"
               data-toggle="tooltip"
               data-placement="top" title="<?= lang('switch_to_details') ?>">
                <i class="fa fa-undo"> </i><?= ' ' . lang('switch') ?>
            </a>
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
                    foreach ($advance_salary_info as $key => $v_advance_salary):
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
                                <i class="fa fa-calendar fa-fw"></i> <?php echo $month_name; ?> </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div><!-- ************ Expense Report Month End ************-->
            <div class="col-md-9"><!-- ************ Expense Report Content Start ************-->
                <div class="tab-content pl0">
                    <?php
                    foreach ($advance_salary_info as $key => $v_advance_salary):
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
                                            <span
                                                class="hidden-print"><?php echo btn_pdf('admin/payroll/advance_salary_pdf/' . $year . '/' . $key); ?></span>
                                        </div>
                                    </div>

                                </div>
                                <!-- Table -->
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th><?= lang('emp_id') ?></th>
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('deduct_month') ?></th>
                                        <th><?= lang('request_date') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <?php if ($this->session->userdata('user_type') == 1) { ?>
                                            <th><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $total_amount = 0;
                                    if (!empty($v_advance_salary)): foreach ($v_advance_salary as $advance_salary) : ?>
                                        <tr>
                                            <td><?php echo $advance_salary->employment_id ?></td>
                                            <td><?php echo $advance_salary->fullname ?></td>
                                            <td><?php echo display_money($advance_salary->advance_amount, default_currency());
                                                $total_amount += $advance_salary->advance_amount;
                                                ?></td>
                                            <td><?php echo date('Y M', strtotime($advance_salary->deduct_month)) ?></td>
                                            <td><?= strftime(config_item('date_format'), strtotime($advance_salary->request_date)) ?></td>

                                            <td><?php
                                                if ($advance_salary->status == '0') {
                                                    echo '<span class="label label-warning">' . lang('pending') . '</span>';
                                                } elseif ($advance_salary->status == '1') {
                                                    echo '<span class="label label-success"> ' . lang('accepted') . '</span>';
                                                } elseif ($advance_salary->status == '2') {
                                                    echo '<span class="label label-danger">' . lang('rejected') . '</span>';
                                                } else {
                                                    echo '<span class="label label-info">' . lang('paid') . '</span>';
                                                }
                                                ?></td>
                                            <?php if ($this->session->userdata('user_type') == 1) { ?>
                                                <td>
                                                    <a href="<?= base_url() ?>admin/payroll/advance_salary_details/<?= $advance_salary->advance_salary_id ?>"
                                                       class="btn btn-info btn-xs" title="<?= lang('view') ?>"
                                                       data-toggle="modal"
                                                       data-target="#myModal"><span
                                                            class="fa fa-list-alt"></span></a>
                                                </td>
                                            <?php } ?>

                                        </tr>
                                        <?php
                                        $key++;
                                    endforeach;
                                        ?>
                                        <tr class="total_amount">
                                            <td class="hidden-print"></td>
                                            <td colspan="1" style="text-align: right;">
                                                <strong><?= lang('total') . ' ' . lang('advance_salary') ?>
                                                    : </strong></td>
                                            <td colspan="3" style="padding-left: 8px;">
                                                <strong><?php echo display_money($total_amount, default_currency()); ?></strong>
                                            </td>
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
<?php } ?>

<?php if (!empty($switch)) { ?>
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6">
            <a href="<?= base_url() ?>admin/payroll/add_advance_salary/true" class="text-danger" data-toggle="modal"
               data-placement="top" data-target="#myModal">
            <span class="fa fa-plus ">
                <?php if ($this->session->userdata('user_type') == 1) {
                    $request = lang('new');
                } else {
                    $request = lang('apply');
                } ?>
                <?= $request . ' ' . lang('advance_salary') ?>
            </span></a>
        </div>
        <div class="col-sm-3 ">
            <a href="<?= base_url() ?>admin/payroll/advance_salary" style="margin-right: 16px"
               class="btn btn-xs btn-purple pull-right"
               data-toggle="tooltip"
               data-placement="top" title="<?= lang('switch_to_previous') ?>">
                <i class="fa fa-undo"> </i><?= ' ' . lang('switch') ?>
            </a>
        </div>
    </div>

    <div class="mt-sm">
        <div class="col-sm-3">
            <ul class="nav nav-pills nav-stacked navbar-custom-nav">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#leave_report"
                                                                   data-toggle="tab"><?= lang('advance_salary_report') ?></a>
                </li>
                <li class="my_advance_salary <?= $active == 2 ? 'active' : '' ?>"><a href="#my_leave"
                                                                                     data-toggle="tab"><?= lang('advance_salary') ?></a>
                </li>
                <?php if ($this->session->userdata('user_type') == 1) { ?>
                    <li class="all_advance_salary <?= $active == 2 ? 'active' : '' ?>"><a href="#all_leave"
                                                                                          data-toggle="tab"><?= lang('all_advance_salary') ?></a>
                    </li>
                <?php } ?>

            </ul>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="my_leave" style="position: relative;">
                <div class="panel panel-custom">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?= lang('advance_salary') ?>
                        </h3>
                    </div>
                    <div class="panel-body row form-horizontal task_details">

                        <!-- Table -->
                        <table class="table table-striped " id="DataTables" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('emp_id') ?></th>
                                <th><?= lang('name') ?></th>
                                <th><?= lang('amount') ?></th>
                                <th><?= lang('request_date') ?></th>
                                <th><?= lang('deduct_month') ?></th>
                                <th><?= lang('status') ?></th>
                                <?php if ($this->session->userdata('user_type') == 1) { ?>
                                    <th><?= lang('action') ?></th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $my_total = 0;
                            $my_advance_salary = $this->payroll_model->my_advance_salary_info();
                            if (!empty($my_advance_salary)) {
                                foreach ($my_advance_salary as $my_salary) {
                                    $my_total += $my_salary->advance_amount;
                                }
                            } ?>
                            </tbody>

                        </table>

                    </div>
                    <div class="panel-footer">
                        <strong><?= lang('total') . ' ' . lang('advance_salary') ?>:<span
                                class="label label-info"><?php echo display_money($my_total, default_currency()); ?></span>
                        </strong>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                $(document).ready(function () {
                    list = base_url + "admin/payroll/my_advance_salaryList";
                    <?php if (admin_head()) { ?>
                    $('.all_advance_salary').on('click', function () {
                        table_url(base_url + "admin/payroll/all_advance_salaryList");
                    });
                    <?php }?>
                    $('.my_advance_salary').on('click', function () {
                        table_url(base_url + "admin/payroll/my_advance_salaryList");
                    });

                });
            </script>

            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="leave_report" style="position: relative;">
                <div class="panel panel-custom">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?= lang('advance_salary_report') ?>
                            <form class="pull-right" action="<?php echo base_url() ?>admin/payroll/advance_salary"
                                  method="post">
                                <label for="field-1"
                                       class="control-label pull-left holiday-vertical"><strong><?= lang('year') ?>
                                        :</strong></label>
                                <div class="col-sm-8" style="margin-top: -7px">
                                    <input type="text" name="year" class="form-control years" value="<?php
                                    if (!empty($year)) {
                                        echo $year;
                                    }
                                    ?>" data-format="yyyy">
                                </div>
                                <button style="margin-top: -5px" type="submit" data-toggle="tooltip"
                                        data-placement="top" title="Search"
                                        class="btn btn-purple pull-right">
                                    <i class="fa fa-search"></i></button>
                            </form>
                        </h3>
                    </div>
                    <div class="panel-body">

                        <?php if ($this->session->userdata('user_type') == 1) { ?>
                            <div id="">
                                <div class="row mb panel-title pl-lg pb-sm"
                                     style="border-bottom: 1px solid #a0a6ad"><?= lang('all') . ' ' . lang('advance_salary_report') ?>

                                </div>
                                <div id="morris_line_all"></div>
                            </div>
                        <?php } ?>
                        <div class="mt-lg ">
                            <div class="row mb panel-title pl-lg pb-sm"
                                 style="border-bottom: 1px solid #a0a6ad"><?= lang('my_report') ?></div>
                            <div id="morris_line_my"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($this->session->userdata('user_type') == 1) { ?>
        <script type="text/javascript">
            $(function () {
                if (typeof Morris === 'undefined') return;

                var chartdata = [
                    <?php foreach ($advance_salary_info as $key => $v_advance_salary){
                    $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                    $total_amount = 0;
                    foreach ($v_advance_salary as $advance_salary) {
                        $total_amount += $advance_salary->advance_amount;
                    }
                    ?>
                    {
                        y: "<?= $month_name ?>",
                        all_report: <?= $total_amount?>,
                    },
                    <?php }?>


                ];
                // Line Chart
                // -----------------------------------

                new Morris.Line({
                    element: 'morris_line_all',
                    data: chartdata,
                    xkey: 'y',
                    ykeys: ["all_report"],
                    labels: ["<?= lang('advance_salary')?>"],
                    lineColors: ["#7266ba"],
                    parseTime: false,
                    resize: true
                });

            });

        </script>
    <?php } ?>
    <script type="text/javascript">
        $(function () {
            if (typeof Morris === 'undefined') return;

            var my_chartdata = [
                <?php foreach ($advance_salary_info as $mkey => $my_advance_salary){
                $my_month_name = date('F', strtotime($year . '-' . $mkey)); // get full name of month by date query
                $my_total = 0;
                foreach ($my_advance_salary as $my_advance_salary) {
                    if ($my_advance_salary->user_id == $this->session->userdata('user_id')) {
                        $my_total += $my_advance_salary->advance_amount;
                    }
                }
                ?>
                {
                    y: "<?= $my_month_name ?>",
                    my_report: <?= $my_total?>,
                },
                <?php }?>

            ];

            // Line Chart
            // -----------------------------------

            new Morris.Line({
                element: 'morris_line_my',
                data: my_chartdata,
                xkey: 'y',
                ykeys: ["my_report"],
                labels: ["<?= lang('my_advance_salary')?>"],
                lineColors: ["#23b7e5"],
                parseTime: false,
                resize: true
            });

        });

    </script>
<?php } ?>