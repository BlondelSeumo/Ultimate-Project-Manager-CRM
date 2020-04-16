<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.css">
<script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.custom.js"></script>

<?php
$created = can_action('100', 'created');
$edited = can_action('100', 'edited');
if (!empty($created) || !empty($edited)) {
    ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
            <h4 class="modal-title"
                id="myModalLabel"><?= lang('new') . ' ' . lang('announcements') ?></h4>
        </div>
        <div class="modal-body wrap-modal wrap">
            <form role="form" id="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                  action="<?php echo base_url(); ?>admin/announcements/save_announcements/<?= (!empty($announcements->announcements_id) ? $announcements->announcements_id : ''); ?>"
                  method="post" class="form-horizontal form-groups-bordered">

                <div class="form-group">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('title') ?> <span
                                class="required">*</span></label>

                    <div class="col-sm-8">
                        <input type="text" name="title"
                               value="<?= (!empty($announcements->title) ? $announcements->title : ''); ?>"
                               class="form-control"
                               requried=""/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('description') ?></label>

                    <div class="col-sm-8">
                    <textarea name="description"
                              class="form-control textarea"><?= (!empty($announcements->description) ? $announcements->description : ''); ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= lang('start_date') ?> <span
                                class="required">*</span></label>

                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="start_date"
                                   placeholder="<?= lang('enter') . ' ' . lang('start_date') ?>"
                                   class="form-control start_date" value="<?php
                            if (!empty($announcements->start_date)) {
                                echo $announcements->start_date;
                            }
                            ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= lang('end_date') ?> <span
                                class="required">*</span></label>

                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="end_date"
                                   placeholder="<?= lang('enter') . ' ' . lang('end_date') ?>"
                                   class="form-control end_date" value="<?php
                            if (!empty($announcements->end_date)) {
                                echo $announcements->end_date;
                            }
                            ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
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
                        if (!empty($announcements->attachment)) {
                            $uploaded_file = json_decode($announcements->attachment);
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
                <?php
                if (!empty($announcements)) {
                    $announcements_id = $announcements->announcements_id;
                } else {
                    $announcements_id = null;
                }
                ?>
                <?= custom_form_Fields(16, $announcements_id); ?>
                <div class="form-group">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('share_with') ?></label>

                    <div class="col-sm-8">
                        <div class="checkbox c-checkbox">
                            <label>
                                <input <?= (!empty($announcements->all_client) ? 'checked' : ''); ?> type="checkbox"
                                       name="all_client"
                                       value="1">
                                <span class="fa fa-check"></span> <?= lang('all_client') ?>
                            </label>
                        </div>


                    </div>
                </div>
                <div class="form-group">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('status') ?></label>

                    <div class="col-sm-8">
                        <div class="col-sm-4 row">
                            <div class="checkbox-inline c-checkbox">
                                <label>
                                    <input
                                        <?= (!empty($announcements->status) && $announcements->status == 'published' ? 'checked' : ''); ?>
                                        class="select_one" type="checkbox" name="status" value="published">
                                    <span class="fa fa-check"></span> <?= lang('published') ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="checkbox-inline c-checkbox">
                                <label>
                                    <input
                                        <?= (!empty($announcements->status) && $announcements->status == 'unpublished' ? 'checked' : ''); ?>
                                        class="select_one" type="checkbox" name="status" value="unpublished">
                                    <span class="fa fa-check"></span> <?= lang('unpublished') ?>
                                </label>
                            </div>
                        </div>


                    </div>
                </div>
                <!--hidden input values -->
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
        var maxAppend = 0;
        $("#add_more").click(function () {

            var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('attachment') ?></label>\n\
        <div class="col-sm-5">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="attachment[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-3">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
            maxAppend++;
            $("#add_new").append(add_new);
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>
