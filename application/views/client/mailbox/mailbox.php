<div class="row">
    <div class="col-md-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <h3 class="panel-title"><?= lang('all_messages') ?>

                </h3>
            </div>

            <div class="panel-body ">
                <ul class="nav nav-pills nav-stacked">
                    <li class="<?php echo ($menu_active == 'inbox') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>client/mailbox/index/inbox"> <i class="fa fa-inbox"></i>
                            <?= lang('inbox') ?>
                            <span class="label label-primary pull-right"><?php
                                if (!empty($unread_mail)) {
                                    echo $unread_mail;
                                } else {
                                    echo '0';
                                }
                                ?></span>
                        </a>
                    </li>
                    <li class="<?php echo ($menu_active == 'sent') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>client/mailbox/index/sent"> <i class="fa fa-envelope-o"></i>
                            <?= lang('sent') ?>
                        </a>
                    </li>
                    <li class="<?php echo ($menu_active == 'draft') ? 'active' : ''; ?>"><a
                            href="<?= base_url() ?>client/mailbox/index/draft"><i class="fa fa-file-text-o"></i>
                            Drafts</a></li>
                    <li class="<?php echo ($menu_active == 'favourites') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>client/mailbox/index/favourites"> <i
                                class="fa fa-star text-yellow"></i>
                            <?= lang('favourites') ?>
                        </a>
                    </li>
                    <li class="<?php echo ($menu_active == 'trash') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>client/mailbox/index/trash"> <i class="fa fa-trash-o"></i>
                            <?= lang('trash') ?><span class="label label-warning pull-right"><?php
                                $inbox_query = $this->db->where(array('to' => $this->session->userdata('email'), 'deleted' => 'Yes'))->get('tbl_inbox');
                                $totat_inbox = $inbox_query->num_rows();
                                $sent_query = $this->db->where(array('user_id' => $this->session->userdata('user_id'), 'deleted' => 'Yes'))->get('tbl_sent');
                                $totat_sent = $sent_query->num_rows();
                                $draft_query = $this->db->where(array('user_id' => $this->session->userdata('user_id'), 'deleted' => 'Yes'))->get('tbl_draft');
                                $tatal_draft = $draft_query->num_rows();
                                echo $totat_inbox + $totat_sent + $tatal_draft;
                                ?></span></a></li>

                </ul>
            </div><!-- /.box-body -->
        </div><!-- /. box -->
    </div><!-- /.col -->
    <div class="col-md-9">
        <?php echo message_box('success'); ?>
        <?php echo message_box('error'); ?>
        <?php $this->load->view('client/mailbox/' . $view) ?>
    </div><!-- /.col -->
