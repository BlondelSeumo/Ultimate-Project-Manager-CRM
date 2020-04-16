<?php
$comment_reply_type = 'new-tickets-reply';
if (!empty($comment_reply_details)) {
    foreach ($comment_reply_details as $v_reply) {
        $r_profile_info = $this->db->where(array('user_id' => $v_reply->replierid))->get('tbl_account_details')->row();
        $reply_info = $this->db->where(array('ticket_reply_id' => $v_reply->ticket_reply_id))->get('tbl_tickets_replies')->row();
        ?>
<div id="<?php echo $comment_reply_type . "-comment-form-container-" . $v_reply->tickets_replies_id ?>"> <div class="col-sm-1"></div> <div class="mb-mails col-sm-11"><img alt="Mail Avatar" src="<?php echo base_url() . $r_profile_info->avatar ?>" class="mb-mail-avatar pull-left"> <div class="mb-mail-date pull-right"><?= time_ago($v_reply->time) ?>
                    <?php if ($v_reply->replierid == $this->session->userdata('user_id')) { ?>
                <?php echo ajax_anchor(base_url("admin/tickets/delete/delete_ticket_replay/" . $v_reply->tickets_id . '/' . $v_reply->tickets_replies_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#" . $comment_reply_type . "-comment-form-container-" . $v_reply->tickets_replies_id)); ?>
                    <?php } ?>
                </div>
                <div class="mb-mail-meta">
                    <div class="pull-left">
                        <div class="mb-mail-from"><a
                                href="<?= base_url() ?>admin/user/user_details/<?= $v_reply->replierid ?>"> <?= ($r_profile_info->fullname) ?></a>
                        </div>
                    </div>
                    <div
                        class="mb-mail-preview"><?php if (!empty($v_reply->body)) echo $v_reply->body; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}
?>