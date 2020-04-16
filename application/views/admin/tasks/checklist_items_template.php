<style>
    .checklist .remove-checklist {
        position: absolute;
        right: 0;
    }
    .checklist {
        cursor: initial;
    }

    .checklist {
        padding: 4px 9px 0px 0px;
        margin-top: 10px;
        border-radius: 4px;
        height: 34px;
        cursor: move;
    }

    .checklist .checkbox.checklist-checkbox {
        margin-top: 0;
        margin-bottom: 0;
    }

    .checklist:hover,
    .checklist:hover textarea[name="checklist-description"]:disabled {
        background: #eef2f4;
    }

    .checkbox.checklist-checkbox label::before {
        width: 20px;
        height: 20px;
        margin-left: -21px;
        border-radius: 50%;
    }

    .checklist label:not(.control-label) {
        font-weight: normal;
    }

    .checkbox.checklist-checkbox label::after {
        margin-left: -20px;
        padding-left: 4.5px;
        padding-top: 3px;
        font-size: 10px;
    }

    .checklist .remove-checklist,
    .checklist .save-checklist-template {
        margin-top: 2px;
    }

    .checklist .remove-checklist {
        margin-right: -5px;
    }

    textarea[name="checklist-box"] {
        cursor: pointer;
    }

    textarea[name="checklist-description"] {
        position: absolute;
        resize: none;
        overflow: hidden;
        left: 25px;
        top: 0;
        font-size: 14px;
        width: 90%;
        border-radius: 3px;
        border: 0;
        outline: 0;
        padding-left: 5px;
    }

    textarea[name="checklist-description"]:focus,
    textarea[name="checklist-description"]:hover,
    textarea[name="checklist-description"]:active {
        outline: 0;
    }

    textarea[name="checklist-description"]:disabled {
        background: #fff;
    }

    .checklist-template-remove {
        position: absolute;
        right: 5px;
        top: 2px;
    }

    .task-single-checklist-templates {
        margin-top: -12px;
    }

    @media (max-width: 767px) {
        .save-checklist-template {
            position: absolute;
            right: 8px;
        }
        textarea[name="checklist-description"] {
            width: 82%;
        }
    }

    .checklist-items-template-select .checklist-item-template-remove {
        position: absolute;
        right: 17px;
        top: 10px;
    }

    .checklist-items-template-select.show-tick .checklist-item-template-remove {
        margin-right: 20px;
        margin-top: -2px;
    }


    .checklist.ui-sortable-helper.relative {
        position: inherit;
    }

    .checklist-item-completed-by {
        position: absolute;
        bottom: -30px;
    }

    .mobile .checklist-item-completed-by {
        display:none;
    }
</style>
<div class="clearfix"></div>
<?php if (count($checklists) > 0) { ?>
    <h4 class="bold chk-heading th font-medium"><?php echo lang('task_checklist_items'); ?></h4>
<?php } ?>
<div class="progress mtop15 hide">
    <div class="progress-bar not-dynamic progress-bar-default task-progress-bar" role="progressbar" aria-valuenow="40"
         aria-valuemin="0" aria-valuemax="100" style="width:0%">
    </div>
</div>
<?php

$edited = can_action('54', 'edited');
$deleted = can_action('54', 'deleted');
foreach ($checklists as $v_list) {
    $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $v_list->module_id));
    $can_delete = $this->tasks_model->can_action('tbl_task', 'delete', array('task_id' => $v_list->module_id));
    ?>
    <div>
        <div
            class="checklist relative<?php if (($v_list->finished == 1 && $v_list->finished_from != my_id()) || ($v_list->added_from != my_id())) {
                echo ' mbot25';
            } ?>" data-checklist-id="<?php echo $v_list->checklist_id; ?>">
            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="">
                <input
                    type="checkbox" <?php if ($v_list->finished == 1 && $v_list->finished_from != my_id() && !admin()) {
                    echo 'disabled';
                } ?> name="checklist-box" <?php if ($v_list->finished == 1) {
                    echo 'checked';
                }; ?>>
                <label for=""><span class="hide"><?php echo $v_list->description; ?></span></label>
                <textarea data-taskid="<?php echo $task_id; ?>" name="checklist-description"
                          rows="1"<?php if ($v_list->added_from != my_id() && !empty($can_edit) && !empty($edited)) {
                    echo ' disabled';
                } ?>><?php echo clear_textarea_breaks($v_list->description); ?></textarea>
                <?php if (!empty($can_delete) && !empty($deleted) || $v_list->added_from == my_id()) { ?>
                    <a href="#" class="pull-right text-muted remove-checklist"
                       onclick="delete_checklist_item(<?php echo $v_list->checklist_id; ?>,this); return false;"><i
                            class="fa fa-remove"></i>
                    </a>
                <?php } ?>
            </div>
            <?php if ($v_list->finished == 1 || $v_list->added_from != my_id()) { ?>
                <p class="font-medium-xs mtop15 text-muted checklist-item-completed-by">
                    <?php
                    if ($v_list->added_from != my_id()) {
                        echo lang('task_created_by', fullname($v_list->added_from));
                    }
                    if ($v_list->added_from != my_id() && $v_list->finished == 1) {
                        echo ' - ';
                    }
                    if ($v_list->finished == 1) {
                        echo lang('task_checklist_item_completed_by', fullname($v_list->finished_from));
                    }
                    ?>
                </p>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<script>
    $(function(){
        $("#checklist-items").sortable({
            helper: 'clone',
            items: 'div.checklist',
            update: function(event, ui) {
                update_checklist_order();
            }
        });
        setTimeout(function(){
            do_task_checklist_items_height();
        },200);
    });
</script>
