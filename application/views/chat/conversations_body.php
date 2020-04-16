<?php
// get all message by private chat id
$messages = array();
if (!empty($all_messages)) {
    foreach ($all_messages as $message) {
        array_push($messages, $message);
    }
//    $messages = array_reverse($messages);
    foreach ($all_messages as $message) : ?>
        <?php
        if ($message->user_id == $this->session->userdata('user_id')) {
            ?>
            <div class="direct-chat-msg right">
                <div class="direct-chat-info clearfix">
                            <span data-toggle="tooltip" data-placement="top"
                                  title="<?php echo strftime(config_item('date_format'), strtotime($message->message_time)) . ' ' . lang('at') . ' ' . display_time($message->message_time); ?>"
                                  class="direct-chat-timestamp pull-left"><?= time_ago($message->message_time); ?></span>
                </div>

                <img data-toggle="tooltip" data-placement="top"
                     title="<?= fullname($message->user_id) ?>" class="direct-chat-img"
                     src="<?= base_url(staffImage($message->user_id)) ?>"
                     alt="message user image"/><!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                    <?php echo $message->message ?>
                </div>
                <!-- /.direct-chat-text -->
            </div><!-- /.direct-chat-msg -->
        <?php } else { ?>
            <div class="direct-chat-msg">
                <div class="direct-chat-info clearfix">
                            <span data-toggle="tooltip" data-placement="top"
                                  title="<?php echo strftime(config_item('date_format'), strtotime($message->message_time)) . ' ' . lang('at') . ' ' . display_time($message->message_time); ?>"
                                  class="direct-chat-timestamp pull-right">
                                    <?= time_ago($message->message_time); ?>
                                </span>
                </div>
                <!-- /.direct-chat-info -->
                <img data-toggle="tooltip" data-placement="top"
                     title="<?= fullname($message->user_id) ?>" class="direct-chat-img"
                     src="<?= base_url(staffImage($message->user_id)) ?>"
                     alt="message user image"/><!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                    <?php echo $message->message ?>
                </div>
                <!-- /.direct-chat-text -->
            </div><!-- /.direct-chat-msg -->
        <?php }
        ?>
    <?php endforeach;
}
?>
