<?= message_box('success'); ?>
<?= message_box('error'); ?>

<div class="row">
    <div class="col-sm-3">
        <?php echo form_open(base_url('admin/utilities/overtime'), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
        <label for="field-1" class="control-label pull-left holiday-vertical"><strong><?= lang('year') ?>
                :</strong></label>
        <div class="col-sm-8">
            <input type="text" name="year" required class="form-control years" value="<?php
            if (!empty($year)) {
                echo $year;
            }
            ?>" data-format="yyyy">
        </div>
        <button type="submit" data-toggle="tooltip" data-placement="top" title="Search"
                class="btn btn-purple pull-right">
            <i class="fa fa-search"></i></button>
        <?php echo form_close(); ?>
    </div>

    <div class="col-sm-9 mt">
        <a href="<?= base_url() ?>admin/utilities/add_overtime" class="text-danger" data-toggle="modal"
           data-placement="top" data-target="#myModal">
            <span class="fa fa-plus ">
            <?php if ($this->session->userdata('user_type') == 1) {
                $new = lang('new');
            } else {
                $new = lang('request_a');
            } ?>
            <?= $new . ' ' . lang('overtime') ?>

            </span></a>
    </div>
</div>

<div class="row">
    <div class="col-md-3 hidden-print"><!-- ************ Expense Report Month Start ************-->
        <ul class="mt nav nav-pills nav-stacked navbar-custom-nav">
            <?php
            foreach ($all_overtime_info as $key => $v_overtime_info):
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
    </div><!-- ************ Overtime Month End ************-->
    <div class="col-md-9"><!-- ************ Overtime Content Start ************-->

        <div class="tab-content pl0">
            <?php
            foreach ($all_overtime_info as $key => $v_overtime_info):

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
                                <strong><i class="fa fa-calendar"></i> <?php echo $month_name . ' ' . $year; ?></strong>
                                <div class="pull-right hidden-print">
                                    <span><?php echo btn_pdf('admin/utilities/overtime_report_pdf/' . $year . '/' . $key); ?></span>
                                </div>
                            </div>

                        </div>
                        <!-- Table -->
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-sm-1"><?= lang('sl') ?></th>
                                <th><?= lang('name') ?></th>
                                <th class="col-sm-2"><?= lang('overtime_date') ?></th>
                                <th class="col-sm-2"><?= lang('overtime_hour') ?></th>
                                <th><?= lang('status') ?></th>
                                <th class="col-sm-2"><?= lang('action') ?></th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $key = 1;
                            $hh = 0;
                            $mm = 0;
                            ?>
                            <?php if (!empty($v_overtime_info)): foreach ($v_overtime_info as $v_overtime) :
                                if ($v_overtime->status == 'pending') {
                                    $status = '<strong class="label label-warning">' . lang($v_overtime->status) . '</strong>';
                                } elseif ($v_overtime->status == 'approved') {
                                    $status = '<strong class="label label-success">' . lang($v_overtime->status) . '</strong>';
                                } else {
                                    $status = '<strong class="label label-danger">' . lang($v_overtime->status) . '</strong>';
                                }
                                ?>

                                <tr>
                                    <td><?php echo $key ?></td>
                                    <td><span data-toggle="tooltip" data-placement="top"
                                              title="<?= $v_overtime->notes ?>"><?php echo $v_overtime->fullname ?></span>
                                    </td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_overtime->overtime_date)) ?></td>
                                    <td><?php echo $v_overtime->overtime_hours; ?></td>
                                    <td><?= $status ?>

                                        <?php
                                        if ($this->session->userdata('user_type') == 1) {
                                            if ($v_overtime->status == 'pending') { ?>
                                                <a data-toggle="tooltip" data-placment="top"
                                                   title="<?= lang('approved') ?>"
                                                   href="<?= base_url() ?>admin/utilities/change_overtime_status/approved/<?= $v_overtime->overtime_id; ?>"
                                                   class="btn btn-xs btn-success ml"><i
                                                        class="fa fa-check"></i> </a>
                                                <a data-toggle="tooltip" data-placment="top"
                                                   title="<?= lang('reject') ?>"
                                                   href="<?= base_url() ?>admin/utilities/change_overtime_status/rejected/<?= $v_overtime->overtime_id; ?>"
                                                   class="btn btn-xs btn-danger ml"><i
                                                        class="fa fa-times"></i></a>
                                            <?php } elseif ($v_overtime->status == 'rejected') { ?>
                                                <a data-toggle="tooltip" data-placment="top"
                                                   title="<?= lang('approved') ?>"
                                                   href="<?= base_url() ?>admin/utilities/change_overtime_status/approved/<?= $v_overtime->overtime_id; ?>"
                                                   class="btn btn-xs btn-success ml"><i
                                                        class="fa fa-check"></i> </a>
                                            <?php } elseif ($v_overtime->status == 'approved') { ?>
                                                <a data-toggle="tooltip" data-placment="top"
                                                   title="<?= lang('reject') ?>"
                                                   href="<?= base_url() ?>admin/utilities/change_overtime_status/rejected/<?= $v_overtime->overtime_id; ?>"
                                                   class="btn btn-xs btn-danger ml"><i
                                                        class="fa fa-times"></i></a>
                                            <?php }
                                        }
                                        ?>
                                    </td>
                                    <?php $hh += $v_overtime->overtime_hours; ?>
                                    <?php $mm += date('i', strtotime($v_overtime->overtime_hours)); ?>
                                    <?php if ($this->session->userdata('user_type') == 1 || $this->session->userdata('user_id') == $v_overtime->user_id) { ?>
                                        <td>
                                            <?php echo btn_edit_modal('admin/utilities/add_overtime/' . $v_overtime->overtime_id) ?>

                                            <?php echo btn_delete('admin/utilities/delete_overtime/' . $v_overtime->overtime_id) ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php
                                $key++;
                            endforeach;
                                ?>
                                <tr class="total_amount">
                                    <td colspan="3" style="text-align: right;">
                                        <strong><?= lang('total_overtime_hour') ?> : </strong></td>
                                    <td colspan="2" style="padding-left: 8px;"><strong><?php
                                            if ($hh > 1 && $hh < 10 || $mm > 1 && $mm < 10) {
                                                $total_mm = '0' . $mm;
                                                $total_hh = '0' . $hh;
                                            } else {
                                                $total_mm = $mm;
                                                $total_hh = $hh;
                                            }
                                            if ($total_mm > 59) {
                                                $total_hh += intval($total_mm / 60);
                                                $total_mm = intval($total_mm % 60);
                                            }
                                            echo $total_hh . " : " . $total_mm . " m";
                                            ?></strong></td>
                                </tr>
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

    </div>
</div>
<!-- Overtime list tab Ends -->


<script type="text/javascript">
    function overtime_report(overtime_report) {
        var printContents = document.getElementById(overtime_report).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
