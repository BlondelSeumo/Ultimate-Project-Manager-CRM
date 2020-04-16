<?= message_box('success'); ?>
<?php
$answered = 0;
$closed = 0;
$open = 0;
$in_progress = 0;

$progress_tickets_info = get_result('tbl_tickets', array('reporter' => $this->session->userdata('user_id')));
// 30 days before
if (!empty($progress_tickets_info)):foreach ($progress_tickets_info as $v_tickets):
    if ($v_tickets->status == 'answered') {
        $answered += count($v_tickets->status);
    }
    if ($v_tickets->status == 'closed') {
        $closed += count($v_tickets->status);
    }
    if ($v_tickets->status == 'open') {
        $open += count($v_tickets->status);
    }
    if ($v_tickets->status == 'in_progress') {
        $in_progress += count($v_tickets->status);
    }
endforeach;
endif;
?>
<div class="col-sm-12 bg-white p-lg" style="margin-bottom:30px">
    <div class="col-sm-3">
        <p class="m0 lead"><?= $answered ?></p>
        <p class="m0">
            <small><a class="filter_by" id="answered"
                      href="#"> <?= lang('answered') . ' ' . lang('tickets') ?></a>
            </small>
        </p>
    </div>
    <div class="col-sm-3">
        <p class="m0 lead"><?= $in_progress ?></p>
        <p class="m0">
            <small><a class="filter_by" id="in_progress"
                      href="#"><?= lang('in_progress') . ' ' . lang('tickets') ?></a>
            </small>
        </p>
    </div>
    <div class="col-sm-3">
        <p class="m0 lead"><?= $open ?></p>
        <p class="m0">
            <small><a class="filter_by" id="open"
                      href="#"><?= lang('open') . ' ' . lang('tickets') ?></a>
            </small>
        </p>
    </div>
    <div class="col-sm-3">
        <p class="m0 lead"><?= $closed ?></p>
        <p class="m0">
            <small><a class="filter_by" id="closed"
                      href="#"><?= lang('close') . ' ' . lang('tickets') ?></a>
            </small>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                    data-toggle="tab"><?= lang('tickets') ?></a>
                </li>
                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                                    data-toggle="tab"><?= lang('new_ticket') ?></a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                    <div class="table-responsive">
                        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('ticket_code') ?></th>
                                <th><?= lang('subject') ?></th>
                                <th class="col-date"><?= lang('date') ?></th>
                                <th><?= lang('department') ?></th>
                                <th><?= lang('status') ?></th>
                                <?php $show_custom_fields = custom_form_table(7, null);
                                if (!empty($show_custom_fields)) {
                                    foreach ($show_custom_fields as $c_label => $v_fields) {
                                        if (!empty($c_label)) {
                                            ?>
                                            <th><?= $c_label ?> </th>
                                        <?php }
                                    }
                                }
                                ?>
                                <th><?= lang('action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    list = base_url + "client/tickets/ticketsList";
                                    $('.filter_by').on('click', function () {
                                        var filter_by = $(this).attr('id');
                                        if (filter_by) {
                                            filter_by = filter_by;
                                        } else {
                                            filter_by = '';
                                        }
                                        table_url(base_url + "client/tickets/ticketsList/" + filter_by);
                                    });
                                });
                            </script>

                            <?php
                            if (!empty($all_tickets_info)) {
                                foreach ($all_tickets_info as $v_tickets_info) {
                                    if ($v_tickets_info->status == 'open') {
                                        $s_label = 'danger';
                                    } elseif ($v_tickets_info->status == 'closed') {
                                        $s_label = 'success';
                                    } else {
                                        $s_label = 'default';
                                    }
                                    $profile_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_account_details')->row();
                                    $dept_info = $this->db->where(array('departments_id' => $v_tickets_info->departments_id))->get('tbl_departments')->row();
                                    if (!empty($dept_info)) {
                                        $dept_name = $dept_info->deptname;
                                    } else {
                                        $dept_name = '-';
                                    }
                                    ?>
                                    <tr>

                                        <td><span class="label label-success"><?= $v_tickets_info->ticket_code ?></span>
                                        </td>
                                        <td><a class="text-info"
                                               href="<?= base_url() ?>client/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>"><?= $v_tickets_info->subject ?></a>
                                        </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?></td>
                                        <?php if ($role == '1') { ?>

                                            <td>
                                                <a class="pull-left recect_task  ">
                                                    <?php if (!empty($profile_info)) {
                                                        ?>
                                                        <img style="width: 30px;margin-left: 18px;
                                                         height: 29px;
                                                         border: 1px solid #aaa;"
                                                             src="<?= base_url() . $profile_info->avatar ?>"
                                                             class="img-circle">
                                                    <?php } ?>

                                                    <?=
                                                    ($profile_info->fullname)
                                                    ?>
                                                </a>
                                            </td>

                                        <?php } ?>
                                        <td><?= $dept_name; ?></td>
                                        <?php
                                        if ($v_tickets_info->status == 'in_progress') {
                                            $status = 'In Progress';
                                        } else {
                                            $status = $v_tickets_info->status;
                                        }
                                        ?>
                                        <td><span class="label label-<?= $s_label ?>"><?= ucfirst($status) ?></span>
                                        </td>
                                        <td>
                                            <?= btn_view('client/tickets/index/tickets_details/' . $v_tickets_info->tickets_id) ?>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                    <form method="post" data-parsley-validate="" novalidate=""
                          action="<?= base_url() ?>client/tickets/create_tickets/<?php
                          if (!empty($tickets_info)) {
                              echo $tickets_info->tickets_id;
                          }
                          ?>" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('ticket_code') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" style="width:260px" value="<?php
                                $this->load->helper('string');
                                if (!empty($tickets_info)) {
                                    echo $tickets_info->ticket_code;
                                } else {
                                    echo strtoupper(random_string('alnum', 7));
                                }
                                ?>" name="ticket_code">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('subject') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" value="<?php
                                if (!empty($tickets_info)) {
                                    echo $tickets_info->subject;
                                }
                                ?>" class="form-control" placeholder="Sample Ticket Subject" name="subject" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('project') ?> <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="col-lg-5">
                                <div class=" ">
                                    <select class="form-control select_box" style="width:100%" required
                                            name="project_id">
                                        <?php
                                        $project = $this->db->where('client_id', $this->session->userdata('client_id'))->get('tbl_project')->result();
                                        $project_id = $this->uri->segment(6);
                                        if (!empty($project)) {
                                            foreach ($project as $v_project):
                                                ?>
                                                <option value="<?= $v_project->project_id ?>" <?php
                                                if (!empty($tickets_info) && $tickets_info->project_id == $v_project->project_id) {
                                                    echo 'selected';
                                                }
                                                if (!empty($project_id) && $project_id == $v_project->project_id) {
                                                    echo 'selected';
                                                }
                                                ?>><?= $v_project->project_name; ?></option>
                                                <?php
                                            endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('priority') ?> <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="col-lg-5">
                                <div class=" ">
                                    <select name="priority" class="form-control">
                                        <?php
                                        $priorities = $this->db->get('tbl_priority')->result();
                                        if (!empty($priorities)) {
                                            foreach ($priorities as $v_priorities):
                                                ?>
                                                <option value="<?= $v_priorities->priority ?>" <?php
                                                if (!empty($tickets_info) && $tickets_info->priority == $v_priorities->priority || config_item('default_priority') == $v_priorities->priority) {
                                                    echo 'selected';
                                                }
                                                ?>><?= ($v_priorities->priority) ?></option>
                                                <?php
                                            endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('department') ?> <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="col-lg-5">
                                <div class=" ">
                                    <select name="departments_id" required class="form-control select_box"
                                            style="width: 100%">
                                        <?php
                                        $all_departments = $this->db->get('tbl_departments')->result();
                                        if (!empty($all_departments)) {
                                            foreach ($all_departments as $v_dept):
                                                ?>
                                                <option value="<?= $v_dept->departments_id ?>" <?php
                                                if (!empty($tickets_info) && $tickets_info->departments_id == $v_dept->departments_id || config_item('default_department') == $v_dept->departments_id) {
                                                    echo 'selected';
                                                }
                                                ?>><?= $v_dept->deptname ?></option>
                                                <?php
                                            endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php
                        if (!empty($tickets_info)) {
                            $tickets_id = $tickets_info->tickets_id;
                        } else {
                            $tickets_id = null;
                        }
                        ?>
                        <?= custom_form_Fields(7, $tickets_id); ?>

                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('attachment') ?></label>

                            <div class="col-sm-5">
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
                                if (!empty($tickets_info->upload_file)) {
                                    $uploaded_file = json_decode($tickets_info->upload_file);
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
                            <label class="col-lg-3 control-label"><?= lang('ticket_message') ?> </label>
                            <div class="col-lg-7">
                        <textarea name="body" class="form-control textarea_" placeholder="<?= lang('message') ?>"><?php
                            if (!empty($tickets_info)) {
                                echo $tickets_info->body;
                            } else {
                                echo set_value('body');
                            }
                            ?></textarea>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-6">
                                <button type="submit"
                                        class="btn btn-sm btn-primary"></i> <?= lang('create_ticket') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

