<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <!-- =============== VENDOR STYLES ===============-->
    <!-- FONT AWESOME-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome/css/font-awesome.min.css">
    <!-- SIMPLE LINE ICONS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/simple-line-icons/css/simple-line-icons.css">
    <!-- =============== BOOTSTRAP STYLES ===============-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" id="bscss">
    <!-- =============== APP STYLES ===============-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app.css" id="maincss">
</head>
<body>
<div class="wrapper " style="margin: 5% auto">
    <?php

    $user_id = $this->session->userdata('user_id');
    $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    ?>


    <div class="abs-center wd-xl">
        <div class="lockscreen-logo">
            <a href=""><span style="font-size: 20px;"><?= config_item('company_name') ?></span></a>
        </div>
        <?php
        $error = $this->session->flashdata('error');

        if (!empty($error)) {
            ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php } ?>
        <!-- START panel-->
        <div class="p">
            <img src="<?= base_url() . $profile_info->avatar ?>" alt="Avatar" width="60" height="60"
                 class="img-thumbnail img-circle center-block">
        </div>

        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center"><?= lang('login_to_unlock_screen') ?></p>
                <form data-parsley-validate="" novalidate=""
                      action="<?php echo base_url() ?>locked/check_login/<?= $this->session->userdata('user_name') ?>"
                      method="post">
                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" name="password" required="" placeholder="password"/>
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left mt-sm">
                            <a href="<?= base_url() ?>login/logout" class="text-muted">
                                <small><?= lang('sign_in_as_different_users') ?></small>
                            </a>
                        </div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('unlock')?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END panel-->
        <div class="p-lg text-center">
            <span>&copy;</span>
            <span><a href="<?= config_item('copyright_url') ?>"> <?= config_item('copyright_name') ?></a></span>
            <br/>
            <span>2015-<?= date('Y') ?></span>
            <span>-</span>
            <span><?= lang('version') . ' ' . config_item('version') ?></span>
        </div>
    </div>
</div>
