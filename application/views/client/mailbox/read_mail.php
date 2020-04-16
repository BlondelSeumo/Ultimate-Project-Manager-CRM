<style>
    .text-justify {
        text-align: justify;
    }
    .col-md-12 {
        width: 100%;
    }
    .navbar-custom-nav, .panel-custom {
        box-shadow: 0 3px 12px 0 rgba(0,0,0,.15);
    }
    .panel {
        margin-bottom: 21px;
        background-color: #fff;
        border: 1px solid transparent;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }
    .panel-custom .panel-heading {
        border-bottom: 2px solid #564aa3;
    }
    .panel-custom .panel-heading {
        margin-bottom: 5px;
    }
    .panel .panel-heading {
        border-bottom: 0;
        font-size: 14px;
    }
    .panel-heading {
        padding: 10px 15px;
        border-bottom: 1px solid transparent;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px;
    }
    .mailbox-attachments {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .mt {
        margin-top: 10px!important;
    }
    .mailbox-attachments li {
        float: left;
        width: 140px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        margin-right: 10px;
    }
    .mailbox-attachment-icon.has-img {
        padding: 0;
    }

    .mailbox-attachment-icon {
        text-align: center;
        font-size: 65px;
        color: #666;
        padding: 10px;
    }
    .mailbox-attachment-icon, .mailbox-attachment-info, .mailbox-attachment-size {
        display: block;
    }
    .mailbox-attachment-info {
        padding: 10px;
        background: #f4f4f4;
    }
    .mailbox-attachment-name {
        font-weight: 700;
        color: #666;
        word-wrap: break-word;
    }
    .btn, :focus, a {
        outline: 0!important;
    }
    .mailbox-attachment-size {
        color: #999;
        font-size: 12px;
    }
</style>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <?php echo $read_mail->subject; ?>
                </div>
                <div class="panel-body mt0 pt0">
                    <div class="mailbox-read-info">
                        <?php if (!empty($reply)) { ?>
                            <div class="pull-right">
                                <a href="<?= base_url() ?>client/mailbox/index/compose/<?= $read_mail->inbox_id ?>/reply"
                                   class="btn btn-primary btn-sm" data-toggle="tooltip" title=""
                                   data-original-title="Reply"><i class="fa fa-reply"></i></a>
                                <a href="<?= base_url() ?>client/mailbox/delete_inbox_mail/<?= $read_mail->inbox_id ?>"
                                   class="btn btn-danger btn-sm" data-toggle="tooltip" title=""
                                   data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
                            </div>
                            <h5>From: <?php echo $read_mail->from; ?></h5>
                        <?php } else { ?>
                            <h5>To: <?php echo $read_mail->to; ?></h5>
                        <?php } ?>
                        <h5><span
                                class="mailbox-read-time"><?php echo date('d M , Y h:i:A', strtotime($read_mail->message_time)) ?></span>
                        </h5>
                    </div><!-- /.mailbox-read-info -->
                    <div class="mailbox-read-message text-justify margin">
                        <p><?php echo $read_mail->message_body; ?></p>
                    </div><!-- /.mailbox-read-message -->
                </div><!-- /.box-body -->
                <ul class="mailbox-attachments clearfix mt">
                    <?php
                    $uploaded_file = json_decode($read_mail->attach_file);
                    if (!empty($uploaded_file)):
                        foreach ($uploaded_file as $v_files):
                            if (!empty($v_files)):
                                ?>
                                <li>
                                    <?php if (!empty($v_files->is_image) && $v_files->is_image == 1) : ?>
                                        <span class="mailbox-attachment-icon has-img"><img
                                                src="<?= base_url() . $v_files->path ?>"
                                                alt="Attachment"></span>
                                    <?php else : ?>
                                        <span class="mailbox-attachment-icon"><i
                                                class="fa fa-file-pdf-o"></i></span>
                                    <?php endif; ?>
                                    <div class="mailbox-attachment-info">
                                        <a target="_blank" href="<?php echo base_url() . $read_mail->attach_file; ?>"
                                           class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                            <?= $v_files->fileName ?></a>
                        <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> <?= lang('kb') ?>
                            <a href="<?= base_url() ?>client/mailbox/download_file/<?= $v_files->fileName ?>"
                               class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                                    </div>
                                </li>
                                <?php
                            endif;
                        endforeach;
                    endif;
                    ?>
                </ul>
            </div><!-- /. box -->
        </div><!-- /.col -->
    </div>
</section>