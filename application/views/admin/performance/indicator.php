<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('86', 'created');
$edited = can_action('86', 'edited');
if (!empty($created) || !empty($edited)){
?>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#indicator_list"
                                                                   data-toggle="tab"><?= lang('indicator_list') ?></a>
                </li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#set_indicator"
                                                                   data-toggle="tab"><?= lang('set_indicator') ?></a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Indicator List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="indicator_list"
                     style="position: relative;">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('indicator_list') ?></strong></div>
                        </header>
                        <?php } ?>
                        <div class="row">
                            <?php if (!empty($all_department_info)): foreach ($all_department_info as $akey => $v_department_info) : ?>
                                <?php if (!empty($v_department_info)):
                                    if (!empty($all_dept_info[$akey]->deptname)) {
                                        $deptname = $all_dept_info[$akey]->deptname;
                                    } else {
                                        $deptname = lang('undefined_department');
                                    }
                                    ?>
                                    <div class="col-sm-6">
                                    <div class="box-heading">
                                        <div class="box-title">
                                            <h4><?php echo $deptname ?>
                                            </h4>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-bold col-sm-1">#</td>
                                        <td class="text-bold"><?= lang('designation') ?></td>
                                        <td class="text-bold col-sm-1"><?= lang('action') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($v_department_info as $key => $v_department) :
                                    if (!empty($v_department->designations)) {
                                        ?>

                                        <tr>
                                            <td><?php echo $key + 1 ?></td>
                                            <td>
                                                <a data-toggle="modal" data-target="#myModal_lg"
                                                   href="<?= base_url() ?>admin/performance/indicator_details/<?= $v_department->designations_id ?>"> <?php echo $v_department->designations ?></a>
                                            </td>
                                            <td>
                                                <?php echo btn_view_modal('admin/performance/indicator_details/' . $v_department->designations_id); ?>
                                            </td>

                                        </tr>
                                        <?php
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="3"><?= lang('no_designation_create_yet') ?></td>
                                        <tr></tr>
                                    <?php }
                                endforeach;
                                    ?>
                                <?php endif; ?>
                                </tbody>
                                </table>
                                </div>

                            <?php endforeach; ?>
                            <?php endif; ?>


                        </div>
                    </div>
                    <!-- Indicator List tab Ends -->
                    <?php if (!empty($created) || !empty($edited)){ ?>
                        <!-- Add Indicator Values tab Starts -->
                        <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="set_indicator"
                             style="position: relative;">

                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                                  action="<?php echo base_url() ?>admin/performance/save_performance_indicator/<?php
                                  if (!empty($indicator_info_by_id->performance_indicator_id)) {
                                      echo $indicator_info_by_id->performance_indicator_id;
                                  }
                                  ?>" method="post" class="form-horizontal">
                                <!-- Select Department / Designation Section --->

                                <div class="form-group">
                                    <label class="col-sm-3 col-sm-offset-1 control-label"><?= lang('designation') ?>
                                        <span class="required">*</span></label>
                                    <div class="col-sm-6">
                                        <select name="designations_id" class="form-control select_box"
                                                style="width:100%"
                                                required>
                                            <option value=""><?= lang('select') . ' ' . lang('designation') ?></option>
                                            <?php if (!empty($all_department_info)): foreach ($all_department_info as $dept_name => $v_department_info) : ?>
                                                <?php if (!empty($v_department_info)):
                                                    if (!empty($all_dept_info[$dept_name]->deptname)) {
                                                        $deptname = $all_dept_info[$dept_name]->deptname;
                                                    } else {
                                                        $deptname = lang('undefined_department');
                                                    }
                                                    ?>
                                                    <optgroup label="<?php echo $deptname; ?>">
                                                        <?php foreach ($v_department_info as $designation) : ?>
                                                            <option
                                                                value="<?php echo $designation->designations_id; ?>"
                                                                <?php
                                                                if (!empty($indicator_info_by_id->designations_id)) {
                                                                    echo $designation->designations_id == $indicator_info_by_id->designations_id ? 'selected' : '';
                                                                }
                                                                ?>><?php echo $designation->designations ?></option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Technical Competency Starts ---->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="panel panel-custom">
                                            <div class="panel-heading">
                                                <h4 class="panel-title"
                                                    style="margin-left: 8px;"><?= lang('technical_competency') ?></h4>
                                            </div>
                                            <div class="box-body ">
                                                <br/>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('customer_experience_management') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="customer_experiece_management"
                                                                class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->customer_experiece_management) && $indicator_info_by_id->customer_experiece_management == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->customer_experiece_management) && $indicator_info_by_id->customer_experiece_management == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->customer_experiece_management) && $indicator_info_by_id->customer_experiece_management == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->customer_experiece_management) && $indicator_info_by_id->customer_experiece_management == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('marketing') ?> </label>
                                                    <div class="col-sm-5">
                                                        <select name="marketing" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->marketing) && $indicator_info_by_id->marketing == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->marketing) && $indicator_info_by_id->marketing == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->marketing) && $indicator_info_by_id->marketing == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->marketing) && $indicator_info_by_id->marketing == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('management') ?> </label>
                                                    <div class="col-sm-5">
                                                        <select name="management" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->management) && $indicator_info_by_id->management == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->management) && $indicator_info_by_id->management == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->management) && $indicator_info_by_id->management == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->management) && $indicator_info_by_id->management == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('administration') ?>  </label>
                                                    <div class="col-sm-5">
                                                        <select name="administration" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->administration) && $indicator_info_by_id->administration == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->administration) && $indicator_info_by_id->administration == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->administration) && $indicator_info_by_id->administration == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->administration) && $indicator_info_by_id->administration == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('presentation_skill') ?> </label>
                                                    <div class="col-sm-5">
                                                        <select name="presentation_skill" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->presentation_skill) && $indicator_info_by_id->presentation_skill == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->presentation_skill) && $indicator_info_by_id->presentation_skill == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->presentation_skill) && $indicator_info_by_id->presentation_skill == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->presentation_skill) && $indicator_info_by_id->presentation_skill == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('quality_of_work') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="quality_of_work" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->quality_of_work) && $indicator_info_by_id->quality_of_work == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->quality_of_work) && $indicator_info_by_id->quality_of_work == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->quality_of_work) && $indicator_info_by_id->quality_of_work == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->quality_of_work) && $indicator_info_by_id->quality_of_work == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('efficiency') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="efficiency" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->efficiency) && $indicator_info_by_id->efficiency == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->efficiency) && $indicator_info_by_id->efficiency == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->efficiency) && $indicator_info_by_id->efficiency == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                            <option
                                                                value="4" <?= (!empty($indicator_info_by_id->efficiency) && $indicator_info_by_id->efficiency == 4 ? 'selected' : '') ?>> <?= lang('expert_leader') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Technical Competency Ends ---->


                                    <!-- Behavioural Competency Ends ---->
                                    <div class="col-sm-6">
                                        <div class="panel panel-custom">
                                            <div class="panel-heading">
                                                <h4 class="panel-title"
                                                    style="margin-left: 8px;"><?= lang('behavioural_competency') ?> </h4>
                                            </div>
                                            <div class="box-body ">

                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('integrity') ?> </label>
                                                    <div class="col-sm-5">
                                                        <select name="integrity" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->integrity) && $indicator_info_by_id->integrity == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->integrity) && $indicator_info_by_id->integrity == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->integrity) && $indicator_info_by_id->integrity == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('professionalism') ?> </label>
                                                    <div class="col-sm-5">
                                                        <select name="professionalism" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->professionalism) && $indicator_info_by_id->professionalism == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->professionalism) && $indicator_info_by_id->professionalism == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->professionalism) && $indicator_info_by_id->professionalism == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('team_work') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="team_work" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->team_work) && $indicator_info_by_id->team_work == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->team_work) && $indicator_info_by_id->team_work == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->team_work) && $indicator_info_by_id->team_work == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('critical_thinking') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="critical_thinking" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->critical_thinking) && $indicator_info_by_id->critical_thinking == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->critical_thinking) && $indicator_info_by_id->critical_thinking == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->critical_thinking) && $indicator_info_by_id->critical_thinking == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('conflict_management') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="conflict_management" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->conflict_management) && $indicator_info_by_id->conflict_management == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->conflict_management) && $indicator_info_by_id->conflict_management == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->conflict_management) && $indicator_info_by_id->conflict_management == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('attendance') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="attendance" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->attendance) && $indicator_info_by_id->attendance == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->attendance) && $indicator_info_by_id->attendance == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->attendance) && $indicator_info_by_id->attendance == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="border-none">
                                                    <label
                                                        class="col-sm-6  control-label"><?= lang('ability_to_meet_deadline') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="ability_to_meed_deadline" class="form-control">
                                                            <option value=""><?= lang('none') ?></option>
                                                            <option
                                                                value="1" <?= (!empty($indicator_info_by_id->ability_to_meed_deadline) && $indicator_info_by_id->ability_to_meed_deadline == 1 ? 'selected' : '') ?>> <?= lang('beginner') ?></option>
                                                            <option
                                                                value="2" <?= (!empty($indicator_info_by_id->ability_to_meed_deadline) && $indicator_info_by_id->ability_to_meed_deadline == 2 ? 'selected' : '') ?>> <?= lang('intermediate') ?></option>
                                                            <option
                                                                value="3" <?= (!empty($indicator_info_by_id->ability_to_meed_deadline) && $indicator_info_by_id->ability_to_meed_deadline == 3 ? 'selected' : '') ?>> <?= lang('advanced') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Behavioural Competency Ends ---->

                                <div class="btn-bottom-toolbar text-right">
                                    <?php
                                    if (!empty($indicator_info_by_id)) { ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                        <button type="button" onclick="goBack()"
                                                class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                    <?php } else {
                                        ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                    <?php }
                                    ?>
                                </div>
                            </form>
                        </div>
                    <?php }else{ ?>
                </div>
                <?php } ?>
            </div>
            <!-- Add Indicator Values Ends --->
        </div>
    </div>
</div>


