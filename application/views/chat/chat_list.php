<?php
$frontend = $this->uri->segment(1);
$mid = my_id();
if (!empty($mid) && $frontend != 'frontend') { ?>
    <div class="chat_frame">
        <?php include_once 'assets/plugins/chat/chat.php'; ?>
        <button type="button" class="btn btn-round custom-bg" id="open_chat_list"><span
                class="fa fa-comments"></span></button>
        <div class="panel b0" id="chat_list">
            <div class="panel-heading custom-bg">
                <div class="">
                    <?= lang('users') . ' ' . lang('list') ?>
                    <div class="pull-right chat-icon">
                        <i data-toggle="tooltip" data-placement="top" title="<?= lang('close') ?>" id="close_chat_list"
                           class="fa fa-times"
                           aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <ul class="nav b bt0">
                <li>
                    <?php
                    $users = $this->admin_model->get_online_users();
                    if (!empty($users)) {
                        foreach ($users as $key => $v_users) {
                            if (!empty($v_users)) {
                                foreach ($v_users as $v_user) {
                                    ?>
                                    <!-- START User status-->
                                    <a href="#" data-user_id="<?= $v_user->user_id ?>"
                                       class="media-box p pb-sm pt-sm bb mt0 start_chat">
                                        <?php
                                        if ($key == 'online') {
                                            ?>
                                            <span class="pull-right">
                                 <span class="circle circle-success circle-lg"></span>
                              </span>
                                        <?php } else {
                                            ?>
                                            <span class="pull-right">
                                 <span class="circle circle-warning circle-lg"></span>
                              </span>
                                        <?php } ?>
                                        <span class="pull-left">
                                 <!-- Contact avatar-->
                                 <img
                                     src="<?= base_url(staffImage($v_user->user_id)) ?>"
                                     alt="Image" class="media-box-object img-circle thumb48">
                              </span>
                                        <!-- Contact info-->
                              <span class="media-box-body">
                                 <span class="media-box-heading">
                                    <strong class="text-sm"><?= fullname($v_user->user_id) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <span class="pull-left">
                                        <?= designation($v_user->user_id) ?></span>
                                        <span class="pull-right"><?php
                                            if(!empty($v_user->online_time)){
                                                echo time_ago($v_user->online_time);
                                            }else{
                                                echo lang('never');
                                            }?></span>
                                    </small>
                                 </span>
                              </span>
                                    </a>
                                    <?php
                                }
                            }
                        }
                    } ?>
                </li>
            </ul>
        </div>
        <div id="chat_box"></div>
        <audio id="chat-tune" controls="">
            <source src="<?= base_url() ?>assets/plugins/chat/chat_tune.mp3" type="audio/mpeg">
        </audio>
    </div><!--End live_chat_section-->
<?php } ?>
