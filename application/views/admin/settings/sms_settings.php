<?php
$gateways = $this->sms->get_gateways();
$triggers = $this->sms->get_available_triggers();
$total_gateways = count($gateways);
if ($total_gateways > 1) { ?>
    <div class="alert alert-info alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo lang('only_one_active_sms_gateway'); ?>
    </div>
<?php } ?>
<form action="<?= base_url() ?>admin/settings/save_sms_settings" method="post"
      class="panel panel-custom form-horizontal">
    <div class="panel-group" id="sms_gateways_options" role="tablist" aria-multiselectable="false">
        <?php foreach ($gateways as $gname => $gateway) { ?>
            <div class="panel panel-custom">
                <div class="panel-heading" role="tab" id="<?php echo 'heading' . $gname; ?>">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#sms_gateways_options"
                           href="#sms_<?php echo $gname; ?>" aria-expanded="true"
                           aria-controls="sms_<?php echo $gname; ?>">
                            <?php echo $gateway['name']; ?> <span class="pull-right"><i
                                        class="fa fa-sort-down"></i></span>
                        </a>
                    </h4>
                </div>
                <div id="sms_<?php echo $gname; ?>"
                     class="panel-collapse collapse<?php if (config_item($gname . '_status') == 1 || $total_gateways == 1) {
                         echo ' in';
                     } ?>" role="tabpanel" aria-labelledby="<?php echo 'heading' . $gname; ?>">
                    <div class="panel-body no-br-tlr no-border-color">
                        <?php
                        if (isset($gateway['info']) && $gateway['info'] != '') {
                            echo $gateway['info'];
                        }
                        foreach ($gateway['options'] as $g_option) {
                            ?>
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= $g_option['label'] ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" value="<?php
                                    if (!empty($g_option['value'])) {
                                        echo $g_option['value'];
                                    }
                                    ?>" name="<?= $g_option['name'] ?>">
                                    <small>
                                        <?php if (isset($g_option['info'])) {
                                            echo $g_option['info'];
                                        } ?>
                                    </small>
                                </div>

                            </div>
                        <?php }
                        echo '<div class="sms_gateway_active">';
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('active') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input class="select_one" type="checkbox" value="1" <?php
                                        if (config_item($gname . '_status') == '1') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="<?= $gname . '_status' ?>">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php
                        echo '</div>';
                        if (config_item($gname . '_status') == '1') {
                            echo '<div class="panel panel-custom"><div class=" panel-heading"><strong>' . lang('test_sms_config') . '</strong></div>';
                            echo '<div class="form-group"><label class="col-lg-3 control-label">' . lang('enter') . ' ' . lang('phone') . ' ' . lang('number') . '</label><div class="col-lg-6"><input type="text" value="" placeholder="' . lang('enter') . ' ' . lang('phone') . ' ' . lang('number') . '" class="form-control test-phone" data-id="' . $gname . '"></div></div>';
                            echo '<div class="form-group"><label class="col-lg-3 control-label">' . lang('test_message') . '</label><div class="col-lg-6"><textarea class="form-control sms-gateway-test-message" placeholder="' . lang('test_message') . '" data-id="' . $gname . '" rows="4"></textarea></div></div>';
                            echo '<div class="form-group"><label class="col-lg-3 control-label">' . lang('') . '</label><div class="col-lg-6"><button type="button" class="btn btn-info send-test-sms" data-id="' . $gname . '">' . lang('send_test_sms') . '</button></div>';
                            echo '<div id="sms_test_response" data-id="' . $gname . '"></div></div> ';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="panel panel-custom">
            <div class="panel-heading">
                <b><?php echo lang('SMS') . ' ' . lang('template'); ?></b>
                <p class="text-sm m0 p0">Leave contents blank to disable
                    specific <?php echo lang('SMS') . ' ' . lang('template'); ?>.</p>
            </div>
            <div class="p ">
                <?php
                foreach ($triggers as $trigger_name => $trigger_opts) {
                    echo '<a href="#" onclick="slideToggle(\'#sms_merge_fields_' . $trigger_name . '\'); return false;" class="pull-right"><small>' . lang('available_merge_fields') . '</small></a>';

                    $label = '<b>' . $trigger_opts['label'] . '</b>';
                    if (isset($trigger_opts['info']) && $trigger_opts['info'] != '') {
                        $number_input = null;
                        if (!empty($trigger_opts['sms_number'])) {
                            $number_input = '<input class="form-control" style="width:20%;display:initial;height:22px;color:red" value="' . $trigger_opts['sms_number'] . '" type="text" name="' . $trigger_name . '_sms_number">';
                        }
                        $label .= '<p class="text-sm">' . $trigger_opts['info'] . ' ' . $number_input . '</p>';
                    }
                    ?>
                    <?= $label ?>
                    <div class="">
                <textarea class="form-control" name="<?= $this->sms->trigger_option_name($trigger_name) ?>"><?php
                    if (!empty($trigger_opts['value'])) {
                        echo $trigger_opts['value'];
                    }
                    ?></textarea>
                    </div>


                    <?php
                    $merge_fields = '';

                    foreach ($trigger_opts['merge_fields'] as $merge_field) {
                        $merge_fields .= $merge_field . ', ';
                    }

                    if ($merge_fields != '') {
                        echo '<div id="sms_merge_fields_' . $trigger_name . '" style="display:none;" class="mt">';
                        echo substr($merge_fields, 0, -2);
                        echo '<hr class="mb-sm mt-sm" />';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
    </div>
</form>
<script type="text/javascript">
    $('.send-test-sms').on('click', function () {
        var id = $(this).data('id');
        var errorContainer = $('#sms_test_response[data-id="' + id + '"]');
        var message = $('textarea[data-id="' + id + '"]').val();
        var number = $('input.test-phone[data-id="' + id + '"]').val();
        var that = $(this);
        var URL = '<?= base_url('admin/settings/test_sms')?>';
        errorContainer.empty();
        message = message.trim();
        if (message != '' && number != '') {
            that.prop('disabled', true);
            $.post(URL, {
                message: message,
                number: number,
                id: id,
                sms_gateway_test: true
            }).done(function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    errorContainer.html('<div style="margin-top: 40px !important;" class="p m-lg alert alert-success mt-lg">SMS Sent Successfully!</div>');
                } else {
                    errorContainer.html('<div style="margin-top: 40px !important;"  class="p m-lg alert alert-warning mt-lg">' + response.error + '</div>');
                }
            }).always(function () {
                that.prop('disabled', false);
            });
        }
    });
</script>