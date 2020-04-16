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
<input type="hidden" id="last_reply_id_<?php echo $chat->private_chat_id ?>" value="<?php echo $last_reply_id ?>">
