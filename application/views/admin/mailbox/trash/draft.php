<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php echo base_url() ?>admin/mailbox/delete_mail/draft/trash">
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <div class="mailbox-controls">

                        <!-- Check all button -->
                        <div class="mail_checkbox mr-sm">
                            <input type="checkbox" id="parent_present">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-default btn-xs mr-sm"><i class="fa fa-trash-o"></i></button>
                        </div><!-- /.btn-group -->
                        <a href="#" onClick="history.go(0)" class="btn btn-default btn-xs mr-sm"><i
                                class="fa fa-refresh"></i></a>
                        <a href="<?php echo base_url() ?>admin/mailbox/index/compose"
                           class="btn btn-danger btn-xs mr-sm">Compose
                            +</a>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive mailbox-messages">
                        <!-- p.lead.text-centerNo mails here-->
                        <table class="table table-hover mb-mails">
                            <tbody>
                            <?php if (!empty($draft_message)):foreach ($draft_message as $v_draft_msg): ?>
                                <tr>
                                    <td>
                                        <div class="checkbox c-checkbox">
                                            <label>
                                                <input class="child_present" type="checkbox" name="selected_id[]"
                                                       value="<?php echo $v_draft_msg->draft_id; ?>"/>
                                                <span class="fa fa-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="mb-mail-date pull-right"><?= time_ago($v_draft_msg->message_time); ?>
                                            <a class="btn btn-primary btn-xs"
                                               href="<?php echo base_url() ?>admin/mailbox/restore/draft/<?php echo $v_draft_msg->draft_id ?>"
                                               data-toggle="tooltip" data-placement="top" title="Restore"><i
                                                    class="fa fa-retweet"></i></a>
                                            <a class="btn btn-danger btn-xs"
                                               href="<?php echo base_url() ?>admin/mailbox/delete_mail/draft/deleted/<?php echo $v_draft_msg->draft_id ?>"
                                               onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
                                               data-toggle="tooltip" data-placement="top" title="Permanent&nbsp;Delete"><i
                                                    class="fa fa-trash-o"></i></a>
                                        </div>
                                        <?php
                                        $subject = (strlen($v_draft_msg->subject) > 50) ? strip_html_tags(mb_substr($v_draft_msg->subject, 0, 50)) . '...' : $v_draft_msg->subject;
                                        ?>
                                        <div class="mb-mail-meta">
                                            <div class="pull-left">
                                                <div class="mb-mail-subject"><a
                                                        href="<?php echo base_url() ?>admin/mailbox/index/compose/<?php echo $v_draft_msg->draft_id ?>"><?= $subject ?></a>
                                                </div>
                                                <div class="mb-mail-from"><?php
                                                    $email_address = unserialize($v_draft_msg->to);
                                                    $total_email = count($email_address);
                                                    if ($total_email > 1) {
                                                        $deduct = "$total_email" - 1;
                                                        echo $email_address[0] . ' , (' . $deduct . ')';
                                                    } else {
                                                        echo $email_address[0];
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="mb-mail-preview"><?php
                                                $body = (strlen($v_draft_msg->message_body) > 100) ? strip_html_tags(mb_substr($v_draft_msg->message_body, 0, 100)) . '...' : $v_draft_msg->message_body;
                                                echo $body;
                                                $uploaded_file = json_decode($v_draft_msg->attach_file);
                                                if (!empty($uploaded_file)):
                                                    ?>
                                                    <small class="block"><i class="fa fa-paperclip"></i></small>
                                                    <?php
                                                endif;
                                                ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td><strong>There is no email to display</strong></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table><!-- /.table -->
                    </div><!-- /.mail-box-messages -->
                </div><!-- /.box-body -->

            </div><!-- /. box -->
        </form>
    </div><!-- /.col -->
</div><!-- /.row -->
