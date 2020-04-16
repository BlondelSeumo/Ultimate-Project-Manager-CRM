<div class="row">
    <div class="col-lg-12">
        <div class="col-md-3">
            <ul class="nav nav-pills nav-stacked navbar-custom-nav">
                <?php
                $can_do = can_do(111);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'general') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings">
                            <i class="fa fa-fw fa-info-circle"></i>
                            <?php echo lang('company_details') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(112);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'system') || ($load_setting == 'all_currency') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/system">
                            <i class="fa fa-fw fa-desktop"></i>
                            <?php echo lang('system_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(113);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'email_settings') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/email">
                            <i class="fa fa-fw fa-envelope"></i>
                            <?php echo lang('email_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(113);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'sms_settings') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/sms_settings">
                            <i class="fa fa-fw fa-envelope"></i>
                            <?php echo lang('sms_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(114);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'templates') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/templates">
                            <i class="fa fa-fw fa-pencil-square"></i>
                            <?php echo lang('email_templates') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(115);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'email_integration') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/email_integration">
                            <i class="fa fa-fw fa-envelope-o"></i>
                            <?php echo lang('email_integration') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(116);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'payments') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/payments">
                            <i class="fa fa-fw fa-dollar"></i>
                            <?php echo lang('payment_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(117);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'invoice') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/invoice">
                            <i class="fa fa-fw fa-money"></i>
                            <?php echo lang('invoice_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(157);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'credit_note') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/credit_note">
                            <i class="fa fa-fw fa-money"></i>
                            <?php echo lang('credit_note_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(118);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'estimate') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/estimate">
                            <i class="fa fa-fw fa-file-o"></i>
                            <?php echo lang('estimate_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(158);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'proposals') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/proposals">
                            <i class="fa fa-fw fa-leaf"></i>
                            <?php echo lang('proposals_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(155);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'purchase') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/purchase">
                            <i class="fa-fw icon-handbag"></i>
                            <?php echo lang('purchase') . ' ' . lang('settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(159);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'projects') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/projects">
                            <i class="fa-fw fa fa-folder-open-o"></i>
                            <?php echo lang('projects') . ' ' . lang('settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(119);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'tickets') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/tickets">
                            <i class="fa fa-fw fa-ticket"></i>
                            <?php echo lang('tickets_leads_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(120);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'theme') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/theme">
                            <i class="fa fa-fw fa-code"></i>
                            <?php echo lang('theme_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(145);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'dashboard') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/dashboard">
                            <i class="fa fa-fw fa-dashboard"></i>
                            <?php echo lang('dashboard_settings') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(121);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'working_days') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/working_days">
                            <i class="fa fa-fw fa-calendar"></i>
                            <?php echo lang('working_days') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(122);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'leave_category') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/leave_category">
                            <i class="fa fa-fw fa-pagelines"></i>
                            <?php echo lang('leave_category') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(123);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'income_category') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/income_category">
                            <i class="fa fa-fw fa-certificate"></i>
                            <?php echo lang('income_category') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(124);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'expense_category') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/expense_category">
                            <i class="fa fa-fw fa-tasks"></i>
                            <?php echo lang('expense_category') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(125);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'customer_group') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/customer_group">
                            <i class="fa fa-fw fa-users"></i>
                            <?php echo lang('customer_group') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(149);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'allowed_ip') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/allowed_ip">
                            <i class="fa fa-fw fa-server"></i>
                            <?php echo lang('allowed_ip') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(127);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'lead_status') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/lead_status">
                            <i class="fa fa-fw fa-list-ul"></i>
                            <?php echo lang('lead_status') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(128);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'lead_source') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/lead_source">
                            <i class="fa fa-fw fa-arrow-down"></i>
                            <?php echo lang('lead_source') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(129);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'opportunities_state_reason') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/opportunities_state_reason">
                            <i class="fa fa-fw fa-dot-circle-o"></i>
                            <?php echo lang('opportunities_state_reason') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(130);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'custom_field') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/custom_field">
                            <i class="fa fa-fw fa-star-o "></i>
                            <?php echo lang('custom_field') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(131);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'payment_method') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/payment_method">
                            <i class="fa fa-fw fa-money"></i>
                            <?php echo lang('payment_method') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(132);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'cronjob') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/cronjob">
                            <i class="fa fa-fw fa-contao"></i>
                            <?php echo lang('cronjob') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(133);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'menu_allocation') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/menu_allocation">
                            <i class="fa fa-fw fa fa-compass"></i>
                            <?php echo lang('menu_allocation') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(134);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'notification') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/notification">
                            <i class="fa fa-fw fa-bell-o"></i>
                            <?php echo lang('notification') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(135);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'email_notification') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/email_notification">
                            <i class="fa fa-fw fa-bell-o"></i>
                            <?php echo lang('email_notification') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(136);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'database_backup') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/database_backup">
                            <i class="fa fa-fw fa-database"></i>
                            <?php echo lang('database_backup') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(137);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'translations') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/translations">
                            <i class="fa fa-fw fa-language"></i>
                            <?php echo lang('translations') ?>
                        </a>
                    </li>
                <?php }
                $can_do = can_do(138);
                if (!empty($can_do)) { ?>
                    <li class="<?php echo ($load_setting == 'system_update') ? 'active' : ''; ?>">
                        <a href="<?= base_url() ?>admin/settings/system_update">
                            <i class="fa fa-fw fa-pencil-square-o"></i>
                            <?php echo lang('system_update') ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <section class="col-sm-9">
            <div class="col-sm-8  ">
                <?php if ($load_setting == 'email') { ?>
                    <div style="margin-bottom: 10px;margin-left: -15px" class="<?php
                    if ($load_setting != 'email') {
                        echo 'hidden';
                    }
                    ?>">
                        <a href="<?= base_url() ?>admin/settings/email" class="btn btn-info"><i
                                class="fa fa fa-inbox text"></i>
                            <span class="text"><?php echo lang('alert_settings') ?></span>
                        </a>
                    </div>
                <?php } ?>

            </div>
            <section class="">
                <!-- Load the settings form in views -->
                <?php $this->load->view('admin/settings/' . $load_setting) ?>
                <!-- End of settings Form -->
            </section>
        </section>
    </div>
</div>
