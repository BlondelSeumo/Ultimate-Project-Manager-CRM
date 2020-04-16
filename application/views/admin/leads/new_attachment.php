<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('new') . ' ' . lang('attachment') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form action="<?= base_url() ?>admin/leads/save_attachment"
              class="form-horizontal form-groups-bordered"
              role="form" method="post" enctype="multipart/form-data" accept-charset="utf-8" novalidate="novalidate">
            <div class="modal-body clearfix">
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('file_title') ?> <span
                                class="text-danger">*</span></label>
                    <div class="col-lg-6">
                        <input name="title" class="form-control" value="<?php
                        if (!empty($add_files_info)) {
                            echo $add_files_info->title;
                        }
                        ?>" required placeholder="<?= lang('file_title') ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('description') ?></label>
                    <div class="col-lg-6">
                                        <textarea name="description" class="form-control"
                                                  placeholder="<?= lang('description') ?>"><?php
                                            if (!empty($add_files_info)) {
                                                echo $add_files_info->description;
                                            }
                                            ?></textarea>
                    </div>
                </div>
                <input type="hidden" name="leads_id" value="<?php
                if (!empty($leads_details->leads_id)) {
                    echo $leads_details->leads_id;
                }
                ?>" class="form-control">
                <div class="form-group">
                    <div class="col-sm-12">
                        <div id="file-dropzone" class="dropzone mb15">

                        </div>
                        <div id="file-dropzone-scrollbar">
                            <div id="file-previews" class="row">
                                <div id="file-upload-row" class="col-sm-6 mt file-upload-row">
                                    <div class="preview box-content pr-lg" style="width:100px;">
                                        <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                        <div class="mb progress progress-striped upload-progress-sm active mt-sm"
                                             role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                             aria-valuenow="0">
                                            <div class="progress-bar progress-bar-success" style="width:0%;"
                                                 data-dz-uploadprogress></div>
                                        </div>
                                    </div>
                                    <div class="box-content">
                                        <p class="clearfix mb0 p0">
                                            <span class="name pull-left" data-dz-name></span>
                                            <span data-dz-remove class="pull-right" style="cursor: pointer">
                                    <i class="fa fa-times"></i>
                                </span>
                                        </p>
                                        <p class="clearfix mb0 p0">
                                            <span class="size" data-dz-size></span>
                                        </p>
                                        <strong class="error text-danger" data-dz-errormessage></strong>
                                        <input class="file-count-field" type="hidden" name="files[]" value=""/>
                                        <textarea class="form-control description-field" type="text"
                                                  style="cursor: auto;"
                                                  placeholder="<?php echo lang("comments") ?>"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="file-modal-footer"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cancel-upload" data-dismiss="modal"><span
                            class="fa fa-close"></span> <?php echo lang('close'); ?></button>
                <button id="file-save-leads_btn" type="submit" disabled="disabled"
                        class="btn btn-primary start-upload"><span
                            class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
            </div>
        </form>
        <script type="text/javascript">
            $(document).on('loaded.bs.modal', function () {
                fileSerial = 0;
                // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                var previewNode = document.querySelector("#file-upload-row");
                previewNode.id = "";
                var previewTemplate = previewNode.parentNode.innerHTML;
                previewNode.parentNode.removeChild(previewNode);
                Dropzone.autoDiscover = false;
                var projectFilesDropzone = new Dropzone("#file-dropzone", {
                    url: "<?= base_url()?>admin/global_controller/upload_file",
                    thumbnailWidth: 80,
                    thumbnailHeight: 80,
                    parallelUploads: 20,
                    previewTemplate: previewTemplate,
                    dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                    autoQueue: true,
                    previewsContainer: "#file-previews",
                    clickable: true,
                    accept: function (file, done) {
                        console.log(file);

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
                        $("#file-save-leads_btn").prop("disabled", true);
                    },
                    queuecomplete: function () {
                        $("#file-save-leads_btn").prop("disabled", false);
                    },
                    fallback: function () {
                        //add custom fallback;
                        $("body").addClass("dropzone-disabled");
                        $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                        $("#file-dropzone").hide();

                        $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                        $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                            var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                + "</div>";
                            $("#file-previews").prepend(newFileRow);
                        });
                        $("#add-more-file-button").trigger("click");
                        $("#file-previews").on("click", ".remove-file", function () {
                            $(this).closest(".file-row").remove();
                        });
                    },
                    success: function (file) {
                        setTimeout(function () {
                            $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                        }, 1000);
                    }
                });

                document.querySelector(".start-upload").onclick = function () {
                    projectFilesDropzone.enqueueFiles(projectFilesDropzone.getFilesWithStatus(Dropzone.ADDED));
                };
                document.querySelector(".cancel-upload").onclick = function () {
                    projectFilesDropzone.removeAllFiles(true);
                };
                initScrollbar("#file-dropzone-scrollbar", {setHeight: 280});
            });
        </script>

    </div>
</div>
