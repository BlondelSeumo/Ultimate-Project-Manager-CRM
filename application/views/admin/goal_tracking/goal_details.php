<?php
echo message_box('success');
$comment_details = $this->db->where('goal_tracking_id', $goal_info->goal_tracking_id)->get('tbl_task_comment')->result();
$all_task_info = $this->db->where('goal_tracking_id', $goal_info->goal_tracking_id)->order_by('task_id', 'DESC')->get('tbl_task')->result();
$activities_info = $this->db->where(array('module' => 'goal_tracking', 'module_field_id' => $goal_info->goal_tracking_id))->order_by('activity_date', 'desc')->get('tbl_activities')->result();
$goal_type_info = $this->db->where('goal_type_id', $goal_info->goal_type_id)->get('tbl_goal_type')->row();

$progress = $this->items_model->get_progress($goal_info);

$can_edit = $this->items_model->can_action('tbl_goal_tracking', 'edit', array('goal_tracking_id' => $goal_info->goal_tracking_id));

$end_date = $goal_info->end_date;
$due_time = strtotime($end_date);
$current_time = strtotime(date('Y-m-d'));
if ($current_time > $due_time) {
    $text = 'text-danger';
    $ribbon = 'danger';

} else {
    $text = null;
}

if ($progress['progress'] == 100) {
    $prgs = '8ec165';
    $p_text = 'success';
    $ribbon = 'success';
    $text = null;
    $status = lang('achieved');
} elseif ($progress['progress'] >= 40 && $progress['progress'] <= 50) {
    $prgs = '5d9cec';
    $p_text = 'primary';
} elseif ($progress['progress'] >= 51 && $progress['progress'] <= 99) {
    $prgs = '7266ba';
    $p_text = 'purple';
} else {
    $prgs = 'fb6b5b';
    $p_text = 'primary';
}
$edited = can_action('69', 'edited');
?>
<div class="row">
    <div class="col-sm-3">
        <!-- Tabs within a box -->
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details"
                                                               data-toggle="tab"><?= lang('goal') . ' ' . lang('details') ?></a>
            </li>

            <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#task_comments"
                                                               data-toggle="tab"><?= lang('comments') ?> <strong
                        class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#task" data-toggle="tab"><?= lang('tasks') ?>
                    <strong
                        class="pull-right"><?= (!empty($all_task_info) ? count($all_task_info) : null) ?></strong></a>
            </li>
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
                        <div class="panel-title"><strong><?= $goal_info->subject ?> - <?= lang('details') ?> </strong>
                            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                <div class="col-sm-2 pull-right">
                                    <a href="<?php echo base_url() ?>admin/goal_tracking/index/<?= $goal_info->goal_tracking_id ?>"
                                       class="btn-xs "><i class="fa fa-edit"></i> <?= lang('edit') ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body row form-horizontal task_details">

                        <div class="ribbon <?php
                        if (!empty($ribbon)) {
                            echo $ribbon;
                        } else {
                            echo 'primary';
                        }
                        ?>"><span><?php
                                if (!empty($text)) {
                                    echo lang('failed');
                                } elseif (!empty($status)) {
                                    echo $status;
                                } else {
                                    echo lang('ongoing');
                                }
                                ?></span></div>
                        <!-- Details START -->
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('subject') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static"><?= $goal_info->subject ?></p>

                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('type') ?>:</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static"><span data-toggle="tooltip" data-placement="top"
                                                                     title="<?= $goal_type_info->description ?>"><?= lang($goal_type_info->type_name) ?></span>
                                </p>

                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('start_date') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static">
                                    <?= strftime(config_item('date_format'), strtotime($goal_info->start_date)); ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4 <?= $text ?>"><strong><?= lang('end_date') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static <?= $text ?>">
                                    <?= strftime(config_item('date_format'), strtotime($goal_info->end_date)); ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label
                                class="control-label col-sm-4 <?php if (!empty($text)) {
                                    echo $text;
                                } else {
                                    echo 'text-' . $p_text;
                                } ?>"><strong><?= lang('status') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <p class="form-control-static <?php if (!empty($text)) {
                                    echo $text;
                                } else {
                                    echo 'text-' . $p_text;
                                } ?>">
                                    <?php
                                    if (!empty($text)) {
                                        echo lang('failed');
                                    } elseif (!empty($status)) {
                                        echo $status;
                                    } else {
                                        echo lang('ongoing');
                                    }
                                    ?>
                                    <span class="pull-right">
                                        <?php
                                        if (!empty($text)) { ?>
                                            <a class="btn btn-danger"
                                               href="<?= base_url() ?>admin/goal_tracking/send_notifier/<?= $goal_info->goal_tracking_id; ?>/field"><?= lang('send_notifier') ?></a>
                                        <?php } elseif (!empty($status)) { ?>
                                            <a class="btn btn-success"
                                               href="<?= base_url() ?>admin/goal_tracking/send_notifier/<?= $goal_info->goal_tracking_id; ?>/success"><?= lang('send_notifier') ?></a>
                                        <?php } ?>

                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4"><strong><?= lang('participants') ?>
                                    :</strong></label>
                            <div class="col-sm-8 ">
                                <?php
                                if ($goal_info->permission != 'all') {
                                    $get_permission = json_decode($goal_info->permission);
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
                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                    <span data-placement="top" data-toggle="tooltip"
                                          title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/goal_tracking/update_users/<?= $goal_info->goal_tracking_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                </p>
                            <?php
                            }
                            ?>
                            </div>
                        </div>
                        <div class="form-group  col-sm-12 text-center  mt-lg">
                            <h4>
                                <small> <?= lang('completed') . ' ' . lang('achievements') ?> :</small>
                                <?= $progress['achievement'] ?>
                            </h4>
                            <small class="text-center block">
                                <?= lang('achievement') ?>:
                                <?= $goal_info->achievement ?>

                            </small>
                            <div class="text-center block mt-lg">
                                <div style="display: inline-block">
                                    <div id="easypie3" data-percent="<?= $progress['progress'] ?>"
                                         data-bar-color="#<?= $prgs ?>"
                                         class="easypie-chart">
                                        <span class="h2"><?= $progress['progress'] ?>%</span>
                                        <div class="easypie-text"><?= lang('done') ?></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-12">
                                <blockquote style="font-size: 12px; height: 100px;"><?php
                                    if (!empty($goal_info->description)) {
                                        echo $goal_info->description;
                                    }
                                    ?></blockquote>
                            </div>
                        </div>

                        <!-- Details END -->
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="task_comments" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat" id="chat-box">

                        <form id="form_validation" action="<?php echo base_url() ?>admin/goal_tracking/save_comments"
                              method="post" class="form-horizontal">
                            <input type="hidden" name="goal_tracking_id" value="<?php
                            if (!empty($goal_info->goal_tracking_id)) {
                                echo $goal_info->goal_tracking_id;
                            }
                            ?>" class="form-control">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea class="form-control textarea"
                                              placeholder="<?= $goal_info->subject . ' ' . lang('comments') ?>"
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
                                     class="img-circle"/>


                                <p class="message">

                                    <small class="text-muted pull-right"><i
                                            class="fa fa-clock-o"></i> <?= time_ago($v_comment->comment_datetime) ?>
                                        <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                                            <?= btn_delete('admin/goal_tracking/delete_comments/' . $v_comment->goal_tracking_id . '/' . $v_comment->task_comment_id) ?>
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
            <!-- Task Comments Panel Ends--->
            <!-- Start Tasks Management-->
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task" style="position: relative;">
                <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $task_active == 1 ? 'active' : ''; ?>"><a href="#manage_task"
                                                                                     data-toggle="tab"><?= lang('task') ?></a>
                            </li>
                            <li class=""><a
                                    href="<?= base_url() ?>admin/tasks/all_task/goal/<?= $goal_info->goal_tracking_id ?>"><?= lang('new_task') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_task">
                                <div class="table-responsive">
                                    <table id="table-milestones" class="table table-striped     DataTables">
                                        <thead>
                                        <tr>
                                            <th data-check-all>

                                            </th>
                                            <th><?= lang('task_name') ?></th>
                                            <th><?= lang('due_date') ?></th>
                                            <th class="col-sm-1"><?= lang('progress') ?></th>
                                            <th class="col-sm-1"><?= lang('status') ?></th>
                                            <th class="col-sm-2"><?= lang('changes/view') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                            ?>
                                            <tr>
                                                <td>
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
                                                <td><a class="text-info" style="<?php
                                                    if ($v_task->task_progress >= 100) {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                                </td>

                                                <td><?php
                                                    $due_date = $v_task->due_date;
                                                    $due_time = strtotime($due_date);
                                                    $current_time = strtotime(date('Y-m-d'));
                                                    ?>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                        <span class="label label-danger"><?= lang('overdue') ?></span>
                                                    <?php } ?></td>
                                                <td>
                                                    <div class="inline ">
                                                        <div class="easypiechart text-success" style="margin: 0px;"
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
                                                    <?php
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
                                                    <?php echo btn_view('admin/tasks/view_task_details/' . $v_task->task_id) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- End Tasks Management-->

                        </div>
                    </div>
                </div>
            </div>
            <!-- Task Comments Panel Starts --->
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="activities" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('activities') ?>
                            <?php
                            $role = $this->session->userdata('user_type');
                            if ($role == 1) {
                                ?>
                                <span class="btn-xs pull-right">
                            <a href="<?= base_url() ?>admin/tasks/claer_activities/goal_tracking/<?= $goal_info->goal_tracking_id ?>"><?= lang('clear') . ' ' . lang('activities') ?></a>
                            </span>
                            <?php } ?>
                        </h3>
                    </div>
                    <div class="panel-body " id="chat-box">
                        <div id="activity">
                            <ul class="list-group no-radius   m-t-n-xxs list-group-lg no-border">
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
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>