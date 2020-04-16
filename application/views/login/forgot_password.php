<p class="text-center pv"><?= lang('password_reset') ?></p>
<form data-parsley-validate="" novalidate="" action="<?php echo base_url() ?>login/forgot_password" method="post">
    <p class="text-center"><?= lang('fill_up_your_mail_to_recieve_instruction') ?></p>
    <div class="form-group has-feedback">
        <label for="resetInputEmail1" class="text-muted"><?= lang('email') ?>/<?= lang('username') ?></label>
        <input type="text" name="email_or_username" required="true" class="form-control"
               placeholder="<?= lang('email') ?>/<?= lang('username') ?>"/>
        <span class="fa fa-envelope form-control-feedback text-muted"></span>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <button type="submit" name="flag" value="1"
                    class="btn btn-danger btn-block btn-flat"><?= lang('submit') ?></button>
        </div><!-- /.col -->
        <div class="col-xs-8">
            <label class="btn pull-right"><a href="<?= base_url() ?>login"><?= lang('remember_password') ?></a></label>
        </div><!-- /.col -->
    </div>
</form>
<?php if (config_item('allow_client_registration') == 'TRUE') { ?>
    <p class="pt-lg text-center"><?= lang('do_not_have_an_account') ?></p>
    <a href="<?= base_url() ?>login/register" class="btn btn-block btn-default"><i
            class="fa fa-sign-in"></i> <?= lang('get_your_account') ?></a>
<?php } ?>
