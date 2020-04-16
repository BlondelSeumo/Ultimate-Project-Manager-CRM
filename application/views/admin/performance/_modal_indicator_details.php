<?php
$edited = can_action('86', 'edited');
?>
<div class="modal-content"">
<div class="modal-header ">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only"><?= lang('close') ?></span></button>
    <h4 class="modal-title" id="myModalLabel"><?= lang('performance_indicator_details') ?></h4>
</div>
<div class="modal-body wrap-modal wrap">
    <form action="<?php echo base_url() ?>admin/performance/performance_indicator/<?php
    if (!empty($performance_indicator_details->performance_indicator_id)) {
        echo $performance_indicator_details->performance_indicator_id;
    }
    ?>" method="post" class="form-horizontal">
        <div class="row">
            <div class="col-sm-6 row"><!-- Technical Competency Starts ---->
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= lang('technical_competency') ?></h4>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"
                               style="font-size: 12px"><?= lang('customer_experience_management') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->customer_experiece_management) && $performance_indicator_details->customer_experiece_management == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class=" col-sm-7 control-label"><?= lang('marketing') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->marketing) && $performance_indicator_details->marketing == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class=" col-sm-7 control-label"><?= lang('management') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->management) && $performance_indicator_details->management == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('administration') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->administration) && $performance_indicator_details->administration == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('presentation_skill') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->presentation_skill) && $performance_indicator_details->presentation_skill == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('quality_of_work') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->quality_of_work) && $performance_indicator_details->quality_of_work == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class=" col-sm-7 control-label"><?= lang('efficiency') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 3) {
                                    echo lang('advanced');
                                } elseif (!empty($performance_indicator_details->efficiency) && $performance_indicator_details->efficiency == 4) {
                                    echo lang('expert_leader');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <!-- Technical Competency Ends ---->
                </div>
            </div>
            <div class="col-sm-6">
                <!-- Behavioural Competency Starts ---->
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= lang('behavioural_competency') ?></h4>
                    </div>

                    <div class="form-group">
                        <label for="field-1" class=" col-sm-7 control-label"><?= lang('integrity') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->integrity) && $performance_indicator_details->integrity == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->integrity) && $performance_indicator_details->integrity == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->integrity) && $performance_indicator_details->integrity == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('professionalism') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->professionalism) && $performance_indicator_details->professionalism == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->professionalism) && $performance_indicator_details->professionalism == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->professionalism) && $performance_indicator_details->professionalism == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class=" col-sm-7 control-label"><?= lang('team_work') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->team_work) && $performance_indicator_details->team_work == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->team_work) && $performance_indicator_details->team_work == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->team_work) && $performance_indicator_details->team_work == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('critical_thinking') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->critical_thinking) && $performance_indicator_details->critical_thinking == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->critical_thinking) && $performance_indicator_details->critical_thinking == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->critical_thinking) && $performance_indicator_details->critical_thinking == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('conflict_management') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->conflict_management) && $performance_indicator_details->conflict_management == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->conflict_management) && $performance_indicator_details->conflict_management == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->conflict_management) && $performance_indicator_details->conflict_management == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class=" col-sm-7 control-label"><?= lang('attendance') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->attendance) && $performance_indicator_details->attendance == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->attendance) && $performance_indicator_details->attendance == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->attendance) && $performance_indicator_details->attendance == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1"
                               class=" col-sm-7 control-label"><?= lang('ability_to_meet_deadline') ?>
                            : </label>
                        <div class="col-sm-5">
                            <p class="form-control-static" style="text-align: justify;"><?php
                                if (!empty($performance_indicator_details->ability_to_meed_deadline) && $performance_indicator_details->ability_to_meed_deadline == 1) {
                                    echo lang('beginner');
                                } elseif (!empty($performance_indicator_details->ability_to_meed_deadline) && $performance_indicator_details->ability_to_meed_deadline == 2) {
                                    echo lang('intermediate');
                                } elseif (!empty($performance_indicator_details->ability_to_meed_deadline) && $performance_indicator_details->ability_to_meed_deadline == 3) {
                                    echo lang('advanced');
                                } else {
                                    echo lang('none');
                                }
                                ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Behavioural Competency Ends ---->

            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?= lang('close') ?></button>
                </div>
                <?php if (!empty($performance_indicator_details->performance_indicator_id)) { ?>
                    <?php if (!empty($edited)) { ?>
                        <div class="col-sm-6 pull-right">
                            <button type="submit" class="btn btn-primary"><?= lang('edit') ?></button>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-sm-6 pull-right" style="margin-right: 10px;">
                        <p style="color: red; font-size: 14px; padding-top: 4px;"><?= lang('indicator_value_not_set') ?></p>
                    </div>
                <?php } ?>
            </div>
    </form>
</div>
