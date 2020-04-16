<link rel="stylesheet" href="<?php echo base_url(); ?>plugins/colorpicker/css/bootstrap-colorpicker.min.css">
<script src="<?php echo base_url(); ?>plugins/colorpicker/js/bootstrap-colorpicker.min.js"></script>

<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" style="margin-top: 13px;;" id="myModalLabel"><?= lang('calendar_settings') ?></h4>
    </div>
    <form role="form" id="from_items" action="<?php echo base_url(); ?>admin/calendar/save_settings"
          method="post" class="form-horizontal form-groups-bordered">
        <div class="modal-body">
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('google_api') ?></label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" value="<?= config_item('gcal_api_key') ?>"
                           name="gcal_api_key">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('calendar_id') ?></label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" value="<?= config_item('gcal_id') ?>" name="gcal_id">
                </div>
            </div>
            <h4 class="mb0"><?php echo lang('show_on_calendar'); ?></h4>
            <hr class="mt-sm"/>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('project') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('project_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="project_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('project_color') ?>" name="project_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <label class="col-lg-3 control-label"><?= lang('milestone') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('milestone_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="milestone_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('milestone_color') ?>" name="milestone_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('tasks') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('tasks_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="tasks_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('tasks_color') ?>" name="tasks_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <label class="col-lg-3 control-label"><?= lang('bugs') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('bugs_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="bugs_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('bugs_color') ?>" name="bugs_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('invoice') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('invoice_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="invoice_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('invoice_color') ?>" name="invoice_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <label class="col-lg-3 control-label"><?= lang('payments') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('payments_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="payments_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('payments_color') ?>" name="payments_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('estimate') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('estimate_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="estimate_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('estimate_color') ?>" name="estimate_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <label class="col-lg-3 control-label"><?= lang('opportunities') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('opportunities_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="opportunities_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('opportunities_color') ?>"
                               name="opportunities_color" class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('goal_tracking') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('goal_tracking_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="goal_tracking_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('goal_tracking_color') ?>"
                               name="goal_tracking_color" class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <label class="col-lg-3 control-label"><?= lang('holiday') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('holiday_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="holiday_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">

                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('absent') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('absent_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="absent_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('absent_color') ?>" name="absent_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <label class="col-lg-3 control-label"><?= lang('on_leave') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('on_leave_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="on_leave_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('on_leave_color') ?>" name="on_leave_color"
                               class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('leads') ?></label>
                <div class="col-lg-1">
                    <div class="checkbox c-checkbox">
                        <label class="needsclick">
                            <input type="checkbox" <?php
                            if (config_item('leads_on_calendar') == 'on') {
                                echo "checked=\"checked\"";
                            }
                            ?> name="leads_on_calendar">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="hidden" value="<?= config_item('leads_color') ?>"
                               name="leads_color" class="form-control"/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>

            <script>
                $(function () {
                    $('.colorpicker-component').colorpicker();
                });
            </script>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
            </div>
        </div>
    </form>
</div>
