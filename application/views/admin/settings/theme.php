<?php echo message_box('success') ?>
<?php echo message_box('error') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form action="<?php echo base_url() ?>admin/settings/save_theme" enctype="multipart/form-data"
              class="form-horizontal" method="post">
            <div class="panel panel-custom">
                <header class="panel-heading  "><?= lang('theme_settings') ?></header>
                <div class="panel-body">
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('site_name') ?></label>
                        <div class="col-lg-7">
                            <input type="text" name="website_name" class="form-control"
                                   value="<?= config_item('website_name') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('logo') ?></label>
                        <div class="col-lg-4">
                            <select name="logo_or_icon" class="form-control">
                                <?php $logoicon = config_item('logo_or_icon'); ?>
                                <option
                                    value="logo_title"<?= ($logoicon == "logo_title" ? ' selected="selected"' : '') ?>><?= lang('logo') ?>
                                    & <?= lang('site_name') ?></option>
                                <option
                                    value="logo"<?= ($logoicon == "logo" ? ' selected="selected"' : '') ?>><?= lang('logo') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('active_pre_loader') ?></label>
                        <div class="col-lg-2 checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" <?php
                                $active_pre_loader = config_item('active_pre_loader');
                                if (!empty($active_pre_loader) && $active_pre_loader == 1) {
                                    echo 'checked';
                                }
                                ?> value="1" name="active_pre_loader">
                                <span class="fa fa-check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('active_custom_color') ?></label>
                        <div class="col-lg-2 checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" <?php
                                $custom_color = config_item('active_custom_color');
                                if (!empty($custom_color) && $custom_color == 1) {
                                    echo 'checked';
                                }
                                ?> value="1" name="active_custom_color" id="active_custom_color">
                                <span class="fa fa-check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="custom-color">
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <!--- Header Custom Color Start---->
                            <div class="col-md-3">
                                <label><?= lang('navbar_logo_background') ?></label>
                                <input id="navbar_logo_background" name="navbar_logo_background" type="text"
                                       class="form-control colorpickerinput"
                                       value="<?= config_item('navbar_logo_background'); ?>"/>
                        <span class="navbar_logo_background color-previewer"
                              style="background-color:<?= config_item('navbar_logo_background'); ?>"></span>
                            </div>
                            <div class="col-md-3">
                                <label><?= lang('top_bar_background') ?></label>
                                <input id="top_bar_background" name="top_bar_background" type="text"
                                       class="form-control colorpickerinput"
                                       value="<?= config_item('top_bar_background'); ?>"/>
                        <span class="top_bar_background color-previewer"
                              style="background-color:<?= config_item('top_bar_background'); ?>"></span>
                            </div>
                            <div class="col-md-3">
                                <label><?= lang('top_bar_color') ?></label>
                                <input id="top_bar_color" name="top_bar_color" type="text"
                                       class="form-control colorpickerinput"
                                       value="<?= config_item('top_bar_color'); ?>"/>
                        <span class="top_bar_color color-previewer"
                              style="background-color:<?= config_item('top_bar_color'); ?>"></span>
                            </div>
                        </div>
                        <!--- Header Custom Color End---->
                        <!--- Sidebar Custom Color Start---->
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="sidebar-custom-color">
                                <div class="col-md-3">
                                    <label><?= lang('sidebar_background') ?></label>
                                    <input id="sidebar_background" name="sidebar_background" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('sidebar_background'); ?>"/>
                            <span class="sidebar_background color-previewer"
                                  style="background-color:<?= config_item('sidebar_background'); ?>"></span>
                                </div>

                                <div class="col-md-3">
                                    <label><?= lang('sidebar_color') ?></label>
                                    <input id="sidebar_color" name="sidebar_color" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('sidebar_color'); ?>"/>
                        <span class="sidebar_color color-previewer"
                              style="background-color:<?= config_item('sidebar_color'); ?>"></span>
                                </div>

                                <div class="col-md-3">
                                    <label><?= lang('sidebar_active_background') ?></label>
                                    <input id="sidebar_active_background" name="sidebar_active_background" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('sidebar_active_background'); ?>"/>
                        <span class="sidebar_active_background color-previewer"
                              style="background-color:<?= config_item('sidebar_active_background'); ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="sidebar-custom-color">
                                <div class="col-md-3">
                                    <label><?= lang('sidebar_active_color') ?></label>
                                    <input id="sidebar_active_color" name="sidebar_active_color" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('sidebar_active_color'); ?>"/>
                            <span class="sidebar_active_color color-previewer"
                                  style="background-color:<?= config_item('sidebar_active_color'); ?>"></span>
                                </div>

                                <div class="col-md-6">
                                    <label><?= lang('submenu_open_background') ?></label>
                                    <input id="submenu_open_background" name="submenu_open_background" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('submenu_open_background'); ?>"/>
                        <span class="submenu_open_background color-previewer"
                              style="background-color:<?= config_item('submenu_open_background'); ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="sidebar-custom-color">
                                <div class="col-md-3">
                                    <label><?= lang('active_background') ?></label>
                                    <input id="active_background" name="active_background" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('active_background'); ?>"/>
                        <span class="active_background color-previewer"
                              style="background-color:<?= config_item('active_background'); ?>"></span>
                                </div>
                                <div class="col-md-3">
                                    <label><?= lang('active_color') ?></label>
                                    <input id="active_color" name="active_color" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('active_color'); ?>"/>
                        <span class="active_color color-previewer"
                              style="background-color:<?= config_item('active_color'); ?>"></span>
                                </div>
                                <div class="col-md-3">
                                    <label><?= lang('body_background') ?></label>
                                    <input id="body_background" name="body_background" type="text"
                                           class="form-control colorpickerinput"
                                           value="<?= config_item('body_background'); ?>"/>
                        <span class="body_background color-previewer"
                              style="background-color:<?= config_item('body_background'); ?>"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('company_logo') ?></label>
                        <div class="col-lg-7">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 210px;">
                                    <?php if (config_item('company_logo') != '') : ?>
                                        <img src="<?php echo base_url() . config_item('company_logo'); ?>">
                                    <?php else: ?>
                                        <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">
                                    <?php endif; ?>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="company_logo" value="upload"
                                                   data-buttonText="<?= lang('choose_file') ?>" id="myImg"/>
                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists"
                                           data-dismiss="fileinput"><?= lang('remove') ?></a>

                                </div>

                                <div id="valid_msg" style="color: #e11221"></div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('favicon') ?></label>
                        <div class="col-lg-7">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 210px;">
                                    <?php if (config_item('favicon') != '') : ?>
                                        <img src="<?php echo base_url() . config_item('favicon'); ?>">
                                    <?php else: ?>
                                        <img src="http://placehold.it/16x16" alt="Please Connect Your Internet">
                                    <?php endif; ?>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="favicon" value="upload"
                                                   data-buttonText="<?= lang('choose_file') ?>" id="myImg"/>
                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists"
                                           data-dismiss="fileinput"><?= lang('remove') ?></a>

                                </div>

                                <div id="valid_msg" style="color: #e11221"></div>

                            </div>
                        </div>
                    </div>
                    <?php
                    $lbg = config_item('login_background');
                    if (!empty($lbg)) {
                        $login_background = _mime_content_type($lbg);
                        $login_background = explode('/', $login_background);
                    }
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('login_background') ?></label>
                        <div class="col-lg-7">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 210px;height: 150px">
                                    <?php if (!empty($login_background[0]) && $login_background[0] == 'video') { ?>
                                        <video style="width: 100%;min-height: 100%" autoplay="autoplay" muted="muted"
                                               preload="auto" loop>
                                            <source
                                                src="<?php echo base_url() . config_item('login_background'); ?>"
                                                type="video/webm">
                                        </video>
                                    <?php } ?>
                                    <?php if (!empty($login_background[0]) && $login_background[0] == 'image') {
                                        ?>
                                        <img src="<?php echo base_url() . config_item('login_background'); ?>">
                                    <?php } ?>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="login_background" value="upload"
                                                   data-buttonText="<?= lang('choose_file') ?>" id="myImg"/>
                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists"
                                           data-dismiss="fileinput"><?= lang('remove') ?></a>

                                </div>
                                <div id="valid_msg" style="color: #e11221">You can add video/image</div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('login_position') ?></label>
                        <div class="col-lg-4">
                            <select name="login_position" class="form-control">
                                <?php $login_position = config_item('login_position'); ?>
                                <option
                                    value="left"<?= ($login_position == "left" ? ' selected="selected"' : '') ?>><?= lang('left') ?></option>
                                <option
                                    value="right" <?= ($login_position == "right" ? ' selected="selected"' : '') ?>><?= lang('right') ?></option>
                                <option
                                    value="center" <?= ($login_position == "center" ? ' selected="selected"' : '') ?>><?= lang('center') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('sidebar_theme') ?></label>
                        <div class="col-lg-9" id="app-settings">
                            <?php $theme = config_item('sidebar_theme'); ?>
                            <div class="mb">
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-info.css">
                                            <input type="radio"
                                                   name="sidebar_theme"
                                                   value="bg-info" <?= $theme == 'bg-info' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-info"></span>
                                       <span class="color bg-info-light"></span>
                                    </span>
                                            <span class="color bg-white"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-green.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-green" <?= $theme == 'bg-green' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-green"></span>
                                       <span class="color bg-green-light"></span>
                                    </span>
                                            <span class="color bg-white"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-purple.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-purple" <?= $theme == 'bg-purple' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-purple"></span>
                                       <span class="color bg-purple-light"></span>
                                    </span>
                                            <span class="color bg-white"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-danger.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-danger" <?= $theme == 'bg-danger' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-danger"></span>
                                       <span class="color bg-danger-light"></span>
                                    </span>
                                            <span class="color bg-white"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-info-dark.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-info-dark" <?= $theme == 'bg-info-dark' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-info-dark"></span>
                                       <span class="color bg-info"></span>
                                    </span>
                                            <span class="color bg-gray-dark"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-green-dark.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-green-dark" <?= $theme == 'bg-green-dark' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-green-dark"></span>
                                       <span class="color bg-green"></span>
                                    </span>
                                            <span class="color bg-gray-dark"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-purple-dark.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-purple-dark" <?= $theme == 'bg-purple-dark' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-purple-dark"></span>
                                       <span class="color bg-purple"></span>
                                    </span>
                                            <span class="color bg-gray-dark"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col mb">
                                    <div class="setting-color">
                                        <label data-load-css="<?php echo base_url(); ?>assets/css/bg-danger-dark.css">
                                            <input type="radio" name="sidebar_theme"
                                                   value="bg-danger-dark" <?= $theme == 'bg-danger-dark' ? 'checked' : null ?>>
                                            <span class="icon-check"></span>
                                    <span class="split">
                                       <span class="color bg-danger-dark"></span>
                                       <span class="color bg-danger"></span>
                                    </span>
                                            <span class="color bg-gray-dark"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('layout') ?></label>
                        <div class="col-lg-7">
                            <div class="p">
                                <div class="clearfix">
                                    <p class="pull-left">Horizontal</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-float" type="checkbox" name="layout-h" value="layout-h"
                                                   data-toggle-state="layout-h" <?= config_item('layout-h') == 'layout-h' ? 'checked' : null ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <p class="pull-left">Fixed</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-fixed" name="layout-fixed" value="layout-fixed"
                                                   type="checkbox"
                                                   data-toggle-state="layout-fixed" <?= config_item('layout-fixed') == 'layout-fixed' ? 'checked' : null ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <p class="pull-left">Boxed</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-boxed" name="layout-boxed" value="layout-boxed"
                                                   type="checkbox"
                                                   data-toggle-state="layout-boxed" <?= config_item('layout-boxed') == 'layout-boxed' ? 'checked' : null ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <p class="pull-left">Collapsed</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-collapsed" type="checkbox" name="aside-collapsed"
                                                   value="aside-collapsed"
                                                   data-toggle-state="aside-collapsed" <?= config_item('aside-collapsed') == 'aside-collapsed' ? 'checked' : null ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <p class="pull-left">Float</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-float" type="checkbox" name="aside-float" value="aside-float"
                                                   data-toggle-state="aside-float" <?= config_item('aside-float') == 'aside-float' ? 'checked' : null ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <p class="pull-left">Show Scrollbar</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-hover" type="checkbox" name="show-scrollbar"
                                                   value="show-scrollbar"
                                                   data-toggle-state="show-scrollbar" <?= config_item('show-scrollbar') == 'show-scrollbar' ? 'checked' : null ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <p class="text-danger pull-left">RTL</p>
                                    <div class="pull-right">
                                        <label class="switch">
                                            <input id="chk-rtl"
                                                   name="RTL" <?= config_item('RTL') == 'on' ? 'checked' : null ?>
                                                   type="checkbox">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- Sidebar Custom Color Start---->
                <div class="form-group">
                    <label class="col-lg-3 control-label"></label>
                    <div class="col-lg-4">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<link href="<?php echo base_url() ?>assets/plugins/bootstrap-colorpicker/bootstrap-colorpicker.css"
      rel="stylesheet">
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script>
    $(document).ready(function () {
        $(".custom-color").hide();
        <?php
        if (!empty($custom_color) && $custom_color == 1) {?>
        $(".custom-color").show();
        <?php } ?>

    });
    $(function () {
        $('#active_custom_color').click(function () {
            if ($(this).prop('checked')) {
                $(".custom-color").slideDown(200);
            } else {
                $(".custom-color").slideUp(200);
            }
        });
        var colors = {
            '#161b1f': '#161b1f',
            '#d8dce3': '#d8dce3',
            '#11a7db': '#11a7db',
            '#2aa96b': '#2aa96b',
            '#5bc0de': '#5bc0de',
            '#f0ad4e': '#f0ad4e',
            '#ed5564': '#ed5564'
        };
        var sliders = {
            saturation: {
                maxLeft: 200,
                maxTop: 200
            },
            hue: {
                maxTop: 200
            },
            alpha: {
                maxTop: 200
            }
        };
        $('.colorpickerinput').colorpicker({
            customClass: 'colorpicker-2x',
            colorSelectors: colors,
            align: 'left',
            sliders: sliders
        }).on('changeColor', function (e) {

            if (e.target.id == "navbar_logo_background") {
                $('.topnavbar .navbar-header')
                    .css('background', e.color);

                $('.navbar_logo_background.color-previewer')
                    .css('background', e.color);
            }
            if (e.target.id == "top_bar_background") {
                $('.topnavbar .nav-wrapper')
                    .css('background', e.color);
                $('li.user-header')
                    .css('background-color', e.color);

                $('.top_bar_background.color-previewer')
                    .css('background', e.color);
            }
            if (e.target.id == "top_bar_color") {
                $('.topnavbar .navbar-nav > li > a, .topnavbar .navbar-nav > .open > a')
                    .css('color', e.color);

                $('.top_bar_color.color-previewer')
                    .css('background', e.color);
            }
            if (e.target.id == "sidebar_background") {
                $('.sidebar')
                    .css('background', e.color);

                $('.sidebar_background.color-previewer')
                    .css('background', e.color);

            }
            if (e.target.id == "sidebar_color") {
                $('.sidebar .nav > li > a,.sidebar .nav > li > .nav-item,.user-block .user-block-info .user-block-name, .user-block .user-block-info .user-block-role')
                    .css('color', e.color);

                $('.sidebar_color.color-previewer')
                    .css('background', e.color);

            }
            if (e.target.id == "sidebar_active_background") {
                $('.sidebar .nav > li.active, .sidebar .nav > li.open, .sidebar .nav > li.active > a, .sidebar .nav > li.open > a, .sidebar .nav > li.active .nav, .sidebar .nav > li.open .nav')
                    .css('background', e.color);

                $('.sidebar_active_background.color-previewer')
                    .css('background', e.color);

            }
            if (e.target.id == "sidebar_active_color") {
                $('.sidebar .nav > li.active, .sidebar .nav > li.open, .sidebar .nav > li.active > a, .sidebar .nav > li.open > a, .sidebar .nav > li.active .nav, .sidebar .nav > li.open .nav,.sidebar .nav > li.active > a > em, .sidebar .nav > li.open > a > em')
                    .css('color', e.color);

                $('.sidebar_active_color.color-previewer')
                    .css('background', e.color);

                $('.sidebar .nav > li.active')
                    .css('border-left-color', e.color);

            }
            if (e.target.id == "submenu_open_background") {
                $('.sidebar-subnav')
                    .css('background', e.color);

                $('.submenu_open_background.color-previewer')
                    .css('background', e.color);
            }
            if (e.target.id == "active_background") {
                $('.nav-pills > li.active > a, .nav-pills > li.active > a:focus, li.user-header')
                    .attr('style', 'background: ' + e.color + ' !important');
                $('.active_background.color-previewer')
                    .css('background', e.color);
            }
            if (e.target.id == "active_color") {
                $('.nav-pills > li.active > a, .nav-pills > li.active > a:focus')
                    .css('color', e.color);

                $('.active_color.color-previewer')
                    .css('background', e.color);
            }
            if (e.target.id == "body_background") {
                $('body,.wrapper > section')
                    .css('background', e.color);

                $('.body_background.color-previewer')
                    .css('background', e.color);
            }

        });
    });
</script>