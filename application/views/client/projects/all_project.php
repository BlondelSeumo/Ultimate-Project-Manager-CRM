<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
     data-title="<?php echo lang('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-right group animated zoomIn"
        style="width:300px;">
        <li class="filter_by"><a href="#"><?php echo lang('all'); ?></a></li>
        <li class="divider"></li>
        <?php
        $all_status = array('cancel' => 'cancel', 'in_progress' => 'in_progress', 'on_hold' => 'on_hold', 'started' => 'started', 'completed' => 'completed');
        if (!empty($all_status)) {
            foreach ($all_status as $key => $v_statuses) {
                ?>
                <li class="filter_by" id="<?= $key ?>">
                    <a href="#"><?php echo lang($v_statuses); ?></a>
                </li>
            <?php }
            ?>
            <div class="clearfix"></div>
        <?php } ?>
    </ul>
</div>

<?php if (config_item('allow_client_project') == 'TRUE') { ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('all_project') ?></a></li>
        <?php if (config_item('allow_client_project') == 'TRUE') { ?>
            <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                data-toggle="tab"><?= lang('new_project') ?></a></li>
        <?php } ?>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php }else{ ?>
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= lang('all_project') ?>
                    </h3>
                </div>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('project_name') ?></th>
                            <th><?= lang('start_date') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th><?= lang('status') ?></th>
                            <?php $show_custom_fields = custom_form_table(4, null);
                            if (!empty($show_custom_fields)) {
                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                    if (!empty($c_label)) {
                                        ?>
                                        <th><?= $c_label ?> </th>
                                    <?php }
                                }
                            }
                            ?>
                            <th class="col-options no-sort"><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "client/projects/projectList";
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
                                    table_url(base_url + "client/projects/projectList/" + filter_by);
                                });
                            });
                        </script>

                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (config_item('allow_client_project') == 'TRUE') { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" enctype="multipart/form-data" data-parsley-validate="" novalidate=""
                          action="<?php echo base_url(); ?>client/projects/saved_project/<?php
                          if (!empty($project_info)) {
                              echo $project_info->project_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('project_no') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" disabled="" value="<?php
                                    if (!empty($project_info)) {
                                        echo $project_info->project_no;
                                    } else {
                                        if (empty(config_item('projects_number_format'))) {
                                            echo config_item('projects_prefix');
                                        }
                                        echo $this->items_model->generate_projects_number();
                                    }
                                    ?>" name="project_no">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('project_name') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" value="<?php
                                    if (!empty($project_info)) {
                                        echo $project_info->project_name;
                                    }
                                    ?>" name="project_name" required="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('start_date') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <input type="text" required name="start_date" class="form-control datepicker"
                                               value="<?php
                                               if (!empty($project_info->start_date)) {
                                                   echo date('Y-m-d', strtotime($project_info->start_date));
                                               }
                                               ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="fa fa-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('end_date') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <input type="text" required name="end_date" class="form-control datepicker"
                                               value="<?php
                                               if (!empty($project_info->end_date)) {
                                                   echo date('Y-m-d', strtotime($project_info->end_date));
                                               }
                                               ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="fa fa-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('billing_type') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <select name="billing_type" onchange="get_billing_value(this.value)"
                                            class="form-control select_box" style="width: 100%" required="">
                                        <option
                                            <?php
                                            if (!empty($project_info->billing_type)) {
                                                echo $project_info->billing_type == 'fixed_rate' ? 'selected' : null;
                                            } ?>
                                                value="fixed_rate"><?= lang('fixed_rate') ?></option>
                                        <option
                                            <?php
                                            if (!empty($project_info->billing_type)) {
                                                echo $project_info->billing_type == 'project_hours' ? 'selected' : null;
                                            } ?>
                                                value="project_hours"><?= lang('only') . ' ' . lang('project_hours') ?></option>
                                        <option
                                            <?php
                                            if (!empty($project_info->billing_type)) {
                                                echo $project_info->billing_type == 'tasks_hours' ? 'selected' : null;
                                            } ?>
                                                value="tasks_hours"><?= lang('only') . ' ' . lang('tasks_hours') ?></option>
                                        <option
                                            <?php
                                            if (!empty($project_info->billing_type)) {
                                                echo $project_info->billing_type == 'tasks_and_project_hours' ? 'selected' : null;
                                            } ?>
                                                value="tasks_and_project_hours"><?= lang('tasks_and_project_hours') ?></option>
                                    </select>
                                    <small class="based_on_tasks_hour" <?php
                                    if (!empty($project_info) && $project_info->billing_type == 'tasks_hours' || !empty($project_info) && $project_info->billing_type == 'tasks_and_project_hours') {
                                        echo 'style="display: block;"';
                                    } else {
                                        echo 'style="display: none;"';
                                    } ?> ><?php echo lang('based_on_hourly_rate') ?></small>
                                </div>
                            </div>
                            <div class="form-group fixed_rate " <?php
                            if (!empty($project_info) && $project_info->billing_type == 'fixed_rate') {
                                echo 'style="display: block;"';
                            } elseif (!empty($project_info) && $project_info->billing_type != 'fixed_rate') {
                                echo 'style="display: none;"';
                            }
                            ?>>
                                <label class="col-lg-2 control-label"><?= lang('fixed_price') ?></label>
                                <div class="col-lg-5">
                                    <input data-parsley-type="number" type="text" class="form-control fixed_rate"
                                           value="<?php
                                           if (!empty($project_info->project_cost)) {
                                               echo $project_info->project_cost;
                                           }
                                           ?>" placeholder="50" name="project_cost">
                                </div>
                            </div>

                            <div class="form-group hourly_rate " <?php
                            if (!empty($project_info) && $project_info->billing_type == 'project_hours' || !empty($project_info) && $project_info->billing_type == 'tasks_and_project_hours') {
                                echo 'style="display: block;"';
                            } elseif (!empty($project_info) && $project_info->billing_type == 'fixed_rate' || !empty($project_info) && $project_info->billing_type == 'tasks_hours') {
                                echo 'style="display: none;"';
                            }
                            ?>>
                                <label class="col-lg-2 control-label"><?= lang('project_hourly_rate') ?></label>
                                <div class="col-lg-5">
                                    <input data-parsley-type="number" type="text" class="form-control hourly_rate"
                                           value="<?php
                                           if (!empty($project_info->hourly_rate)) {
                                               echo $project_info->hourly_rate;
                                           }
                                           ?>" placeholder="50" name="hourly_rate">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('demo_url') ?></label>
                                <div class="col-lg-5">
                                    <input type="text" value="<?php
                                    if (!empty($project_info->demo_url)) {
                                        echo $project_info->demo_url;
                                    }
                                    ?>" class="form-control" placeholder="http://www.demourl.com" name="demo_url">
                                </div>
                            </div>
                            <?php
                            if (!empty($project_info)) {
                                $project_id = $project_info->project_id;
                            } else {
                                $project_id = null;
                            }
                            ?>
                            <?= custom_form_Fields(4, $project_id, true); ?>
                            <div class="form-group">
                                <label class="col-lg-2 control-label"><?= lang('description') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-10">

                            <textarea style="" name="description" class="form-control textarea_"
                                      placeholder="<?= lang('description') ?>"><?php
                                if (!empty($project_info->description)) {
                                    echo $project_info->description;
                                }
                                ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label"></label>
                                <div class="col-lg-5">
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
    <script type="text/javascript">
        <?php if(empty($project_info)){?>
        $('.hourly_rate').hide();
        <?php }?>
        function get_billing_value(val) {

            if (val == 'fixed_rate') {
                $('.fixed_rate').show();
                $(".fixed_rate").removeAttr('disabled');
                $('.hourly_rate').hide();
                $(".hourly_rate").attr('disabled', 'disabled');
                $('.based_on_tasks_hour').hide();
            } else if (val == 'tasks_hours') {
                $('.hourly_rate').hide();
                $(".hourly_rate").attr('disabled', 'disabled');
                $('.fixed_rate').hide();
                $(".fixed_rate").attr('disabled', 'disabled');
                $('.based_on_tasks_hour').show();
            } else {
                $('.hourly_rate').show();
                $(".hourly_rate").removeAttr('disabled');
                $('.fixed_rate').hide();
                $(".fixed_rate").attr('disabled', 'disabled');
                $('.based_on_tasks_hour').show();
            }
            if (val == 'project_hours') {
                $('.based_on_tasks_hour').hide();
            }
        }
    </script>