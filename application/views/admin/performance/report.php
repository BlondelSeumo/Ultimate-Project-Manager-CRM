<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<!-- ************ Expense Report List start ************-->

<div class="row">
    <div class="col-sm-3">
        <form data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/performance/performance_report"
              method="post">
            <label for="field-1" class="control-label pull-left holiday-vertical"><strong><?= lang('year') ?>:</strong></label>
            <div class="col-sm-8">
                <input type="text" required name="year" class="form-control years" value="<?php
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
    <?php if ($this->session->userdata('user_type') == 1) { ?>
        <div class="col-sm-9 mt">
            <a href="<?= base_url() ?>admin/performance/give_performance_appraisal" class="text-danger">
            <span class="fa fa-plus ">
                <?= lang('give_appraisal') ?>
            </span></a>
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-md-3 hidden-print"><!-- ************ Performance Report Month Start ************-->
        <ul class="mt nav nav-pills nav-stacked navbar-custom-nav">
            <?php
            foreach ($all_performance_info as $key => $v_performance_info):
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
    </div><!-- ************ Performance Report Month End ************-->

    <div class="col-md-9"><!-- ************ Expense Report Content Start ************-->
        <div class="tab-content pl0">
            <?php
            foreach ($all_performance_info as $key => $v_performance_info):
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
                            </div>
                        </div>
                        <!-- Table -->
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-sm-1"><?= lang('emp_id') ?></th>
                                <th>
                                    <?= lang('employee') . ' ' . lang('name') ?>
                                </th>
                                <th><?= lang('departments') ?> > <?= lang('designation') ?></th>
                                <th class="col-sm-4"><?= lang('remarks') ?></th>
                                <th class="col-sm-1"><?= lang('action') ?></th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $key = 1;
                            ?>
                            <?php if (!empty($v_performance_info)): foreach ($v_performance_info as $appraisal_info) : ?>
                                <tr>
                                    <td><?php echo $appraisal_info->employment_id ?></td>
                                    <td>
                                        <a href="<?= base_url() ?>admin/performance/appraisal_details/<?= $appraisal_info->performance_appraisal_id ?>"
                                           title="<?= lang('view') ?>" data-toggle="modal"
                                           data-target="#myModal_lg">
                                            <?php echo $appraisal_info->fullname ?>
                                        </a>
                                    </td>
                                    <td><?php echo $appraisal_info->deptname . ' > ' . $appraisal_info->designations ?></td>
                                    <td><?php echo $appraisal_info->general_remarks; ?></td>
                                    <td>
                                        <?php echo btn_view_modal('admin/performance/appraisal_details/' . $appraisal_info->performance_appraisal_id); ?>
                                    </td>
                                </tr>
                                <?php
                                $key++;
                            endforeach;
                                ?>
                            <?php else : ?>
                                <td colspan="5">
                                    <strong><?= lang('nothing_to_display') ?></strong>
                                </td>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div><!-- ************ Performance Report Content Start ************-->
</div>

