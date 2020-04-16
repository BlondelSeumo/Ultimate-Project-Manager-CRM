<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>
<?php
$edited = can_action('58', 'edited');
$can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $bug_details->bug_id));
$comment_details = $this->db->where(array('bug_id' => $bug_details->bug_id, 'comments_reply_id' => '0', 'task_attachment_id' => '0', 'uploaded_files_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
$activities_info = $this->db->where(array('module' => 'bugs', 'module_field_id' => $bug_details->bug_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
$all_task_info = $this->db->where('bug_id', $bug_details->bug_id)->order_by('task_id', 'DESC')->get('tbl_task')->result();
$where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $bug_details->bug_id, 'module_name' => 'bugs');
$check_existing = $this->bugs_model->check_by($where, 'tbl_pinaction');
if (!empty($check_existing)) {
    $url = 'remove_todo/' . $check_existing->pinaction_id;
    $btn = 'danger';
    $title = lang('remove_todo');
} else {
    $url = 'add_todo_list/bugs/' . $bug_details->bug_id;
    $btn = 'warning';
    $title = lang('add_todo_list');
}
?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details"
                                                               data-toggle="tab"><?= lang('details') ?></a></li>
            <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#task_comments"
                                                               data-toggle="tab"><?= lang('comments') ?><strong
                        class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_attachments"
                                                               data-toggle="tab"><?= lang('attachment') ?><strong
                        class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#tasks"
                                                               data-toggle="tab"><?= lang('tasks') ?><strong
                        class="pull-right"><?= (!empty($all_task_info) ? count($all_task_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_notes"
                                                               data-toggle="tab"><?= lang('notes') ?></a></li>
            <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#activities"
                                                               data-toggle="tab"><?= lang('activities') ?><strong
                        class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
        </ul>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php
                            if (!empty($bug_details->bug_title)) {
                                echo $bug_details->bug_title;
                            }
                            ?>
                            <div class="pull-right ml-sm " style="margin-top: -6px">
                                <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
                                   href="<?= base_url() ?>admin/projects/<?= $url ?>"
                                   class="btn btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
                            </div>
                            <span class="btn-xs pull-right">
                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                    <a href="<?= base_url() ?>admin/bugs/index/<?= $bug_details->bug_id ?>"><?= lang('edit') . ' ' . lang('bugs') ?></a>
                <?php } ?>
                    </span>
                        </h3>
                    </div>
                    <div class="panel-body row form-horizontal task_details">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('issue_#') ?> :</strong>
                                </label>
                                <p class="form-control-static"><?php if (!empty($bug_details->issue_no)) echo $bug_details->issue_no; ?></p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('bug_title') ?> :</strong>
                                </label>
                                <p class="form-control-static"><?php if (!empty($bug_details->bug_title)) echo $bug_details->bug_title; ?></p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('bug_status') ?>
                                        :</strong></label>
                                <div class="pull-left">
                                    <?php

                                    if ($bug_details->bug_status == 'unconfirmed') {
                                        $label = 'warning';
                                    } elseif ($bug_details->bug_status == 'confirmed') {
                                        $label = 'info';
                                    } elseif ($bug_details->bug_status == 'in_progress') {
                                        $label = 'primary';
                                    } elseif ($bug_details->bug_status == 'resolved') {
                                        $label = 'purple';
                                    } else {
                                        $label = 'success';
                                    }
                                    $user_info = $this->db->where('user_id', $bug_details->reporter)->get('tbl_users')->row();
                                    ?>
                                    <p class="form-control-static"><span
                                            class="label label-<?= $label ?>"><?php if (!empty($bug_details->bug_status)) echo lang($bug_details->bug_status); ?></span>
                                    </p>
                                </div>
                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                    <div class="col-sm-1 mt">
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle"
                                                    data-toggle="dropdown">
                                                <?= lang('change') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu animated zoomIn">

                                                <li>
                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/unconfirmed"><?= lang('unconfirmed') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/confirmed"><?= lang('confirmed') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/in_progress"><?= lang('in_progress') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/resolved"><?= lang('resolved') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/verified"><?= lang('verified') ?></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('reporter') ?>
                                        : </strong></label>
                                <p class="form-control-static">
                                    <?php if (!empty($bug_details->reporter)) {
                                        $users_info = $this->db->where('user_id', $bug_details->reporter)->get('tbl_account_details')->row();
                                        if ($user_info->role_id == '1') {
                                            $badge = 'danger';
                                        } elseif ($user_info->role_id == '2') {
                                            $badge = 'info';
                                        } else {
                                            $badge = 'primary';
                                        } ?>
                                        <a href="<?= base_url() ?>admin/user/user_details/<?= $user_info->user_id ?>"> <span
                                                class="badge btn-<?= $badge ?> "><?= $users_info->fullname ?></span></a>
                                    <?php } ?>
                                </p>
                            </div>

                        </div>
                        <?php
                        if (!empty($bug_details->project_id)):
                            $project_info = $this->db->where('project_id', $bug_details->project_id)->get('tbl_project')->row();
                            ?>
                            <div class="form-group  col-sm-10">
                                <label class="control-label col-sm-3 "><strong
                                        class="mr-sm"><?= lang('project_name') ?></strong></label>
                                <div class="col-sm-8 " style="margin-left: -5px;">
                                    <p class="form-control-static"><?php if (!empty($project_info->project_name)) echo $project_info->project_name; ?></p>
                                </div>
                            </div>
                        <?php endif ?>

                        <?php
                        if (!empty($bug_details->opportunities_id)):
                            $opportunity_info = $this->db->where('opportunities_id', $bug_details->opportunities_id)->get('tbl_opportunities')->row();
                            ?>
                            <div class="form-group  col-sm-10">
                                <label class="control-label col-sm-3 "><strong
                                        class="mr-sm"><?= lang('opportunity_name') ?></strong></label>
                                <div class="col-sm-8 " style="margin-left: -5px;">
                                    <p class="form-control-static"><?php if (!empty($opportunity_info->opportunity_name)) echo $opportunity_info->opportunity_name; ?></p>
                                </div>
                            </div>
                        <?php endif ?>

                        <div class="form-group col-sm-12">


                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('priority') ?>
                                        :</strong>
                                </label>
                                <?php
                                if ($bug_details->priority == 'High') {
                                    $label = 'danger';
                                } elseif ($bug_details->priority == 'Medium') {
                                    $label = 'info';
                                } else {
                                    $label = 'primary';
                                }
                                ?>
                                <p class="form-control-static">
                                    <span
                                        class="badge btn-<?= $label ?>"><?php if (!empty($bug_details->priority)) echo lang($bug_details->priority); ?></span>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('severity') ?>
                                        :</strong>
                                </label>
                                <?php
                                if ($bug_details->severity == 'must_be_fixed') {
                                    $label = 'danger';
                                } elseif ($bug_details->priority == 'major') {
                                    $label = 'warning';
                                } elseif ($bug_details->priority == 'minor') {
                                    $label = 'info';
                                } else {
                                    $label = 'primary';
                                }
                                ?>
                                <p class="form-control-static">
                                    <span
                                        class="badge btn-<?= $label ?>"><?php if (!empty($bug_details->severity)) echo lang($bug_details->severity); ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('update_on') ?>
                                        : </strong></label>
                                <p class="form-control-static">
                                    <?= strftime(config_item('date_format'), strtotime($bug_details->update_time)) . ' ' . display_time($bug_details->update_time) ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('created_date') ?> :</strong>
                                </label>

                                <p class="form-control-static">
                                    <?= strftime(config_item('date_format'), strtotime($bug_details->created_time)) . ' ' . display_time($bug_details->created_time) ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group  col-sm-12">
                            <label class="control-label col-sm-2"><strong><?= lang('participants') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">

                                <?php
                                if ($bug_details->permission != 'all') {
                                    $get_permission = json_decode($bug_details->permission);
                                    if (!empty($get_permission)) :
                                        foreach ($get_permission as $permission => $v_permission) :
                                            $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                            if ($user_info->role_id == 1) {
                                                $label = 'circle-danger';
                                            } else {
                                                $label = 'circle-success';
                                            }
                                            $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                            ?>


                                            <a href="#" data-toggle="tooltip" data-placement="top"
                                               title="<?= $profile_info->fullname ?>"><img
                                                    src="<?= base_url() . $profile_info->avatar ?>"
                                                    class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle <?= $label ?>  circle-lg"></span>
                                            </a>
                                            <?php
                                        endforeach;
                                    endif;
                                } else { ?>
                                <p class="form-control-static"><strong><?= lang('everyone') ?></strong>
                                    <i
                                        title="<?= lang('permission_for_all') ?>"
                                        class="fa fa-question-circle" data-toggle="tooltip"
                                        data-placement="top"></i>

                                    <?php
                                    }
                                    ?>
                                    <?php if (!empty($can_edit) && !empty($edited)){ ?>
                                    <span data-placement="top" data-toggle="tooltip"
                                          title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/bugs/update_users/<?= $bug_details->bug_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                </p>
                            <?php
                            }
                            ?>
                            </div>
                        </div>
                        <?php $show_custom_fields = custom_form_label(6, $bug_details->bug_id);

                        if (!empty($show_custom_fields)) {
                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                if (!empty($v_fields)) {
                                    if (count($v_fields) == 1) {
                                        $col = 'col-sm-12';
                                        $sub_col = 'col-sm-2';
                                        $style = null;
                                    } else {
                                        $col = 'col-sm-6';
                                        $sub_col = 'col-sm-5';
                                        $style = null;
                                    }

                                    ?>
                                    <div class="form-group  <?= $col ?>" style="<?= $style ?>">
                                        <label class="control-label <?= $sub_col ?>"><strong><?= $c_label ?>
                                                :</strong></label>
                                        <div class="col-sm-7 ">
                                            <p class="form-control-static">
                                                <strong><?= $v_fields ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                <?php }
                            }
                        }
                        ?>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-12">
                                <blockquote style="font-size: 12px;word-wrap: break-word;"><?php
                                    if (!empty($bug_details->bug_description)) {
                                        echo $bug_details->bug_description;
                                    }
                                    ?></blockquote>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Task Details tab Ends -->

            <?php $comment_type = 'bugs'; ?>
            <!-- Task Comments Panel Starts --->
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="task_comments"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat" id="chat-box">
                        <?php echo form_open(base_url("admin/bugs/save_comments"), array("id" => $comment_type . "-comment-form", "class" => "form-horizontal general-form", "enctype" => "multipart/form-data", "role" => "form")); ?>
                        <input type="hidden" name="bug_id" value="<?php
                        if (!empty($bug_details->bug_id)) {
                            echo $bug_details->bug_id;
                        }
                        ?>" class="form-control">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo form_textarea(array(
                                    "id" => "comment_description",
                                    "name" => "comment",
                                    "class" => "form-control comment_description",
                                    "placeholder" => $bug_details->bug_title . ' ' . lang('comments'),
                                    "data-rule-required" => true,
                                    "rows" => 4,
                                    "data-msg-required" => lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
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
                                                    <div class="progress-bar progress-bar-success" style="width:0%;"
                                                         data-dz-uploadprogress></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <button type="submit" id="file-save-button"
                                            class="btn btn-primary"><?= lang('post_comment') ?></button>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <?php echo form_close();
                        $comment_reply_type = 'bugs-reply';
                        ?>
                        <?php $this->load->view('admin/bugs/comments_list', array('comment_details' => $comment_details)) ?>
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
            </div>
            <!-- Task Comments Panel Ends--->

            <!-- Task Attachment Panel Starts --->
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_attachments">
                <div class="panel panel-custom">
                    <div class="panel-heading mb0">
                        <?php
                        $attach_list = $this->session->userdata('bugs_media_view');
                        if (empty($attach_list)) {
                            $attach_list = 'list_view';
                        }
                        ?>
                        <h3 class="panel-title"><?= lang('attach_file_list') ?>
                            <a data-toggle="tooltip" data-placement="top"
                               href="<?= base_url('admin/global_controller/download_all_attachment/bug_id/' . $bug_details->bug_id) ?>"
                               class="btn btn-default"
                               title="<?= lang('download') . ' ' . lang('all') . ' ' . lang('attachment') ?>"><i
                                    class="fa fa-cloud-download"></i></a>
                            <a data-toggle="tooltip" data-placement="top"
                               class="btn btn-default toggle-media-view <?= (!empty($attach_list) && $attach_list == 'list_view' ? 'hidden' : '') ?>"
                               data-type="list_view"
                               title="<?= lang('switch_to') . ' ' . lang('media_view') ?>"><i
                                    class="fa fa-image"></i></a>
                            <a data-toggle="tooltip" data-placement="top"
                               class="btn btn-default toggle-media-view <?= (!empty($attach_list) && $attach_list == 'media_view' ? 'hidden' : '') ?>"
                               data-type="media_view"
                               title="<?= lang('switch_to') . ' ' . lang('list_view') ?>"><i
                                    class="fa fa-list"></i></a>

                            <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                                <a href="<?= base_url() ?>admin/bugs/new_attachment/<?= $bug_details->bug_id ?>"
                                   class="text-purple text-sm" data-toggle="modal" data-placement="top"
                                   data-target="#myModal_extra_lg">
                                    <i class="fa fa-plus "></i> <?= lang('new') . ' ' . lang('attachment') ?></a>
                            </div>
                        </h3>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $(".toggle-media-view").click(function () {
                                $(".media-view-container").toggleClass('hidden');
                                $(".toggle-media-view").toggleClass('hidden');
                                $(".media-list-container").toggleClass('hidden');
                                var type = $(this).data('type');
                                var module = 'bugs';
                                $.get('<?= base_url()?>admin/global_controller/set_media_view/' + type + '/' + module, function (response) {
                                });
                            });
                        });
                    </script>
                    <?php
                    $this->load->helper('file');
                    if (empty($project_files_info)) {
                        $project_files_info = array();
                    } ?>
                    <div
                        class="p media-view-container <?= (!empty($attach_list) && $attach_list == 'media_view' ? 'hidden' : '') ?>">
                        <div class="row">
                            <?php $this->load->view('admin/bugs/attachment_list', array('project_files_info' => $project_files_info)); ?>
                        </div>
                    </div>
                    <div
                        class="media-list-container <?= (!empty($attach_list) && $attach_list == 'list_view' ? 'hidden' : '') ?>">
                        <?php
                        if (!empty($project_files_info)) {
                            foreach ($project_files_info as $key => $v_files_info) {
                                ?>
                                <div class="panel-group"
                                     id="media_list_container-<?= $files_info[$key]->task_attachment_id ?>"
                                     style="margin:8px 0px;" role="tablist"
                                     aria-multiselectable="true">
                                    <div class="box box-info" style="border-radius: 0px ">
                                        <div class="p pb-sm" role="tab" id="headingOne"
                                             style="border-bottom: 1px solid #dde6e9">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion"
                                                   href="#<?php echo $key ?>" aria-expanded="true"
                                                   aria-controls="collapseOne">
                                                    <strong
                                                        class="text-alpha-inverse"><?php echo $files_info[$key]->title; ?> </strong>
                                                    <small style="color:#ffffff " class="pull-right">
                                                        <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                            <?php echo ajax_anchor(base_url("admin/bugs/delete_bug_files/" . $files_info[$key]->task_attachment_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#media_list_container-" . $files_info[$key]->task_attachment_id)); ?>
                                                        <?php } ?></small>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="<?php echo $key ?>" class="panel-collapse collapse <?php
                                        if (!empty($in) && $files_info[$key]->files_id == $in) {
                                            echo 'in';
                                        }
                                        ?>" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="content p">
                                                <div class="table-responsive">
                                                    <table id="table-files" class="table table-striped ">
                                                        <thead>
                                                        <tr>
                                                            <th><?= lang('files') ?></th>
                                                            <th class=""><?= lang('size') ?></th>
                                                            <th><?= lang('date') ?></th>
                                                            <th><?= lang('total') . ' ' . lang('comments') ?></th>
                                                            <th><?= lang('uploaded_by') ?></th>
                                                            <th><?= lang('action') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $this->load->helper('file');

                                                        if (!empty($v_files_info)) {
                                                            foreach ($v_files_info as $v_files) {
                                                                $user_info = $this->db->where(array('user_id' => $files_info[$key]->user_id))->get('tbl_users')->row();
                                                                $total_file_comment = count($this->db->where(array('uploaded_files_id' => $v_files->uploaded_files_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result());
                                                                ?>
                                                                <tr class="file-item">
                                                                    <td data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        data-original-title="<?= $files_info[$key]->description ?>">
                                                                        <?php if ($v_files->is_image == 1) : ?>
                                                                            <div class="file-icon"><a
                                                                                    data-toggle="modal"
                                                                                    data-target="#myModal_extra_lg"
                                                                                    href="<?= base_url() ?>admin/bugs/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>">
                                                                                    <img
                                                                                        style="width: 50px;border-radius: 5px;"
                                                                                        src="<?= base_url() . $v_files->files ?>"/></a>
                                                                            </div>
                                                                        <?php else : ?>
                                                                            <div class="file-icon"><i
                                                                                    class="fa fa-file-o"></i>
                                                                                <a data-toggle="modal"
                                                                                   data-target="#myModal_extra_lg"
                                                                                   href="<?= base_url() ?>admin/bugs/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>

                                                                    <td class=""><?= $v_files->size ?>Kb</td>
                                                                    <td class="col-date"><?= date('Y-m-d' . "<br/> h:m A", strtotime($files_info[$key]->upload_time)); ?></td>
                                                                    <td class=""><?= $total_file_comment ?></td>
                                                                    <td>
                                                                        <?= $user_info->username ?>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-xs btn-dark"
                                                                           data-toggle="tooltip"
                                                                           data-placement="top"
                                                                           title="Download"
                                                                           href="<?= base_url() ?>admin/bugs/download_files/<?= $v_files->uploaded_files_id ?>"><i
                                                                                class="fa fa-download"></i></a>
                                                                    </td>

                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="5">
                                                                    <?= lang('nothing_to_display') ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 6 ? 'active' : '' ?>" id="tasks" style="position: relative;">
                <div class="nav-tabs-custom ">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#manageTasks"
                                              data-toggle="tab"><?= lang('all_task') ?></a>
                        </li>
                        <li class=""><a
                                href="<?= base_url() ?>admin/tasks/all_task/bugs/<?= $bug_details->bug_id ?>"><?= lang('new_task') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane active" id="manageTasks"
                             style="position: relative;">

                            <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                                <div class="box-body">
                                    <table class="table table-hover" id="">
                                        <thead>
                                        <tr>
                                            <th data-check-all>

                                            </th>
                                            <th class="col-sm-4"><?= lang('task_name') ?></th>
                                            <th class="col-sm-2"><?= lang('due_date') ?></th>
                                            <th class="col-sm-1"><?= lang('status') ?></th>
                                            <th class="col-sm-1"><?= lang('progress') ?></th>
                                            <th class="col-sm-3"><?= lang('changes/view') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                            ?>
                                            <tr>
                                                <td class="col-sm-1">
                                                    <div class="is_complete checkbox c-checkbox">
                                                        <label>
                                                            <input type="checkbox" data-id="<?= $v_task->task_id ?>"
                                                                   style="position: absolute;" <?php
                                                            if ($v_task->task_progress >= 100) {
                                                                echo 'checked';
                                                            }
                                                            ?>>
                                                            <span class="fa fa-check"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a style="<?php
                                                    if ($v_task->task_progress >= 100) {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                                </td>
                                                <td><?php
                                                    $due_date = $v_task->due_date;
                                                    $due_time = strtotime($due_date);
                                                    ?>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if (strtotime(date('Y-m-d')) > $due_time && $v_task->task_progress < 100) { ?>
                                                        <span class="label label-danger"><?= lang('overdue') ?></span>
                                                    <?php } ?></td>
                                                <td><?php
                                                    if ($v_task->task_status == 'completed') {
                                                        $label = 'success';
                                                    } elseif ($v_task->task_status == 'not_started') {
                                                        $label = 'info';
                                                    } elseif ($v_task->task_status == 'deferred') {
                                                        $label = 'danger';
                                                    } else {
                                                        $label = 'warning';
                                                    }
                                                    ?>
                                                    <span
                                                        class="label label-<?= $label ?>"><?= lang($v_task->task_status) ?> </span>
                                                </td>
                                                <td>
                                                    <div class="inline ">
                                                        <div class="easypiechart text-success"
                                                             style="margin: 0px;"
                                                             data-percent="<?= $v_task->task_progress ?>"
                                                             data-line-width="5" data-track-Color="#f0f0f0"
                                                             data-bar-color="#<?php
                                                             if ($v_task->task_progress == 100) {
                                                                 echo '8ec165';
                                                             } else {
                                                                 echo 'fb6b5b';
                                                             }
                                                             ?>" data-rotate="270" data-scale-Color="false"
                                                             data-size="50" data-animate="2000">
                                                            <span class="small text-muted"><?= $v_task->task_progress ?>
                                                                %</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo btn_delete('admin/tasks/delete_task/' . $v_task->task_id) ?>
                                                    <?php echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) ?>
                                                    <?php

                                                    if ($v_task->timer_status == 'on') { ?>
                                                        <a class="btn btn-xs btn-danger"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $v_task->task_id ?>"><?= lang('stop_timer') ?> </a>

                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-success"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $v_task->task_id ?>"><?= lang('start_timer') ?> </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Task Attachment Panel Ends --->
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_notes" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('notes') ?></h3>
                    </div>
                    <div class="panel-body">

                        <form action="<?= base_url() ?>admin/bugs/save_bugs_notes/<?php
                        if (!empty($bug_details)) {
                            echo $bug_details->bug_id;
                        }
                        ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                            <div class="form-group">
                                <div class="col-lg-12">
                                        <textarea class="form-control textarea"
                                                  name="notes"><?= $bug_details->notes ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <button type="submit" id="sbtn"
                                            class="btn btn-primary"><?= lang('updates') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="activities" style="position: relative;">
                <div class="tab-pane " id="activities">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('activities') ?>
                                <?php
                                $role = $this->session->userdata('user_type');
                                if ($role == 1) {
                                    ?>
                                    <span class="btn-xs pull-right">
                            <a href="<?= base_url() ?>admin/tasks/claer_activities/bugs/<?= $bug_details->bug_id ?>"><?= lang('clear') . ' ' . lang('activities') ?></a>
                            </span>
                                <?php } ?>
                            </h3>
                        </div>
                        <div class="panel-body " id="chat-box">
                            <?php
                            if (!empty($activities_info)) {
                                foreach ($activities_info as $v_activities) {
                                    $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                                    $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                                    ?>
                                    <div class="timeline-2">
                                        <div class="time-item">
                                            <div class="item-info">
                                                <small data-toggle="tooltip" data-placement="top"
                                                       title="<?= display_datetime($v_activities->activity_date) ?>"
                                                       class="text-muted"><?= time_ago($v_activities->activity_date); ?></small>

                                                <p><strong>
                                                        <?php if (!empty($profile_info)) {
                                                            ?>
                                                            <a href="<?= base_url() ?>admin/user/user_details/<?= $profile_info->user_id ?>"
                                                               class="text-info"><?= $profile_info->fullname ?></a>
                                                        <?php } ?>
                                                    </strong> <?= sprintf(lang($v_activities->activity)) ?>
                                                    <strong><?= $v_activities->value1 ?></strong>
                                                    <?php if (!empty($v_activities->value2)){ ?>
                                                <p class="m0 p0"><strong><?= $v_activities->value2 ?></strong></p>
                                                <?php } ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
