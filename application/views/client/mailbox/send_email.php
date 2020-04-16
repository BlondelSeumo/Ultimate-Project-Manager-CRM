<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <?php echo $read_mail->subject; ?>
                </div>
                <div class="mailbox-read-message text-justify margin">
                    <p><?php echo $read_mail->message_body; ?></p>
                </div><!-- /.mailbox-read-message -->
            </div><!-- /.box-body -->
        </div><!-- /. box -->
    </div><!-- /.col -->
    </div>
</section>