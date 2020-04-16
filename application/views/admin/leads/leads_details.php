<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>
<?php
$where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $leads_details->leads_id, 'module_name' => 'leads');
$check_existing = $this->items_model->check_by($where, 'tbl_pinaction');
if (!empty($check_existing)) {
    $url = 'remove_todo/' . $check_existing->pinaction_id;
    $btn = 'danger';
    $title = lang('remove_todo');
} else {
    $url = 'add_todo_list/leads/' . $leads_details->leads_id;
    $btn = 'warning';
    $title = lang('add_todo_list');
}

$can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $leads_details->leads_id));
$can_delete = $this->items_model->can_action('tbl_leads', 'delete', array('leads_id' => $leads_details->leads_id));
$all_calls_info = $this->db->where('leads_id', $leads_details->leads_id)->get('tbl_calls')->result();
$all_meetings_info = $this->db->where('leads_id', $leads_details->leads_id)->get('tbl_mettings')->result();

$comment_details = $this->db->where(array('leads_id' => $leads_details->leads_id, 'comments_reply_id' => '0', 'task_attachment_id' => '0', 'uploaded_files_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
$all_task_info = $this->db->where('leads_id', $leads_details->leads_id)->order_by('leads_id', 'DESC')->get('tbl_task')->result();
$activities_info = $this->db->where(array('module' => 'leads', 'module_field_id' => $leads_details->leads_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
$all_proposals_info = $this->db->where(array('module' => 'leads', 'module_id' => $leads_details->leads_id))->order_by('proposals_id', 'DESC')->get('tbl_proposals')->result();
$edited = can_action('55', 'edited');
$deleted = can_action('55', 'deleted');
?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <?php
        if ($leads_details->converted_client_id == 0) {
            if (!empty($can_edit) && !empty($edited)) { ?>
                <a href="<?= base_url() ?>admin/leads/index/<?= $leads_details->leads_id ?>"
                   class="btn-xs btn btn-primary"
                   title=""
                   data-toggle="tooltip" data-placement="top" data-original-title="Edit"><i
                            class="fa fa-pencil-square-o"></i></a>
            <?php } ?>

            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                <a data-toggle="modal" data-target="#myModal"
                   onclick="return confirm('Are you sure to <?= lang('convert') ?> This <?= $leads_details->lead_name ?> ?')"
                   href="<?= base_url() ?>admin/leads/convert/<?= $leads_details->leads_id ?>"
                   class="btn-xs btn btn-purple pull-right"><i
                            class="fa fa-copy"></i> <?= lang('convert_to_client') ?></a>
                <?php
            }
        }
        $notified_reminder = count($this->db->where(array('module' => 'leads', 'module_id' => $leads_details->leads_id, 'notified' => 'No'))->get('tbl_reminders')->result());
        ?>
        <!-- Tabs within a box -->
        <ul class="<?php
        if ($leads_details->converted_client_id == 0) {
            echo 'mt';
        } ?> nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details"
                                                               data-toggle="tab"><?= lang('leads_details') ?></a>
            </li>
            <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#call"
                                                               data-toggle="tab"><?= lang('call') ?>
                    <strong
                            class="pull-right"><?= (!empty($all_calls_info) ? count($all_calls_info) : null) ?></strong>
                </a></li>
            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#mettings"
                                                               data-toggle="tab"><?= lang('mettings') ?>
                    <strong
                            class="pull-right"><?= (!empty($all_meetings_info) ? count($all_meetings_info) : null) ?></strong>
                </a></li>
            <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_comments"
                                                               data-toggle="tab"><?= lang('comments') ?>
                    <strong
                            class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong>
                </a></li>
            <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#task_attachments"
                                                               data-toggle="tab"><?= lang('attachment') ?><strong
                            class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong>
                </a></li>
            <li class="<?= $active == 8 ? 'active' : '' ?>"><a href="#tasks"
                                                               data-toggle="tab"><?= lang('tasks') ?>
                    <strong
                            class="pull-right"><?= (!empty($all_task_info) ? count($all_task_info) : null) ?></strong>
                </a></li>
            <li class="<?= $active == 8 ? 'active' : '' ?>"><a href="#proposals"
                                                               data-toggle="tab"><?= lang('proposals') ?>
                    <strong
                            class="pull-right"><?= (!empty($all_proposals_info) ? count($all_proposals_info) : null) ?></strong></a>
            </li>
            <li class="<?= $url == 'reminder' ? 'active' : '' ?>"><a href="#reminder" data-toggle="tab"
                                                                     aria-expanded="false"><?= lang('reminder') ?>
                    <strong
                            class="pull-right"><?= (!empty($notified_reminder) ? $notified_reminder : null) ?></strong>
                </a>
            </li>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#activities"
                                                               data-toggle="tab"><?= lang('activities') ?><strong
                            class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9">

        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->

            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php
                            if (!empty($leads_details->lead_name)) {
                                echo $leads_details->lead_name;
                            }
                            ?>
                            <div class="pull-right ml-sm " style="margin-top: -6px">
                                <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
                                   href="<?= base_url() ?>admin/projects/<?= $url ?>"
                                   class="btn-xs btn btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
                            </div>
                            <span class="btn-xs pull-right">
<?php
if ($leads_details->converted_client_id == 0) {
    if (!empty($can_edit) && !empty($edited)) { ?>
        <a href="<?= base_url() ?>admin/leads/index/<?= $leads_details->leads_id ?>"><?= lang('edit') . ' ' . lang('leads') ?></a>
    <?php }
} else {
    $c_edited = can_action('4', 'edited');
    if (!empty($c_edited)) {
        ?>
        <a href="<?php echo base_url() ?>admin/client/manage_client/<?= $leads_details->converted_client_id ?>"
           class="btn-xs pull-right"><i class="fa fa-edit"></i> <?= lang('edit') . ' ' . lang('client') ?></a>
    <?php }
} ?>
                    </span>
                        </h3>
                    </div>
                    <div class="panel-body row form-horizontal task_details">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_name') ?> :</strong>
                                </label>
                                <p class="form-control-static"><?php
                                    if (!empty($leads_details->lead_name)) {
                                        echo $leads_details->lead_name;
                                    }
                                    ?></p>

                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_source') ?> :</strong>
                                </label>
                                <?php
                                if (!empty($leads_details->lead_source_id)) {
                                    $lead_source = $this->db->where('lead_source_id', $leads_details->lead_source_id)->get('tbl_lead_source')->row();
                                    if (!empty($lead_source->lead_source)) {
                                        ?>
                                        <div class="mt">
                                            <p class="label label-info form-control-static"><?php echo $lead_source->lead_source; ?></p>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>

                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('contact_name') ?>
                                        :</strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($leads_details->contact_name)) {
                                        echo $leads_details->contact_name;
                                    }
                                    ?></p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('organization') ?> :</strong>
                                </label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->organization)) {
                                        echo $leads_details->organization;
                                    }
                                    ?></p>
                            </div>


                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_status') ?>
                                        :</strong></label>
                                <div class="pull-left">
                                    <?php
                                    if (!empty($leads_details->lead_status_id)) {
                                        $lead_status = $this->db->where('lead_status_id', $leads_details->lead_status_id)->get('tbl_lead_status')->row();

                                        if ($lead_status->lead_type == 'open') {
                                            $status = "<span class='label label-success'>" . lang($lead_status->lead_type) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($lead_status->lead_type) . "</span>";
                                        } ?>
                                        <p class="form-control-static"><?= $status . ' ' . $lead_status->lead_status ?></p>
                                    <?php }
                                    ?>
                                </div>
                                <?php
                                if ($leads_details->converted_client_id == 0) {
                                    if (!empty($can_edit) && !empty($edited)) {
                                        ?>
                                        <div class="col-sm-1 pull-right mt">
                                            <div class="btn-group">
                                                <button class="btn btn-xs btn-success dropdown-toggle"
                                                        data-toggle="dropdown">
                                                    <?= lang('change') ?>
                                                    <span class="caret"></span></button>
                                                <ul class="dropdown-menu animated zoomIn">
                                                    <?php
                                                    $status_info = $this->db->get('tbl_lead_status')->result();
                                                    if (!empty($status_info)) {
                                                        foreach ($status_info as $v_status) {
                                                            ?>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/leads/change_status/<?= $leads_details->leads_id ?>/<?= $v_status->lead_status_id ?>"><?= lang($v_status->lead_type) . '-' . $v_status->lead_status ?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php }
                                }
                                ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('email') ?> : </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->email)) {
                                        echo $leads_details->email;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('phone') ?> : </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->phone)) {
                                        echo $leads_details->phone;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('mobile') ?> :</strong> </label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->mobile)) {
                                        echo $leads_details->mobile;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('address') ?> :</strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->address)) {
                                        echo $leads_details->address;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('city') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->city)) {
                                        echo $leads_details->city;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('country') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->country)) {
                                        echo $leads_details->country;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('state') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($leads_details->state)) {
                                        echo $leads_details->state;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label
                                        class="control-label col-sm-5"><strong><?= lang('facebook_profile_link') ?>
                                        : </strong></label>
                                <a target="_blank" href="//<?php
                                if (!empty($leads_details->facebook)) {
                                    echo $leads_details->facebook;
                                }
                                ?>">
                                    <p class="form-control-static"><?php
                                        if (!empty($leads_details->facebook)) {
                                            echo $leads_details->facebook;
                                        }
                                        ?></p></a>
                            </div>
                            <div class="col-sm-6"><label
                                        class="control-label col-sm-5"><strong><?= lang('twitter_profile_link') ?>
                                        : </strong></label>
                                <a target="_blank" href="//<?php
                                if (!empty($leads_details->twitter)) {
                                    echo $leads_details->twitter;
                                }
                                ?>">
                                    <p class="form-control-static"><?php
                                        if (!empty($leads_details->twitter)) {
                                            echo $leads_details->twitter;
                                        }
                                        ?></p></a>
                            </div>

                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('skype_id') ?>: </strong></label>
                                <a href="skype:'<?php
                                if (!empty($leads_details->skype)) {
                                    echo $leads_details->skype;
                                }
                                ?>'">
                                    <p class="form-control-static"><?php
                                        if (!empty($leads_details->skype)) {
                                            echo $leads_details->skype;
                                        }
                                        ?></p></a>
                            </div>
                        </div>
                        <?php $show_custom_fields = custom_form_label(5, $leads_details->leads_id);
                        if (!empty($show_custom_fields)) {
                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                if (!empty($v_fields)) {
                                    if (count($v_fields) == 1) {
                                        $col = 'col-sm-10';
                                        $sub_col = 'col-sm-3';
                                        $style = 'padding-left:21px';
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

                            <?php if ($leads_details->permission != '-') { ?>
                                <div class="form-group  col-sm-6">
                                    <label class="control-label col-sm-5"><strong><?= lang('participants') ?>
                                            :</strong></label>
                                    <div class="col-sm-7 ">
                                        <?php
                                        if ($leads_details->permission != 'all') {
                                            $get_permission = json_decode($leads_details->permission);
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
                                            <?php
                                            if ($leads_details->converted_client_id == 0) { ?>
                                            <?php
                                            if (!empty($can_edit) && !empty($edited)) {
                                            ?>
                                            <span data-placement="top" data-toggle="tooltip"
                                                  title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/leads/update_users/<?= $leads_details->leads_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                        </p>
                                    <?php
                                    }
                                    }
                                    ?>

                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-sm-12">
                            <blockquote style="font-size: 12px;"><?php
                                if (!empty($leads_details->notes)) {
                                    echo $leads_details->notes;
                                }
                                ?> </blockquote>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Task Details tab Ends -->
            <!-- Task Comments Panel Starts --->
            <div class=" tab-pane <?= $active == 2 ? 'active' : '' ?>" id="call" style="position:
                            relative;">
                <div class="nav-tabs-custom ">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="<?= $sub_active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                                data-toggle="tab"><?= lang('all_call') ?></a>
                        </li>
                        <li class="<?= $sub_active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                                data-toggle="tab"><?= lang('new_call') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane <?= $sub_active == 1 ? 'active' : ''; ?>" id="manage">

                            <div class="table-responsive">
                                <table class="table table-striped "
                                       cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('call_summary') ?></th>
                                        <th><?= lang('contact') ?></th>
                                        <th><?= lang('responsible') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($all_calls_info)):
                                        foreach ($all_calls_info as $v_calls):
                                            $client_info = $this->items_model->check_by(array('client_id' => $v_calls->client_id), 'tbl_client');
                                            $user = $this->items_model->check_by(array('user_id' => $v_calls->user_id), 'tbl_users');
                                            ?>
                                            <tr id="leads_call_<?= $leads_details->leads_id ?>">
                                                <td><?= strftime(config_item('date_format'), strtotime($v_calls->date)) ?></td>
                                                <td><?= $v_calls->call_summary ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($client_info)) {
                                                        $client_info->name;
                                                    }
                                                    ?></td>
                                                <td><?= $user->username ?></td>
                                                <td>
                                                    <?= btn_edit('admin/leads/leads_details/' . $leads_details->leads_id . '/call/' . $v_calls->calls_id) ?>
                                                    <?php echo ajax_anchor(base_url("admin/leads/delete_leads_call/" . $leads_details->leads_id . '/' . $v_calls->calls_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_call_" . $leads_details->leads_id)); ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane <?= $sub_active == 2 ? 'active' : ''; ?>" id="create">
                            <form role="form" enctype="multipart/form-data" id="form"
                                  action="<?php echo base_url(); ?>admin/leads/saved_call/<?= $leads_details->leads_id ?>/<?php
                                  if (!empty($call_info)) {
                                      echo $call_info->calls_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('date') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                            <input type="text" required="" name="date"
                                                   class="form-control datepicker" value="<?php
                                            if (!empty($call_info->date)) {
                                                echo $call_info->date;
                                            } else {
                                                echo date('Y-m-d');
                                            }
                                            ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- End discount Fields -->
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('call_summary') ?><span
                                                class="text-danger"> *</span> </label>
                                    <div class="col-lg-5">
                                        <input type="text" required="" name="call_summary"
                                               class="form-control"
                                               value="<?php
                                               if (!empty($call_info->call_summary)) {
                                                   echo $call_info->call_summary;
                                               }
                                               ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('contact') ?></label>
                                    <div class="col-lg-5">
                                        <select name="client_id" class="form-control select_box"
                                                style="width: 100%">
                                            <option value=""><?= lang('select_client') ?></option>
                                            <?php
                                            $all_client = $this->db->get('tbl_client')->result();
                                            if (!empty($all_client)) {
                                                foreach ($all_client as $v_client) {
                                                    ?>
                                                    <option value="<?= $v_client->client_id ?>" <?php
                                                    if (!empty($call_info) && $call_info->client_id == $v_client->client_id) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?= $v_client->name ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('responsible') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-5">
                                        <select name="user_id" class="form-control select_box"
                                                style="width: 100%"
                                                required="">
                                            <option value=""><?= lang('admin_staff') ?></option>
                                            <?php
                                            $user_info = $this->db->where(array('role_id !=' => '2'))->get('tbl_users')->result();
                                            if (!empty($user_info)) {
                                                foreach ($user_info as $key => $v_user) {
                                                    ?>
                                                    <option value="<?= $v_user->user_id ?>" <?php
                                                    if (!empty($call_info) && $call_info->user_id == $v_user->user_id) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?= $v_user->username ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Task Comments Panel Ends--->
            <!-- Task Attachment Panel Starts --->
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="mettings"
                 style="position: relative;">

                <div class="nav-tabs-custom ">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="<?= $sub_metting == 1 ? 'active' : ''; ?>"><a href="#all_metting"
                                                                                 data-toggle="tab"><?= lang('all_metting') ?></a>
                        </li>
                        <li class="<?= $sub_metting == 2 ? 'active' : ''; ?>"><a href="#new_metting"
                                                                                 data-toggle="tab"><?= lang('new_metting') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane <?= $sub_metting == 1 ? 'active' : ''; ?>" id="all_metting">

                            <div class="table-responsive">
                                <table class="table table-striped "
                                       cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('subject') ?></th>
                                        <th><?= lang('start_date') ?></th>
                                        <th><?= lang('end_date') ?></th>
                                        <th><?= lang('responsible') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($all_meetings_info)):
                                        foreach ($all_meetings_info as $v_mettings):
                                            $user = $this->items_model->check_by(array('user_id' => $v_mettings->user_id), 'tbl_users');
                                            ?>
                                            <tr id="leads_meetings_<?= $leads_details->leads_id ?>">
                                                <td><?= $v_mettings->meeting_subject ?></td>
                                                <td><?= strftime(config_item('date_format'), ($v_mettings->start_date)) . '<span style="color:#3c8dbc"> at </span>' . display_time($v_mettings->start_date, true) ?></td>
                                                <td><?= strftime(config_item('date_format'), ($v_mettings->end_date)) . '<span style="color:#3c8dbc"> at </span>' . display_time($v_mettings->end_date, true) ?></td>
                                                <td><?= $user->username ?></td>
                                                <td>
                                                    <?= btn_edit('admin/leads/leads_details/' . $leads_details->leads_id . '/metting/' . $v_mettings->mettings_id) ?>
                                                    <?php echo ajax_anchor(base_url("admin/leads/delete_leads_mettings/" . $leads_details->leads_id . '/' . $v_mettings->mettings_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_meetings_" . $leads_details->leads_id)); ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane <?= $sub_metting == 2 ? 'active' : ''; ?>" id="new_metting">
                            <form role="form" enctype="multipart/form-data" id="form"
                                  action="<?php echo base_url(); ?>admin/leads/saved_metting/<?= $leads_details->leads_id ?>/<?php
                                  if (!empty($mettings_info)) {
                                      echo $mettings_info->mettings_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('metting_subject') ?>
                                        <span
                                                class="text-danger"> *</span> </label>
                                    <div class="col-lg-9">
                                        <input type="text" required="" name="meeting_subject"
                                               class="form-control"
                                               value="<?php
                                               if (!empty($mettings_info->meeting_subject)) {
                                                   echo $mettings_info->meeting_subject;
                                               }
                                               ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('start_date') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <input type="text" required="" name="start_date"
                                                   class="form-control datepicker" value="<?php
                                            if (!empty($mettings_info->start_date)) {
                                                echo date('Y-m-d', strftime($mettings_info->start_date));
                                            } else {
                                                echo date('Y-m-d');
                                            }
                                            ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="col-lg-2 control-label"><?= lang('start_time') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <input type="text" required="" name="start_time"
                                                   class="form-control timepicker" value="<?php
                                            if (!empty($mettings_info->start_date)) {
                                                echo display_time($mettings_info->start_date, true);
                                            }
                                            ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-clock-o"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('end_date') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <input type="text" required="" name="end_date"
                                                   class="form-control datepicker" value="<?php
                                            if (!empty($mettings_info->end_date)) {
                                                echo date('Y-m-d', strftime($mettings_info->end_date));
                                            } else {
                                                echo date('Y-m-d');
                                            }
                                            ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="col-lg-2 control-label"><?= lang('end_time') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <input type="text" required="" name="end_time"
                                                   class="form-control timepicker" value="<?php
                                            if (!empty($mettings_info->end_date)) {
                                                echo display_time($mettings_info->end_date, true);
                                            }
                                            ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-clock-o"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('attendess') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-5">
                                        <select multiple="multiple" name="attendees[]" style="width: 100%"
                                                class="select_multi" required="">
                                            <option
                                                    value=""><?= lang('select') . lang('attendess') ?></option>
                                            <?php
                                            $all_user_attendees = $this->db->get('tbl_users')->result();
                                            if (!empty($all_user_attendees)) {
                                                foreach ($all_user_attendees as $v_user_attendees) {
                                                    ?>
                                                    <option value="<?= $v_user_attendees->user_id ?>" <?php
                                                    if (!empty($mettings_info->attendees)) {
                                                        $user_id = unserialize($mettings_info->attendees);
                                                        foreach ($user_id['attendees'] as $assding_id) {
                                                            echo $v_user_attendees->user_id == $assding_id ? 'selected' : '';
                                                        }
                                                    }
                                                    ?>><?= $v_user_attendees->username ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('responsible') ?><span
                                                class="text-danger"> *</span></label>
                                    <div class="col-lg-5">
                                        <select name="user_id" class="form-control select_box"
                                                style="width: 100%"
                                                required="">
                                            <option value=""><?= lang('admin_staff') ?></option>
                                            <?php
                                            $responsible_user_info = $this->db->where(array('role_id !=' => '2'))->get('tbl_users')->result();
                                            if (!empty($responsible_user_info)) {
                                                foreach ($responsible_user_info as $v_responsible_user) {
                                                    ?>
                                                    <option
                                                            value="<?= $v_responsible_user->user_id ?>" <?php
                                                    if (!empty($mettings_info) && $mettings_info->user_id == $v_responsible_user->user_id) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?= $v_responsible_user->username ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('location') ?><span
                                                class="text-danger"> *</span> </label>
                                    <div class="col-lg-5">
                                        <input type="text" required="" name="location" class="form-control"
                                               value="<?php
                                               if (!empty($mettings_info->location)) {
                                                   echo $mettings_info->location;
                                               }
                                               ?>">
                                    </div>
                                </div>
                                <div class="form-group terms">
                                    <label
                                            class="col-lg-3 control-label"><?= lang('description') ?> </label>
                                    <div class="col-lg-8">
                                            <textarea name="description" class="form-control"><?php
                                                if (!empty($mettings_info)) {
                                                    echo $mettings_info->description;
                                                }
                                                ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Task Comments Panel Starts --->
            <?php $comment_type = 'leads'; ?>
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_comments"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat" id="chat-box">

                        <?php echo form_open(base_url("admin/leads/save_comments"), array("id" => $comment_type . "-comment-form", "class" => "form-horizontal general-form", "enctype" => "multipart/form-data", "role" => "form")); ?>

                        <input type="hidden" name="leads_id" value="<?php
                        if (!empty($leads_details->leads_id)) {
                            echo $leads_details->leads_id;
                        }
                        ?>" class="form-control">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo form_textarea(array(
                                    "id" => "comment_description",
                                    "name" => "comment",
                                    "class" => "form-control comment_description",
                                    "placeholder" => $leads_details->lead_name . ' ' . lang('comments'),
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
                        $comment_reply_type = 'leads-reply';
                        ?>
                        <?php $this->load->view('admin/leads/comments_list', array('comment_details' => $comment_details)) ?>
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
            <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="task_attachments">
                <div class="panel panel-custom">
                    <div class="panel-heading mb0">
                        <?php
                        $attach_list = $this->session->userdata('leads_media_view');
                        if (empty($attach_list)) {
                            $attach_list = 'list_view';
                        }
                        ?>
                        <h3 class="panel-title"><?= lang('attach_file_list') ?>

                            <a data-toggle="tooltip" data-placement="top"
                               href="<?= base_url('admin/global_controller/download_all_attachment/leads_id/' . $leads_details->leads_id) ?>"
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
                                <a href="<?= base_url() ?>admin/leads/new_attachment/<?= $leads_details->leads_id ?>"
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
                                var module = 'leads';
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
                            <?php $this->load->view('admin/leads/attachment_list', array('project_files_info' => $project_files_info)) ?>
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
                                                            <?php echo ajax_anchor(base_url("admin/leads/delete_files/" . $files_info[$key]->task_attachment_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#media_list_container-" . $files_info[$key]->task_attachment_id)); ?>
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
                                                                                        href="<?= base_url() ?>admin/leads/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>">
                                                                                    <img
                                                                                            style="width: 50px;border-radius: 5px;"
                                                                                            src="<?= base_url() . $v_files->files ?>"/></a>
                                                                            </div>
                                                                        <?php else : ?>
                                                                            <div class="file-icon"><i
                                                                                        class="fa fa-file-o"></i>
                                                                                <a data-toggle="modal"
                                                                                   data-target="#myModal_extra_lg"
                                                                                   href="<?= base_url() ?>admin/leads/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
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
                                                                           href="<?= base_url() ?>admin/leads/download_files/<?= $v_files->uploaded_files_id ?>"><i
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
            <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="activities"
                 style="position: relative;">
                <div class="tab-pane " id="activities">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('activities') ?>
                                <?php
                                $role = $this->session->userdata('user_type');
                                if ($role == 1) {
                                    ?>
                                    <span class="btn-xs pull-right">
                            <a href="<?= base_url() ?>admin/tasks/claer_activities/leads/<?= $leads_details->leads_id ?>"><?= lang('clear') . ' ' . lang('activities') ?></a>
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
            <div class="tab-pane <?= $active == 8 ? 'active' : '' ?>" id="tasks"
                 style="position: relative;">
                <div class="nav-tabs-custom ">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="<?= $sub_active == 1 ? 'active' : ''; ?>"><a href="#manageTasks"
                                                                                data-toggle="tab"><?= lang('all_task') ?></a>
                        </li>
                        <li class=""><a
                                    href="<?= base_url() ?>admin/tasks/all_task/leads/<?= $leads_details->leads_id ?>"><?= lang('new_task') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane <?= $sub_active == 1 ? 'active' : ''; ?>" id="manageTasks"
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
                                            <tr id="leads_tasks_<?= $v_task->task_id ?>">
                                                <td class="col-sm-1">
                                                    <div class="is_complete checkbox c-checkbox">
                                                        <label>
                                                            <input type="checkbox"
                                                                   data-id="<?= $v_task->task_id ?>"
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
                                                    $current_time = strtotime(date('Y-m-d'));
                                                    ?>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                        <span
                                                                class="label label-danger"><?= lang('overdue') ?></span>
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
                                                    <?php echo ajax_anchor(base_url("admin/tasks/delete_task/" . $v_task->task_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_tasks_" . $v_task->task_id)); ?>
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
            <div class="tab-pane <?= $active == 9 ? 'active' : '' ?>" id="proposals"
                 style="position: relative;">
                <div class="nav-tabs-custom ">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="<?= $sub_active == 1 ? 'active' : ''; ?>"><a href="#manageProposals"
                                                                                data-toggle="tab"><?= lang('all_proposals') ?></a>
                        </li>
                        <li class=""><a
                                    href="<?= base_url() ?>admin/proposals/index/leads/<?= $leads_details->leads_id ?>"><?= lang('create_proposal') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane <?= $sub_active == 1 ? 'active' : ''; ?>" id="manageProposals"
                             style="position: relative;">

                            <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped " cellspacing="0"
                                               width="100%">
                                            <thead>
                                            <tr>
                                                <th><?= lang('proposal') ?> #</th>
                                                <th><?= lang('proposal_date') ?></th>
                                                <th><?= lang('expire_date') ?></th>
                                                <th><?= lang('status') ?></th>
                                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                                    <th class="hidden-print"><?= lang('action') ?></th>
                                                <?php } ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php

                                            if (!empty($all_proposals_info)) {
                                                foreach ($all_proposals_info as $v_proposals) {
                                                    $can_edit = $this->items_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $v_proposals->proposals_id));
                                                    $can_delete = $this->items_model->can_action('tbl_proposals', 'delete', array('proposals_id' => $v_proposals->proposals_id));

                                                    if ($v_proposals->status == 'pending') {
                                                        $label = "info";
                                                    } elseif ($v_proposals->status == 'accepted') {
                                                        $label = "success";
                                                    } else {
                                                        $label = "danger";
                                                    }
                                                    ?>
                                                    <tr id="leads_proposals_<?= $v_proposals->proposals_id ?>">
                                                        <td>
                                                            <a class="text-info"
                                                               href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= $v_proposals->reference_no ?></a>
                                                            <?php if ($v_proposals->convert == 'Yes') {
                                                                if ($v_proposals->convert_module == 'invoice') {
                                                                    $c_url = base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_proposals->convert_module_id;
                                                                    $text = lang('invoiced');
                                                                } else {
                                                                    $text = lang('estimated');
                                                                    $c_url = base_url() . 'admin/estimates/index/estimates_details/' . $v_proposals->convert_module_id;
                                                                }
                                                                if (!empty($c_url)) { ?>
                                                                    <p class="text-sm m0 p0">
                                                                        <a class="text-success"
                                                                           href="<?= $c_url ?>">
                                                                            <?= $text ?>
                                                                        </a>
                                                                    </p>
                                                                <?php }
                                                            } ?>
                                                        </td>
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_proposals->proposal_date)) ?></td>
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_proposals->due_date)) ?>
                                                            <?php
                                                            if (strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == 'pending' || strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == ('draft')) { ?>
                                                                <span
                                                                        class="label label-danger "><?= lang('expired') ?></span>
                                                            <?php }
                                                            ?>
                                                        </td>
                                                        <?php ?>
                                                        <td><span
                                                                    class="label label-<?= $label ?>"><?= lang($v_proposals->status) ?></span>
                                                        </td>
                                                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                                                            <td>
                                                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                                    <?= btn_edit('admin/proposals/index/edit_proposals/' . $v_proposals->proposals_id) ?>
                                                                <?php }
                                                                if (!empty($can_delete) && !empty($deleted)) {
                                                                    ?>
                                                                    <?php echo ajax_anchor(base_url("admin/proposals/delete/delete_proposals/" . $v_proposals->proposals_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_proposals_" . $v_proposals->proposals_id)); ?>
                                                                <?php } ?>
                                                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                                    <div class="btn-group">
                                                                        <button
                                                                                class="btn btn-xs btn-default dropdown-toggle"
                                                                                data-toggle="dropdown">
                                                                            <?= lang('change_status') ?>
                                                                            <span class="caret"></span></button>
                                                                        <ul class="dropdown-menu animated zoomIn">
                                                                            <li>
                                                                                <a href="<?= base_url() ?>admin/proposals/index/email_proposals/<?= $v_proposals->proposals_id ?>"><?= lang('send_email') ?></a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= lang('view_details') ?></a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="<?= base_url() ?>admin/proposals/index/proposals_history/<?= $v_proposals->proposals_id ?>"><?= lang('history') ?></a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="<?= base_url() ?>admin/proposals/change_status/declined/<?= $v_proposals->proposals_id ?>"><?= lang('declined') ?></a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="<?= base_url() ?>admin/proposals/change_status/accepted/<?= $v_proposals->proposals_id ?>"><?= lang('accepted') ?></a>
                                                                            </li>

                                                                        </ul>
                                                                    </div>
                                                                <?php } ?>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane <?= $active == 10 ? 'active' : '' ?>" id="reminder">
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#reminder_manage"
                                              data-toggle="tab"><?= lang('reminder') . ' ' . lang('list') ?></a>
                        </li>
                        <li class=""><a href="#reminder_create"
                                        data-toggle="tab"><?= lang('set') . ' ' . lang('reminder') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane active" id="reminder_manage">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= lang('description') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('remind') ?></th>
                                        <th><?= lang('notified') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $all_reminder = $this->db->where(array('module' => 'leads', 'module_id' => $leads_details->leads_id))->get('tbl_reminders')->result();
                                    if (!empty($all_reminder)) {
                                        foreach ($all_reminder as $v_reminder):
                                            $remind_user_info = $this->db->where('user_id', $v_reminder->user_id)->get('tbl_account_details')->row();
                                            ?>
                                            <tr id="leads_reminder_<?= $v_reminder->reminder_id ?>">
                                                <td><?= $v_reminder->description ?></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_reminder->date)) . ' ' . display_time($v_reminder->date) ?></td>
                                                <td>
                                                    <a href="<?= base_url() ?>admin/user/user_details/<?= $v_reminder->user_id ?>"> <?= $remind_user_info->fullname ?></a>
                                                </td>
                                                <td><?= $v_reminder->notified ?></td>
                                                <td>
                                                    <?php echo ajax_anchor(base_url("admin/invoice/delete_reminder/" . $v_reminder->module . '/' . $v_reminder->module_id . '/' . $v_reminder->reminder_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_reminder_" . $v_reminder->reminder_id)); ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5"><?= lang('nothing_to_display') ?></td>
                                        </tr>
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="reminder_create">
                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                                  id="form"
                                  action="<?php echo base_url(); ?>admin/invoice/reminder/leads/<?= $leads_details->leads_id ?>/<?php
                                  if (!empty($reminder_info)) {
                                      echo $reminder_info->reminder_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('date_to_notified') ?> <span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                            <input type="text" name="date"
                                                   class="form-control datetimepicker"
                                                   value="<?php
                                                   if (!empty($reminder_info->date)) {
                                                       echo $reminder_info->date;
                                                   } else {
                                                       echo date('Y-m-d h:i');
                                                   }
                                                   ?>"
                                                   data-date-min-date="<?= date('Y-m-d'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- End discount Fields -->
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                                    <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($reminder_info)) {
                                echo $reminder_info->description;
                            }
                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('set_reminder_to') ?> <span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <select class="form-control select_box" name="user_id" style="width: 100%">
                                            <?php
                                            $permission_user = $this->items_model->all_permission_user('55');
                                            if (!empty($permission_user)) {
                                                foreach ($permission_user as $key => $v_users) {
                                                    ?>
                                                    <option <?php
                                                    if (!empty($reminder_info)) {
                                                        echo $reminder_info->user_id == $v_users->user_id ? 'selected' : null;
                                                    }
                                                    ?> value="<?= $v_users->user_id ?>"><?= $v_users->fullname ?></option>
                                                <?php }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick">
                                                <input type="checkbox" value="Yes"
                                                    <?php if (!empty($reminder_info) && $reminder_info->notify_by_email == 'Yes') {
                                                        echo 'checked';
                                                    } ?> name="notify_by_email">
                                                <span class="fa fa-check"></span>
                                                <?= lang('send_also_email_this_reminder') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <button type="submit" class="btn btn-purple"><?= lang('update') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <link rel="stylesheet"
                      href="<?= base_url() ?>assets/plugins/datetimepicker/jquery.datetimepicker.min.css">
                <?php include_once 'assets/plugins/datetimepicker/jquery.datetimepicker.full.php'; ?>

                <script type="text/javascript">
                    init_datepicker();

                    // Date picker init with selected timeformat from settings
                    function init_datepicker() {
                        var datetimepickers = $('.datetimepicker');
                        if (datetimepickers.length == 0) {
                            return;
                        }
                        var opt_time;
                        // Datepicker with time
                        $.each(datetimepickers, function () {
                            opt_time = {
                                lazyInit: true,
                                scrollInput: false,
                                format: 'Y-m-d H:i',
                            };

                            opt_time.formatTime = 'H:i';
                            // Check in case the input have date-end-date or date-min-date
                            var max_date = $(this).data('date-end-date');
                            var min_date = $(this).data('date-min-date');
                            if (max_date) {
                                opt_time.maxDate = max_date;
                            }
                            if (min_date) {
                                opt_time.minDate = min_date;
                            }
                            // Init the picker
                            $(this).datetimepicker(opt_time);
                        });
                    }
                </script>
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