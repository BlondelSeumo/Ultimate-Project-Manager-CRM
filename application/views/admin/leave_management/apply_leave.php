<?php include_once 'asset/admin-ajax.php';
$office_hours = config_item('office_hours');
?>
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.css">
<script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.custom.js"></script>

<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>

        <h4 class="modal-title"
            id="myModalLabel"><?= lang('apply') . ' ' . lang('leave'); ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="row">
            <div class="col-sm-8">
                <form data-parsley-validate="" novalidate=""
                      action="<?php echo base_url() ?>admin/leave_management/save_leave_application"
                      method="post" enctype="multipart/form-data" class="form-horizontal">
                    <div class="panel_controls">
                        <?php if (!empty(admin_head())) { ?>
                            <div class="form-group">
                                <label for="field-1"
                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                    <span
                                        class="required"> *</span></label>

                                <div class="col-sm-8">
                                    <select name="user_id" style="width: 100%" onchange="get_leave_details(this.value)"
                                            class="form-control select_box"
                                            id="users_id" required>
                                        <option
                                            value=""><?= lang('select') . ' ' . lang('users') ?></option>
                                        <?php
                                        $all_users = $this->db->where('role_id !=', 2)->get('tbl_users')->result();
                                        if (!empty($all_users)) {
                                            foreach ($all_users as $v_users) :
                                                $profile = $this->db->where('user_id', $v_users->user_id)->get('tbl_account_details')->row();
                                                ?>
                                                <option <?= ($v_users->user_id == $this->session->userdata('user_id')) ? 'selected' : '' ?>
                                                    value="<?php echo $profile->user_id ?>">
                                                    <?php echo $profile->fullname ?></option>
                                            <?php endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <div class="required" id="username_result"></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" id="user_id"
                                   value="<?php echo $this->session->userdata('user_id') ?>">
                        <?php } ?>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('leave_category') ?>
                                <span
                                    class="required"> *</span></label>

                            <div class="col-sm-8 ">
                                <div class="input-group">
                                    <select name="leave_category_id" style="width: 100%"
                                            class="form-control select_box"
                                            id="leave_category" required>
                                        <option
                                            value=""><?= lang('select') . ' ' . lang('leave_category') ?></option>
                                        <?php
                                        $all_leave_category = $this->db->get('tbl_leave_category')->result();
                                        if (!empty($all_leave_category)) {
                                            foreach ($all_leave_category as $v_category) : ?>
                                                <option
                                                    value="<?php echo $v_category->leave_category_id ?>">
                                                    <?php echo $v_category->leave_category ?></option>
                                            <?php endforeach;
                                        }
                                        $_created = can_action('122', 'created');
                                        ?>
                                    </select>
                                    <?php if (!empty($_created)) { ?>
                                        <div class="input-group-addon" id="new__leave_category"
                                             title="<?= lang('new') . ' ' . lang('leave_category') ?>"
                                             data-toggle="tooltip" data-placement="top">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/leave_management/leave_category"><i
                                                    class="fa fa-plus"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-sm-3"></div>
                            <div class="col-sm-8">
                                <div class="required" id="username_result"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= lang('duration') ?>
                                <span class="required"> *</span></label>
                            <div class="col-sm-8">
                                <label class="radio-inline c-radio">
                                    <input type="radio" name="leave_type" value="single_day" checked="">
                                    <span class="fa fa-circle"></span><?= lang('single_day') ?></label>
                                <label class="radio-inline c-radio">
                                    <input type="radio" name="leave_type" value="multiple_days">
                                    <span class="fa fa-circle"></span><?= lang('multiple_days') ?></label>
                                <label class="radio-inline c-radio">
                                    <input type="radio" name="leave_type" value="hours">
                                    <span class="fa fa-circle"></span><?= lang('hours') ?></label>
                            </div>
                        </div>


                        <div class="form-group" id="single_day">
                            <label class="col-sm-3 control-label"><?= lang('start_date') ?>
                                <span
                                    class="required"> *</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" name="single_day_start_date" id="single_day_start_date"
                                           onchange="check_available_leave(this.value)"
                                           class="form-control datepicker" value=""
                                           data-format="dd-mm-yyyy">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="multiple_days">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('start_date') ?>
                                    <span
                                        class="required"> *</span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" name="multiple_days_start_date" id="multiple_days_start_date"
                                               class="form-control start_date" value=""
                                               data-format="dd-mm-yyyy">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="fa fa-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('end_date') ?> <span
                                        class="required"> *</span></label>

                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" name="multiple_days_end_date" id="multiple_days_end_date"
                                               onchange="check_available_leave(this.value)"
                                               class="form-control end_date" value=""
                                               data-format="dd-mm-yyyy">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="fa fa-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="hours">
                            <label class="col-sm-3 control-label"><?= lang('start_date') ?>
                                <span
                                    class="required"> *</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" name="hours_start_date" id="hours_start_date"
                                           onchange="check_available_leave(this.value)"
                                           class="form-control datepicker" value=""
                                           data-format="dd-mm-yyyy">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="col-sm-6 control-label"><?= lang('hours') ?>
                                    <span
                                        class="required"> *</span></label>
                                <div class="col-sm-6 pr0">
                                    <select name="hours" class="form-control">
                                        <?php for ($i = 1; $i <= $office_hours; $i++) {
                                            ?>
                                            <option value="<?= $i ?>"><?php if ($office_hours <= 9) {
                                                    echo '0';
                                                }
                                                echo $i ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('reason') ?></label>
                            <div class="col-sm-8"><textarea id="present" name="reason" class="form-control"
                                                            rows="6"></textarea></div>
                        </div>

                        <?= custom_form_Fields(17, null); ?>

                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('attachment') ?></label>

                            <div class="col-sm-8">
                                <div id="comments_file-dropzone" class="dropzone mb15">

                                </div>
                                <div id="comments_file-dropzone-scrollbar">
                                    <div id="comments_file-previews">
                                        <div id="file-upload-row" class="mt pull-left">

                                            <div class="preview box-content pr-lg" style="width:100px;">
                                                    <span data-dz-remove class="pull-right" style="cursor: pointer">
                                    <i class="fa fa-times"></i>
                                </span>
                                                <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                                <input class="file-count-field" type="hidden" name="files[]"
                                                       value=""/>
                                                <div
                                                    class="mb progress progress-striped upload-progress-sm active mt-sm"
                                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                    aria-valuenow="0">
                                                    <div class="progress-bar progress-bar-success"
                                                         style="width:0%;"
                                                         data-dz-uploadprogress></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $(".existing_image").click(function () {
                                            $(this).parent().remove();
                                        });
                                        fileSerial = 0;
                                        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                        var previewNode = document.querySelector("#file-upload-row");
                                        previewNode.id = "";
                                        var previewTemplate = previewNode.parentNode.innerHTML;
                                        previewNode.parentNode.removeChild(previewNode);
                                        Dropzone.autoDiscover = false;
                                        var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                            url: "<?= base_url()?>admin/global_controller/upload_file",
                                            thumbnailWidth: 80,
                                            thumbnailHeight: 80,
                                            parallelUploads: 20,
                                            previewTemplate: previewTemplate,
                                            dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                            autoQueue: true,
                                            previewsContainer: "#comments_file-previews",
                                            clickable: true,
                                            accept: function (file, done) {
                                                if (file.name.length > 200) {
                                                    done("Filename is too long.");
                                                    $(file.previewTemplate).find(".description-field").remove();
                                                }
                                                //validate the file
                                                $.ajax({
                                                    url: "<?= base_url()?>admin/global_controller/validate_project_file",
                                                    data: {file_name: file.name, file_size: file.size},
                                                    cache: false,
                                                    type: 'POST',
                                                    dataType: "json",
                                                    success: function (response) {
                                                        if (response.success) {
                                                            fileSerial++;
                                                            $(file.previewTemplate).find(".description-field").attr("name", "comment_" + fileSerial);
                                                            $(file.previewTemplate).append("<input type='hidden' name='file_name_" + fileSerial + "' value='" + file.name + "' />\n\
                                                                        <input type='hidden' name='file_size_" + fileSerial + "' value='" + file.size + "' />");
                                                            $(file.previewTemplate).find(".file-count-field").val(fileSerial);
                                                            done();
                                                        } else {
                                                            $(file.previewTemplate).find("input").remove();
                                                            done(response.message);
                                                        }
                                                    }
                                                });
                                            },
                                            processing: function () {
                                                $("#file-save-button").prop("disabled", true);
                                            },
                                            queuecomplete: function () {
                                                $("#file-save-button").prop("disabled", false);
                                            },
                                            fallback: function () {
                                                //add custom fallback;
                                                $("body").addClass("dropzone-disabled");
                                                $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                                                $("#comments_file-dropzone").hide();

                                                $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                                                $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                                                    var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                                        + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                                        + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                                        + "</div>";
                                                    $("#comments_file-previews").prepend(newFileRow);
                                                });
                                                $("#add-more-file-button").trigger("click");
                                                $("#comments_file-previews").on("click", ".remove-file", function () {
                                                    $(this).closest(".file-row").remove();
                                                });
                                            },
                                            success: function (file) {
                                                setTimeout(function () {
                                                    $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                                                }, 1000);
                                            }
                                        });

                                    })
                                </script>
                            </div>
                        </div>
                        <div class="form-group mt-lg">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" id="file-save-button" name="sbtn" value="1"
                                        class="btn btn-primary">Submit
                                </button>
                            </div>
                        </div>
                        <br/>
                    </div>
                </form>
            </div>
            <div class="col-sm-4" id="leave_details">
                <div class="panel panel-custom">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><?= lang('my_leave') . ' ' . lang('details') ?></strong>
                        </div>
                    </div>
                    <table class="table">
                        <tbody>
                        <?php
                        $total_taken = 0;
                        $total_quota = 0;
                        $leave_report = leave_report($this->session->userdata('user_id'));

                        if (!empty($leave_report['leave_category'])) {
                            foreach ($leave_report['leave_category'] as $lkey => $v_l_report) {
                                $total_quota += $leave_report['leave_quota'][$lkey];
                                $total_taken += $leave_report['leave_taken'][$lkey];
                                ?>
                                <tr>
                                    <td><strong> <?= $leave_report['leave_category'][$lkey] ?></strong>:</td>
                                    <td>
                                        <?= $leave_report['leave_taken'][$lkey] ?>
                                        /<?= $leave_report['leave_quota'][$lkey]; ?> </td>
                                </tr>
                            <?php }
                        }
                        ?>
                        <tr>
                            <td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;">
                                <strong> <?= lang('total') ?></strong>:
                            </td>
                            <td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;"> <?= $total_taken; ?>
                                /<?= $total_quota; ?> </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#myModal_extra_lg').on('hidden.bs.modal', function () {
        location.reload();
    });

    $(document).ready(function () {
        $("#new__leave_category").click(function () {
            $("#myModal").css("zIndex", '1052');
        });
        $('div#single_day').show();
        $('div#multiple_days').hide();
        $('div#hours').hide();
        $('input[name="leave_type"]').click(function () {
            var leave_type = this.value;
            if (leave_type == 'single_day') {
                $('div#single_day').show().attr('required', true);
                $('div#multiple_days').hide().removeAttr('required');
                $('div#hours').hide().removeAttr('required');
            } else if (leave_type == 'multiple_days') {
                $('div#multiple_days').show().attr('required', true);
                $('div#single_day').hide().removeAttr('required');
                $('div#hours').hide().removeAttr('required');
            } else if (leave_type == 'hours') {
                $('div#hours').show().attr('required', true);
                $('div#single_day').hide().removeAttr('required');
                $('div#multiple_days').hide().removeAttr('required');
            } else {
                $('div#single_day').show().attr('required', true);
                $('div#multiple_days').hide().removeAttr('required');
                $('div#hours').hide().removeAttr('required');
            }
        });
        $('#leave_category').on('change', function () {
            $('#single_day_start_date').val('');
            $('#multiple_days_start_date').val('');
            $('#multiple_days_end_date').val('');
            $('#hours_start_date').val('');
        });
        <?php if (!empty(admin_head())) { ?>
        $('#users_id').on('change', function () {
            $('#single_day_start_date').val('');
            $('#multiple_days_start_date').val('');
            $('#multiple_days_end_date').val('');
            $('#hours_start_date').val('');
        });
        <?php }?>
        $('#multiple_days_start_date').on('change', function () {
            $('#multiple_days_end_date').val('');
        });
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });

    });
</script>