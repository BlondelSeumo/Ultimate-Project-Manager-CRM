<?php echo message_box('success') ?>
<div class="row" xmlns="http://www.w3.org/1999/html">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form role="form" id="form" action="<?php echo base_url(); ?>admin/settings/save_system" method="post"
              class="form-horizontal  ">
            <section class="panel panel-custom">
                <header class="panel-heading  "><?= lang('system_settings') ?></header>
                <div class="panel-body">
                    <?php echo validation_errors(); ?>
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_language') ?></label>
                        <div class="col-lg-4">
                            <select name="default_language" class="form-control select_box">

                                <?php
                                if (!empty($languages)) {
                                    foreach ($languages as $lang) :
                                        ?>
                                        <option lang="<?= $lang->code ?>"
                                                value="<?= $lang->name ?>"<?= (config_item('default_language') == $lang->name ? ' selected="selected"' : '') ?>><?= ucfirst($lang->name) ?></option>
                                        <?php
                                    endforeach;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('locale') ?></label>
                        <div class="col-lg-4">
                            <select name="locale" class="form-control select_box" required>
                                <?php foreach ($locales as $loc) : ?>
                                    <option lang="<?= $loc->code ?>"
                                            value="<?= $loc->locale ?>"<?= (config_item('locale') == $loc->locale ? ' selected="selected"' : '') ?>><?= $loc->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('timezone') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <select name="timezone" class="form-control select_box" required>
                                <?php foreach ($timezones as $timezone => $description) : ?>
                                    <option
                                        value="<?= $timezone ?>"<?= (config_item('timezone') == $timezone ? ' selected="selected"' : '') ?>><?= $description ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_currency') ?></label>
                        <div class="col-lg-4">
                            <select name="default_currency" class="form-control select_box">
                                <?php $cur = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row(); ?>

                                <?php foreach ($currencies as $cur) : ?>
                                    <option
                                        value="<?= $cur->code ?>"<?= (config_item('default_currency') == $cur->code ? ' selected="selected"' : '') ?>><?= $cur->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if ($this->session->userdata('user_type') == '1') { ?>
                            <div class="col-sm-1">
                                <span data-toggle="tooltip" data-placement="top" title="<?= lang('new_currency'); ?>"
                                </span>
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/settings/new_currency" class="btn btn-sm btn-success">
                                    <i class="fa fa-plus text-white"></i></a>
                            </div>
                            <div class="col-sm-1">
                                <span data-toggle="tooltip" data-placement="top"
                                      title="<?= lang('view_all_currency'); ?>"
                                </span>
                                <a href="<?= base_url() ?>admin/settings/all_currency" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list-alt text-white"></i></a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_account') ?></label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <select name="default_account" style="width:100%;" class="form-control select_box">
                                    <?php
                                    $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                                    if (!empty($account_info)) {
                                        foreach ($account_info as $v_account) : ?>
                                            <option
                                                value="<?= $v_account->account_id ?>"<?= (config_item('default_account') == $v_account->account_id ? ' selected="selected"' : '') ?>><?= $v_account->account_name ?></option>
                                        <?php endforeach;
                                    }
                                    $acreated = can_action('36', 'created');
                                    ?>
                                </select>
                                <?php if (!empty($acreated)) { ?>
                                    <div class="input-group-addon"
                                         title="<?= lang('new') . ' ' . lang('account') ?>"
                                         data-toggle="tooltip" data-placement="top">
                                        <a data-toggle="modal" data-target="#myModal"
                                           href="<?= base_url() ?>admin/account/new_account"><i
                                                class="fa fa-plus"></i></a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default') . ' ' . lang('payment_method') ?></label>
                        <div class="col-lg-5">
                            <select class="form-control select_box" style="width: 100%"
                                    name="default_payment_method">
                                <option value="0"><?= lang('select_payment_method') ?></option>
                                <?php
                                $payment_methods = $this->db->order_by('payment_methods_id', 'DESC')->get('tbl_payment_methods')->result();
                                if (!empty($payment_methods)) {
                                    foreach ($payment_methods as $p_method) {
                                        ?>
                                        <option value="<?= $p_method->payment_methods_id ?>" <?php
                                        if (!empty(config_item('default_payment_method'))) {
                                            echo config_item('default_payment_method') == $p_method->payment_methods_id ? 'selected' : '';
                                        }
                                        ?>><?= $p_method->method_name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('attendance_report') ?></label>
                        <div class="col-lg-5">
                            <?php $options = array(
                                '1' => lang('attendance_report') . ' 1',
                                '2' => lang('attendance_report') . ' 2',
                                '3' => lang('attendance_report') . ' 3',
                            );
                            echo form_dropdown('attendance_report', $options, config_item('attendance_report'), 'style="width:100%" class="form-control"'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('project_details_view') ?></label>
                        <div class="col-lg-5">
                            <?php $options = array(
                                '1' => lang('project_details_view') . ' 1',
                                '2' => lang('project_details_view') . ' 2',
                            );
                            echo form_dropdown('project_details_view', $options, config_item('project_details_view'), 'style="width:100%" class="form-control"'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('task_details_view') ?></label>
                        <div class="col-lg-5">
                            <?php $options = array(
                                '1' => lang('task_details_view') . ' 1',
                                '2' => lang('task_details_view') . ' 2',
                            );
                            echo form_dropdown('task_details_view', $options, config_item('task_details_view'), 'style="width:100%" class="form-control"'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('currency_position') ?></label>
                        <div class="col-lg-3">
                            <?php $options = array(
                                '1' => "$ 100",
                                '2' => "100 $",
                            );
                            echo form_dropdown('currency_position', $options, config_item('currency_position'), 'style="width:100%" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_tax') ?></label>
                        <div class="col-lg-5">
                            <?php
                            $taxes = $this->db->order_by('tax_rate_percent', 'ASC')->get('tbl_tax_rates')->result();
                            $default_tax = config_item('default_tax');
                            if (!is_numeric($default_tax)) {
                                $default_tax = unserialize($default_tax);
                            }
                            $select = '<select class="selectpicker" data-width="100%" name="default_tax[]" multiple data-none-selected-text="' . lang('no_tax') . '">';
                            foreach ($taxes as $tax) {
                                $selected = '';
                                if (!empty($default_tax) && is_array($default_tax)) {
                                    if (in_array($tax->tax_rates_id, $default_tax)) {
                                        $selected = ' selected ';
                                    }
                                }
                                $select .= '<option value="' . $tax->tax_rates_id . '"' . $selected . 'data-taxrate="' . $tax->tax_rate_percent . '" data-taxname="' . $tax->tax_rate_name . '" data-subtext="' . $tax->tax_rate_name . '">' . $tax->tax_rate_percent . '%</option>';
                            }
                            $select .= '</select>';
                            echo $select;
                            ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('tables_pagination_limit') ?></label>
                        <div class="col-lg-2">
                            <input type="text" class="form-control"
                                   value="<?= config_item('tables_pagination_limit') ?>"
                                   name="tables_pagination_limit">
                        </div>
                    </div>
                    <?php
                    $this->settings_model->set_locale();
                    $date_format = config_item('date_format');
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('date_format') ?></label>
                        <div class="col-lg-3">
                            <select name="date_format" class="form-control">
                                <option
                                    value="%d-%m-%Y"<?= ($date_format == "%d-%m-%Y" ? ' selected="selected"' : '') ?>><?= strftime("%d-%m-%Y", time()) ?></option>
                                <option
                                    value="%m-%d-%Y"<?= ($date_format == "%m-%d-%Y" ? ' selected="selected"' : '') ?>><?= strftime("%m-%d-%Y", time()) ?></option>
                                <option
                                    value="%Y-%m-%d"<?= ($date_format == "%Y-%m-%d" ? ' selected="selected"' : '') ?>><?= strftime("%Y-%m-%d", time()) ?></option>
                                <option
                                    value="%d-%m-%y"<?= ($date_format == "%d-%m-%y" ? ' selected="selected"' : '') ?>><?= strftime("%d-%m-%y", time()) ?></option>
                                <option
                                    value="%m-%d-%y"<?= ($date_format == "%m-%d-%y" ? ' selected="selected"' : '') ?>><?= strftime("%m-%d-%y", time()) ?></option>
                                <option
                                    value="%m.%d.%Y"<?= ($date_format == "%m.%d.%Y" ? ' selected="selected"' : '') ?>><?= strftime("%m.%d.%Y", time()) ?></option>
                                <option
                                    value="%d.%m.%Y"<?= ($date_format == "%d.%m.%Y" ? ' selected="selected"' : '') ?>><?= strftime("%d.%m.%Y", time()) ?></option>
                                <option
                                    value="%Y.%m.%d"<?= ($date_format == "%Y.%m.%d" ? ' selected="selected"' : '') ?>><?= strftime("%Y.%m.%d", time()) ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('time_format') ?></label>
                        <div class="col-lg-3">
                            <?php
                            $options = array(
                                'g:i a' => date("g:i a"),
                                'g:i A' => date("g:i A"),
                                'H:i' => date("H:i"),
                            );
                            echo form_dropdown('time_format', $options, config_item('time_format'), ' class="form-control"'); ?>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('money_format') ?></label>
                        <div class="col-lg-5">
                            <div class="input-group ">
                                <div class="col-md-8 row">
                                    <?php
                                    if (empty(config_item('decimal_separator'))) {
                                        $decimal_separator = 2;
                                    } else {
                                        $decimal_separator = config_item('decimal_separator');
                                    }
                                    $decimal = sprintf('%0' . $decimal_separator . 'd', 0);
                                    $options = array(
                                        '1' => "1,234." . $decimal,
                                        '2' => "1.234," . $decimal,
                                        '3' => "1234." . $decimal,
                                        '4' => "1234," . $decimal,
                                        '5' => "1'234." . $decimal,
                                        '6' => "1 234." . $decimal,
                                        '7' => "1 234," . $decimal,
                                        '8' => "1 234'" . $decimal,
                                    );
                                    echo form_dropdown('money_format', $options, config_item('money_format'), 'style="width:100%" class="form-control select_2"'); ?>
                                </div>
                                <div class="col-md-4 row">
                                    <div class="input-group-addon pt0 pb0 pl-sm pr-sm ">
                                        <input type="text" class="form-control pt0 pb0" name="decimal_separator"
                                               value="<?= $decimal_separator ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('allowed_files') ?></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="<?= config_item('allowed_files') ?>"
                                   name="allowed_files">
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-lg-3 control-label"><?= lang('max_file_size') ?></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="<?= config_item('max_file_size') ?>"
                                   name="max_file_size">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('google_api_key') ?></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="<?= config_item('google_api_key') ?>"
                                   name="google_api_key">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('recaptcha_site_key') ?></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="<?= config_item('recaptcha_site_key') ?>"
                                   name="recaptcha_site_key">
                            <?= lang('recaptcha_help') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('recaptcha_secret_key') ?></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="<?= config_item('recaptcha_secret_key') ?>"
                                   name="recaptcha_secret_key">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('auto_close_ticket') ?></label>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" value="<?= config_item('auto_close_ticket') ?>"
                                   name="auto_close_ticket">
                        </div>
                        <div class="col-sm-4">
                            <?= lang('hours') . ' <span class="required" >' . lang('required_cron') . '</span>' ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('enable_languages') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('enable_languages') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="enable_languages">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('allow_sub_tasks') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('allow_sub_tasks') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="allow_sub_tasks">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('only_allowed_ip_can_clock') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('only_allowed_ip_can_clock') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="only_allowed_ip_can_clock">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('allow_client_registration') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('allow_client_registration') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="allow_client_registration">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('allow_apply_job_from_login') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('allow_apply_job_from_login') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="allow_apply_job_from_login">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <input name="client_default_menu[]" value="17" type="hidden">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('client_default_menu_permission') ?><span
                                class="text-danger"> *</span></label>
                        <div class="col-lg-5">
                            <select multiple="multiple" name="client_default_menu[]" style="width: 100%"
                                    class="select_multi" required="">
                                <option
                                    value=""><?= lang('select') . ' ' . lang('client_default_menu_permission') ?></option>
                                <?php
                                $all_client_menu = $this->db->where('parent', 0)->order_by('sort')->get('tbl_client_menu')->result();
                                if (!empty($all_client_menu)) {
                                    foreach ($all_client_menu as $v_client_menu) {
                                        if ($v_client_menu->label != 'dashboard') {
                                            ?>
                                            <option value="<?= $v_client_menu->menu_id ?>" <?php
                                            $client_menu = unserialize(config_item('client_default_menu'));
                                            if (!empty($client_menu['client_default_menu'])) {
                                                foreach ($client_menu['client_default_menu'] as $v_menu) {
                                                    echo $v_client_menu->menu_id == $v_menu ? 'selected' : '';
                                                }
                                            }
                                            ?>><?= lang($v_client_menu->label) ?></option>
                                            <?php
                                        }
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('allow_client_project') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('allow_client_project') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="allow_client_project">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"></label>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </section>
        </form>
    </div>
    <!-- End Form -->
</div>