<style type="text/css">
    .reply_ {
        display: none;
    }
</style>
<?php
$comment_type = 'attachment';
$uploaded_by = $this->db->where('user_id', $attachment_info->user_id)->get('tbl_account_details')->row();
if ($type == 'g') {
    $v_files_info = $this->db->where('task_attachment_id', $attachment_info->task_attachment_id)->get('tbl_task_uploaded_files')->result();
    $height = '450px';
    $title = $attachment_info->title;
    $comment_details = $this->db->where(array('task_attachment_id' => $attachment_info->task_attachment_id, 'comments_reply_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
    $description = $attachment_info->description;
} else {
    $uploaded_files_id = $this->uri->segment(6);
    $v_files_info = $this->db->where('uploaded_files_id', $uploaded_files_id)->get('tbl_task_uploaded_files')->result();
    $height = '600px';
    $title = $v_files_info[0]->file_name;
    $description = lang('upload') . ' ' . $uploaded_by->fullname;
    $comment_details = $this->db->where(array('uploaded_files_id' => $uploaded_files_id, 'comments_reply_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();

}
?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="panel-title" id="myModalLabel"><?= $title . '-' . lang('details') ?>
            <a data-toggle="tooltip" data-placement="top"
               title="<?= lang('download') . ' ' . lang('all') ?>"
               style="position: absolute;top: 0;right: 30px"
               href="<?= base_url() ?>admin/tasks/download_all_files/<?= $attachment_info->task_attachment_id ?>"
               class="pull-right"><i
                    class="fa fa-2x fa-cloud-download"></i></a>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="p">
            <div class="row">
                <div class="col-md-7 pr0">
                    <div class="box-shadow">

                        <?php
                        if (!empty($v_files_info)) {
                            foreach ($v_files_info as $l_key => $v_files) {
                                if (!empty($v_files)) {
                                    $extension = _get_file_extension($v_files->file_name);
                                    $full_ex = _mime_content_type($v_files->files);
                                    ?>
                                    <?php

                                    if ($v_files->is_image == 1) { ?>
                                        <img
                                            src="<?php echo base_url($v_files->files); ?>"
                                            class="img img-responsive">
                                    <?php } else if ($extension == 'pdf') { ?>

                                        <iframe
                                            src="<?php echo base_url($v_files->files); ?>"
                                            height="<?= $height ?>" width="100%" frameborder="0"></iframe>
                                    <?php } else if (is_html5_video($v_files->files)) {
                                        ?>
                                        <video width="100%" height="100%" type="<?= $full_ex ?>"
                                               src="<?php echo site_url($v_files->files); ?>"
                                               controls>
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php } else { ?>
                                        <div class="no-preview">
                                            <span><p><?= lang('no_preview_available') ?></p>
                                            <strong><a style="color: #fff"
                                                       href="<?= base_url() ?>admin/tasks/download_files/<?= $v_files->uploaded_files_id ?>"> <?= $v_files->file_name ?></a></strong>
                                                </span>

                                            <a href="<?= base_url() ?>admin/tasks/download_files/<?= $v_files->uploaded_files_id ?>"
                                               class="pull-right"><i
                                                    class="fa fa-5x fa-cloud-download"></i></a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <hr/>
                            <?php }
                        } ?>
                    </div>
                </div>
                <div class="col-md-5 pr0">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('comments') ?></h3>
                        </div>
                        <div class="panel-body chat" id="chat-box">
                            <?php echo form_open(base_url("admin/tasks/save_attachment_comments"), array("id" => $comment_type . "-comment-form", "class" => "form-horizontal", "enctype" => "multipart/form-data", "role" => "form")); ?>
                            <?php if ($type == 'g') { ?>
                                <input type="hidden" name="task_attachment_id" value="<?php
                                if (!empty($attachment_info->task_attachment_id)) {
                                    echo $attachment_info->task_attachment_id;
                                }
                                ?>" class="form-control">
                            <?php } else { ?>
                                <input type="hidden" name="uploaded_files_id" value="<?php
                                if (!empty($uploaded_files_id)) {
                                    echo $uploaded_files_id;
                                }
                                ?>" class="form-control">
                            <?php } ?>
                            <input type="hidden" name="task_id" value="<?php
                            if (!empty($attachment_info)) {
                                echo $attachment_info->task_id;
                            }
                            ?>" class="form-control">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php
                                    echo form_textarea(array(
                                        "id" => "comment_description",
                                        "name" => "description",
                                        "class" => "form-control comment_description",
                                        "placeholder" => $title . ' ' . lang('comments'),
                                        "data-rule-required" => true,
                                        "rows" => 4,
                                        "data-msg-required" => lang("field_required"),
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group mb0">
                                <div class="col-sm-12">
                                    <div id="attachemnt_comments-dropzone" class="dropzone mb15">

                                    </div>
                                    <div id="attachemnt_comments-dropzone-scrollbar">
                                        <div id="attachemnt_comments-previews">
                                            <div id="attachemnt_file-upload-row" class="mt pull-left">
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
                                </div>
                            </div>
                            <div class="form-group mt-lg">
                                <div class="col-sm-12">
                                    <div class="pull-right">
                                        <button type="submit" id="file-save-button"
                                                class="btn btn-primary"><?= lang('post_comment') ?></button>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <?php echo form_close();
                            $comment_reply_type = 'tasks-reply';
                            ?>
                            <?php $this->load->view('admin/tasks/comments_list', array('comment_details' => $comment_details)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#file-save-button').on('click', function (e) {
            var ubtn = $(this);
            ubtn.html('Please wait...');
            ubtn.addClass('disabled');
        });
        $("#<?php echo $comment_type; ?>-comment-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                $(".comment_description").val("");
                $(".dz-complete").remove();
                $('#file-save-button').removeClass("disabled").html('<?= lang('post_comment')?>');
                $(result.data).insertAfter("#<?php echo $comment_type; ?>-comment-form");
                toastr[result.status](result.message);
            }
        });
        fileSerial = 0;
        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#attachemnt_file-upload-row");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);
        Dropzone.autoDiscover = false;
        var projectFilesDropzone = new Dropzone("#attachemnt_comments-dropzone", {
            url: "<?= base_url()?>admin/global_controller/upload_file",
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
            autoQueue: true,
            previewsContainer: "#attachemnt_comments-previews",
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

                $("#attachemnt_comments-dropzone").hide();

                $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                    var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                        + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                        + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                        + "</div>";
                    $("#attachemnt_comments-previews").prepend(newFileRow);
                });
                $("#add-more-file-button").trigger("click");
                $("#attachemnt_comments-previews").on("click", ".remove-file", function () {
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