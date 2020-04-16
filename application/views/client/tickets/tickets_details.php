<?= message_box('success') ?>
<?= message_box('error');
?>
<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">

                <?= lang('all_tickets') ?>
            </div>
            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <?php
                        if (!empty($all_tickets_info)) :
                            foreach ($all_tickets_info as $v_tickets_info) :
                                ?>
                                <ul class="nav"><?php
                                    if ($v_tickets_info->status == 'open') {
                                        $s_label = 'danger';
                                    } elseif ($v_tickets_info->status == 'closed') {
                                        $s_label = 'success';
                                    } else {
                                        $s_label = 'default';
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_tickets_info->tickets_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <a href="<?= base_url() ?>client/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>">
                                            <?= $v_tickets_info->ticket_code ?>
                                            <?php
                                            if ($v_tickets_info->status == 'in_progress') {
                                                $status = 'In Progress';
                                            } else {
                                                $status = $v_tickets_info->status;
                                            }
                                            ?>
                                            <div class="pull-right">
                                                <span
                                                    class="label label-<?= $s_label ?>"><?= ucfirst($status) ?> </span>
                                            </div>
                                            <br>
                                            <?php $user_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_users')->row();

                                            ?>
                                            <small class="block small text-muted"><?= ucfirst($user_info->username) ?>
                                                | <?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?> </small>
                                        </a></li>
                                </ul>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <section class="col-sm-9">
        <header class="hidden-print">
            <div class="row ">
                <div class="col-sm-12">
                    <a class="btn btn-purple btn-sm" id="tab_collapse">
                        <i class="fa fa-caret-left"></i></a>
                    <?php

                    if ($tickets_info->project_id != '0') {
                        $project_info = $this->db->where('project_id', $tickets_info->project_id)->get('tbl_project')->row();

                        if (!empty($project_info)) {
                            ?>
                            <strong><?= lang('project') ?>:</strong>
                            <a
                                href="<?= base_url() ?>client/projects/project_details/<?= $tickets_info->project_id ?>"
                                class="">
                                <?= $project_info->project_name ?>
                            </a>
                        <?php }
                    }
                    ?>
                </div>
            </div>
        </header>
        <!-- Start Display Details -->
        <div class="row mt">
            <div class="col-lg-4" id="list_tab">
                <ul class="list-group no-radius">
                    <?php
                    if ($tickets_info->status == 'open') {
                        $s_label = 'danger';
                    } elseif ($tickets_info->status == 'closed') {
                        $s_label = 'success';
                    } else {
                        $s_label = 'default';
                    }
                    ?>
                    <li class="list-group-item">
                        <?= lang('reporter') ?>
                        <span class="pull-right">
                            <a class="recect_task pull-left">
                                <?php
                                $profile_info = $this->db->where(array('user_id' => $tickets_info->reporter))->get('tbl_account_details')->row();
                                if (!empty($profile_info)) {
                                    ?>
                                    <img style="width: 18px;margin-left: 18px;
                                         height: 18px;
                                         border: 1px solid #aaa;" src="<?= base_url() . $profile_info->avatar ?>"
                                         class="img-circle">
                                <?php } ?>

                                <?=
                                ($profile_info->fullname)
                                ?>
                            </a>
                        </span>
                    </li>

                    <li class="list-group-item">
                        <span class="pull-right">
                            <?php
                            $dept_info = $this->db->where(array('departments_id' => $tickets_info->departments_id))->get('tbl_departments')->row();
                            if (!empty($dept_info)) {
                                $dept_name = $dept_info->deptname;
                            } else {
                                $dept_name = '-';
                            }
                            echo $dept_name;
                            ?>
                        </span><?= lang('department') ?>
                    </li>
                    <?php
                    if ($tickets_info->status == 'in_progress') {
                        $status = 'In Progress';
                    } else {
                        $status = $tickets_info->status;
                    }
                    ?>
                    <li class="list-group-item">
                        <span class="pull-right"><label
                                class="label label-<?= $s_label ?>"><?= ucfirst($status) ?></label>
                        </span><?= lang('status') ?>
                    </li>
                    <li class="list-group-item"><span
                            class="pull-right"><?= $tickets_info->priority ?></span><?= lang('priority') ?></li>
                    <li class="list-group-item"><span
                            class="pull-right"><?= $tickets_info->created ?></span><?= lang('created') ?></li>
                    <?php $show_custom_fields = custom_form_label(7, $tickets_info->tickets_id);
                    if (!empty($show_custom_fields)) {
                        foreach ($show_custom_fields as $c_label => $v_fields) {
                            if (!empty($v_fields)) {
                                ?>
                                <li class="list-group-item"><span
                                            class="pull-right"><?= $v_fields ?></span><?= $c_label ?></li>
                            <?php }
                        }
                    }
                    ?>
                </ul>
            </div>
            <!-- End details C1-->
            <div class="col-sm-12" id="tab">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title"> [ <?= $tickets_info->ticket_code ?>
                            ] <?= $tickets_info->subject; ?></div>
                    </div>
                    <div class="panel-body chat">
                        <?= nl2br($tickets_info->body) ?>

                        <ul class="mailbox-attachments clearfix mt">
                            <?php
                            $uploaded_file = json_decode($tickets_info->upload_file);
                            if (!empty($uploaded_file)):
                                foreach ($uploaded_file as $v_files):

                                    if (!empty($v_files)):?>
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
                                                <a href="<?= base_url() ?>client/tickets/index/download_file/<?= $tickets_info->tickets_id . '/' . $v_files->fileName ?>"
                                                   class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                                    <?= $v_files->fileName ?></a>
                        <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> KB
                          <a href="<?= base_url() ?>client/tickets/index/download_file/<?= $tickets_info->tickets_id . '/' . $v_files->fileName ?>"
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
                        <button data-toggle="collapse" data-target="#topic-reply" class="btn btn-primary mb mt"
                                aria-expanded="true"><?= lang('reply_ticket') ?>
                        </button>
                        <div id="topic-reply" class="collapse" aria-expanded="true">
                            <form method="post" enctype="multipart/form-data"
                                  action="<?= base_url() ?>client/tickets/index/save_reply/<?= $tickets_info->tickets_id ?>">
                                <div class="form-group col-sm-12">
                            <textarea class="form-control no-border" name="body" rows="3"
                                      placeholder="Ticket #<?= $tickets_info->ticket_code ?> reply"></textarea>
                                </div>
                                <div id="add_new">
                                    <div class="form-group">
                                        <div class="col-sm-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">

                                                <span class="btn btn-default btn-file"><span
                                                        class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="attachment[]">
                                                        </span>
                                                <span class="fileinput-filename"></span>
                                                <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                                                   style="float: none;">&times;</a>
                                            </div>
                                            <div id="msg_pdf" style="color: #e11221"></div>
                                        </div>
                                        <div class="col-sm-4">
                                            <strong><a href="javascript:void(0);" id="add_more" class="addCF "><i
                                                        class="fa fa-plus"></i>&nbsp;<?= lang('add_more') ?>
                                                </a></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group panel-footer ">

                                    <button class="btn btn-info pull-right btn-sm mt"
                                            type="submit"><?= lang('submit') ?></button>
                                </div>
                            </form>
                        </div>

                        <?php
                        $ticket_replies = $this->db->where(array('tickets_id' => $tickets_info->tickets_id))->get('tbl_tickets_replies')->result();
                        if (!empty($ticket_replies)) :
                            foreach ($ticket_replies as $v_replies) :

                                $profile_info = $this->db->where(array('user_id' => $v_replies->replierid))->get('tbl_account_details')->row();

                                $user_info = $this->db->where(array('user_id' => $v_replies->replierid))->get('tbl_users')->row();

                                $username = $user_info->username;
                                if ($user_info->role_id == 1) {
                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">' . lang('admin') . '</small>';
                                } elseif ($user_info->role_id == 3) {
                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-primary">' . lang('staff') . '</small>';
                                } else {
                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-success">' . lang('client') . '</small>';
                                }

                                ?>
                                <hr/>
                                <div class="col-sm-12 item mt">
                                    <img src="<?php echo base_url() . $profile_info->avatar ?>" alt="user image"
                                         class="img-xs img-circle"/>
                                    <p class="message ">
                                        <small class="text-muted pull-right"><i
                                                class="fa fa-clock-o"></i> <?= time_ago($v_replies->time) ?>
                                            <?php if ($v_replies->replierid == $this->session->userdata('user_id')) { ?>
                                                <?= btn_delete('client/tickets/delete/delete_ticket_replay/' . $v_replies->tickets_id . '/' . $v_replies->tickets_replies_id) ?>
                                            <?php } ?></small>
                                        <a href="#" class="name">
                                            <?= ($profile_info->fullname) . ' ' . $label ?>
                                        </a>

                                        <?= $v_replies->body ?>

                                        <?php
                                        $ticket_file = json_decode($v_replies->attachment);
                                        if (!empty($ticket_file)):
                                            foreach ($ticket_file as $t_files):

                                                if (!empty($t_files)):?>
                                                    <span class="file-icon block mt">
                                                    <?php if ($t_files->is_image == 1) : ?>
                                                        <a
                                                            href="<?= base_url() ?>client/tickets/index/download_file/<?= $tickets_info->tickets_id . '/' . $t_files->fileName ?>">
                                                            <img style="width: 50px;border-radius: 5px;"
                                                                 src="<?= base_url() . $t_files->path ?>"></a></span>
                                                    <?php else: ?>
                                                        <a class="btn btn-default btn-file"
                                                           href="<?= base_url() ?>client/tickets/index/download_file/<?= $tickets_info->tickets_id . '/' . $t_files->fileName ?>"><i
                                                                class="fa fa-file-o"></i> <?= $t_files->fileName ?></a>
                                                    <?php endif; ?>
                                                    <?php
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div><!-- /.panel body -->
                </div>
            </div>

            <!-- End ticket replies -->
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#list_tab').addClass("hide");
        $('#tab').addClass("col-sm-12");
        $('#tab_collapse').click(function () {
            $('#list_tab').toggleClass("hide");
            if ($('#tab').hasClass("col-sm-8")) {
                $('#tab').removeClass("col-sm-8");
                $('#tab').addClass("col-sm-12");
            } else {
                $('#tab').removeClass("col-sm-12");
                $('#tab').addClass("col-sm-8");
            }

        });
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4) {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\<div class="col-sm-8">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="attachment[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-4">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>
<!-- End details -->