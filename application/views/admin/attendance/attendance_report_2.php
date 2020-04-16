<style type="text/css" media="print">
    @media print {
        @page {
            size: landscape
        }
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('attendance_report') ?></strong>
                </div>
            </div>
            <div class="panel-body">
                <form id="attendance-form" role="form" enctype="multipart/form-data"
                      action="<?php echo base_url(); ?>admin/attendance/get_report_2" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('department') ?><span
                                class="required">*</span></label>

                        <div class="col-sm-5">
                            <select required name="departments_id" class="form-control select_box">
                                <option value=""><?= lang('select') . ' ' . lang('department') ?></option>
                                <?php if (!empty($all_department)): foreach ($all_department as $department):
                                    if (!empty($department->deptname)) {
                                        $deptname = $department->deptname;
                                    } else {
                                        $deptname = lang('undefined_department');
                                    }
                                    ?>
                                    <option value="<?php echo $department->departments_id; ?>"
                                        <?php if (!empty($departments_id)): ?>
                                            <?php echo $department->departments_id == $departments_id ? 'selected ' : '' ?>
                                        <?php endif; ?>>
                                        <?php echo $deptname ?>
                                    </option>
                                    <?php
                                endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('month') ?><span
                                class="required"> *</span></label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <input required type="text" class="form-control monthyear" value="<?php
                                if (!empty($date)) {
                                    echo date('Y-n', strtotime($date));
                                }
                                ?>" name="date">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-5 ">
                            <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('search') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="EmpprintReport">
    <?php if (!empty($attendance)): ?>
        <div class="show_print" hidden style="background-color: rgb(224, 224, 224);margin-bottom: 5px;padding: 5px;">
            <table style="margin: 3px 10px 0px 24px; width:100%;">
                <tr>
                    <td style="font-size: 15px"><strong><?= lang('department') ?>
                            : </strong><?php echo $dept_name->deptname ?>
                    </td>
                    <td style="font-size: 15px"><strong><?= lang('date') ?> :</strong><?php echo $month ?></td>
                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-sm-12 std_print">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <strong><?= lang('attendance_list') . ' ' . lang('of') . ' ' . $month; ?> </strong>
                            <div class="pull-right hidden-print">
                                <a href="<?= base_url() ?>admin/attendance/attendance_pdf/2/<?= $departments_id . '/' . $date; ?>"
                                   class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top"
                                   title="<?= lang('pdf') ?>"><span><i class="fa fa-file-pdf-o"></i></span></a>
                                <a href="" onclick="printEmp_report('EmpprintReport')" class="btn btn-danger btn-xs"
                                   data-toggle="tooltip" data-placement="top" title="<?= lang('print') ?>"><span><i
                                            class="fa fa-print"></i></span></a>
                            </div>
                        </h3>
                    </div>
                    <table id="" class="table table-bordered table-responsive">
                        <thead>
                        <tr>
                            <th style="width: 100%" class="col-sm-3"><?= lang('name') ?></th>
                            <?php foreach ($dateSl as $edate) : ?>
                                <th class="std_p"><?php echo $edate ?></th>
                            <?php endforeach; ?>

                        </tr>

                        </thead>

                        <tbody>
                        <?php

                        foreach ($attendance as $key => $v_employee){ ?>
                            <tr>

                                <td style="width: 100%"
                                    class="col-sm-3"><?php echo $employee[$key]->fullname ?></td>
                                <?php

                                foreach ($v_employee as $v_result){
                                    ?>
                                    <?php foreach ($v_result as $emp_attendance){ ?>
                                        <td>
                                            <?php
                                            if ($emp_attendance->attendance_status == 1) {
                                                echo '<span  style="padding:2px; 4px" class="label label-success std_p">' . lang('p') . '</span>';
                                            }
                                            if ($emp_attendance->attendance_status == '0') {
                                                echo '<span style="padding:2px; 4px" class="label label-danger std_p">' . lang('a') . '</span>';
                                            }
                                            if ($emp_attendance->attendance_status == 'H') {
                                                echo '<span style="padding:2px; 4px" class="label label-info std_p">' . lang('h') . '</span>';
                                            }
                                            if ($emp_attendance->attendance_status == 3) {
                                                echo '<span style="padding:2px; 4px" class="label label-warning std_p">' . lang('l') . '</span>';
                                            }
                                            ?>
                                        </td>
                                    <?php }; ?>


                                <?php }; ?>
                            </tr>
                        <?php }; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    function printEmp_report(EmpprintReport) {
        var printContents = document.getElementById(EmpprintReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
