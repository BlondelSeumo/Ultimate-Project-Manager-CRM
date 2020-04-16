<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('clone') . ' ' . lang('projects') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" id="from_items"
              action="<?php echo base_url(); ?>admin/projects/cloned_project/<?= $project_info->project_id ?>"
              method="post"
              class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('client') ?> <span
                        class="text-danger">*</span>
                </label>
                <div class="col-lg-8">
                    <select class="form-control select_box" style="width: 100%" name="client_id" required>
                        <?php
                        if (!empty($all_client)) {
                            foreach ($all_client as $v_client) {
                                ?>
                                <option value="<?= $v_client->client_id ?>"
                                    <?php
                                    if (!empty($project_info)) {
                                        $project_info->client_id == $v_client->client_id ? 'selected' : '';
                                    }
                                    ?>
                                ><?= ($v_client->name) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('start_date') ?> <span
                        class="text-danger">*</span></label>
                <div class="col-lg-8">
                    <div class="input-group">
                        <input required type="text" id="start_date" name="start_date"
                               class="form-control datepicker"
                               value="<?php
                               if (!empty($project_info->start_date)) {
                                   echo date('Y-m-d', strtotime($project_info->start_date));
                               }
                               ?>"
                               data-date-format="<?= config_item('date_picker_format'); ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('end_date') ?> <span
                        class="text-danger">*</span></label>
                <div class="col-lg-8">
                    <div class="input-group">
                        <input required type="text" id="end_date" name="end_date"
                               data-rule-required="true"
                               data-msg-greaterThanOrEqual="end_date_must_be_equal_or_greater_than_start_date"
                               data-rule-greaterThanOrEqual="#start_date"
                               class="form-control datepicker"
                               value="<?php
                               if (!empty($project_info->end_date)) {
                                   echo date('Y-m-d', strtotime($project_info->end_date));
                               }
                               ?>"
                               data-date-format="<?= config_item('date_picker_format'); ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('also_added') ?></label>
                <div class="col-lg-8">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input name="milestones"
                                   value="1" <?php
                            if (!empty($milestone_info)) {
                                echo "checked=\"checked\"";
                            }
                            ?> type="checkbox">
                            <span class="fa fa-check"></span>
                            <?= lang('milestones') ?>
                        </label>
                    </div>
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input name="tasks"
                                   value="1" <?php
                            if (!empty($task_info)) {
                                echo "checked=\"checked\"";
                            }
                            ?> type="checkbox">
                            <span class="fa fa-check"></span>
                            <?= lang('tasks') ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('clone') ?></button>
            </div>
        </form>
    </div>
</div>
