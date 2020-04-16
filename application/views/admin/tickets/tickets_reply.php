<?php
if (!empty($ticket_replies)) :
    foreach ($ticket_replies as $v_replies) :
        $profile_info = $this->db->where(array('user_id' => $v_replies->replierid))->get('tbl_account_details')->row();
        $user_info = $this->db->where(array('user_id' => $v_replies->replierid))->get('tbl_users')->row();
        if (!empty($user_info)) {
            $username = $user_info->username;
            if ($user_info->role_id == 1) {
                $label = '<small style="font-size:10px;" class="label label-danger ">' . lang('admin') . '</small>';
            } else {
                $label = '<small style="font-size:10px;" class="label label-primary">' . lang('staff') . '</small>';
            }
            $today = time();
            $reply_day = strtotime($v_replies->time);
            ?>
<div class="col-sm-12 mb-mails" id="ticket-reply-container-<?php echo $v_replies->tickets_replies_id ?>">
                <img alt="Mail Avatar" src="<?php echo base_url() . $profile_info->avatar ?>"
                     class="mb-mail-avatar pull-left">
                <div
                    class="mb-mail-date pull-right"><?= time_ago($v_replies->time) ?>
                    <?php if ($v_replies->replierid == $this->session->userdata('user_id')) { ?>
                        <?php echo ajax_anchor(base_url("admin/tickets/delete/delete_ticket_replay/" . $v_replies->tickets_id . '/' . $v_replies->tickets_replies_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#ticket-reply-container-" . $v_replies->tickets_replies_id)); ?>
                    <?php } ?>
                </div>
                <div class="mb-mail-meta">
                    <div class="pull-left">
                        <div class="mb-mail-from"><a
                                href="<?= base_url() ?>admin/user/user_details/<?= $v_replies->replierid ?>"> <?= ($profile_info->fullname) . ' ' . $label ?></a>
                        </div>
                    </div>
                    <div
                        class="mb-mail-preview"><?php if (!empty($v_replies->body)) echo $v_replies->body; ?>
                    </div>
                    <div class="mb-mail-album pull-left">
                        <?php
                        $uploaded_file = json_decode($v_replies->attachment);
                        if (!empty($uploaded_file)) {
                            foreach ($uploaded_file as $v_files) {
                                if (!empty($v_files)) {
                                    ?>
                                    <a href="<?= base_url() ?>admin/projects/download_files/<?= $v_files->fileName . '/' . true ?>">
                                        <?php if ($v_files->is_image == 1) { ?>
                                            <img alt="" src="<?= base_url() . $v_files->path ?>">
                                        <?php } else { ?>
                                            <div class="mail-attachment-info">
                                                <a href="<?= base_url() ?>admin/tickets/index/download_file/<?= $v_replies->tickets_id . '/' . $v_files->fileName ?>"
                                                   class="mail-attachment-name"><i
                                                        class="fa fa-paperclip"></i> <?= $v_files->size ?> <?= lang('kb') ?>
                                                </a>

                                                <a href="<?= base_url() ?>admin/tickets/index/download_file/<?= $v_replies->tickets_id . '/' . $v_files->fileName ?>"
                                                   class="btn btn-default btn-xs pull-right"><i
                                                        class="fa fa-cloud-download"></i></a>

                                            </div>
                                        <?php } ?>
                                    </a>


                                    <?php
                                }
                            }
                        }
                        $comment_reply_type = 'tickets-reply';
                        $comment_reply_details = $this->db->where('ticket_reply_id', $v_replies->tickets_replies_id)->order_by('time', 'ASC')->get('tbl_tickets_replies')->result();
                        ?>
                    </div>
                    <button class="text-primary reply" data-toggle="tooltip"
                            data-placement="top" title="<?= lang('click_to_reply') ?>"
                            id="reply__<?php echo $v_replies->tickets_replies_id ?>"><i
                            class="fa fa-reply "></i> <?= lang('reply') ?>
                    </button>
                    <?php $this->load->view('admin/tickets/comments_reply', array('comment_reply_details' => $comment_reply_details)) ?>
                    <div class="reply__" id="reply_<?php echo $v_replies->tickets_replies_id?>">
                        <form id="<?php echo $comment_reply_type; ?>-comment-form-<?php echo $v_replies->tickets_replies_id ?>"
                              action="<?php echo base_url() ?>admin/tickets/save_comments_reply/<?php
                              if (!empty($v_replies->tickets_replies_id)) {
                                  echo $v_replies->tickets_replies_id;
                              }
                              ?>" method="post">
                            <input type="hidden" name="tickets_id" value="<?php
                            if (!empty($v_replies->tickets_id)) {
                                echo $v_replies->tickets_id;
                            }
                            ?>" class="form-control">
                            <div class="form-group mb-sm">
                                                    <textarea name="reply_comments" class="form-control reply_comments"
                                                              rows="3"></textarea>
                            </div>
                            <button type="submit" id="reply-<?php echo $v_replies->tickets_replies_id ?>"
                                    class="btn btn-xs btn-primary"><?= lang('save') ?></button>

                        </form>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $(".reply__").hide();
                            $("button.reply").click(function () {
                                var id = $(this).attr("id");
                                var sectionId = id.replace("reply__", "reply_");
                                $(".reply__").hide();
                                $("div#" + sectionId).fadeIn("fast");
                                $("div#" + sectionId).css("margin-top", "10" + "px");
                            });
                            $('#reply-<?php echo $v_replies->tickets_replies_id ?>').on('click', function (e) {
                                var ubtn = $(this);
                                ubtn.html('Please wait...');
                                ubtn.addClass('disabled');
                            });
                            $("#<?php echo $comment_reply_type; ?>-comment-form-<?php echo $v_replies->tickets_replies_id ?>").appForm({
                                isModal: false,
                                onSuccess: function (result) {
                                    $(".reply_comments").val("");
                                    $('#reply-<?php echo $v_replies->tickets_replies_id ?>').removeClass("disabled").html('<?= lang('save')?>');
                                    $(result.data).insertBefore("#reply_<?php echo $v_replies->tickets_replies_id ?>").last();
                                    toastr[result.status](result.message);
                                }
                            });

                        });
                    </script>
                </div>
            </div>
            <?php
        }
    endforeach; ?>
<?php endif; ?>