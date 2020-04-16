<?php echo message_box('success') ?>

<div class="row" xmlns="http://www.w3.org/1999/html">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form role="form" id="form" action="<?php echo base_url(); ?>admin/settings/save_cronjob"
              method="post"
              class="form-horizontal  ">
            <section class="panel panel-custom">
                <header class="panel-heading  "><?= lang('cronjob') . ' ' . lang('settings') ?>
                    <span><a href="<?= base_url() ?>cronjob/manually"><?= lang('run_cron_manually') ?></a></span>
                </header>
                <div class="panel-body">

                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-4 control-label"><?= lang('active') . ' ' . lang('cronjob') ?></label>
                        <div class="col-lg-8">
                            <label class="checkbox-inline c-checkbox">
                                <input type="checkbox" name="active_cronjob" <?php
                                if (config_item('active_cronjob') == 'on') {
                                    echo "checked=\"checked\"";
                                }
                                ?>>
                                <span class="fa fa-check"></span>
                            </label>
                            <small><?= lang('cronjob_help') ?></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label
                            class="col-lg-4 control-label"><?= lang('automatic') . ' ' . lang('database_backup') ?></label>
                        <div class="col-lg-8">
                            <label class="checkbox-inline c-checkbox">
                                <input type="checkbox" name="automatic_database_backup" <?php
                                if (config_item('automatic_database_backup') == 'on') {
                                    echo "checked=\"checked\"";
                                }
                                ?>>
                                <span class="fa fa-check"></span>
                            </label>
                            <small><?= lang('database_backup_help') ?></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label"><?= lang('cronjob_link') ?></label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><strong><i><?= base_url() ?>cronjob/index</i></strong></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><?= lang('last_cronjob_run') ?></label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php
                                $last_cronjob_run = config_item('last_cronjob_run');
                                if (!empty($last_cronjob_run)) {
                                    echo date("Y-m-d H:i", config_item('last_cronjob_run'));
                                } else {
                                    echo "-";
                                } ?>
                            </p>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"></label>
                        <div class="col-lg-8">
                            <button type="submit"
                                    class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-lg-12">
                            <span>If cronjobs are not included in your hosting subscription, you can use a free
                                cronjob service
                                like <a href="http://www.easycron.com?ref=18097" target="_blank">Free Cronjob
                                    Service</a></span>
                        </div>
                    </div>

                </div>
            </section>
        </form>
    </div>
    <!-- End Form -->
</div>