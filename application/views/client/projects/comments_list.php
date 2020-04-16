<?php
$comment_type = 'projects';
$comment_reply_type = 'projects-reply';
if (!empty($comment_details)) {
    foreach ($comment_details as $key => $v_comment) {
        $user_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_users')->row();
        $profile_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_account_details')->row();
        ?>
<div class="mb-mails col-sm-12" id="<?php echo $comment_type . "-comment-form-container-".$v_comment->task_comment_id ?>"><img alt="Mail Avatar" src="<?php echo base_url() . $profile_info->avatar ?>" class="mb-mail-avatar pull-left"><div class="mb-mail-date pull-right"><?= time_ago($v_comment->comment_datetime) ?>
                <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                    <?php echo ajax_anchor(base_url("client/projects/delete_comments/$v_comment->task_comment_id"), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#" . $comment_type . "-comment-form-container-".$v_comment->task_comment_id)); ?>
                <?php } ?>
            </div>
            <div class="mb-mail-meta">
                <div class="pull-left">
                    <div class="mb-mail-from"><a
                            href="#"> <?= ($profile_info->fullname) ?></a>
                    </div>
                </div>
                <div
                    class="mb-mail-preview"><?php if (!empty($v_comment->comment)) echo $v_comment->comment; ?>
                </div>
                <div class="mb-mail-album pull-left">
                    <?php
                    $uploaded_file = json_decode($v_comment->comments_attachment);
                    if (!empty($uploaded_file)) {
                        foreach ($uploaded_file as $v_files) {
                            if (!empty($v_files)) {
                                ?>
                                <a href="<?= base_url() ?>client/projects/download_files/<?= $v_files->fileName . '/' . true ?>">
                                    <?php if ($v_files->is_image == 1) { ?>
                                        <img alt="" src="<?= base_url() . $v_files->path ?>">
                                    <?php } else { ?>
                                        <div class="mail-attachment-info">
                                            <a href="<?= base_url() ?>client/projects/download_files/<?= $v_files->fileName . '/' . true ?>"
                                               class="mail-attachment-name"><i
                                                    class="fa fa-paperclip"></i> <?= $v_files->size ?> <?= lang('kb') ?>
                                            </a>

                                            <a href="<?= base_url() ?>client/projects/download_files/<?= $v_files->fileName . '/' . true ?>"
                                               class="btn btn-default btn-xs pull-right"><i
                                                    class="fa fa-cloud-download"></i></a>

                                        </div>
                                    <?php } ?>
                                </a>


                                <?php
                            }
                        }
                    }
                    $comment_reply_details = $this->db->where('comments_reply_id', $v_comment->task_comment_id)->order_by('comment_datetime', 'ASC')->get('tbl_task_comment')->result();
                    ?>
                </div>
                <button class="text-primary reply" data-toggle="tooltip"
                        data-placement="top" title="<?= lang('click_to_reply') ?>"
                        id="reply__<?php echo $v_comment->task_comment_id ?>"><i
                        class="fa fa-reply "></i> <?= lang('reply') ?>
                </button>
                <?php $this->load->view('client/projects/comments_reply', array('comment_reply_details' => $comment_reply_details)) ?>
                <div class="reply__" id="reply_<?php echo $v_comment->task_comment_id ?>">
                    <form id="<?php echo $comment_reply_type; ?>-comment-form-<?php echo $v_comment->task_comment_id ?>"
                          action="<?php echo base_url() ?>client/projects/save_comments_reply/<?php
                          if (!empty($v_comment->task_comment_id)) {
                              echo $v_comment->task_comment_id . '/' . $v_comment->user_id;
                          }
                          ?>" method="post">
                        <input type="hidden" name="project_id" value="<?php
                        if (!empty($v_comment->project_id)) {
                            echo $v_comment->project_id;
                        }
                        ?>" class="form-control">
                        <div class="form-group mb-sm">
                                                    <textarea name="reply_comments" class="form-control reply_comments"
                                                              rows="3"></textarea>
                        </div>
                        <button type="submit" id="reply-<?php echo $v_comment->task_comment_id ?>"
                                class="btn btn-xs btn-primary"><?= lang('save') ?></button>

                    </form>
                </div>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#reply-<?php echo $v_comment->task_comment_id ?>').on('click', function (e) {
                            var ubtn = $(this);
                            ubtn.html('Please wait...');
                            ubtn.addClass('disabled');
                        });
                        $("#<?php echo $comment_reply_type; ?>-comment-form-<?php echo $v_comment->task_comment_id ?>").appForm({
                            isModal: false,
                            onSuccess: function (result) {
                                $(".reply_comments").val("");
                                $('#reply-<?php echo $v_comment->task_comment_id ?>').removeClass("disabled").html('<?= lang('save')?>');
                                $(result.data).insertBefore("#reply_<?php echo $v_comment->task_comment_id ?>").last();
                                toastr[result.status](result.message);
                            }
                        });
                    });
                </script>

            </div>
        </div>
    <?php } ?>
<?php } ?>
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
    });
</script>
