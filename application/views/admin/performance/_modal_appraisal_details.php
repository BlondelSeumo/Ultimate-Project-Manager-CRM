<div id="printableArea">
    <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only"><?= lang('close') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('performance_appraisal_details') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form action="<?php echo base_url() ?>admin/performance/give_performance_appraisal/<?php
        if (!empty($get_appraisal_info->performance_appraisal_id)) {
            echo $get_appraisal_info->performance_appraisal_id;
        }
        ?>" method="post" class="form-horizontal">
            <div class="col-lg-12" style="margin-top: 5px;">
                <div class="row">
                    <div class="col-lg-2 col-sm-2">
                        <div class="fileinput-new thumbnail"
                             style="width: 140px; height: 154px; margin-top: 14px; margin-left: 16px; background-color: #EBEBEB;">
                            <?php if ($get_appraisal_info->avatar): ?>
                                <img src="<?php echo base_url() . $get_appraisal_info->avatar; ?>"
                                     style="width: 138px; height: 144px; border-radius: 3px;">
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
                                <h3><?php echo $get_appraisal_info->fullname; ?>
                                    <div class="pull-right" style="margin-left: 15px;">
                                        <div class="pull-left" style="margin-left: -15px;">
                                            <button class="btn btn-xs pull-left btn-danger btn-print" type="button"
                                                    data-toggle="tooltip"
                                                    title="Print" onclick="printDiv('printableArea')"
                                                    style="margin-left: -15px;"><i class="fa fa-print"></i></button>
                                        </div>
                                        <div class="pull-left">
                                            <a href="<?= base_url() ?>admin/performance/appraisal_details_pdf/<?= $get_appraisal_info->performance_appraisal_id ?>"
                                               class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top"
                                               title="PDF" style="margin-top: -7px;"><i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </div>
                                    </div>
                                </h3>
                                <hr class="mt0"/>
                                <table class="table-hover">
                                    <tr>
                                        <td><strong><?= lang('emp_id') ?></strong></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td><?php echo $get_appraisal_info->employment_id ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= lang('departments') ?></strong></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td><?php echo "$get_appraisal_info->deptname"; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= lang('designation') ?></strong></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td><?php echo "$get_appraisal_info->designations"; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= lang('appraisal_month') ?></strong></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td><?php echo date('M Y', strtotime($get_appraisal_info->appraisal_month)); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= lang('technical_competency') ?></h4>
                        </div>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><?= lang('performance_indicator') ?></th>
                                <th><?= lang('expected_value') ?></th>
                                <th><?= lang('set_value') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr> <!-- customer experience management row ---->
                                <td style="font-size: 12px"><?= lang('customer_experience_management') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->customer_experiece_management) && $get_appraisal_info->customer_experiece_management == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->customer_experiece_management) && $get_appraisal_info->customer_experiece_management == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->customer_experiece_management) && $get_appraisal_info->customer_experiece_management == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->customer_experiece_management) && $get_appraisal_info->customer_experiece_management == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- marketing row ---->
                                <td><?= lang('marketing') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->marketing) && $get_appraisal_info->marketing == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->marketing) && $get_appraisal_info->marketing == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->marketing) && $get_appraisal_info->marketing == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->marketing) && $get_appraisal_info->marketing == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- management row ---->
                                <td><?= lang('management') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->management) && $get_appraisal_info->management == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->management) && $get_appraisal_info->management == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->management) && $get_appraisal_info->management == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->management) && $get_appraisal_info->management == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- administration row ---->
                                <td><?= lang('administration') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->administration) && $get_appraisal_info->administration == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->administration) && $get_appraisal_info->administration == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->administration) && $get_appraisal_info->administration == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->administration) && $get_appraisal_info->administration == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- presentation_skill row ---->
                                <td><?= lang('presentation_skill') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->presentation_skill) && $get_appraisal_info->presentation_skill == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->presentation_skill) && $get_appraisal_info->presentation_skill == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->presentation_skill) && $get_appraisal_info->presentation_skill == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->presentation_skill) && $get_appraisal_info->presentation_skill == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- quality_of_work row ---->
                                <td><?= lang('quality_of_work') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->quality_of_work) && $get_appraisal_info->quality_of_work == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->quality_of_work) && $get_appraisal_info->quality_of_work == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->quality_of_work) && $get_appraisal_info->quality_of_work == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->quality_of_work) && $get_appraisal_info->quality_of_work == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- efficiency row ---->
                                <td><?= lang('efficiency') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->efficiency) && $get_appraisal_info->efficiency == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->efficiency) && $get_appraisal_info->efficiency == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->efficiency) && $get_appraisal_info->efficiency == 3) {
                                        echo lang('advanced');
                                    } elseif (!empty($performance_indicator_details->efficiency) && $get_appraisal_info->efficiency == 4) {
                                        echo lang('expert_leader');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div><!-- Technical Competency Ends ---->

                <!-- Behavioural Competency Starts ---->
                <div class="col-sm-6">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= lang('behavioural_competency') ?> </h4>
                        </div>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th><?= lang('performance_indicator') ?></th>
                                <th><?= lang('expected_value') ?></th>
                                <th><?= lang('set_value') ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr> <!-- integrity row ---->
                                <td><?= lang('integrity') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->integrity) && $performance_indicator_details->integrity == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->integrity) && $performance_indicator_details->integrity == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->integrity) && $performance_indicator_details->integrity == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->integrity) && $get_appraisal_info->integrity == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->integrity) && $get_appraisal_info->integrity == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->integrity) && $get_appraisal_info->integrity == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- professionalism row ---->
                                <td><?= lang('professionalism') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->professionalism) && $performance_indicator_details->professionalism == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->professionalism) && $performance_indicator_details->professionalism == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->professionalism) && $performance_indicator_details->professionalism == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->professionalism) && $get_appraisal_info->professionalism == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->professionalism) && $get_appraisal_info->professionalism == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->professionalism) && $get_appraisal_info->professionalism == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- team_work row ---->
                                <td><?= lang('team_work') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->team_work) && $performance_indicator_details->team_work == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->team_work) && $performance_indicator_details->team_work == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->team_work) && $performance_indicator_details->team_work == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->team_work) && $get_appraisal_info->team_work == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->team_work) && $get_appraisal_info->team_work == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->team_work) && $get_appraisal_info->team_work == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- critical_thinking row ---->
                                <td><?= lang('critical_thinking') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->critical_thinking) && $performance_indicator_details->critical_thinking == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->critical_thinking) && $performance_indicator_details->critical_thinking == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->critical_thinking) && $performance_indicator_details->critical_thinking == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->critical_thinking) && $get_appraisal_info->critical_thinking == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->critical_thinking) && $get_appraisal_info->critical_thinking == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->critical_thinking) && $get_appraisal_info->critical_thinking == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- conflict_management row ---->
                                <td><?= lang('conflict_management') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->conflict_management) && $performance_indicator_details->conflict_management == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->conflict_management) && $performance_indicator_details->conflict_management == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->conflict_management) && $performance_indicator_details->conflict_management == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->conflict_management) && $get_appraisal_info->conflict_management == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->conflict_management) && $get_appraisal_info->conflict_management == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->conflict_management) && $get_appraisal_info->conflict_management == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- attendance row ---->
                                <td><?= lang('attendance') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->attendance) && $performance_indicator_details->attendance == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->attendance) && $performance_indicator_details->attendance == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->attendance) && $performance_indicator_details->attendance == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->attendance) && $get_appraisal_info->attendance == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->attendance) && $get_appraisal_info->attendance == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->attendance) && $get_appraisal_info->attendance == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr> <!-- ability_to_meet_deadline row ---->
                                <td><?= lang('ability_to_meet_deadline') ?></td>
                                <td><?php
                                    if (!empty($performance_indicator_details->ability_to_meed_deadline) && $performance_indicator_details->ability_to_meed_deadline == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->ability_to_meed_deadline) && $performance_indicator_details->ability_to_meed_deadline == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->ability_to_meed_deadline) && $performance_indicator_details->ability_to_meed_deadline == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($performance_indicator_details->ability_to_meed_deadline) && $get_appraisal_info->ability_to_meed_deadline == 1) {
                                        echo lang('beginner');
                                    } elseif (!empty($performance_indicator_details->ability_to_meed_deadline) && $get_appraisal_info->ability_to_meed_deadline == 2) {
                                        echo lang('intermediate');
                                    } elseif (!empty($performance_indicator_details->ability_to_meed_deadline) && $get_appraisal_info->ability_to_meed_deadline == 3) {
                                        echo lang('advanced');
                                    } else {
                                        echo "<span style='color:red;font - style: italic;line - height:2.4;'>" . lang('not_set') . "</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Behavioural Competency Ends ---->
                    </div>
                </div>
                <div class="col-sm-12">
                    <blockquote style="font-size: 12px;"><?php
                        if (!empty($get_appraisal_info->general_remarks)) {
                            echo $get_appraisal_info->general_remarks;
                        }
                        ?></blockquote>
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
                            <?php if (!empty($get_appraisal_info->performance_appraisal_id)) { ?>
                                <div class="col-sm-5 pull-right">
                                    <button type="submit"
                                            class="btn col-sm-12 btn-primary btn-block"><?= lang('edit') ?></button>
                                </div>
                            <?php } ?>
                        </div>
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