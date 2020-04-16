<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('99', 'created');
$edited = can_action('99', 'edited');
$deleted = can_action('99', 'deleted');
?>
<?php if (empty($switch)) { ?>
    <div class="row">
        <div class="col-sm-3">
            <form action="<?php echo base_url() ?>admin/award/index" method="post">
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
            <?php if (!empty($created)) { ?>
                <a href="<?= base_url() ?>admin/award/give_award" class="btn btn-xs btn-danger" data-toggle="modal"
                   data-placement="top" data-target="#myModal">
                    <i class="fa fa-plus "></i> <?= ' ' . lang('give_award') ?></a>
            <?php } ?>
        </div>
        <div class="col-sm-4 mt">
            <a href="<?= base_url() ?>admin/award/index/true" style="margin-right: 21px"
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
                    foreach ($all_employee_award as $key => $v_employee_award):
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
                    foreach ($all_employee_award as $key => $v_employee_award):
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
                                                class="hidden-print"><?php echo btn_pdf('admin/award/employee_award_pdf/' . $year . '/' . $key); ?></span>
                                        </div>
                                    </div>

                                </div>
                                <!-- Table -->
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-sm-1"><?= lang('emp_id') ?></th>
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('award_name') ?></th>
                                        <th><?= lang('gift') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('month') ?></th>
                                        <th><?= lang('award_date') ?></th>
                                        <?php if (!empty($deleted) || !empty($edited)) { ?>
                                            <th><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($v_employee_award)): foreach ($v_employee_award as $employee_award) :
                                        $account_details = get_staff_details($employee_award->user_id);
                                        ?>
                                        <tr id="#table_<?= $employee_award->employee_award_id ?>">
                                            <td><?php echo $account_details->employment_id ?></td>
                                            <td><?php echo $account_details->fullname ?></td>
                                            <td><?php echo $employee_award->award_name; ?></td>
                                            <td><?php echo $employee_award->gift_item; ?></td>
                                            <td><?php echo display_money($employee_award->award_amount, default_currency()) ?></td>
                                            <td><?= date('M,Y', strtotime($employee_award->award_date)) ?></td>
                                            <td><?= display_date($employee_award->given_date) ?></td>
                                            <?php if (!empty($deleted) || !empty($edited)) { ?>
                                                <td>
                                                    <?php if (!empty($edited)) { ?>
                                                        <span data-toggle="tooltip" data-placement="top"
                                                              title="<?= lang('edit') ?>">
                        <a href="<?= base_url() ?>admin/award/give_award/<?= $employee_award->employee_award_id ?>"
                           class="btn btn-xs btn-primary"
                           data-toggle="modal"
                           data-placement="top" data-target="#myModal">
                            <i class="fa fa-pencil-square-o "></i>
                        </a>
                            </span>
                                                    <?php }
                                                    if (!empty($deleted)) { ?>
                                                        <?php echo ajax_anchor(base_url('admin/award/delete_employee_award/' . $employee_award->employee_award_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $employee_award->employee_award_id)); ?>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        $key++;
                                    endforeach;
                                        ?>
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
    <div class="panel panel-custom" style="border: none;" data-collapsed="0">
        <div class="panel-heading">
            <div class="panel-title">
                <?= lang('award_list') ?>
                <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">

                    <?php if (!empty($created)) { ?>
                        <a href="<?= base_url() ?>admin/award/give_award" class="btn btn-xs btn-info"
                           data-toggle="modal"
                           data-placement="top" data-target="#myModal">
                            <i class="fa fa-plus "></i> <?= ' ' . lang('give_award') ?></a>
                    <?php } ?>

                    <span><?php echo btn_pdf('admin/award/employee_award_pdf'); ?></span>
                    <a href="<?= base_url() ?>admin/award"
                       class="btn btn-xs btn-purple"
                       data-toggle="tooltip"
                       data-placement="top" title="<?= lang('switch_to_previous') ?>">
                        <i class="fa fa-undo"> </i><?= ' ' . lang('switch') ?>
                    </a>
                    <div class="btn-group filtered">
                        <button class="btn btn-xs btn-primary dropdown-toggle " data-toggle="dropdown"
                                aria-expanded="false">
                            <i class="fa fa-search"></i><span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right group animated zoomIn"
                            style="width:300px;">
                            <li class="filter_by"><a href="#"><?php echo lang('all'); ?></a></li>
                            <li class="divider"></li>
                            <?php
                            $all_staff = get_staff_details();
                            if (!empty($all_staff)) {
                                foreach ($all_staff as $v_staff) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_staff->user_id ?>">
                                        <a href="#"><?php echo $v_staff->fullname . ' (' . designation($v_staff->user_id) . ') '; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <!-- Table -->
        <div class="panel-body">
            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th class="col-sm-1"><?= lang('emp_id') ?></th>
                    <th><?= lang('name') ?></th>
                    <th><?= lang('award_name') ?></th>
                    <th><?= lang('gift') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('month') ?></th>
                    <th><?= lang('award_date') ?></th>
                    <?php if (!empty($deleted) || !empty($edited)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "admin/award/awardList";
                        $('.filtered > .dropdown-toggle').on('click', function () {
                            if ($('.group').css('display') == 'block') {
                                $('.group').css('display', 'none');
                            } else {
                                $('.group').css('display', 'block')
                            }
                        });
                        $('.filter_by').on('click', function () {
                            $('.filter_by').removeClass('active');
                            $('.group').css('display', 'block');
                            $(this).addClass('active');
                            var filter_by = $(this).attr('id');
                            if (filter_by) {
                                filter_by = filter_by + '/1';
                            } else {
                                filter_by = '';
                            }
                            table_url(base_url + "admin/award/awardList/" + filter_by);
                        });
                    });
                </script>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>
