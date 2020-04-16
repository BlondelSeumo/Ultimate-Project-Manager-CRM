<?php
echo message_box('success');
echo message_box('error');
$created = can_action('130', 'created');
$edited = can_action('130', 'edited');
$deleted = can_action('130', 'deleted');
if (!empty($created) || !empty($edited)){
?>
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
        $all_form = $this->db->get('tbl_form')->result();
        if (!empty($all_form)) {
            foreach ($all_form as $v_form) { ?>
                <li class="filter_by" id="<?= $v_form->form_id ?>"><a
                        href="#"><?php echo lang($v_form->form_name); ?></a></li>
            <?php }

        }
        ?>

        <div class="clearfix"></div>
    </ul>
</div>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs" style="margin-top: 1px">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('custom_field') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_field') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('custom_field') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('label') ?></th>
                            <th><?= lang('custom_field_for') ?></th>
                            <th><?= lang('type') ?></th>
                            <th><?= lang('active') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "admin/settings/custom_fieldList";
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
                                        filter_by = filter_by;
                                    } else {
                                        filter_by = '';
                                    }
                                    table_url(base_url + "admin/settings/custom_fieldList/" + filter_by);
                                });
                            });
                        </script>

                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)) { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" enctype="multipart/form-data" id="form" data-parsley-validate="" novalidate=""
                          action="<?php echo base_url(); ?>admin/settings/save_custom_field/<?php
                          if (!empty($field_info)) {
                              echo $field_info->custom_field_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="form-group" id="border-none">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('custom_field_for') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-5">

                                <select name="form_id" class="form-control select_box" style="width:100%" required>
                                    <?php
                                    $all_form = $this->db->get('tbl_form')->result();
                                    if (!empty($all_form)) {
                                        foreach ($all_form as $v_form) { ?>
                                            <option
                                                value="<?= $v_form->form_id ?>" <?= (!empty($field_info->form_id) && $field_info->form_id == $v_form->form_id ? 'selected' : null) ?>> <?= lang($v_form->form_name) ?> </option>
                                        <?php }

                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('field_label') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($field_info)) {
                                    echo $field_info->field_label;
                                }
                                ?>" name="field_label" required="">
                            </div>

                        </div>

                        <div class="default_value type">
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('default_value') ?> </label>
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" value="<?=
                                    (isset($field_info->field_type) && $field_info->field_type == 'text' || isset($field_info->field_type) && $field_info->field_type == 'email' ? json_decode($field_info->default_value)[0] : null)
                                    ?>" name="default_value[]">
                                </div>

                            </div>
                        </div>
                        <div class="checkbox_type type">
                            <div class="form-group ">
                                <label class="col-lg-3 control-label"><?= lang('default_value') ?> </label>
                                <div class="col-md-4">
                                    <?php
                                    if (!empty($field_info->field_type) && $field_info->field_type == 'checkbox') {
                                        $default_value = json_decode($field_info->default_value)[0];
                                    } else {
                                        $default_value = null;
                                    }
                                    $options = array(
                                        'checked' => lang('checked'),
                                        'unchecked' => lang('unchecked'),
                                    );
                                    echo form_dropdown('default_value[]', $options, $default_value, 'style="width:100%" class="select_box" required'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- End discount Fields -->
                        <div class="form-group terms">
                            <label class="col-lg-3 control-label"><?= lang('help_text') ?> </label>
                            <div class="col-lg-5">
                        <textarea name="help_text" class="form-control"><?php
                            if (!empty($field_info)) {
                                echo $field_info->help_text;
                            }
                            ?></textarea>
                            </div>
                        </div>
                        <div class="form-group" id="border-none">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('field_type') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <?php
                                if (!empty($field_info->field_type)) {
                                    $field_type = $field_info->field_type;
                                } else {
                                    $field_type = null;
                                }
                                $options = array(
                                    'text' => lang('text_field'),
                                    'textarea' => lang('textarea'),
                                    'dropdown' => lang('select'),
                                    'email' => lang('email'),
                                    'date' => lang('date'),
                                    'checkbox' => lang('checkbox'),
                                    'numeric' => lang('numeric'),
                                );
                                echo form_dropdown('field_type', $options, $field_type, 'style="width:100%" id="type" class="select_box" required'); ?>
                            </div>
                        </div>
                        <div class="form-group" id="show_dropdown">
                            <label class="col-lg-3 control-label"><?= lang('option') ?></label>
                            <div class="col-lg-9 show_dropdown" id="option">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('required') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->required) && $field_info->required == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="required">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('show_on_table') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->show_on_table) && $field_info->show_on_table == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="show_on_table">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('show_on_details') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->show_on_details) && $field_info->show_on_details == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="show_on_details">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('visible_for_admin') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->visible_for_admin) && $field_info->visible_for_admin == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="visible_for_admin">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('visible_for_client') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->visible_for_client) && $field_info->visible_for_client == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="visible_for_client">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('active') ?></label>
                            <div class="col-lg-6">
                                <input data-toggle="toggle"
                                       name="status" value="active" <?php
                                if (!empty($field_info->status) && $field_info->status == 'active') {
                                    echo 'checked';
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                       data-onstyle="success btn-xs"
                                       data-offstyle="danger btn-xs" type="checkbox">
                            </div>
                        </div>


                        <div class="btn-bottom-toolbar text-right">
                            <?php
                            if (!empty($field_info)) { ?>
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
</div>
<script type="text/javascript">
    var option = 0;

    $(document).ready(function () {
        $("#type").change(function () {
            $(this).find("option:selected").each(function () {
                if ($(this).attr("value") == "dropdown") {
                    <?php
                    if(!empty($field_info->field_type)){
                    foreach(json_decode($field_info->default_value) as $optionValue){ ?>
                    addOption("<?= $optionValue;?>");
                    <?php }
                    }else{
                    ?>
                    addOption("");
                    addOption("");
                    <?php }?>
                    $('.show_dropdown').show();
                    $('#show_dropdown').show();
                    $(".show_dropdown :input").attr("disabled", false);

                    $('.checkbox_type').hide();
                    $(".checkbox_type :input").attr("disabled", true);
                    $('.default_value').hide();
                    $(".default_value :input").attr("disabled", true);
                }
                else if ($(this).attr("value") == "checkbox") {
                    $('.checkbox_type').show();
                    $(".checkbox_type :input").attr("disabled", false);
                    $('.default_value').hide();
                    $(".default_value :input").attr("disabled", true);
                    $('#show_dropdown').hide();
                    $('.show_dropdown').hide();
                    $(".show_dropdown :input").attr("disabled", true);
                } else {
                    $('#show_dropdown').hide();
                    $('.show_dropdown').hide();
                    $(".show_dropdown :input").attr("disabled", true);
                    $('.default_value').show();
                    $(".default_value :input").attr("disabled", false);
                    $('.checkbox_type').hide();
                    $(".checkbox_type :input").attr("disabled", true);
                }
            });
        }).change();

    });

    function addOption(event, curr_option_id) {
        var optionValue = '';
        if (typeof event !== 'undefined') {
            if (typeof event === 'object') {
                event.preventDefault();
            } else {
                optionValue = event;
            }
        }
        var option_id = ++option;

        var add_new = $('<div class="form-group remCF" id="' + option_id + '">\n\
        <div class="col-lg-6">\n\
        <input type="text" class="form-control" value="' + optionValue + '" id="inputFocus_' + option_id + '" name="default_value[]"/>\n\
        </div>\n\
        <div class="col-lg-2">\n\
        <a href="#" onclick="addOption(event, ' + option_id + ')"><i class="text-success fa fa-plus-circle"></i></a>\n\
        <a href="javascript:void(0);" onclick="removeOption(' + option_id + ')"><i class="text-danger fa fa-minus-circle"></i></a>\n\
        </div></div></div><div id="nextOption_' + option_id + '"> </div>');

        if (typeof curr_option_id !== 'undefined') {
            $('#nextOption_' + curr_option_id + '').prepend(add_new);
        } else {
            $("#option").append(add_new);
        }
        $('#inputFocus_' + option_id + '').focus();
    }

    function removeOption(option_id) {
        $('#' + option_id + '').remove();
    }
</script>
<script type="text/javascript">

    $(document).on("click", function () {
        $('.status input[type="checkbox"]').change(function () {
            var id = $(this).attr('id');
            if ($(this).is(":checked")) {
                var status = 'active';
            } else {
                var status = 'deactive';
            }
            var formData = {
                'status': status,
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/settings/change_field_status/' + id, // the url where we want to POST
                data: formData, // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    console.log(res);
                    if (res) {
                        location.reload();
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });

    })
    ;
</script>