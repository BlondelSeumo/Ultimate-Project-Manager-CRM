<?php $script = "";

?>
<?php foreach ($all_chats as $v_chat) : ?>
    <?php
    if (empty($v_chat->title)) {
        $v_chat->title = 'cool';
    }
    ?>
    <?php if ($v_chat->active == 0) : ?>
        <div class="chat_badge custom-bg" onclick="open_chat_box(<?php echo $v_chat->private_chat_id ?>)"
             id="open_chat_box_<?php echo $v_chat->private_chat_id ?>">
            <?php if ($v_chat->unread) : ?><span
                class="badge-chat small-text"><?php echo lang("new") ?></span><?php endif; ?> <?php echo $v_chat->title ?>
        </div>
    <?php elseif ($v_chat->active == 1) : ?>
        <?php
        // get all message by private chat id
        $messages = array();
        $limit = 5;
        $last_reply_id = 0;
        $all_messages = $this->chat_model->get_chat_messages($v_chat->private_chat_id, $v_chat->deleted);
        foreach ($all_messages as $message) {
            array_push($messages, $message);
            if ($last_reply_id == 0) {
                $last_reply_id = $message->private_chat_messages_id;
            }
        }
        $messages = array_reverse($messages);

        $window_id = "open_chat_" . $v_chat->private_chat_id;
        $script .= '$("#' . $window_id . '").scrollTop($("#' . $window_id . '")[0].scrollHeight);';
        ?>
        <div class="panel b0 mb0 chat_<?= $v_chat->to_user_id ?>" id="open_chat_box_<?php echo $v_chat->private_chat_id ?>">
            <div class="panel-heading custom-bg pt-sm ">
                <div class="">
                    <span class="chat_title"> <?php echo $v_chat->title ?></span>
                    <div class="pull-right chat-icon">
                        <!--                        <i data-toggle="tooltip" data-placement="top" title="-->
                        <? //= lang('add_more_to_chat') ?><!--"-->
                        <!--                           class="fa fa-plus" aria-hidden="true"></i>-->

                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"
                           title="<?= lang('settings') ?>"
                           href="#"> <i class="fa fa-cog" aria-hidden="true"></i>
                            <ul class="dropdown-menu chat-setting-dropdown animated zoomIn">
                                <li>
                                    <a href="<?= base_url('chat/conversations/' . $v_chat->to_user_id) ?>"><?= lang('full_conversation') ?></a>
                                </li>
                                <li><a data-toggle="modal" data-target="#myModal"
                                       href="<?= base_url('chat/change_title/' . $v_chat->private_chat_users_id) ?>"><?= lang('edit') . ' ' . lang('name') ?></a>
                                </li>
                                <li><a href="#"
                                       onclick="delete_chat_box(<?php echo $v_chat->private_chat_id ?>)"><?= lang('delete_conversation') ?></a>
                                </li>
                            </ul>
                        </a>
                        <i data-toggle="tooltip" data-placement="top"
                           onclick="minimize_chat_box(<?php echo $v_chat->private_chat_id ?>)"
                           title="<?= lang('minimize') ?>" class="fa fa-minus"></i>

                        <i data-toggle="tooltip" onclick="close_chat_box(<?php echo $v_chat->private_chat_id ?>)"
                           data-placement="top" title="<?= lang('close') ?>" class="fa fa-times" aria-hidden="true"></i>

                    </div>
                </div>
            </div>
            <div class="chat-body br bl"
                 id="open_chat_<?php echo $v_chat->private_chat_id ?>">
                <ul>
                    <?php foreach ($messages as $message) : ?>
                        <?php
                        if ($message->user_id == $this->session->userdata('user_id')) {
                            ?>
                            <li style="width:100%;">
                                <div class="message-right chat-message">
                                    <div class="text text-r"><p><?php echo $message->message ?></p>
                                        <p data-toggle="tooltip" data-placement="left"
                                           title="<?php echo strftime(config_item('date_format'), strtotime($message->message_time)) . lang('at') . display_time($message->message_time); ?>">
                                            <small><?= time_ago($message->message_time); ?></small>
                                        </p>
                                    </div>
                                    <div class="avatar" style="padding:0px 0px 0px 10px !important">
                                        <img class="img-circle" style="width:100%;"
                                             src="<?= base_url(staffImage($message->user_id)) ?>">
                                    </div>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li>
                                <div class="message chat-message">
                                    <div class="avatar">
                                        <img class="img-circle" style="width:100%;"
                                             src="<?= base_url(staffImage($message->user_id)) ?>">
                                    </div>
                                    <div class="text text-l"><p><?php echo $message->message ?></p>
                                        <p data-toggle="tooltip" data-placement="left"
                                           title="<?php echo strftime(config_item('date_format'), strtotime($message->message_time)) . lang('at') . display_time($message->message_time); ?>">
                                            <small><?= time_ago($message->message_time); ?></small>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        <?php }
                        ?>
                    <?php endforeach; ?>
                </ul>
                <input type="hidden" id="last_reply_id_<?php echo $v_chat->private_chat_id ?>"
                       value="<?php echo $last_reply_id ?>">
            </div>
            <div class="panel-footer b0 chat-input-box">
                <input class="form-control" required
                       onkeypress="return send_message(event, <?php echo $v_chat->private_chat_id ?>);"
                       name="reply" id="chat_input_message_<?php echo $v_chat->private_chat_id ?>"
                       placeholder="<?= lang('chat_input_placeholder') ?>">
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<script type="text/javascript">
    $(document).ready(function () {
        <?php echo $script ?>
    });
</script>
