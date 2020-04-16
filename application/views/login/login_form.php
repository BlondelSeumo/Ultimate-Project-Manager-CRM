<p class="text-center pv"><?= lang('sing_in_to_continue') ?></p>
<form data-parsley-validate="" novalidate="" action="<?php echo base_url() ?>login" method="post">
    <div class="form-group has-feedback">
        <input type="text" name="user_name" required="true" class="form-control" placeholder="<?= lang('username') ?>"/>
        <span class="fa fa-envelope form-control-feedback text-muted"></span>
    </div>
    <div class="form-group has-feedback">
        <input type="password" name="password" required="true" class="form-control"
               placeholder="<?= lang('password') ?>"/>
        <span class="fa fa-lock form-control-feedback text-muted"></span>
    </div>
    <div class="clearfix">
        <div class="checkbox c-checkbox pull-left mt0">
            <label>
                <input type="checkbox" value="" name="remember">
                <span class="fa fa-check"></span><?= lang('remember_me') ?></label>
        </div>
        <div class="pull-right"><a href="<?= base_url() ?>login/forgot_password"
                                   class="text-muted"><?= lang('forgot_password') ?></a>
        </div>
    </div>
    <?php if (config_item('recaptcha_secret_key') != '' && config_item('recaptcha_site_key') != '') { ?>
        <div class="g-recaptcha mb-lg mt-lg" data-sitekey="<?php echo config_item('recaptcha_site_key'); ?>"></div>
    <?php }
    $mark_attendance_from_login = config_item('mark_attendance_from_login');
    if (!empty($mark_attendance_from_login) && $mark_attendance_from_login == 'Yes') {
        $class = null;
    } else {
        $class = 'btn-block';
    }
    ?>
    <button type="submit" class="btn btn-primary <?= $class ?> btn-flat"><?= lang('sign_in') ?> <i
            class="fa fa-arrow-right"></i></button>
    <?php if (empty($class)) { ?>
        <button type="submit" name="mark_attendance" value="mark_attendance" class="btn btn-purple btn-flat pull-right">
            <i class="fa fa-clock-o"></i> <?= lang('mark_attendance') ?> </button>
    <?php } ?>
</form>
<?php if (config_item('allow_client_registration') == 'TRUE') { ?>
    <p class="pt-lg text-center"><?= lang('do_not_have_an_account') ?></p><a href="<?= base_url() ?>login/register"
                                                                             class="btn btn-block btn-default"><i
            class="fa fa-sign-in"></i> <?= lang('get_your_account') ?></a>
<?php } ?>