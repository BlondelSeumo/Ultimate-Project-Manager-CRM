<?php
$created = can_action('143', 'created');
$edited = can_action('143', 'edited');
$deleted = can_action('143', 'deleted');
if (!empty($created) || !empty($edited)) {
    ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="panel-title"><?= lang('new') . ' ' . lang('articles') ?>
                <?php
                if (!empty($articles_info->kb_id)) { ?>
                    <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                        <a target="_blank"
                           href="<?= base_url() ?>admin/knowledgebase/details/articles/<?= $articles_info->kb_id ?>"
                           class="btn btn-xs btn-info">
                            <?= ' ' . lang('view_details') ?>
                        </a>
                        <a href="<?= base_url() ?>admin/knowledgebase/new_articles" class="btn btn-xs btn-primary">
                            <?= ' ' . lang('new') . ' ' . lang('articles') ?>
                        </a>
                        <?php if (!empty($deleted)) { ?>
                            <a href="<?= base_url() ?>admin/knowledgebase/delete_articles/<?= $articles_info->kb_id ?>/1"
                               class="btn btn-xs btn-danger">
                                <?= ' ' . lang('delete') . ' ' . lang('articles') ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="modal-body wrap-modal wrap">
            <form id="form_validation" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                  action="<?php echo base_url() ?>admin/knowledgebase/saved_articles/<?php
                  if (!empty($articles_info->kb_id)) {
                      echo $articles_info->kb_id;
                  }
                  ?>" method="post" class="form-horizontal">

                <div class="form-group">
                    <label class="col-sm-2 control-label"><?= lang('title') ?> <span
                            class="required">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="title" required class="form-control gen_slug" id="title" value="<?php
                        if (!empty($articles_info->title)) {
                            echo $articles_info->title;
                        }
                        ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?= lang('slug') ?> <span
                            class="required">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="slug" required class="form-control" id="slug" value="<?php
                        if (!empty($articles_info->slug)) {
                            echo $articles_info->slug;
                        }
                        ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('categories') ?> <span
                            class="text-danger">*</span>
                    </label>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <select class="form-control select_box" style="width: 100%" name="kb_category_id"
                                    required>
                                <option value=""><?= lang('select') . ' ' . lang('categories') ?></option>
                                <?php
                                $kb_category_info = get_result('tbl_kb_category', array('status' => 1));
                                if (!empty($kb_category_info)) {
                                    $kb_category_info = array_reverse($kb_category_info);
                                    foreach ($kb_category_info as $v_kb_category) {
                                        ?>
                                        <option value="<?= $v_kb_category->kb_category_id ?>"
                                            <?php
                                            if (!empty($articles_info)) {
                                                echo $articles_info->kb_category_id == $v_kb_category->kb_category_id ? 'selected' : '';
                                            }
                                            ?>
                                        ><?= $v_kb_category->category ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="input-group-addon"
                                 title="<?= lang('new') . ' ' . lang('categories') ?>"
                                 data-toggle="tooltip" data-placement="top">
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/knowledgebase/new_categories/inline"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('internal_view') ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox-inline c-checkbox">
                            <label>
                                <input <?= (!empty($articles_info->for_all) && $articles_info->for_all == 'Yes' ? 'checked' : ''); ?>
                                    type="checkbox" name="for_all" value="Yes">
                                <span class="fa fa-check"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('status') ?></label>
                    <div class="col-sm-9">
                        <div class="col-sm-4 row">
                            <div class="checkbox-inline c-checkbox">
                                <label>
                                    <input <?= (!empty($articles_info->status) && $articles_info->status == '1' || empty($articles_info) ? 'checked' : ''); ?>
                                        class="select_one" type="checkbox" name="status" value="1">
                                    <span class="fa fa-check"></span> <?= lang('active') ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="checkbox-inline c-checkbox">
                                <label>
                                    <input <?= (!empty($articles_info->status) && $articles_info->status == '2' ? 'checked' : ''); ?>
                                        class="select_one" type="checkbox" name="status" value="2">
                                    <span class="fa fa-check"></span> <?= lang('inactive') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><?= lang('description') ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control textarea_lg"
                                  name="description"><?php if (!empty($articles_info->description)) {
                                echo $articles_info->description;
                            } ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="field-1"
                           class="col-sm-2 control-label"><?= lang('attachment') ?></label>

                    <div class="col-sm-9">
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
                        if (!empty($articles_info->attachments)) {
                            $uploaded_file = json_decode($articles_info->attachments);
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
                            $(document).ready(function () {
                                function getSlug(title) {
                                    var slug_url = '<?= base_url('admin/knowledgebase/getSlug')?>';
                                    $.get(slug_url, {title: title}, function (slug) {
                                        $('#slug').val(slug).change();
                                    });
                                }

                                $('.gen_slug').change(function (e) {
                                    getSlug($(this).val());
                                });


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
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-3">
                        <button type="submit" id="file-save-button"
                                class="btn btn-primary btn-block"><?= lang('save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
