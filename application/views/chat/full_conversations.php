<style>
    .active {
        background: #C8CAC9;
        color: #000;
    }
</style>
<?php $script = ""; ?>
<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?= lang('all_users') ?>
            </div>
            <div class="panel-body">
                <section>
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <?php
                        $profile = profile();
                        if ($profile->role_id == 2) {
                            $where = array('role_id !=' => '2', 'activated' => '1');
                        } else {
                            $where = array('activated' => '1');
                        }
                        $all_user_info = get_result('tbl_users', $where);
                        if (!empty($all_user_info)): foreach ($all_user_info as $v_user) :
                            $account_info = $this->chat_model->check_by(array('user_id' => $v_user->user_id), 'tbl_account_details');
                            if (!empty($account_info) && $account_info->user_id != $this->session->userdata('user_id')) {
                                ?>
                                <ul class="nav"><?php
                                    if ($v_user->role_id == 1) {
                                        $user = '<span class="text-sm text-danger">' . lang('admin') . '</span>';
                                    } elseif ($v_user->role_id == 3) {
                                        $user = '<span class="text-sm text-success">' . lang('staff') . '</span>';
                                    } else {
                                        $user = '<span class="text-sm text-warning">' . lang('client') . '</span>';
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_user->user_id == $user_id) {
                                        echo "active";
                                    }
                                    ?>">
                                        <a style="color:inherit" href="<?php echo base_url(); ?>chat/conversations/<?php echo $v_user->user_id ?>">
                                            <?= $account_info->fullname ?>
                                            <small><?= $user ?></small>
                                        </a></li>
                                </ul>
                                <?php
                            };
                        endforeach;
                        endif;
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <!-- DIRECT CHAT PRIMARY -->
        <div class="panel panel-custom direct-chat direct-chat-primary">
            <div class="panel-heading">
                <?php
                if (!empty($chats)) {
                    $title = $chats->title;
                    $id = 'conversation_chat_' . $chats->private_chat_id;
                } else {
                    $title = fullname($user_id);
                    $id = null;
                }
                ?>
                <h3 class="panel-title"><?= $title ?></h3>
            </div><!-- /.box-header -->
            <?php
            // get all message by private chat id
            $messages = array();
            if (!empty($all_messages)) {
                foreach ($all_messages as $message) {
                    array_push($messages, $message);
                }
                $messages = array_reverse($messages);
                $window_id = "conversation_chat_" . $chats->private_chat_id;
                $script .= '$("#' . $window_id . '").scrollTop($("#' . $window_id . '")[0].scrollHeight);';
                ?>
                <div class="panel-body chat conversation_chat" id="<?= $id ?>">

                    <?php

                    foreach ($messages as $message) : ?>
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
                        <?php } ?>
                    <?php endforeach; ?>
                </div>
            <?php } ?>
            <div class="panel-footer">
                <button type="submit" data-user_id="<?= $user_id ?>"
                        class="btn btn-primary btn-flat start_chat"><?= lang('start') . ' ' . lang('chat') ?>
                </button>
            </div><!-- /.box-footer-->
        </div><!--/.direct-chat -->
    </div><!-- /.col -->
</div>
<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            all_conversations(<?= $user_id ?>);
        }, interval_time);

        <?php echo $script ?>
    });
</script>
