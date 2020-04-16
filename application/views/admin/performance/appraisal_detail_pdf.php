<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body >
<br/>
<?php
$img = ROOTPATH . '/' . config_item('company_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('company_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= $img ?>" alt="" class="img-circle"/>
            </td>
            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="padding: 5px 0; width: 100%;">
    <div>
        <table style="width: 100%; border-radius: 3px;">
            <tr>
                <td style="width: 150px;">
                    <table style="border: 1px solid grey;">
                        <tr>
                            <td style="background-color: lightgray; border-radius: 2px;">
                                <?php if ($get_appraisal_info->avatar): ?>
                                    <img src="<?php echo base_url() . $get_appraisal_info->avatar; ?>"
                                         style="width: 138px; height: 144px; border-radius: 3px;">
                                <?php else: ?>
                                    <img alt="Employee_Image">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="width: 500px; margin-left: 10px; margin-bottom: 10px; font-size: 13px;">
                        <tr>
                            <td colspan="2" style="width: 100%"><h2><?php echo "$get_appraisal_info->fullname"; ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%"><strong><?= lang('emp_id') ?> : </strong></td>
                            <td colspan="2"><?php echo "$get_appraisal_info->employment_id "; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%"><strong><?= lang('departments') ?> : </strong></td>
                            <td><?php echo "$get_appraisal_info->deptname"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%"><strong><?= lang('designation') ?> :</strong></td>
                            <td><?php echo "$get_appraisal_info->designations"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%"><strong><?= lang('appraisal_month') ?>: </strong></td>
                            <td><?php echo date('M Y', strtotime($get_appraisal_info->appraisal_month)); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<div style="width: 100%; margin-top: 20px;">
    <div>
        <!-- Technical Competency Block Starts --->
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('technical_competency') ?></strong></p>
        </div>
        <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0; page-break-after: always;">
            <tr> <!-- customer experience management row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('customer_experience_management') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- marketing row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('marketing') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr> <!-- management row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('management') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr> <!-- administration row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('administration') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- presentation_skill row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('presentation_skill') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- quality_of_work row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('quality_of_work') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- efficiency row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('efficiency') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- Technical Competency Block Ends --->


        <!-- Behavioural Competency Block Starts --->
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('behavioural_competency') ?></strong></p>
        </div>
        <table style="width: 100%; /*border: 1px solid blue;*/ padding: 10px 0;">
            <tr> <!-- integrity row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('integrity') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- professionalism row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('professionalism') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr> <!-- team_work row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('team_work') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr> <!-- critical_thinking row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('critical_thinking') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- conflict_management row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('conflict_management') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- attendance row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('attendance') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> <!-- ability_to_meet_deadline row ---->
                <td>
                    <table style="width: 650px;">
                        <tr>
                            <td style="border-bottom: 2px solid black; width: 50%"><?= lang('ability_to_meet_deadline') ?></td>
                            <td style="border-bottom: 1px solid black; width: 50%">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-bottom: 1px solid black; background-color: #E3E3E3; padding-left: 10px; width: 50%">
                                            <?= lang('expected')?>:
                                        </td>
                                        <td style="border-bottom: 1px solid black; text-align: center;">
                                            <?php
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
                                    </tr>
                                    <tr>
                                        <td style="background-color: #E3E3E3 ; padding-left: 10px; width: 50%">
                                            <?= lang('assigned')?>:
                                        </td>
                                        <td style="text-align: center;">
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br/>
        <!-- General Remarks Block Starts --->
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; margin-top: 10px;">
            <p style="font-size: 15px; font-weight: lighter;"><strong><?= lang('remarks') ?></strong></p>
        </div>
        <table style="width: 100%;padding: 5px 0;">
            <tr>
                <td>
                    <?php
                    if (!empty($get_appraisal_info->general_remarks)) {
                        echo $get_appraisal_info->general_remarks;
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>