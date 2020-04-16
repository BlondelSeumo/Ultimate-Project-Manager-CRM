<p class="text-center pv"><?= lang('sing_up_to_get_access') ?></p>
<form method="post" data-parsley-validate="" novalidate="" action="<?= base_url() ?>login/registered_user">

    <div class="form-group has-feedback">
        <label for="signupInputEmail1" class="text-muted"><?= lang('company_name') ?></label>
        <input type="text" name="name" required="true" class="form-control"
               placeholder="<?= lang('company_name') ?>">
        <span class="fa fa-male form-control-feedback text-muted"></span>
    </div>
    <div class="form-group has-feedback">
        <label for="signupInputEmail1" class="text-muted"><?= lang('company_email') ?></label>
        <input type="email" name="email" required="true" class="form-control"
               placeholder="<?= lang('company_email') ?>">
        <span class="fa fa-envelope form-control-feedback text-muted"></span>
    </div>
    <div class="form-group has-feedback">
        <label for="signupInputEmail1" class="text-muted"><?= lang('language') ?></label>
        <select name="language" class="form-control"
                style="width: 100%">
            <?php
            $languages = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
            if (!empty($languages)) {
                foreach ($languages as $lang) : ?>
                    <option
                        value="<?= $lang->name ?>"<?php
                    if (!empty($client_info->language) && $client_info->language == $lang->name) {
                        echo 'selected';
                    } elseif (empty($client_info->language) && $this->config->item('language') == $lang->name) {
                        echo 'selected';
                    } ?>
                    ><?= ucfirst($lang->name) ?></option>
                <?php endforeach;
            } else {
                ?>
                <option
                    value="<?= $this->config->item('language') ?>"><?= ucfirst($this->config->item('language')) ?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="form-group has-feedback">
        <label for="signupInputEmail1" class="text-muted"><?= lang('username') ?></label>
        <input type="text" name="username" required="true" class="form-control"
               placeholder="<?= lang('username') ?>">
        <span class="fa fa-user form-control-feedback text-muted"></span>
    </div>
    <div class="form-group has-feedback">
        <label for="signupInputPassword1" class="text-muted"><?= lang('password') ?></label>
        <input type="password" id="password" placeholder="<?= lang('password') ?>" required="true" class="form-control"
               name="password">
        <span class="fa fa-lock form-control-feedback text-muted"></span>
    </div>
    <div class="form-group has-feedback">
        <label for="signupInputRePassword1" class="text-muted"><?= lang('confirm_password') ?></label>
        <input id="signupInputRePassword1" data-parsley-equalto="#password" type="password" placeholder="<?= lang('confirm_password') ?>"
               required="true" class="form-control" value="" name="confirm_password">
        <span class="fa fa-lock form-control-feedback text-muted"></span>
    </div>
    <button type="submit" class="btn btn-block btn-primary mt-lg"><?= lang('sign_up') ?></button>
</form>
<p class="pt-lg text-center"><?= lang('already_have_an_account') ?></p><a href="<?= base_url() ?>login"
                                                                          class="btn btn-block btn-default"><?= lang('sign_in') ?></a>
