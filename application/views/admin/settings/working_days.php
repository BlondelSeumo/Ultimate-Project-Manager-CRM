<?= message_box('success'); ?>
<?= message_box('error'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $(".different_time_input").attr('disabled', 'disabled');
        $(".different_time_hours").hide();
        // $(".same_time").attr('disabled', 'disabled');
    });
</script>

<?php
$working_days = get_result('tbl_working_days');;
if (config_item('office_time') == 'different_time') {
    $d_working_days = get_result('tbl_working_days', array('flag' => 1));
    if (!empty($d_working_days)) {
        foreach ($d_working_days as $v_d_days) {
            ?>
            <script type="text/javascript">
                $(function () {
                    $(".different_time_hours_" + <?= $v_d_days->day_id?>).removeClass('disabled');
                    $(".different_time_hours_" + <?= $v_d_days->day_id?>).removeAttr('disabled');
                    $("#different_time_" + <?= $v_d_days->day_id?>).show();
                    $(".different_time_input").removeAttr('disabled');
                });
            </script>
            <?php
        }
    }
} ?>

<?php

if (config_item('office_time') == 'same_time') {
    $s_working_days = get_result('tbl_working_days', array('flag' => 1));;

    if (!empty($s_working_days)) {
        foreach ($s_working_days as $v_s_days) {
            ?>
            <script type="text/javascript">
                $(function () {
                    $(".same_time").removeAttr('disabled');
                });
            </script>
            <?php
        }
    }
}
$days = $this->db->get('tbl_days')->result();
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('working_days') ?></header>
    <div class="panel-body">
        <form role="form" id="form" action="<?php echo base_url(); ?>admin/settings/save_working_days"
              method="post"
              class="form-horizontal  ">
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('office_time') ?></label>
                <div class="col-lg-9">
                    <label class="checkbox-inline c-checkbox">
                        <input class="select_one " value="same_time" <?php
                        if (config_item('office_time') == 'same_time') {
                            echo "checked=\"checked\"";
                        }
                        ?> id="same_time" type="checkbox" name="office_time">
                        <span class="fa fa-check"></span><?= lang('every_days_same_time') ?>
                    </label>

                    <label class="checkbox-inline c-checkbox">
                        <input class="select_one" <?php
                        if (config_item('office_time') == 'different_time') {
                            echo "checked=\"checked\"";
                        }
                        ?> value="different_time" id="different_time" type="checkbox"
                               name="office_time">
                        <span class="fa fa-check"></span><?= lang('set_different_time') ?>
                    </label>
                </div>
            </div>
            <?php if (!empty($working_days)) { ?>
                <input type="hidden" name="already_exist" value="1"/>
            <?php } ?>
            <div class="same_time" style="display: <?php
            if (config_item('office_time') == 'same_time') {
                echo 'block';
            } else {
                echo 'none';
            }
            ?>">
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label class="col-lg-6 control-label"><?= lang('start_hours') ?></label>
                        <div class="col-lg-6">

                            <div class="input-group">
                                <input type="text" name="s_start_hours" class="form-control timepicker same_time"
                                       value="<?php
                                       if (!empty($s_working_days)) {
                                           echo (date('h:i A',strtotime($s_working_days[0]->start_hours)));
                                       }
                                       ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-clock-o"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-6 control-label"><strong><?= lang('end_hours') ?> <span
                                        class="required"> *</span></strong></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" name="s_end_hours" class="form-control timepicker same_time"
                                       value="<?php
                                       if (!empty($s_working_days)) {
                                           echo (date('h:i A',strtotime($s_working_days[0]->end_hours)));
                                       }
                                       ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-clock-o"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group ml">
                    <!-- List  of days -->
                    <?php
                    foreach ($days as $v_day): ?><!--Retrieve Days from Database -->
                    <label class="checkbox-inline c-checkbox">
                        <input type="checkbox" class="same_time" name="day[]" value="<?php echo $v_day->day_id ?>"<?php
                        if (!empty($s_working_days)) {
                            foreach ($s_working_days as $v_s_work) { ?>
                                <?php if ($v_s_work->flag == 1 && $v_s_work->day_id == $v_day->day_id) {
                                    ?>
                                    checked
                                    <?php
                                }
                            }
                        }
                        ?>/>
                        <span class="fa fa-check"></span><strong><?= lang($v_day->day) ?></strong>
                        <input type="hidden" name="day_id[]"
                               value="<?php echo $v_day->day_id ?>"/>
                    </label>
                    <?php endforeach; ?>

                    <div class=" pull-right mr-sm">
                        <button type="submit"
                                class="btn btn-primary"><?= lang('save') ?></button>
                    </div>
                </div>

            </div>
            <?php foreach ($working_days as $v_w_days) { ?>
                <input type="hidden" name="working_days_id[]"
                       value="<?php echo $v_w_days->working_days_id ?>"/>
            <?php } ?>
            <div class="different_time" style="display:<?php
            if (config_item('office_time') == 'different_time') {
                echo 'block';
            } else {
                echo 'none';
            }
            ?>">
                <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>asset/css/kendo.common.min.css"/>
                <?php
                foreach ($days

                as $v_day): ?><!--Retrieve Days from Database -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong><?= lang($v_day->day) ?> </strong></label>
                    <div class="col-sm-1">
                        <div class="checkbox">
                            <input class="different_time_input ml0"
                                <?php
                                if (!empty($d_working_days)) {
                                    foreach ($d_working_days as $v_d_work) {
                                        if ($v_d_work->flag == 1 && $v_d_work->day_id == $v_day->day_id) {
                                            ?>
                                            checked
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                   type="checkbox" name="day[]"
                                   value="<?php echo $v_day->day_id ?>"/>
                        </div>
                    </div>
                    <div class="different_time_hours" id="different_time_<?= $v_day->day_id ?>">
                        <div class="col-sm-3">
                            <label class="col-lg-3 control-label"><?= lang('start') ?></label>
                            <div class="col-lg-9">
                                <input type="text" name="start_hours_<?= $v_day->day_id ?>"
                                       class="disabled form-control timepicker different_time_hours_<?= $v_day->day_id ?>"
                                       value="<?php
                                       if (!empty($d_working_days)) {
                                           foreach ($d_working_days as $v_d_work) {
                                               if ($v_d_work->day_id == $v_day->day_id) {
                                                   echo(date('h:i A', strtotime($v_d_work->start_hours)));
                                               }
                                           }
                                       }
                                       ?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-sm-3 control-label"><strong><?= lang('end') ?> </strong></label>
                            <div class="col-sm-9">
                                <input type="text" name="end_hours_<?= $v_day->day_id ?>"
                                       class="disabled different_time_hours_<?= $v_day->day_id ?> form-control timepicker "
                                       value="<?php
                                       if (!empty($d_working_days)) {
                                           foreach ($d_working_days as $v_d_work) {
                                               if ($v_d_work->day_id == $v_day->day_id) {
                                                   echo(date('h:i A', strtotime($v_d_work->end_hours)));
                                               }
                                           }
                                       }
                                       ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-5">
                        <button type="submit"
                                class="btn btn-primary btn-block"><?= lang('save') ?></button>
                    </div>
                </div>
                <!-- List  of days -->
            </div>


        </form>
    </div>
</div>
