<?php
$office_hours = config_item('office_hours');
$leave_cate_info = $this->db->where('leave_category_id', $application_info->leave_category_id)->get('tbl_leave_category')->row();
$profile_info = $this->db->where('user_id', $application_info->user_id)->get('tbl_account_details')->row();
$approve_by = $this->db->where('user_id', $application_info->approve_by)->get('tbl_account_details')->row();

if ($application_info->application_status == '1') {
    $text = lang('pending');
    $ribbon = 'warning';
} elseif ($application_info->application_status == '2') {
    $text = lang('approved');
    $ribbon = 'success';
} else {
    $text = lang('rejected');
    $ribbon = 'danger';
}
?>

<div class="">
    <div class="panel panel-custom">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= $profile_info->fullname . ' ' . '<span class="text-danger"> '
                    . strftime(config_item('date_format'), strtotime($application_info->leave_start_date));
                    if ($application_info->leave_type == 'multiple_days') {
                        if (!empty($application_info->leave_end_date)) {
                            echo '</span> ' . lang('leave_to') . '<span class="text-danger"> ' . strftime(config_item('date_format'), strtotime($application_info->leave_end_date)) . '</span>';
                        }
                    }
                    ?></strong>
            </div>
            <div class="ribbon <?php
            if (!empty($ribbon)) {
                echo $ribbon;
            } else {
                echo 'primary';
            }
            ?>"><span><?= $text
                    ?></span></div>
        </div>
        <div class="panel-body row form-horizontal task_details">
            <div class="col-md-8">

                <div class="form-group ">
                    <label class="control-label col-sm-4"><strong><?= lang('leave_category') ?>
                            :</strong></label>
                    <div class="col-sm-8">
                        <?php if (!empty($leave_cate_info)) { ?>
                            <p class="form-control-static "><?= ($leave_cate_info->leave_category) ?></p>
                        <?php }
                        ?>
                    </div>
                </div>
                <div class="form-group ">
                    <label class="control-label col-sm-4"><strong><?= lang('date') ?>
                            :</strong></label>
                    <div class="col-sm-8">
                        <?= strftime(config_item('date_format'), strtotime($application_info->leave_start_date)) ?>
                        <?php
                        if ($application_info->leave_type == 'multiple_days') {
                            if (!empty($application_info->leave_end_date)) {
                                echo lang('TO') . ' ' . strftime(config_item('date_format'), strtotime($application_info->leave_end_date));
                            }
                        } ?>
                    </div>
                </div>
                <div class="form-group ">
                    <label class="control-label col-sm-4"><strong><?= lang('duration') ?>
                            :</strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static "><?php
                            if ($application_info->leave_type == 'single_day') {
                                echo ' 1 ' . lang('day') . ' (<span class="text-danger">' . $office_hours . '.00' . lang('hours') . '</span>)';
                            }
                            if ($application_info->leave_type == 'multiple_days') {
                                $ge_days = 0;
                                $m_days = 0;

                                $month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($application_info->leave_start_date)), date('Y', strtotime($application_info->leave_start_date)));
                                $datetime1 = new DateTime($application_info->leave_start_date);
                                if (empty($application_info->leave_end_date)) {
                                    $application_info->leave_end_date = $application_info->leave_start_date;
                                }
                                $datetime2 = new DateTime($application_info->leave_end_date);
                                $difference = $datetime1->diff($datetime2);
                                if ($difference->m != 0) {
                                    $m_days += $month;
                                } else {
                                    $m_days = 0;
                                }
                                $ge_days += $difference->d + 1;
                                $total_token = $m_days + $ge_days;
                                echo $total_token . ' ' . lang('days') . ' (<span class="text-danger">' . $total_token * $office_hours . '.00' . lang('hours') . '</span>)';
                            }
                            if ($application_info->leave_type == 'hours') {
                                $total_hours = ($application_info->hours / $office_hours);
                                echo number_format($total_hours, 2) . ' ' . lang('days') . ' (<span class="text-danger">' . $application_info->hours . '.00' . lang('hours') . '</span>)';
                            }
                            ?></p>

                    </div>
                </div>
                <div class="form-group ">
                    <label class="control-label col-sm-4"><strong><?= lang('applied_on') ?>
                            :</strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static "><?= strftime(config_item('date_format'), strtotime($application_info->application_date)) . lang('at') . display_time($application_info->application_date); ?></p>
                    </div>
                </div>
                <?php if (!empty($approve_by)) { ?>
                    <div class="form-group ">
                        <label class="control-label col-sm-4"><strong><?= lang('approved_by') ?>
                                :</strong></label>
                        <div class="col-sm-8">
                            <p class="form-control-static "><?= $approve_by->fullname ?></p>

                        </div>
                    </div>
                <?php } ?>
                <div class="form-group ">
                    <label class="control-label col-sm-4"><strong><?= lang('reason') ?>
                            :</strong></label>
                    <div class="col-sm-8">
                        <blockquote
                            style="font-size: 12px; margin-top: 5px"><?= nl2br($application_info->reason) ?></blockquote>
                    </div>
                </div>
                <?php if (!empty($approve_by)) { ?>
                    <div class="form-group ">
                        <label class="control-label col-sm-4"><strong><?= lang('comments') ?>
                                :</strong></label>
                        <div class="col-sm-8">
                            <blockquote
                                style="font-size: 12px; margin-top: 5px"><?= nl2br($application_info->comments) ?></blockquote>
                        </div>
                    </div>
                <?php } ?>
                <?php
                $show_custom_fields = custom_form_label(17, $application_info->leave_application_id);
                if (!empty($show_custom_fields)) {
                    foreach ($show_custom_fields as $c_label => $v_fields) {
                        if (!empty($v_fields)) {
                            ?>
                            <div class="form-group ">
                                <label class="control-label col-sm-4"><strong><?= $c_label ?>
                                        :</strong></label>
                                <div class="col-sm-8">
                                    <p class="form-control-static "><?= $v_fields ?></p>

                                </div>
                            </div>
                        <?php }
                    }
                }
                ?>
                <?php if (!empty($application_info->attachment)) {
                    $uploaded_file = json_decode($application_info->attachment);
                    ?>
                    <div class="col-md-12 notice-details-margin">
                        <div class="col-sm-4 text-right">
                            <label class="control-label"><strong><?= lang('attachment') ?> :</strong></label>
                        </div>
                        <div class="col-sm-8">
                            <ul class="mailbox-attachments clearfix mt">
                                <?php
                                if (!empty($uploaded_file)):
                                    foreach ($uploaded_file as $v_files):

                                        if (!empty($v_files)):

                                            ?>
                                            <li>
                                                <?php if ($v_files->is_image == 1) : ?>
                                                    <span class="mailbox-attachment-icon has-img"><img
                                                            src="<?= base_url() . $v_files->path ?>"
                                                            alt="Attachment"></span>
                                                <?php else : ?>
                                                    <span class="mailbox-attachment-icon"><i
                                                            class="fa fa-file-pdf-o"></i></span>
                                                <?php endif; ?>
                                                <div class="mailbox-attachment-info">
                                                    <a href="<?= base_url() ?>admin/leave_management/download_files/<?= $application_info->leave_application_id . '/' . $v_files->fileName ?>"
                                                       class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                                        <?= $v_files->fileName ?></a>
                        <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> <?= lang('kb') ?>
                            <a href="<?= base_url() ?>admin/leave_management/download_files/<?= $application_info->leave_application_id . '/' . $v_files->fileName ?>"
                               class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                                                </div>
                                            </li>
                                            <?php
                                        endif;
                                    endforeach;
                                endif;
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php }
                $my_details = MyDetails();

                $designation_info = $this->application_model->check_by(array('designations_id' => $my_details->designations_id), 'tbl_designations');
                if (!empty($designation_info)) {
                    $dept_head = $this->application_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                }
                ?>
                <?php if ($this->session->userdata('user_type') == 1 || !empty($dept_head) && $dept_head->department_head_id == $my_details->user_id) { ?>
                        <div class="form-group ">
                            <label
                                class="control-label col-sm-4"><strong><?= lang('change') . ' ' . lang('status') ?>
                                    :</strong></label>
                            <div class="col-sm-8">
                                <p class="form-control-static ">
                                    <?php

if ($application_info->application_status == '1') { ?>
<span data-toggle="tooltip" data-placment="top"
          title="<?= lang('approved_alert') ?>">
                <a data-toggle="modal" data-target="#myModal"
                   href="<?= base_url() ?>admin/leave_management/change_status/2/<?= $application_info->leave_application_id; ?>"
                   class="btn btn-success ml"><i
                        class="fa fa-thumbs-o-up"></i> <?= lang('approved') ?></a>
                    </span>
    <a data-toggle="modal" data-target="#myModal"
       href="<?= base_url() ?>admin/leave_management/change_status/3/<?= $application_info->leave_application_id; ?>"
       class="btn btn-danger ml"><i
            class="fa fa-times"></i> <?= lang('reject') ?></a>
<?php }else if ($application_info->application_status == '2') { ?>
    <a data-toggle="modal" data-target="#myModal"
                                           href="<?= base_url() ?>admin/leave_management/change_status/1/<?= $application_info->leave_application_id; ?>"
                                           class="btn btn-warning ml"><i
                                                class="fa fa-times"></i> <?= lang('pending') ?></a>
                                        <a data-toggle="modal" data-target="#myModal"
                                           href="<?= base_url() ?>admin/leave_management/change_status/3/<?= $application_info->leave_application_id; ?>"
                                           class="btn btn-danger ml"><i
                                                class="fa fa-times"></i> <?= lang('reject') ?></a>
                                    <?php } elseif ($application_info->application_status == '3') { ?>
                                        <span data-toggle="tooltip" data-placment="top"
                                              title="<?= lang('approved_alert') ?>">
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/leave_management/change_status/2/<?= $application_info->leave_application_id; ?>"
                                                       class="btn btn-success ml"><i
                                                            class="fa fa-thumbs-o-up"></i> <?= lang('approved') ?></a>
                                                        </span>
                                        <a data-toggle="modal" data-target="#myModal"
                                           href="<?= base_url() ?>admin/leave_management/change_status/1/<?= $application_info->leave_application_id; ?>"
                                           class="btn btn-warning ml"><i
                                                class="fa fa-times"></i> <?= lang('pending') ?></a>
                                    <?php }
                                    ?>
                                </p>
                            </div>
                        </div>
                    <?php 
                }
                ?>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-custom">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong
                                class="text-sm"><?= lang('details_of') . ' ' . $profile_info->fullname ?></strong>
                        </div>
                    </div>
                    <table class="table">
                        <tbody>
                        <?php
                        $total_taken = 0;
                        $total_quota = 0;
                        $leave_report = leave_report($profile_info->user_id);

                        if (!empty($leave_report['leave_category'])) {
                            foreach ($leave_report['leave_category'] as $lkey => $v_l_report) {
                                $total_quota += $leave_report['leave_quota'][$lkey];
                                $total_taken += $leave_report['leave_taken'][$lkey];
                                ?>
                                <tr>
                                    <td><strong> <?= $leave_report['leave_category'][$lkey] ?></strong>:</td>
                                    <td>
                                        <?= $leave_report['leave_taken'][$lkey] ?>
                                        /<?= $leave_report['leave_quota'][$lkey]; ?> </td>
                                </tr>
                            <?php }
                        }
                        ?>

                        <tr>
                            <td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;">
                                <strong> <?= lang('total') ?></strong>:
                            </td>
                            <td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;"> <?= $total_taken; ?>
                                /<?= $total_quota; ?> </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('body').on('hidden.bs.modal', '.modal', function () {
        location.reload();
    });
</script>