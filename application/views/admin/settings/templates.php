<?php
$group = $this->uri->segment(5);
$language = $this->uri->segment(4);
if (empty($language)) {
    $system_lang = $this->admin_model->get_lang();
    if (!empty($system_lang)) {
        $language = get_any_field('tbl_languages', array('name' => $system_lang), 'code');
    } else {
        $language = 'en';
    }
}
if (!empty($group)) {
    $group = $group;
} else {
    $group = 'user';
}
$template_group = $group;

$editor = $this->data;
switch ($template_group) {
    case "extra":
        $default = "estimate_email";
        break;
    case "invoice":
        $default = "invoice_message";
        break;
    case "tasks":
        $default = "task_assigned";
        break;
    case "bugs":
        $default = "bug_assigned";
        break;
    case "project":
        $default = "client_notification";
        break;
    case "ticket":
        $default = "ticket_client_email";
        break;
    case "hrm":
        $default = "leave_request_email";
        break;
    case "user":
        $default = "activate_account";
        break;
}
$sub_menu = $this->uri->segment(6);
if (!empty($sub_menu)) {
    $sub_menu = $sub_menu;
} else {
    $sub_menu = $default;
}
$setting_email = $sub_menu;


$email['extra'] = array("credit_note_email" => array("{credit_note_REF}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}", "{credit_note_LINK}", "{SITE_NAME}"), "estimate_email" => array("{ESTIMATE_REF}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}", "{ESTIMATE_LINK}", "{SITE_NAME}"), "estimate_overdue_email" => array("{ESTIMATE_REF}", "{CLIENT}", "{DUE_DATE}", "{CURRENCY}", "{AMOUNT}", "{ESTIMATE_LINK}", "{SITE_NAME}"), "proposal_email" => array("{PROPOSAL_REF}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}", "{PROPOSAL_LINK}", "{SITE_NAME}"), "proposal_overdue_email" => array("{PROPOSAL_REF}", "{CLIENT}", "{DUE_DATE}", "{CURRENCY}", "{AMOUNT}", "{PROPOSAL_LINK}", "{SITE_NAME}"), "message_received" => array("{RECIPIENT}", "{SENDER}", "{MESSAGE}", "{SITE_URL}", "{SITE_NAME}"), "quotations_form" => array("{CLIENT}", "{DATE}", "{CURRENCY}", "{AMOUNT}", "{NOTES}", "{QUOTATION_LINK}", "{SITE_NAME}"), "goal_achieve" => array("{Goal_Type}", "{achievement}", "{total_achievement}", "{start_date}", "{End_date}"), "goal_not_achieve" => array("{Goal_Type}", "{achievement}", "{total_achievement}", "{start_date}", "{End_date}"));
$email['invoice'] = array("invoice_message" => array("{SITE_NAME}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}"), "invoice_reminder" => array("{SITE_NAME}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}"), "payment_email" => array("{SITE_NAME}", "{INVOICE_CURRENCY}", "{PAID_AMOUNT}"), "invoice_overdue_email" => array("{SITE_NAME}", "{REF}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}", "{DUE_DATE}"), "refund_confirmation" => array("{SITE_NAME}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}", "{INVOICE_DATE}"), "invoice_item_refund_request" => array("{SITE_NAME}", "{CLIENT}", "{CURRENCY}", "{AMOUNT}", "{INVOICE_DATE}"));
$email['tasks'] = array("task_assigned" => array("{SITE_NAME}", "{TASK_NAME}", "{ASSIGNED_BY}"), "tasks_comments" => array("{SITE_NAME}", "{POSTED_BY}", "{TASK_NAME}", "{COMMENT_MESSAGE}"), "tasks_attachment" => array("{SITE_NAME}", "{UPLOADED_BY}", "{TASK_NAME}"), "tasks_updated" => array("{SITE_NAME}", "{TASK_NAME}", "{ASSIGNED_BY}"));
$email['bugs'] = array("bug_assigned" => array("{SITE_NAME}", "{BUG_TITLE}", "{ASSIGNED_BY}"), "bug_comments" => array("{SITE_NAME}", "{POSTED_BY}", "{BUG_TITLE}", "{COMMENT_MESSAGE}"), "bug_attachment" => array("{SITE_NAME}", "{UPLOADED_BY}", "{BUG_TITLE}"), "bug_updated" => array("{SITE_NAME}", "{STATUS}", "{BUG_TITLE}", "{MARKED_BY}"), 'bug_reported' => array("{SITE_NAME}", "{ADDED_BY}", "{BUG_TITLE}"));
$email['project'] = array("client_notification" => array("{SITE_NAME}", "{CLIENT_NAME}", "{PROJECT_NAME}"), "assigned_project" => array("{SITE_NAME}", "{PROJECT_NAME}"), 'complete_projects' => array("{SITE_NAME}", "{PROJECT_NAME}", "{CLIENT_NAME}"), "project_comments" => array("{SITE_NAME}", "{PROJECT_NAME}", "{POSTED_BY}", "{COMMENT_MESSAGE}"), "project_attachment" => array("{SITE_NAME}", "{PROJECT_NAME}", "{UPLOADED_BY}"), 'responsible_milestone' => array("{SITE_NAME}", "{PROJECT_NAME}", "{MILESTONE_NAME}", "{ASSIGNED_BY}"), 'project_overdue_email' => array("{SITE_NAME}", "{PROJECT_NAME}", "{CLIENT}", "{DUE_DATE}"));
$email['ticket'] = array("ticket_client_email" => array("{SITE_NAME}", "{CLIENT_EMAIL}", "{TICKET_CODE}"), "ticket_closed_email" => array("{SITE_NAME}", "{REPORTER_EMAIL}", "{TICKET_CODE}", "{STAFF_USERNAME}", "{TICKET_STATUS}", "{NO_OF_REPLIES}"), "ticket_reply_email" => array("{SITE_NAME}", "{TICKET_CODE}", "{TICKET_STATUS}"), "ticket_staff_email" => array("{SITE_NAME}", "{TICKET_CODE}", "{REPORTER_EMAIL}"), "auto_close_ticket" => array("{SITE_NAME}", "{TICKET_CODE}", "{REPORTER_EMAIL}", "{SUBJECT}", "{TICKET_STATUS}"), "ticket_reopened_email" => array("{SITE_NAME}", "{RECIPIENT}", "{SUBJECT}", "{USER}"));
$email['user'] = array("activate_account" => array("{SITE_NAME}", "{ACTIVATE_URL}", "{USERNAME}", "{EMAIL}", "{PASSWORD}"), "change_email" => array("{SITE_NAME}", "{NEW_EMAIL}", "{NEW_EMAIL_KEY_URL}"), "forgot_password" => array("{SITE_NAME}", "{PASS_KEY_URL}"), "registration" => array("{SITE_NAME}", "{SITE_URL}", "{USERNAME}", "{EMAIL}", "{PASSWORD}"), "reset_password" => array("{SITE_NAME}", "{USERNAME}", "{EMAIL}", "{NEW_PASSWORD}"), 'wellcome_email' => array("{NAME}", "{COMPANY_NAME}", "{COMPANY_URL}"));

$email['hrm'] = array("leave_request_email" => array("{SITE_NAME}", "{NAME}"), "leave_approve_email" => array("{SITE_NAME}", "{START_DATE}", "{END_DATE}"), "leave_reject_email" => array("{SITE_NAME}", "{START_DATE}", "{END_DATE}"), "overtime_request_email" => array("{SITE_NAME}", "{NAME}"), "overtime_approved_email" => array("{SITE_NAME}", "{DATE}", "{HOUR}"), "overtime_reject_email" => array("{SITE_NAME}", "{DATE}", "{HOUR}"), "payslip_generated_email" => array("{SITE_NAME}", "{NAME}", "{MONTH_YEAR}")
, "advance_salary_email" => array("{SITE_NAME}", "{NAME}"), "advance_salary_approve_email" => array("{SITE_NAME}", "{AMOUNT}", "{DEDUCT_MOTNH}"), "advance_salary_reject_email" => array("{SITE_NAME}"), "award_email" => array("{SITE_NAME}", "{NAME}", "{AWARD_NAME}", "{MONTH}"), "new_job_application_email" => array("{SITE_NAME}", "{NAME}", "{JOB_TITLE}", "{EMAIL}", "{MOBILE}", "{COVER_LETTER}"), "call_for_interview" => array("{SITE_NAME}", "{NAME}", "{JOB_TITLE}", "{DESIGNATION}", "{DATE}")
, "new_notice_published" => array("{SITE_NAME}", "{NAME}", "{TITLE}"), "new_training_email" => array("{SITE_NAME}", "{TRAINING_NAME}", "{ASSIGNED_BY}"), 'deposit_email' => array("{SITE_NAME}", "{AMOUNT}", "{ACCOUNT}", "{BALANCE}"), 'expense_request_email' => array("{SITE_NAME}", "{AMOUNT}", "{NAME}"), 'expense_approved_email' => array("{SITE_NAME}", "{AMOUNT}", "{NAME}"), 'expense_paid_email' => array("{SITE_NAME}", "{AMOUNT}", "{NAME}", "{PAID_BY}"), 'trying_clock_email' => array("{SITE_NAME}", "{IP}", "{NAME}", "{TIME}"), 'clock_in_email' => array("{SITE_NAME}", "{IP}", "{NAME}", "{TIME}"), 'clock_out_email' => array("{SITE_NAME}", "{IP}", "{NAME}", "{TIME}"));
?>
<form action="<?= base_url('admin/settings/templates/' . $language . '/') ?><?= $setting_email ?>" method="post"
      class="bs-example form-horizontal">
    <section class="panel panel-custom">
        <header class="panel-heading  "> <?= lang('email_templates') ?>
            <div class="btn-group">
                <div class="nav-item dropdown">
                    <a href="#" class="navbar-nav-link dropdown-toggle legitRipple" data-toggle="dropdown"
                       aria-expanded="false">
                        <?= lang('languages') ?>
                    </a>
                    <?php
                    $languages = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result(); ?>
                    <ul class="dropdown-menu animated zoomIn">
                        <?php
                        foreach ($languages as $langs) :
                            ?>
                            <li class="<?php
                            if ($language == $langs->code) {
                                echo "active";
                            } ?>"><a href="<?= base_url('admin/settings/templates/' . $langs->code . '/') ?>"
                                     class="code"><?= lang(ucwords($langs->name)) ?></a></li>
                        <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
            <div class="btn-group pull-right" style="margin-bottom: 10px;">
                <button type="button" class="btn btn-xs btn-primary" title="Filter" data-toggle="dropdown"><i
                            class="fa fa-cogs"></i> <?= lang('choose_template') ?><span class="caret"></span></button>
                <ul class="dropdown-menu animated zoomIn">
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>user"><?= lang('account_emails') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>invoice"><?= lang('invoicing_emails') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>tasks"><?= lang('tasks_email') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>bugs"><?= lang('bugs_email') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>project"><?= lang('project_emails') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>ticket"><?= lang('ticketing_emails') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>hrm"><?= lang('hrm_emails') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?>extra"><?= lang('extra_emails') ?></a>
                    </li>
                </ul>
            </div>
        </header>
        <div class="panel-body">
            <div class="col-sm-12">
                <div class="btn-group">
                    <?php
                    foreach ($email[$template_group] as $label => $temp) :
                        $lang = $label;

                        switch ($label) {
                            case "registration":
                                $lang = 'register_email';
                        }
                        ?>
                        <a href="<?= base_url('admin/settings/templates/' . $language . '/') ?><?= $template_group; ?>/<?= $label; ?>"
                           class="<?php
                           if ($setting_email == $label) {
                               echo "active";
                           }
                           ?> btn btn-default mb-sm"><?= lang($label) ?></a>
                    <?php endforeach;
                    ?>
                </div>
            </div>
            <input type="hidden" name="email_group" value="<?= $setting_email; ?>">
            <input type="hidden" name="code" id="code" value="<?= $language ?>">

            <input type="hidden" name="return_url"
                   value="<?= base_url('admin/settings/templates/' . $language . '/') ?><?= $template_group . '/' . $setting_email; ?>">
            <div class="form-group">
                <label class="col-lg-12"><?= lang('subject') ?></label>
                <div class="col-lg-12">
                    <input class="form-control" name="subject" value="<?=
                    get_any_field('tbl_email_templates', array(
                        'email_group' => $setting_email, 'code' => $language
                    ), 'subject')
                    ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-12"><?= lang('message') ?></label>
                <div class="available_merge_fields_container">
                    <div class="col-md-12 merge_fields_col">
                        <?php
                        foreach ($email[$template_group][$setting_email] as $ltexts) { ?>
                            <span class="ml-2"><button type="button"
                                                       class="add_merge_field"><?= $ltexts ?></button> </span>
                        <?php } ?>

                    </div>
                </div>
                <div class="col-lg-12">
                            <textarea class="form-control textarea" style="height: 600px;" name="email_template">
                                <?=
                                get_any_field('tbl_email_templates', array(
                                    'email_group' => $setting_email, 'code' => $language
                                ), 'template_body')
                                ?></textarea>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
        </div>
    </section>
</form>
<script>

    $('.add_merge_field').on('click', function (e) {

        document.execCommand('insertHtml', false, $(this).text());
    });
</script>