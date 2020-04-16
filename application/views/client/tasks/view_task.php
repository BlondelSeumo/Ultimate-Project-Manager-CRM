<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }

    a:hover {
        text-decoration: none;
    }
</style>
<?php
if (!empty($task_details->project_id)) {
    $project_info = $this->db->where('project_id', $task_details->project_id)->get('tbl_project')->row();
    $project_settings = json_decode($project_info->project_settings);
}
// get all comments by tasks id
$comment_details = $this->db->where('task_id', $task_details->task_id)->get('tbl_task_comment')->result();
// get all $total_timer by tasks id
$activities_info = $this->db->where(array('module' => 'tasks', 'module_field_id' => $task_details->task_id))->order_by('activity_date', 'desc')->get('tbl_activities')->result();
?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <!-- Tabs within a box -->
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details"
                                                               data-toggle="tab"><?= lang('tasks') . ' ' . lang('details') ?></a>
            </li>
            <?php if (!empty($project_settings[11]) && $project_settings[11] == 'comment_on_project_tasks') { ?>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#task_comments"
                                                                   data-toggle="tab"><?= lang('comments') ?> <strong
                            class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
                </li>
            <?php } ?>
            <?php if (!empty($project_settings[12]) && $project_settings[12] == 'show_project_tasks_attachments') { ?>
                <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_attachments"
                                                                   data-toggle="tab"><?= lang('attachment') ?>
                        <strong
                            class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong></a>
                </li>
            <?php } ?>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#activities"
                                                               data-toggle="tab"><?= lang('activities') ?><strong
                        class="pull-right"></strong><strong
                        class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php if (!empty($task_details->task_name)) echo $task_details->task_name; ?>


                        </h3>
                    </div>
                    <div class="panel-body row form-horizontal task_details">

                        <div class="form-group col-sm-6">
                            <label class="control-label col-sm-5"><strong><?= lang('task_status') ?>
                                    :</strong></label>
                            <div class="pull-left mt">
                                <?php
                                if ($task_details->task_status == 'completed') {
                                    $label = 'success';
                                } elseif ($task_details->task_status == 'not_started') {
                                    $label = 'info';
                                } elseif ($task_details->task_status == 'deferred') {
                                    $label = 'danger';
                                } else {
                                    $label = 'warning';
                                }
                                ?>
                                <p class="form-control-static label label-<?= $label ?>  "><?= lang($task_details->task_status) ?></p>
                            </div>

                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('timer_status') ?>:</strong></label>
                            <div class="col-sm-8 mt">
                                <?php if ($task_details->timer_status == 'on') { ?>

                                    <span class="label label-success"><?= lang('on') ?></span>

                                <?php } else {
                                    ?>
                                    <span class="label label-danger"><?= lang('off') ?></span>

                                <?php }
                                ?>
                            </div>
                        </div>
                        <?php
                        if (!empty($task_details->project_id)):
                            $project_info = $this->db->where('project_id', $task_details->project_id)->get('tbl_project')->row();
                            $project_settings = json_decode($project_info->project_settings);

                            $milestones_info = $this->db->where('milestones_id', $task_details->milestones_id)->get('tbl_milestones')->row();
                            ?>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('project_name') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static"><?php if (!empty($project_info->project_name)) echo $project_info->project_name; ?></p>
                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('milestone') ?>
                                        :</strong></label>
                                <div class="col-sm-8 ">
                                    <p class="form-control-static"><?php if (!empty($milestones_info->milestone_name)) echo $milestones_info->milestone_name; ?></p>
                                </div>
                            </div>
                        <?php endif ?>

                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-5"><strong><?= lang('start_date') ?>
                                    :</strong></label>
                            <div class="col-sm-7 ">
                                <p class="form-control-static"><?php
                                    if (!empty($task_details->task_start_date)) {
                                        echo strftime(config_item('date_format'), strtotime($task_details->task_start_date));
                                    }
                                    ?></p>
                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <?php
                            $due_date = $task_details->due_date;
                            $due_time = strtotime($due_date);
                            $current_time = strtotime(date('Y-m-d'));
                            if ($current_time > $due_time) {
                                $text = 'text-danger';
                            } else {
                                $text = null;
                            }
                            ?>

                            <label class="control-label col-sm-4"><strong
                                    class="<?= $text ?>"><?= lang('due_date') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static"><?php
                                    if (!empty($task_details->due_date)) {
                                        echo strftime(config_item('date_format'), strtotime($task_details->due_date));
                                    }
                                    ?></p>

                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-5"><strong><?= lang('created_by') ?>
                                    :</strong></label>
                            <div class="col-sm-7 ">
                                <p class="form-control-static"><?php
                                    if (!empty($task_details->created_by)) {
                                        echo $this->db->where('user_id', $task_details->created_by)->get('tbl_account_details')->row()->fullname;
                                    }
                                    ?></p>

                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('created_date') ?>:</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static"><?php
                                    if (!empty($task_details->due_date)) {
                                        echo strftime(config_item('date_format'), strtotime($task_details->task_created_date)) . ' ' . display_time($task_details->task_created_date);
                                    }
                                    ?></p>

                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-5"><strong><?= lang('estimated_hour') ?>
                                    :</strong></label>
                            <div class="col-sm-7 ">
                                <p class="form-control-static">
                                    <strong><?php if (!empty($task_details->task_hour)) echo $task_details->task_hour; ?> <?= lang('hours') ?></strong>
                                </p>
                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('participants') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <?php
                                if ($task_details->permission != 'all') {
                                    $get_permission = json_decode($task_details->permission);
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

                            </div>
                        </div>

                        <div class="form-group  col-sm-10">
                            <label class="control-label col-sm-3 "><strong class="mr-sm"><?= lang('completed') ?>
                                    :</strong></label>
                            <div class="col-sm-9 " style="margin-left: -5px;">
                                <?php
                                if ($task_details->task_progress < 49) {
                                    $progress = 'progress-bar-danger';
                                } elseif ($task_details->task_progress > 50 && $task_details->task_progress < 99) {
                                    $progress = 'progress-bar-primary';
                                } else {
                                    $progress = 'progress-bar-success';
                                }
                                ?>
                                <span class="">
                                <div class="mt progress progress-striped ">
                                    <div class="progress-bar <?= $progress ?> " data-toggle="tooltip"
                                         data-original-title="<?= $task_details->task_progress ?>%"
                                         style="width: <?= $task_details->task_progress ?>%"></div>
                                </div>
                                </span>
                            </div>
                        </div>
                        <?php if (!empty($project_settings[11]) && $project_settings[11] == 'show_tasks_hours') { ?>
                            <div class="form-group col-sm-12">
                                <?= $this->tasks_model->get_time_spent_result($this->tasks_model->task_spent_time_by_id($task_details->task_id)) ?>
                            </div>
                        <?php } ?>
                        <div class="col-sm-12">
                            <blockquote
                                style="font-size: 12px; margin-top: 5px"><?php if (!empty($task_details->task_description)) echo $task_details->task_description; ?></blockquote>
                        </div>

                    </div>
                </div>

            </div>
            <!-- Task Details tab Ends -->

            <!-- Task Comments Panel Starts --->
            <?php if (!empty($project_settings[11]) && $project_settings[11] == 'comment_on_project_tasks') { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="task_comments"
                     style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('comments') ?></h3>
                        </div>
                        <div class="panel-body chat" id="chat-box">
                            <form id="form_validation"
                                  action="<?php echo base_url() ?>client/tasks/save_comments" method="post"
                                  class="form-horizontal">
                                <input type="hidden" name="task_id" value="<?php
                                if (!empty($task_details->task_id)) {
                                    echo $task_details->task_id;
                                }
                                ?>" class="form-control">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                                <textarea class="form-control textarea"
                                                          placeholder="<?= $task_details->task_name . ' ' . lang('comments') ?>"
                                                          name="comment" style="height: 70px;"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="pull-right">
                                            <button type="submit" id="sbtn"
                                                    class="btn btn-primary"><?= lang('post_comment') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr/>

                            <?php

                            if (!empty($comment_details)):foreach ($comment_details as $key => $v_comment):
                                $user_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_users')->row();
                                $profile_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_account_details')->row();
                                if ($user_info->role_id == 1) {
                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">' . lang('admin') . '</small>';
                                } elseif ($user_info->role_id == 3) {
                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-primary">' . lang('staff') . '</small>';
                                } else {
                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-success">' . lang('client') . '</small>';
                                }
                                ?>

                                <div class="col-sm-12 item ">

                                    <img src="<?php echo base_url() . $profile_info->avatar ?>" alt="user image"
                                         class="img-xs img-circle"/>


                                    <p class="message">
                                        <small class="text-muted pull-right"><i
                                                class="fa fa-clock-o"></i> <?= time_ago($v_comment->comment_datetime) ?>
                                            <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                                                <?= btn_delete('client/tasks/delete_task_comments/' . $v_comment->task_id . '/' . $v_comment->task_comment_id) ?>
                                            <?php } ?></small>
                                        <a href="#" class="name">
                                            <?= ($profile_info->fullname) . ' ' . $label ?>
                                        </a>

                                        <?php if (!empty($v_comment->comment)) echo $v_comment->comment; ?>
                                    </p>

                                </div><!-- /.item -->
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- Task Comments Panel Ends--->
            <!-- Task Attachment Panel Starts --->
            <?php if (!empty($project_settings[12]) && $project_settings[12] == 'show_project_tasks_attachments') { ?>
                <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_attachments"
                     style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('attachment') ?></h3>
                        </div>
                        <div class="panel-body">

                            <form action="<?= base_url() ?>client/tasks/save_task_attachment/<?php
                            if (!empty($add_files_info)) {
                                echo $add_files_info->task_attachment_id;
                            }
                            ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
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
                                <?php if (empty($add_files_info)) { ?>
                                    <div id="add_new">
                                        <div class="form-group" style="margin-bottom: 0px">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('upload_file') ?></label>
                                            <div class="col-sm-6">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <?php if (!empty($project_files)):foreach ($project_files as $v_files_image): ?>
                                                        <span class=" btn btn-default btn-file"><span
                                                                class="fileinput-new"
                                                                style="display: none">Select file</span>
                                                                <span class="fileinput-exists"
                                                                      style="display: block"><?= lang('change') ?></span>
                                                                <input type="hidden" name="task_files[]"
                                                                       value="<?php echo $v_files_image->files ?>">
                                                                <input type="file" name="task_files[]">
                                                            </span>
                                                        <span
                                                            class="fileinput-filename"> <?php echo $v_files_image->file_name ?></span>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="btn btn-default btn-file"><span
                                                                class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="task_files[]">
                                                        </span>
                                                        <span class="fileinput-filename"></span>
                                                        <a href="#" class="close fileinput-exists"
                                                           data-dismiss="fileinput"
                                                           style="float: none;">&times;</a>
                                                    <?php endif; ?>
                                                </div>
                                                <div id="msg_pdf" style="color: #e11221"></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <strong><a href="javascript:void(0);" id="add_more"
                                                           class="addCF "><i
                                                            class="fa fa-plus"></i>&nbsp;<?= lang('add_more') ?>
                                                    </a></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <br/>
                                <input type="hidden" name="task_id" value="<?php
                                if (!empty($task_details->task_id)) {
                                    echo $task_details->task_id;
                                }
                                ?>" class="form-control">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit"
                                                class="btn btn-primary"><?= lang('upload_file') ?></button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <?php
                    if (!empty($project_files_info)) {
                        ?>
                        <div class="panel">
                            <div class="panel-heading" style="border-bottom: 2px solid #00BCD4">
                                <strong><?= lang('attach_file_list') ?></strong></div>
                            <div class="panel-body">
                                <?php
                                $this->load->helper('file');
                                foreach ($project_files_info as $key => $v_files_info) {
                                    ?>
                                    <div class="panel-group" id="accordion" style="margin:8px 0px"
                                         role="tablist" aria-multiselectable="true">
                                        <div class="box box-info" style="border-radius: 0px ">
                                            <div class="panel-heading pl-sm" role="tab" id="headingOne" style="">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion"
                                                       href="#<?php echo $key ?>" aria-expanded="true"
                                                       aria-controls="collapseOne">
                                                        <strong
                                                            style="text-decoration: underline"><?php echo $files_info[$key]->title; ?> </strong>
                                                        <small style="color:#ffffff " class="pull-right">
                                                            <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                                <?= btn_delete('client/tasks/delete_task_files/' . $files_info[$key]->task_id . '/' . $files_info[$key]->task_attachment_id) ?>
                                                            <?php } ?></small>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="<?php echo $key ?>" class="panel-collapse collapse <?php
                                            if (!empty($in) && $files_info[$key]->files_id == $in) {
                                                echo 'in';
                                            }
                                            ?>" role="tabpanel" aria-labelledby="headingOne">
                                                <div class="content">
                                                    <div class="table-responsive">
                                                        <table id="table-files" class="table table-striped ">
                                                            <thead>
                                                            <tr>
                                                                <th width="45%"><?= lang('files') ?></th>
                                                                <th class=""><?= lang('size') ?></th>
                                                                <th><?= lang('date') ?></th>
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
                                                                    ?>
                                                                    <tr class="file-item">
                                                                        <td>
                                                                            <?php if ($v_files->is_image == 1) : ?>
                                                                                <div class="file-icon"><a
                                                                                        href="<?= base_url() ?>client/tasks/download_files/<?= $files_info[$key]->task_id ?>/<?= $v_files->uploaded_files_id ?>">
                                                                                        <img
                                                                                            style="width: 50px;border-radius: 5px;"
                                                                                            src="<?= base_url() . $v_files->files ?>"/></a>
                                                                                </div>
                                                                            <?php else : ?>
                                                                                <div class="file-icon"><i
                                                                                        class="fa fa-file-o"></i>
                                                                                    <a href="<?= base_url() ?>client/tasks/download_files/<?= $files_info[$key]->task_id ?>/<?= $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
                                                                                </div>
                                                                            <?php endif; ?>

                                                                            <a data-toggle="tooltip"
                                                                               data-placement="top"
                                                                               data-original-title="<?= $files_info[$key]->description ?>"
                                                                               class="text-info"
                                                                               href="<?= base_url() ?>client/tasks/download_files/<?= $files_info[$key]->task_id ?>/<?= $v_files->uploaded_files_id ?>">
                                                                                <?= $files_info[$key]->title ?>
                                                                                <?php if ($v_files->is_image == 1) : ?>
                                                                                    <em><?= $v_files->image_width . "x" . $v_files->image_height ?></em>
                                                                                <?php endif; ?>
                                                                            </a>
                                                                            <p class="file-text"><?= $files_info[$key]->description ?></p>
                                                                        </td>
                                                                        <td class=""><?= $v_files->size ?>Kb
                                                                        </td>
                                                                        <td class="col-date"><?= date('Y-m-d' . "<br/> h:m A", strtotime($files_info[$key]->upload_time)); ?></td>
                                                                        <td>
                                                                            <?= $user_info->username ?>
                                                                        </td>
                                                                        <td>
                                                                            <a class="btn btn-xs btn-dark"
                                                                               data-toggle="tooltip"
                                                                               data-placement="top"
                                                                               title="Download"
                                                                               href="<?= base_url() ?>client/tasks/download_files/<?= $files_info[$key]->task_id ?>/<?= $v_files->uploaded_files_id ?>"><i
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
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <!-- Task Attachment Panel Ends --->

            <div class="tab-pane " id="activities">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('activities') ?>

                        </h3>
                    </div>
                    <div class="panel-body " id="chat-box">
                        <div id="activity">
                            <ul class="list-group no-radius   mt-xs list-group-lg no-border">
                                <?php
                                if (!empty($activities_info)) {
                                    foreach ($activities_info as $v_activities) {
                                        $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();

                                        $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                                        ?>
                                        <div class="timeline-2">
                                            <div class="time-item">
                                                <div class="item-info">
                                                    <small data-toggle="tooltip" data-placement="top" title="<?= display_datetime($v_activities->activity_date)?>"
                                                           class="text-muted"><?= time_ago($v_activities->activity_date); ?></small>

                                                    <p><strong>
                                                            <?php if (!empty($profile_info)) {
                                                                ?>
                                                                <a href="#"
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
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4) {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('upload_file') ?></label>\n\
        <div class="col-sm-5">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="task_files[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
    });
</script>