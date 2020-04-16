<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form test -->
    <div class="col-lg-12">
        <?php
        $email_error = $this->session->userdata('email_error');
        if (!empty($email_error)) {
            ?>
            <div class="panel panel-custom copyright-wrap" id="copyright-wrap">
                <div class="panel-heading">
                    Not Connected . Please Follow The instruction !
                    <button type="button" class="close" data-target="#copyright-wrap" data-dismiss="alert"><span
                            aria-hidden="true">×</span><span class="sr-only">Close</span>

                    </button>
                </div>
                <div class="panel-body">
                <pre>
                    <?= $email_error ?>
                </pre>
                </div>
            </div>
        <?php } ?>
        <form method="post" action="<?php echo base_url() ?>admin/settings/update_email" class="form-horizontal">
            <div class="panel panel-custom">
                <header class="panel-heading "><?= lang('email_settings') ?></header>
                <div class="panel-body">
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('company_email') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="email" required="" class="form-control"
                                   value="<?= $this->config->item('company_email') ?>" name="company_email"
                                   data-type="email" data-required="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('use_postmark') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="hidden" value="off" name="use_postmark"/>
                                    <input type="checkbox" <?php
                                    if (config_item('use_postmark') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="use_postmark" id="use_postmark">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div
                        id="postmark_config" <?php echo (config_item('use_postmark') != 'TRUE') ? 'style="display:none"' : '' ?>>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('postmark_api_key') ?></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" placeholder="xxxxx" name="postmark_api_key"
                                       value="<?= config_item('postmark_api_key') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('postmark_from_address') ?></label>
                            <div class="col-lg-6">
                                <input type="email" class="form-control" placeholder="xxxxx"
                                       name="postmark_from_address" value="<?= config_item('postmark_from_address') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('email_protocol') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select name="protocol" required="" class="form-control">
                                <?php $prot = config_item('protocol'); ?>
                                <option
                                    value="mail" <?= ($prot == "mail" ? ' selected="selected"' : '') ?>><?= lang('php_mail') ?></option>
                                <option
                                    value="smtp" <?= ($prot == "smtp" ? ' selected="selected"' : '') ?>><?= lang('smtp') ?></option>
                                <option
                                    value="sendmail" <?= ($prot == "sendmail" ? ' selected="selected"' : '') ?>><?= lang('sendmail') ?></option>
                            </select>
                        </div>
                    </div>
                    <?php $prot = config_item('protocol'); ?>
                    <div id="smtp_config">
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('smtp_host') ?> </label>
                            <div class="col-lg-6">
                                <input type="text" required="" class="form-control"
                                       value="<?= $this->config->item('smtp_host') ?>" name="smtp_host">
                                <span class="help-block  ">SMTP Server Address</strong>.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('smtp_user') ?></label>
                            <div class="col-lg-6">
                                <input type="text" required="" class="form-control"
                                       value="<?= $this->config->item('smtp_user') ?>" name="smtp_user">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('smtp_pass') ?></label>
                            <div class="col-lg-6">
                                <?php
                                $password = strlen(decrypt(config_item('smtp_pass')));
                                ?>
                                <input type="password" name="smtp_pass" placeholder="<?php
                                if (!empty($password)) {
                                    for ($p = 1; $p <= $password; $p++) {
                                        echo '*';
                                    }
                                } ?>" value="" class="form-control">
                                <strong id="show_password" class="required"></strong>
                            </div>
                            <div class="col-lg-3">
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url('admin/client/see_password/smtp') ?>"
                                   id="see_password"><?= lang('see_password') ?></a>
                                <strong id="hosting_password" class="required"></strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('smtp_port') ?></label>
                            <div class="col-lg-6">
                                <input type="text" required="" class="form-control"
                                       value="<?= $this->config->item('smtp_port') ?>" name="smtp_port">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('email_encryption') ?></label>
                            <div class="col-lg-3">
                                <select name="smtp_encryption" class="form-control">
                                    <?php $crypt = config_item('smtp_encryption'); ?>
                                    <option
                                        value=""<?= ($crypt == "" ? ' selected="selected"' : '') ?>><?= lang('none') ?></option>
                                    <option value="ssl"<?= ($crypt == "ssl" ? ' selected="selected"' : '') ?>>SSL
                                    </option>
                                    <option value="tls"<?= ($crypt == "tls" ? ' selected="selected"' : '') ?>>TLS
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('sent_test_email') ?></header>
    <div class="panel-body">
        <form method="post" action="<?php echo base_url() ?>admin/settings/sent_test_email" class="form-horizontal">
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('email') . ' ' . lang('address') ?></label>
                <div class="col-lg-6">
                    <input type="email" required="" class="form-control"
                           value="" name="test_email">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"></label>
                <div class="col-lg-6">
                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('sent_test_email') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
