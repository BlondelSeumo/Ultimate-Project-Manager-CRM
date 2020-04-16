<?php echo message_box('success') ?>
<!-- =============== VENDOR STYLES ===============-->
<!-- FONT AWESOME-->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome/css/font-awesome.min.css">
<!-- SIMPLE LINE ICONS-->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/simple-line-icons/css/simple-line-icons.css">
<!-- =============== BOOTSTRAP STYLES ===============-->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" id="bscss">
<!-- =============== APP STYLES ===============-->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app.css" id="maincss">
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper">
            <div class="<?= ($this->uri->segment(1) == '404' ? 'abs-center ' : ' ') ?>wd-xl" style="margin: auto;">
                <!-- START panel-->
                <div class="text-center mb-xl">
                    <div class="text-lg mb-lg">404</div>
                    <p class="lead m0"><?= lang('we_could_not_found') ?></p>
                    <p><?= lang('page_not_exist') ?></p>
                </div>
                <div class="input-group mb-xl">
                    <input type="text" placeholder="Try with a search" class="form-control">
            <span class="input-group-btn">
               <button type="button" class="btn btn-default">
                   <em class="fa fa-search"></em>
               </button>
            </span>
                </div>
                <ul class="list-inline text-center text-sm mb-xl">
                    <li><a href="<?= base_url() ?>admin/dashboard" class="text-muted"><?= lang('go_to_app') ?></a>
                    </li>
                    <li class="text-muted">|</li>
                    <li><a href="<?= base_url() ?>login/logout" class="text-muted"><?= lang('login') ?></a>
                    </li>
                    <li class="text-muted">|</li>
                    <li><a href="<?= base_url() ?>login/register" class="text-muted"><?= lang('register') ?></a>
                    </li>
                </ul>
                <!-- END panel-->
                <div class="p-lg text-center">
                    <span>&copy;</span>
                    <span><a href="<?= config_item('copyright_url') ?>"> <?= config_item('copyright_name') ?></a></span>
                    <br/>
                    <span>2016-<?= date('Y')?></span>
                    <span>-</span>
                    <span><?= lang('version') . ' ' . config_item('version') ?></span>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
</div>