<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.css">
<script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.custom.js"></script>
<?php
$created = can_action('101', 'created');
$edited = can_action('101', 'edited');
if (!empty($created) || !empty($edited)) {
    ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
            <h4 class="modal-title"
                id="myModalLabel"><?= lang('new') . ' ' . lang('training') ?></h4>
        </div>
        <div class="modal-body wrap-modal wrap">
            <form id="form_validation" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                  action="<?php echo base_url() ?>admin/training/save_training/<?php
                  if (!empty($training_info->training_id)) {
                      echo $training_info->training_id;
                  }
                  ?>" method="post" class="form-horizontal">
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('employee') ?> <span
                                class="required">*</span></label>
                    <div class="col-sm-5">
                        <select name="user_id" data-width="100%" id="employee" required
                                data-none-selected-text="<?php echo lang('select') . ' ' . lang('employee'); ?>"
                                data-live-search="true"
                                class="selectpicker">
                            <?php if (!empty($all_employee)): ?>
                                <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                    <optgroup label="<?php echo $dept_name; ?>">
                                        <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                            <option value="<?php echo $v_employee->user_id; ?>"
                                                <?php
                                                if (!empty($training_info->user_id)) {
                                                    $user_id = $training_info->user_id;
                                                } else {
                                                    $user_id = $this->session->userdata('user_id');
                                                }
                                                if (!empty($user_id)) {
                                                    echo $v_employee->user_id == $user_id ? 'selected' : '';
                                                }
                                                ?>><?php echo $v_employee->fullname . ' ( ' . $v_employee->designations . ' )' ?></option>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= lang('course_training') ?> <span
                                class="required">*</span></label>
                    <div class="col-sm-5">
                        <input type="text" name="training_name" required class="form-control" value="<?php
                        if (!empty($training_info->training_name)) {
                            echo $training_info->training_name;
                        }
                        ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= lang('vendor') ?> <span class="required">*</span></label>
                    <div class="col-sm-5">
                        <input type="text" name="vendor_name" class="form-control" value="<?php
                        if (!empty($training_info->vendor_name)) {
                            echo $training_info->vendor_name;
                        }
                        ?>" required/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?= lang('start_date') ?><span
                                class="required">*</span></label>
                    <div class="col-sm-5">
                        <div class="input-group ">
                            <input type="text" name="start_date" value="<?php
                            if (!empty($training_info->start_date)) {
                                echo $training_info->start_date;
                            }
                            ?>" class="form-control start_date" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3"><?= lang('finish_date') ?><span
                                class="required">*</span></label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="finish_date" value="<?php
                            if (!empty($training_info->finish_date)) {
                                echo $training_info->finish_date;
                            }
                            ?>" class="form-control end_date" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= lang('training_cost') ?></label>
                    <div class="col-sm-5">
                        <input type="text" data-parsley-type="number" name="training_cost" class="form-control"
                               value="<?php
                               if (!empty($training_info->training_cost)) {
                                   echo $training_info->training_cost;
                               }
                               ?>"/>
                    </div>
                </div>
                <?php
                if (!empty($training_info)) {
                    $training_id = $training_info->training_id;
                } else {
                    $training_id = null;
                }
                ?>
                <?= custom_form_Fields(15, $training_id); ?>

                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('status') ?> <span
                                class="required">*</span></label>
                    <div class="col-sm-5">
                        <select name="status" class="form-control" required>
                            <option
                                    value="0 <?php if (!empty($training_info->status)) echo $training_info->status == 0 ? 'selected' : '' ?>">
                                <?= lang('pending') ?>
                            </option>
                            <option
                                    value="1 <?php if (!empty($training_info->status)) echo $training_info->status == 1 ? 'selected' : '' ?>">
                                <?= lang('started') ?>
                            </option>
                            <option
                                    value="2 <?php if (!empty($training_info->status)) echo $training_info->status == 2 ? 'selected' : '' ?>">
                                <?= lang('completed') ?>
                            </option>
                            <option
                                    value="3 <?php if (!empty($training_info->status)) echo $training_info->status == 3 ? 'selected' : '' ?>">
                                <?= lang('terminated') ?>

                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('performance') ?></label>
                    <div class="col-sm-5">
                        <select name="performance" id="employee" class="form-control">
                            <option
                                    value="0 <?php if (!empty($training_info->performance)) echo $training_info->performance == 0 ? 'selected' : '' ?>">
                                <?= lang('not_concluded') ?>
                            </option>
                            <option
                                    value="1 <?php if (!empty($training_info->performance)) echo $training_info->performance == 1 ? 'selected' : '' ?>">
                                <?= lang('satisfactory') ?>

                            </option>
                            <option
                                    value="2 <?php if (!empty($training_info->performance)) echo $training_info->performance == 2 ? 'selected' : '' ?>">
                                <?= lang('average') ?>
                            </option>
                            <option
                                    value="3 <?php if (!empty($training_info->performance)) echo $training_info->performance == 3 ? 'selected' : '' ?>">
                                <?= lang('poor') ?>
                            </option>
                            <option
                                    value="4 <?php if (!empty($training_info->performance)) echo $training_info->performance == 4 ? 'selected' : '' ?>">
                                <?= lang('excellent') ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= lang('remarks') ?></label>
                    <div class="col-sm-8">
                                        <textarea class="form-control textarea_2" name="remarks"><?php
                                            if (!empty($training_info->remarks)) {
                                                echo $training_info->remarks;
                                            }
                                            ?></textarea>
                    </div>
                </div>
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
                        <?php
                        if (!empty($training_info->upload_file)) {
                            $uploaded_file = json_decode($training_info->upload_file);
                        }
                        if (!empty($uploaded_file)) {
                            foreach ($uploaded_file as $v_files_image) { ?>
                                <div class="pull-left mt pr-lg mb" style="width:100px;">
                                                        <span data-dz-remove class="pull-right existing_image"
                                                              style="cursor: pointer"><i
                                                                    class="fa fa-times"></i></span>
                                    <?php if ($v_files_image->is_image == 1) { ?>
                                        <img data-dz-thumbnail
                                             src="<?php echo base_url() . $v_files_image->path ?>"
                                             class="upload-thumbnail-sm"/>
                                    <?php } else { ?>
                                        <span data-toggle="tooltip" data-placement="top"
                                              title="<?= $v_files_image->fileName ?>"
                                              class="mailbox-attachment-icon"><i
                                                    class="fa fa-file-text-o"></i></span>
                                    <?php } ?>

                                    <input type="hidden" name="path[]"
                                           value="<?php echo $v_files_image->path ?>">
                                    <input type="hidden" name="fileName[]"
                                           value="<?php echo $v_files_image->fileName ?>">
                                    <input type="hidden" name="fullPath[]"
                                           value="<?php echo $v_files_image->fullPath ?>">
                                    <input type="hidden" name="size[]"
                                           value="<?php echo $v_files_image->size ?>">
                                    <input type="hidden" name="is_image[]"
                                           value="<?php echo $v_files_image->is_image ?>">
                                </div>
                            <?php }; ?>
                        <?php }; ?>
                        <script type="text/javascript">
                            $(document).on('loaded.bs.modal', function () {
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
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                class="required">*</span></label>
                    <div class="col-sm-9">
                        <div class="checkbox c-radio needsclick">
                            <label class="needsclick">
                                <input id="" <?php
                                if (!empty($training_info->permission) && $training_info->permission == 'all') {
                                    echo 'checked';
                                } elseif (empty($training_info)) {
                                    echo 'checked';
                                }
                                ?> type="radio" name="permission" value="everyone">
                                <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                <i title="<?= lang('permission_for_all') ?>"
                                   class="fa fa-question-circle" data-toggle="tooltip"
                                   data-placement="top"></i>
                            </label>
                        </div>
                        <div class="checkbox c-radio needsclick">
                            <label class="needsclick">
                                <input id="" <?php
                                if (!empty($training_info->permission) && $training_info->permission != 'all') {
                                    echo 'checked';
                                }
                                ?> type="radio" name="permission" value="custom_permission"
                                >
                                <span class="fa fa-circle"></span><?= lang('custom_permission') ?> <i
                                        title="<?= lang('permission_for_customization') ?>"
                                        class="fa fa-question-circle" data-toggle="tooltip"
                                        data-placement="top"></i>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group <?php
                if (!empty($training_info->permission) && $training_info->permission != 'all') {
                    echo 'show';
                }
                ?>" id="permission_user">
                    <label for="field-1"
                           class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                        <span
                                class="required">*</span></label>
                    <div class="col-sm-9">
                        <?php
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $key => $v_user) {

                                if ($v_user->role_id == 1) {
                                    $disable = true;
                                    $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                } else {
                                    $disable = false;
                                    $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                }

                                ?>
                                <div class="checkbox c-checkbox needsclick">
                                    <label class="needsclick">
                                        <input type="checkbox"
                                            <?php
                                            if (!empty($training_info->permission) && $training_info->permission != 'all') {
                                                $get_permission = json_decode($training_info->permission);
                                                foreach ($get_permission as $user_id => $v_permission) {
                                                    if ($user_id == $v_user->user_id) {
                                                        echo 'checked';
                                                    }
                                                }

                                            }
                                            ?>
                                               value="<?= $v_user->user_id ?>"
                                               name="assigned_to[]"
                                               class="needsclick">
                                        <span
                                                class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                    </label>

                                </div>
                                <div class="action p
                                                <?php

                                if (!empty($training_info->permission) && $training_info->permission != 'all') {
                                    $get_permission = json_decode($training_info->permission);

                                    foreach ($get_permission as $user_id => $v_permission) {
                                        if ($user_id == $v_user->user_id) {
                                            echo 'show';
                                        }
                                    }

                                }
                                ?>
                                                " id="action_<?= $v_user->user_id ?>">
                                    <label class="checkbox-inline c-checkbox">
                                        <input id="<?= $v_user->user_id ?>" checked type="checkbox"
                                               name="action_<?= $v_user->user_id ?>[]"
                                               disabled
                                               value="view">
                                        <span
                                                class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                    </label>
                                    <label class="checkbox-inline c-checkbox">
                                        <input <?php if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        } ?> id="<?= $v_user->user_id ?>"
                                            <?php

                                            if (!empty($training_info->permission) && $training_info->permission != 'all') {
                                                $get_permission = json_decode($training_info->permission);

                                                foreach ($get_permission as $user_id => $v_permission) {
                                                    if ($user_id == $v_user->user_id) {
                                                        if (in_array('edit', $v_permission)) {
                                                            echo 'checked';
                                                        };

                                                    }
                                                }

                                            }
                                            ?>
                                             type="checkbox"
                                             value="edit" name="action_<?= $v_user->user_id ?>[]">
                                        <span
                                                class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                    </label>
                                    <label class="checkbox-inline c-checkbox">
                                        <input <?php if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        } ?> id="<?= $v_user->user_id ?>"
                                            <?php

                                            if (!empty($training_info->permission) && $training_info->permission != 'all') {
                                                $get_permission = json_decode($training_info->permission);
                                                foreach ($get_permission as $user_id => $v_permission) {
                                                    if ($user_id == $v_user->user_id) {
                                                        if (in_array('delete', $v_permission)) {
                                                            echo 'checked';
                                                        };
                                                    }
                                                }

                                            }
                                            ?>
                                             name="action_<?= $v_user->user_id ?>[]"
                                             type="checkbox"
                                             value="delete">
                                        <span
                                                class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                    </label>
                                    <input id="<?= $v_user->user_id ?>" type="hidden"
                                           name="action_<?= $v_user->user_id ?>[]" value="view">

                                </div>


                                <?php
                            }
                        }
                        ?>


                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-2">
                        <button type="submit" id="file-save-button"
                                class="btn btn-primary btn-block"><?= lang('save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>


<script type="text/javascript">
    $(document).ready(function () {
        $('body').find('select.selectpicker').not('.ajax-search').selectpicker({
            showSubtext: true,
        });
    });
</script>