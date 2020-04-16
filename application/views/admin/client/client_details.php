<?= message_box('success'); ?>
<?= message_box('error');

$eeror_message = $this->session->userdata('error');
if (!empty($eeror_message)):foreach ($eeror_message as $key => $message):
    ?>
    <div class="alert alert-danger">
        <?php echo $message; ?>
    </div>
<?php
endforeach;
endif;
$this->session->unset_userdata('error');

?>
<?php
$all_bug_info = $this->client_model->get_permission('tbl_bug');
$total_bugs = 0;
if (!empty($all_bug_info)) {
    foreach ($all_bug_info as $v_bugs) {
        if (!empty($v_bugs)) {
            $profile = $this->db->where(array('user_id' => $v_bugs->reporter))->get('tbl_account_details')->row();
            if (!empty($profile->company)) {
                if ($profile->company == $client_details->client_id) {
                    $total_bugs += count($v_bugs->bug_id);
                }
            }
        }
    }
}
$recently_paid = $this->db
    ->where('paid_by', $client_details->client_id)
    ->order_by('created_date', 'desc')
    ->get('tbl_payments')
    ->result();
$all_tickets_info = $this->client_model->get_permission('tbl_tickets');
$total_tickets = 0;
if (!empty($all_tickets_info)) {
    foreach ($all_tickets_info as $v_tickets_info) {
        if (!empty($v_tickets_info)) {
            $profile_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_account_details')->row();
            if (!empty($profile_info->company))
                if ($profile_info->company == $client_details->client_id) {
                    $total_tickets += count($v_tickets_info->tickets_id);
                }
        }
    }
}
$all_project = $this->db->where('client_id', $client_details->client_id)->get('tbl_project')->result();
$client_notes = $this->db->where(array('user_id' => $client_details->client_id, 'is_client' => 'Yes'))->get('tbl_notes')->result();

$client_outstanding = $this->invoice_model->client_outstanding($client_details->client_id);
$client_payments = $this->invoice_model->get_sum('tbl_payments', 'amount', $array = array('paid_by' => $client_details->client_id));
$client_payable = $client_payments + $client_outstanding;
$client_currency = $this->invoice_model->client_currency_symbol($client_details->client_id);
if (!empty($client_currency)) {
    $cur = $client_currency->symbol;
} else {
    $currency = get_row('tbl_currencies', array('code' => config_item('default_currency')));
    $cur = $currency->symbol;
}
if ($client_payable > 0 AND $client_payments > 0) {
    $perc_paid = round(($client_payments / $client_payable) * 100, 1);
    if ($perc_paid > 100) {
        $perc_paid = '100';
    }
} else {
    $perc_paid = 0;
}
$client_transactions = $this->db->where('paid_by', $client_details->client_id)->get('tbl_transactions')->result();
$all_proposals_info = $this->db->where(array('module' => 'client', 'module_id' => $client_details->client_id))->order_by('proposals_id', 'DESC')->get('tbl_proposals')->result();
$edited = can_action('4', 'edited');
$notified_reminder = count($this->db->where(array('module' => 'client', 'module_id' => $client_details->client_id, 'notified' => 'No'))->get('tbl_reminders')->result());
?>
<div class="row">
    <div class="col-md-3">
        <div class="panel widget mb0 b0">
            <div class="row-table row-flush">
                <div class="col-xs-4 bg-info text-center">
                    <em class="fa fa-money fa-2x"></em>
                </div>
                <div class="col-xs-8">
                    <div class="text-center">
                        <h4 class="mb-sm"><?php
                            if (!empty($client_payments)) {
                                echo display_money($client_payments, client_currency($client_details->client_id));
                            } else {
                                echo '0.00';
                            }
                            ?></h4>
                        <p class="mb0 text-muted"><?= lang('paid_amount') ?></p>
                        <a href="<?= base_url() ?>admin/invoice/all_payments"
                           class="small-box-footer"><?= lang('more_info') ?> <i
                                    class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel widget mb0 b0">
            <div class="row-table row-flush">
                <div class="col-xs-4 bg-danger text-center">
                    <em class="fa fa-usd fa-2x"></em>
                </div>
                <div class="col-xs-8">
                    <div class="text-center">
                        <h4 class="mb-sm"><?php
                            if ($client_outstanding > 0) {
                                echo display_money($client_outstanding, client_currency($client_details->client_id));
                            } else {
                                echo '0.00';
                            }
                            ?></h4>
                        <p class="mb0 text-muted"><?= lang('due_amount') ?></p>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice"
                           class="small-box-footer"><?= lang('more_info') ?>
                            <i
                                    class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel widget mb0 b0">
            <div class="row-table row-flush">
                <div class="col-xs-4 bg-inverse text-center">
                    <em class="fa fa-usd fa-2x"></em>
                </div>
                <div class="col-xs-8">
                    <div class="text-center">
                        <h4 class="mb-sm">
                            <?php
                            if ($client_payable > 0) {
                                echo display_money($client_payable, client_currency($client_details->client_id));
                            } else {
                                echo '0.00';
                            }
                            ?></h4>
                        <p class="mb0 text-muted"><?= lang('invoice_amount') ?></p>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice"
                           class="small-box-footer"><?= lang('more_info') ?>
                            <i
                                    class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel widget mb0 b0">
            <div class="row-table row-flush">
                <div class="col-xs-4 bg-purple text-center">
                    <em class="fa fa-usd fa-2x"></em>
                </div>
                <div class="col-xs-8">
                    <div class="text-center">
                        <h4 class="mb-sm">
                            <?= $perc_paid ?>%</h4>
                        <p class="mb0 text-muted"><?= lang('paid') . ' ' . lang('percentage') ?></p>
                        <a href="<?= base_url() ?>admin/invoice/all_payments"
                           class="small-box-footer"><?= lang('more_info') ?>
                            <i
                                    class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$url = $this->uri->segment(5);

?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= empty($url) ? 'active' : '' ?>"><a href="#task_details" data-toggle="tab"
                                                              aria-expanded="true"><?= lang('details') ?></a>
            </li>
            <li class="dcontacts <?= $url == 'add_contacts' ? 'active' : '' ?>"><a href="#contacts"
                                                                                   data-toggle="tab"
                                                                                   aria-expanded="false"><?= lang('contacts') ?>
                    <strong
                            class="pull-right"><?= (!empty($client_contacts) ? count($client_contacts) : null) ?></strong></a>
            </li>
            <li class="dnotes <?= $url == 'notes' ? 'active' : '' ?>"><a href="#notes" data-toggle="tab"
                                                                         aria-expanded="false"><?= lang('notes') ?>
                    <strong
                            class="pull-right"><?= (!empty($client_notes) ? count($client_notes) : null) ?></strong></a>
            </li>
            <li class="dinvoices <?= $url == 'invoice' ? 'active' : '' ?>"><a href="#invoices" data-toggle="tab"
                                                                              aria-expanded="false"><?= lang('invoices') ?>
                    <strong
                            class="pull-right"><?= (!empty($client_invoices) ? count($client_invoices) : null) ?></strong></a>
            </li>
            <li class="dpayments <?= $url == 'payment' ? 'active' : '' ?>"><a href="#payments" data-toggle="tab"
                                                                              aria-expanded="false"><?= lang('payments') ?>
                    <strong
                            class="pull-right"><?= (!empty($recently_paid) ? count($recently_paid) : null) ?></strong></a>
            </li>
            <li class="<?= $url == 'estimate' ? 'active' : '' ?>"><a href="#estimates" data-toggle="tab"
                                                                     aria-expanded="false"><?= lang('estimates') ?>
                    <strong
                            class="pull-right"><?= (!empty($client_estimates) ? count($client_estimates) : null) ?></strong></a>
            </li>
            <li class="<?= $url == 'proposal' ? 'active' : '' ?>"><a href="#proposals"
                                                                     data-toggle="tab"><?= lang('proposals') ?>
                    <strong
                            class="pull-right"><?= (!empty($all_proposals_info) ? count($all_proposals_info) : null) ?></strong></a>
            </li>

            <li class=""><a href="#transaction" data-toggle="tab" aria-expanded="false"><?= lang('transactions') ?>
                    <strong
                            class="pull-right"><?= (!empty($client_transactions) ? count($client_transactions) : null) ?></strong></a>
            </li>

            <li class=""><a href="#projects" data-toggle="tab" aria-expanded="false"><?= lang('project') ?><strong
                            class="pull-right"><?= (!empty($all_project) ? count($all_project) : null) ?></strong></a>
            </li>
            <li class=""><a href="#ticket" data-toggle="tab" aria-expanded="false"><?= lang('tickets') ?><strong
                            class="pull-right"><?= (!empty($total_tickets) ? $total_tickets : null) ?></strong></a>
            </li>
            <li class=""><a href="#bugs" data-toggle="tab" aria-expanded="false"><?= lang('bugs') ?><strong
                            class="pull-right"><?= (!empty($total_bugs) ? $total_bugs : null) ?></strong></a></li>
            <li class="<?= $url == 'reminder' ? 'active' : '' ?>"><a href="#reminder" data-toggle="tab"
                                                                     aria-expanded="false"><?= lang('reminder') ?>
                    <strong
                            class="pull-right"><?= (!empty($notified_reminder) ? $notified_reminder : null) ?></strong>
                </a>
            </li>
            <li class="<?= $url == 'filemanager' ? 'active' : '' ?>"><a href="#filemanager" data-toggle="tab"
                                                                        aria-expanded="false"><?= lang('filemanager') ?></a>
            </li>
            <li class="<?= $url == 'map' ? 'active' : '' ?>"><a
                        href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id ?>/map"><?= lang('map') ?>
                    <strong class="pull-right"></strong></a></li>
        </ul>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= empty($url) ? 'active' : '' ?> " id="task_details"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title"><strong><?= $client_details->name ?> - <?= lang('details') ?> </strong>
                            <div class="pull-right">
                                <?php
                                if ($client_details->leads_id != 0) {
                                    echo lang('converted_from')
                                    ?>
                                    <a href="<?= base_url() ?>admin/leads/leads_details/<?= $client_details->leads_id ?>"><?= lang('leads') ?></a>
                                <?php }
                                if (!empty($edited)) {
                                    ?>
                                    <a href="<?php echo base_url() ?>admin/client/manage_client/<?= $client_details->client_id ?>"
                                       class="btn-xs "><i class="fa fa-edit"></i> <?= lang('edit') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Details START -->
                        <div class="col-md-6">
                            <div class="group">
                                <h4 class="subdiv text-muted"><?= lang('contact_details') ?></h4>
                                <div class="row inline-fields">
                                    <div class="col-md-4"><?= lang('name') ?></div>
                                    <div class="col-md-6"><?= $client_details->name ?></div>
                                </div>
                                <div class="row inline-fields">
                                    <div class="col-md-4"><?= lang('contact_person') ?></div>
                                    <div class="col-md-6">
                                        <?php
                                        if ($client_details->primary_contact != 0) {
                                            $contacts = $client_details->primary_contact;
                                        } else {
                                            $contacts = NULL;
                                        }
                                        $primary_contact = $this->client_model->check_by(array('user_id' => $contacts), 'tbl_account_details');
                                        if ($primary_contact) {
                                            echo $primary_contact->fullname;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row inline-fields">
                                    <div class="col-md-4"><?= lang('email') ?></div>
                                    <div class="col-md-6"><?= $client_details->email ?></div>
                                </div>
                            </div>

                            <div class="row inline-fields">
                                <div class="col-md-4"><?= lang('city') ?></div>
                                <div class="col-md-6"><?= $client_details->city ?></div>
                            </div>
                            <div class="row inline-fields">
                                <div class="col-md-4"><?= lang('zipcode') ?></div>
                                <div class="col-md-6"><?= $client_details->zipcode ?></div>
                            </div>

                            <?php $show_custom_fields = custom_form_label(12, $client_details->client_id);

                            if (!empty($show_custom_fields)) {
                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                    if (!empty($v_fields)) {
                                        ?>
                                        <div class="row inline-fields">
                                            <div class="col-md-4"><?= $c_label ?></div>
                                            <div class="col-md-6"><?= $v_fields ?></div>
                                        </div>
                                    <?php }
                                }
                            }
                            ?>
                            <div class=" mt">
                                <?php if (!empty($client_details->website)) { ?>
                                    <div class="row inline-fields">
                                        <div class="col-md-4"><?= lang('website') ?></div>
                                        <div class="col-md-6"><?= $client_details->website ?></div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($client_details->skype_id)) { ?>
                                    <div class="row inline-fields">
                                        <div class="col-md-4"><?= lang('skype_id') ?></div>
                                        <div class="col-md-6"><?= $client_details->skype_id ?></div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($client_details->facebook)) { ?>
                                    <div class="row inline-fields">
                                        <div class="col-md-4"><?= lang('facebook_profile_link') ?></div>
                                        <div class="col-md-6"><?= $client_details->facebook ?></div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($client_details->twitter)) { ?>
                                    <div class="row inline-fields">
                                        <div class="col-md-4"><?= lang('twitter_profile_link') ?></div>
                                        <div class="col-md-6"><?= $client_details->twitter ?></div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($client_details->linkedin)) { ?>
                                    <div class="row inline-fields">
                                        <div class="col-md-4"><?= lang('linkedin_profile_link') ?></div>
                                        <div class="col-md-6"><?= $client_details->linkedin ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-lg">
                            <div class="group">
                                <div class="row" style="margin-top: 5px">
                                    <div class="rec-pay col-md-12">

                                        <div class="row inline-fields">
                                            <div class="col-md-4"><?= lang('address') ?></div>
                                            <div class="col-md-6"><?= $client_details->address ?></div>
                                        </div>
                                        <div class="row inline-fields">
                                            <div class="col-md-4"><?= lang('phone') ?></div>
                                            <div class="col-md-6"><a
                                                        href="tel:<?= $client_details->phone ?>"><?= $client_details->phone ?></a>
                                            </div>
                                        </div>
                                        <?php if (!empty($client_details->mobile)) { ?>
                                            <div class="row inline-fields">
                                                <div class="col-md-4"><?= lang('mobile') ?></div>
                                                <div class="col-md-6"><a
                                                            href="tel:<?= $client_details->mobile ?>"><?= $client_details->mobile ?></a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="row inline-fields">
                                            <div class="col-md-4"><?= lang('fax') ?></div>
                                            <div class="col-md-6"><?= $client_details->fax ?>
                                            </div>
                                        </div>

                                        <div class=" mt">
                                            <?php if (!empty($client_details->hosting_company)) { ?>
                                                <div class="row inline-fields">
                                                    <div class="col-md-4"><?= lang('hosting_company') ?></div>
                                                    <div class="col-md-6"><?= $client_details->hosting_company ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($client_details->hostname)) { ?>
                                                <div class="row inline-fields">
                                                    <div class="col-md-4"><?= lang('hostname') ?></div>
                                                    <div class="col-md-6"><?= $client_details->hostname ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($client_details->username)) { ?>
                                                <div class="row inline-fields">
                                                    <div class="col-md-4"><?= lang('username') ?></div>
                                                    <div class="col-md-6"><?= $client_details->username ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($client_details->password)) {
                                                $hosting_password = strlen(decrypt($client_details->password));
                                                ?>
                                                <div class="row inline-fields">
                                                    <div class="col-md-4"><?= lang('password') ?></div>
                                                    <div class="col-md-6">
                                                        <span id="show_password">
                                                        <?php
                                                        if (!empty($hosting_password)) {
                                                            for ($p = 1; $p <= $hosting_password; $p++) {
                                                                echo '*';
                                                            }
                                                        } ?>
                                                            </span>
                                                        <a data-toggle="modal" data-target="#myModal"
                                                           href="<?= base_url('admin/client/see_password/c_' . $client_details->client_id) ?>"
                                                           id="see_password"><?= lang('see_password') ?></a>
                                                        <strong id="hosting_password" class="required"></strong>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($client_details->port)) { ?>
                                                <div class="row inline-fields">
                                                    <div class="col-md-4"><?= lang('port') ?></div>
                                                    <div class="col-md-6"><?= $client_details->port ?></div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center block mt">
                            <h4 class="subdiv text-muted"><?= lang('received_amount') ?></h4>
                            <h3 class="amount text-danger cursor-pointer"><strong>
                                    <?php
                                    ?><?= display_money($this->client_model->client_paid($client_details->client_id), client_currency($client_details->client_id)); ?>
                                </strong></h3>
                            <div style="display: inline-block">
                                <div id="easypie3" data-percent="<?= $perc_paid ?>" class="easypie-chart">
                                    <span class="h2"><?= $perc_paid ?>%</span>
                                    <div class="easypie-text"><?= lang('paid') ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Details END -->
                    </div>
                    <div class="panel-footer">
                        <span><?= lang('invoice_amount') ?>: <strong
                                    class="label label-primary">
                                <?= display_money($client_payable, client_currency($client_details->client_id)); ?>
                            </strong></span>
                        <span class="text-danger pull-right">
                            <?= lang('outstanding') ?>
                            :<strong
                                    class="label label-danger"> <?= display_money($client_outstanding, client_currency($client_details->client_id)) ?></strong>
                        </span>
                    </div>
                </div>
            </div>

            <!--            *************** contact tab start ************-->
            <div class="tab-pane <?= $url == 'add_contacts' ? 'active' : '' ?>" id="contacts"
                 style="position: relative;">
                <?php if (!empty($company)): ?>
                    <?php include_once 'assets/admin-ajax.php'; ?>
                    <?php
                    $edited = can_action('4', 'edited');
                    if (!empty($edited)) {
                        ?>
                        <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
                              action="<?php echo base_url(); ?>admin/client/save_contact/<?php
                              if (!empty($account_details)) {
                                  echo $account_details->user_id;
                              }
                              ?>" method="post" class="form-horizontal  ">

                            <div class="panel panel-custom">
                                <!-- Default panel contents -->
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <?= lang('add_contact') ?>.
                                        <a href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id ?>"
                                           class="btn-sm pull-right">Return to Details</a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="col-sm-8">
                                        <input type="hidden" name="r_url"
                                               value="<?= base_url() ?>admin/client/client_details/<?= $company ?>">
                                        <input type="hidden" name="company" value="<?= $company ?>">
                                        <input type="hidden" name="role_id" value="2">
                                        <input type="hidden" id="user_id" value="<?php
                                        if (!empty($account_details)) {
                                            echo $account_details->user_id;
                                        }
                                        ?>">
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('full_name') ?> <span
                                                        class="text-danger"> *</span></label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($account_details)) {
                                                    echo $account_details->fullname;
                                                }
                                                ?>" placeholder="E.g John Doe" name="fullname" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('email') ?><span
                                                        class="text-danger"> *</span></label>
                                            <div class="col-lg-7">
                                                <input class="form-control" id="check_email_addrees" type="email"
                                                       value="<?php
                                                       if (!empty($user_info)) {
                                                           echo $user_info->email;
                                                       }
                                                       ?>" placeholder="me@domin.com" name="email" required>
                                                <span id="email_addrees_error" class="required"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('phone') ?> </label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($account_details)) {
                                                    echo $account_details->phone;
                                                }
                                                ?>" name="phone" placeholder="+52 782 983 434">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('mobile') ?> <span
                                                        class="text-danger"> *</span></label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($account_details)) {
                                                    echo $account_details->mobile;
                                                }
                                                ?>" name="mobile" placeholder="+8801723611125">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('skype_id') ?> </label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($account_details)) {
                                                    echo $account_details->skype;
                                                }
                                                ?>" name="skype" placeholder="john">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('language') ?></label>
                                            <div class="col-lg-7">
                                                <select name="language" class="form-control">
                                                    <?php foreach ($languages as $lang) : ?>
                                                        <option value="<?= $lang->name ?>"<?php
                                                        if (!empty($account_details->language) && $account_details->language == $lang->name) {
                                                            echo 'selected="selected"';
                                                        } else {
                                                            echo($this->config->item('language') == $lang->name ? ' selected="selected"' : '');
                                                        }
                                                        ?>><?= ucfirst($lang->name) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label"><?= lang('locale') ?></label>
                                            <div class="col-lg-7">
                                                <select class="  form-control" name="locale">
                                                    <?php foreach ($locales as $loc) : ?>
                                                        <option lang="<?= $loc->code ?>"
                                                                value="<?= $loc->locale ?>"<?= ($this->config->item('locale') == $loc->locale ? ' selected="selected"' : '') ?>><?= $loc->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php

                                        if (!empty($account_details->direction)) {
                                            $direction = $account_details->direction;
                                        } else {
                                            $RTL = config_item('RTL');
                                            if (!empty($RTL)) {
                                                $direction = 'rtl';
                                            }
                                        }
                                        ?>
                                        <div class="form-group">
                                            <label for="direction"
                                                   class="control-label col-sm-4"><?= lang('direction') ?></label>
                                            <div class="col-sm-7">
                                                <select name="direction" class="selectpicker"
                                                        data-width="100%">
                                                    <option <?php
                                                    if (!empty($direction)) {
                                                        echo $direction == 'ltr' ? 'selected' : '';
                                                    }
                                                    ?> value="ltr"><?= lang('ltr') ?></option>
                                                    <option <?php
                                                    if (!empty($direction)) {
                                                        echo $direction == 'rtl' ? 'selected' : '';
                                                    }
                                                    ?> value="rtl"><?= lang('rtl') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if (empty($account_details)): ?>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label"><?= lang('username') ?> <span
                                                            class="text-danger">*</span></label>
                                                <div class="col-lg-7">
                                                    <input class="form-control" id="check_username" type="text"
                                                           value="<?= set_value('username') ?>" placeholder="johndoe"
                                                           name="username" required>
                                                    <div class="required" id="check_username_error"></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label"><?= lang('password') ?> <span
                                                            class="text-danger"> *</span></label>
                                                <div class="col-lg-7">
                                                    <input type="password" class="form-control" id="new_password"
                                                           value="<?= set_value('password') ?>" name="password"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label"><?= lang('confirm_password') ?>
                                                    <span
                                                            class="text-danger"> *</span></label>
                                                <div class="col-lg-7">
                                                    <input type="password" class="form-control"
                                                           data-parsley-equalto="#new_password"
                                                           value="<?= set_value('confirm_password') ?>"
                                                           name="confirm_password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                        class="col-lg-4 control-label"><?= lang('send_email') . ' ' . lang('password') ?></label>
                                                <div class="col-lg-6">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="needsclick">
                                                            <input type="checkbox" name="send_email_password">
                                                            <span class="fa fa-check"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="panel ">
                                            <div class="panel-title">
                                                <strong><?= lang('permission') ?></strong>
                                            </div>
                                        </div>
                                        <?php
                                        $all_client_menu = $this->db->where('parent', 0)->order_by('sort')->get('tbl_client_menu')->result();
                                        if (!empty($user_info)) {
                                            $user_menu = $this->db->where('user_id', $user_info->user_id)->get('tbl_client_role')->result();
                                        }
                                        foreach ($all_client_menu as $key => $v_menu) {
                                            ?>
                                            <div class="form-group">
                                                <label
                                                        class="col-lg-6 control-label"><?= lang($v_menu->label) ?></label>
                                                <div class="col-lg-5 checkbox">
                                                    <input data-id="" data-toggle="toggle"
                                                           name="<?= $v_menu->label ?>"
                                                           value="<?= $v_menu->menu_id ?>" <?php
                                                    if (!empty($user_menu)) {
                                                        foreach ($user_menu as $v_u_menu) {
                                                            if ($v_u_menu->menu_id == $v_menu->menu_id) {
                                                                echo 'checked';
                                                            }
                                                        }
                                                    } ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success btn-xs"
                                                           data-offstyle="danger btn-xs" type="checkbox">
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-4">
                                        <button type="submit" id="new_uses_btn"
                                                class="btn btn-primary btn-block"><?= lang('save') . ' ' . lang('client_contact') ?></button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    <?php } ?>
                <?php else: ?>
                    <section class="panel panel-custom">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <strong><?= lang('contacts') ?></strong>
                                <?php
                                $edited = can_action('4', 'edited');
                                if (!empty($edited)) {
                                    ?>
                                    <a href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id ?>/add_contacts"
                                       class="btn-sm pull-right"><?= lang('add_contact') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped" id="datatable_action" cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('full_name') ?></th>
                                    <th><?= lang('email') ?></th>
                                    <th><?= lang('phone') ?> </th>
                                    <th><?= lang('mobile') ?> </th>
                                    <th><?= lang('skype_id') ?></th>
                                    <th class="col-sm-2"><?= lang('last_login') ?> </th>
                                    <th class="col-sm-3"><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($client_contacts)) {
                                    foreach ($client_contacts as $key => $contact) {
                                        ?>
                                        <tr>
                                            <td><?= $contact->fullname ?></td>
                                            <td class="text-info"><?= $contact->email ?> </td>
                                            <td><a href="tel:<?= $contact->phone ?>"><?= $contact->phone ?></a></td>
                                            <td><a href="tel:<?= $contact->mobile ?>"><?= $contact->mobile ?></a></td>
                                            <td><a href="skype:<?= $contact->skype ?>?call"><?= $contact->skype ?></a>
                                            </td>
                                            <?php
                                            if ($contact->last_login == '0000-00-00 00:00:00' || empty($contact->last_login)) {
                                                $login_time = "-";
                                            } else {
                                                $login_time = strftime(config_item('date_format'), strtotime($contact->last_login)) . ' ' . display_time($contact->last_login);
                                            } ?>
                                            <td><?= $login_time ?> </td>
                                            <td class="col-sm-3">
                                                <a href="<?= base_url() ?>admin/client/make_primary/<?= $contact->user_id ?>/<?= $client_details->client_id ?>"
                                                   data-toggle="tooltip" class="btn <?php
                                                if ($client_details->primary_contact == $contact->user_id) {
                                                    echo "btn-success";
                                                } else {
                                                    echo "btn-default";
                                                }
                                                ?> btn-xs " title="<?= lang('primary_contact') ?>">
                                                    <i class="fa fa-chain"></i> </a>
                                                <a href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id . '/add_contacts/' . $contact->user_id ?>"
                                                   class="btn btn-primary btn-xs" title="<?= lang('edit') ?>">
                                                    <i class="fa fa-edit"></i> </a>
                                                <a href="<?= base_url() ?>admin/client/delete_contacts/<?= $client_details->client_id . '/' . $contact->user_id ?>"
                                                   class="btn btn-danger btn-xs" title="<?= lang('delete') ?>">
                                                    <i class="fa fa-trash-o"></i> </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif ?>

            </div>
            <div class="tab-pane <?= $url == 'notes' ? 'active' : '' ?>" id="notes" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>
                                <?= lang('notes') ?>
                            </strong>
                            <button id="new_notes"
                                    class="btn btn-xs pull-right b0"><?= lang('new') . ' ' . lang('notes') ?></button>
                        </div>
                    </div>
                    <?php
                    if ($url == 'notes') {
                        $notes_id = $this->uri->segment(6);
                        $notes_info = $this->db->where('notes_id', $notes_id)->get('tbl_notes')->row();
                    } else {
                        $notes_id = null;
                    }

                    ?>
                    <div class="panel-body">
                        <div class="new_notes mb-lg" style="display: <?= !empty($notes_info) ? 'block' : 'none' ?>">
                            <form action="<?php echo base_url() ?>admin/client/new_notes/<?= $notes_id ?>" method="post"
                                  class="form-horizontal">
                                <textarea name="notes" class="form-control textarea-md"><?php if (!empty($notes_info)) {
                                        echo $notes_info->notes;
                                    } ?></textarea>
                                <input type="hidden" name="client_id" value="<?= $client_details->client_id ?>">

                                <div class="">
                                    <button class="btn btn-primary pull-right mt-lg mb-lg "
                                            type="submit"><?= lang('save') ?></button>
                                </div>

                            </form>
                        </div>
                        <script>
                            $(document).ready(function () {
                                $('#new_notes').click(function () {
                                    $('.new_notes').toggle('slow');
                                });
                            });
                        </script>
                        <table class="table table-striped " cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('description') ?></th>
                                <th><?= lang('added_by') ?></th>
                                <th class="col-sm-3"><?= lang('date') ?> </th>
                                <th class="col-sm-2"><?= lang('action') ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            if (!empty($client_notes)) {
                                foreach ($client_notes as $v_notes) {
                                    $n_user = $this->db->where('user_id', $v_notes->added_by)->get('tbl_users')->row();
                                    if (empty($n_user)) {
                                        $n_user->fullname = '-';
                                        $n_url = '#';
                                    } else {
                                        $n_url = base_url() . 'admin/user/user_details/' . $n_user->user_id;
                                    }
                                    ?>
                                    <tr>
                                        <td><a class="text-info"
                                               href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id . '/notes/' . $v_notes->notes_id ?>"><?= $v_notes->notes ?></a>
                                        </td>
                                        <td>

                                            <a href="<?= $n_url ?>"> <?= $n_user->username ?></a>
                                        </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($v_notes->added_date)) . ' ' . display_time($v_notes->added_date); ?> </td>
                                        <td>
                                            <?= btn_edit('admin/client/client_details/' . $client_details->client_id . '/notes/' . $v_notes->notes_id) ?>
                                            <?php echo btn_delete('admin/client/delete_notes/' . $v_notes->notes_id . '/' . $client_details->client_id); ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            <!--            *************** invoice tab start ************-->

            <div class="tab-pane <?= $url == 'invoice' ? 'active' : '' ?>" id="invoices">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>
                                <?= lang('invoices') ?>
                            </strong>
                            <div class="pull-right">
                                <?php
                                $in_created = can_action('13', 'created');
                                $in_edited = can_action('13', 'edited');
                                if (!empty($in_created) || !empty($in_edited)) {
                                    ?>
                                    <a href="<?= base_url() ?>admin/invoice/manage_invoice/create_invoice/c_<?= $client_details->client_id ?>"
                                       class="btn btn-purple btn-xs"><?= lang('new_invoice') ?></a>
                                <?php } ?>
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/invoice/zipped/invoice/<?= $client_details->client_id ?>"
                                   class="btn btn-success btn-xs"><?= lang('zip_invoice') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped" id="datatable_action" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('reference_no') ?></th>
                                <th><?= lang('date_issued') ?></th>
                                <th><?= lang('due_date') ?> </th>
                                <th class="col-currency"><?= lang('amount') ?> </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            //                            setlocale(LC_ALL, config_item('locale') . ".UTF-8");
                            $total_invoice = 0;
                            if (!empty($client_invoices)) {
                                foreach ($client_invoices as $key => $invoice) {
                                    $total_invoice += $this->invoice_model->invoice_payable($invoice->invoices_id);
                                    ?>
                                    <tr>
                                        <td><a class="text-info"
                                               href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $invoice->invoices_id ?>"><?= $invoice->reference_no ?></a>
                                        </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($invoice->date_saved)); ?> </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($invoice->due_date)); ?> </td>
                                        <td>
                                            <?= display_money($this->invoice_model->invoice_payable($invoice->invoices_id), $cur); ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <strong><?= lang('invoice') . ' ' . lang('amount') ?>:</strong> <strong
                                class="label label-success">
                            <?php
                            echo display_money($total_invoice, client_currency($client_details->client_id));
                            ?>
                        </strong>
                    </div>
                </section>
            </div>
            <!--            *************** invoice tab start ************-->
            <div class="tab-pane <?= $url == 'payment' ? 'active' : '' ?>" id="payments" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('payments') ?>
                            <div class="pull-right">
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/invoice/zipped/payment/<?= $client_details->client_id ?>"
                                   class="btn btn-success btn-xs"><?= lang('zip_payment') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped " id="datatable_action" cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('payment_date') ?></th>
                                    <th><?= lang('invoice_date') ?></th>
                                    <th><?= lang('invoice') ?></th>
                                    <th><?= lang('amount') ?></th>
                                    <th><?= lang('payment_method') ?></th>
                                    <th class="col-sm-3"><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $total_amount = 0;
                                if (!empty($recently_paid)) {
                                    foreach ($recently_paid as $key => $v_paid) {
                                        $invoice_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();
                                        $payment_method = $this->db->where(array('payment_methods_id' => $v_paid->payment_method))->get('tbl_payment_methods')->row();

                                        if ($v_paid->payment_method == '1') {
                                            $label = 'success';
                                        } elseif ($v_paid->payment_method == '2') {
                                            $label = 'danger';
                                        } else {
                                            $label = 'dark';
                                        }
                                        $total_amount += $v_paid->amount;
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_paid->payments_id ?>"> <?= strftime(config_item('date_format'), strtotime($v_paid->payment_date)); ?></a>
                                            </td>
                                            <td><?= strftime(config_item('date_format'), strtotime($invoice_info->date_saved)) ?></td>
                                            <td><a class="text-info"
                                                   href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_paid->invoices_id ?>"><?= $invoice_info->reference_no; ?></a>
                                            </td>
                                            <?php $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id); ?>
                                            <td><?= display_money($v_paid->amount, $currency->symbol) ?></td>
                                            <td><span
                                                        class="label label-<?= $label ?>"><?= !empty($payment_method->method_name) ? $payment_method->method_name : '-'; ?></span>
                                            </td>
                                            <td>
                                                <?= btn_edit('admin/invoice/all_payments/' . $v_paid->payments_id) ?>
                                                <?= btn_view('admin/invoice/manage_invoice/payments_details/' . $v_paid->payments_id) ?>
                                                <?= btn_delete('admin/invoice/delete/delete_payment/' . $v_paid->payments_id) ?>
                                                <a data-toggle="tooltip" data-placement="top"
                                                   href="<?= base_url() ?>admin/invoice/send_payment/<?= $v_paid->payments_id . '/' . $v_paid->amount ?>"
                                                   title="<?= lang('send_email') ?>"
                                                   class="btn btn-xs btn-success">
                                                    <i class="fa fa-envelope"></i> </a>
                                            </td>
                                        </tr>

                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <strong><?= lang('paid_amount') ?>:</strong> <strong class="label label-success">
                            <?= display_money($total_amount, client_currency($client_details->client_id)); ?>
                        </strong>
                    </div>
                </section>
            </div>

            <!--            *************** invoice tab start ************-->
            <div class="tab-pane <?= $url == 'estimate' ? 'active' : '' ?>" id="estimates" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>
                                <?= lang('estimates') ?>
                            </strong>
                            <div class="pull-right">
                                <?php
                                $es_created = can_action('14', 'created');
                                $es_edited = can_action('14', 'edited');
                                if (!empty($es_created) || !empty($es_edited)) {
                                    ?>
                                    <a href="<?= base_url() ?>admin/estimates/index/edit_estimates/c_<?= $client_details->client_id ?>"
                                       class="btn btn-purple btn-xs"><?= lang('new_estimate') ?></a>
                                <?php } ?>
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/invoice/zipped/estimate/<?= $client_details->client_id ?>"
                                   class="btn btn-success btn-xs"><?= lang('zip_estimate') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped " cellspacing="0" id="datatable_action"
                               width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('reference_no') ?></th>
                                <th><?= lang('date_issued') ?></th>
                                <th><?= lang('due_date') ?> </th>
                                <th class="col-currency"><?= lang('amount') ?> </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_estimate = 0;
                            if (!empty($client_estimates)) {
                                foreach ($client_estimates as $key => $estimate) {
                                    $total_estimate += $this->estimates_model->estimate_calculation('estimate_amount', $estimate->estimates_id);
                                    ?>
                                    <tr>
                                        <td><a class="text-info"
                                               href="<?= base_url() ?>admin/estimates/index/estimates_details//<?= $estimate->estimates_id ?>"><?= $estimate->reference_no ?></a>
                                        </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($estimate->date_saved)); ?> </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($estimate->due_date)); ?> </td>
                                        <td>
                                            <?php echo display_money($this->estimates_model->estimate_calculation('estimate_amount', $estimate->estimates_id), client_currency($client_details->client_id)); ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <strong><?= lang('estimate') . ' ' . lang('amount') ?>:</strong> <strong
                                class="label label-success">
                            <?= display_money($total_estimate, client_currency($client_details->client_id)); ?>
                        </strong>
                    </div>
                </section>
            </div>
            <div class="tab-pane <?= $url == 'proposal' ? 'active' : '' ?>" id="proposals"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('all_proposals') ?>

                            <div class="pull-right">
                                <?php
                                $prop_created = can_action('140', 'created');
                                $prop_edited = can_action('140', 'edited');
                                if (!empty($prop_created) || !empty($prop_edited)) {
                                    ?>
                                    <a href="<?= base_url() ?>admin/proposals/index/client/<?= $client_details->client_id ?>"
                                       class="btn btn-purple btn-xs"><?= lang('create_proposal') ?></a>
                                <?php } ?>
                                <a data-toggle="modal" data-target="#myModal"
                                   href="<?= base_url() ?>admin/invoice/zipped/proposal/<?= $client_details->client_id ?>"
                                   class="btn btn-success btn-xs"><?= lang('zip_proposal') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped " id="datatable_action"
                                   cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('proposal') ?> #</th>
                                    <th><?= lang('proposal_date') ?></th>
                                    <th><?= lang('expire_date') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                                        <th class="col-sm-3 hidden_print"><?= lang('action') ?></th>
                                    <?php } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                if (!empty($all_proposals_info)) {
                                    foreach ($all_proposals_info as $v_proposals) {
                                        $can_edit = $this->client_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $v_proposals->proposals_id));
                                        $can_delete = $this->client_model->can_action('tbl_proposals', 'delete', array('proposals_id' => $v_proposals->proposals_id));

                                        if ($v_proposals->status == 'pending') {
                                            $label = "info";
                                        } elseif ($v_proposals->status == 'accepted') {
                                            $label = "success";
                                        } else {
                                            $label = "danger";
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <a class="text-info"
                                                   href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= $v_proposals->reference_no ?></a>
                                                <?php if ($v_proposals->convert == 'Yes') {
                                                    if ($v_proposals->convert_module == 'invoice') {
                                                        $c_url = base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_proposals->convert_module_id;
                                                        $text = lang('invoiced');
                                                    } else {
                                                        $text = lang('estimated');
                                                        $c_url = base_url() . 'admin/estimates/index/estimates_details/' . $v_proposals->convert_module_id;
                                                    }
                                                    if (!empty($c_url)) { ?>
                                                        <p class="text-sm m0 p0">
                                                            <a class="text-success"
                                                               href="<?= $c_url ?>">
                                                                <?= $text ?>
                                                            </a>
                                                        </p>
                                                    <?php }
                                                } ?>
                                            </td>
                                            <td><?= strftime(config_item('date_format'), strtotime($v_proposals->proposal_date)) ?></td>
                                            <td><?= strftime(config_item('date_format'), strtotime($v_proposals->due_date)) ?>
                                                <?php
                                                if (strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == 'pending' || strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == ('draft')) { ?>
                                                    <span
                                                            class="label label-danger "><?= lang('expired') ?></span>
                                                <?php }
                                                ?>
                                            </td>
                                            <?php ?>
                                            <td><span
                                                        class="label label-<?= $label ?>"><?= lang($v_proposals->status) ?></span>
                                            </td>
                                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                                <td>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <?= btn_edit('admin/proposals/index/edit_proposals/' . $v_proposals->proposals_id) ?>
                                                    <?php }
                                                    if (!empty($can_delete) && !empty($deleted)) {
                                                        ?>
                                                        <?= btn_delete('admin/proposals/delete/delete_proposals/' . $v_proposals->proposals_id) ?>
                                                    <?php } ?>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <div class="btn-group">
                                                            <button
                                                                    class="btn btn-xs btn-default dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <?= lang('change_status') ?>
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu animated zoomIn">
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/proposals/index/email_proposals/<?= $v_proposals->proposals_id ?>"><?= lang('send_email') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= lang('view_details') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/proposals/index/proposals_history/<?= $v_proposals->proposals_id ?>"><?= lang('history') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/proposals/change_status/declined/<?= $v_proposals->proposals_id ?>"><?= lang('declined') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/proposals/change_status/accepted/<?= $v_proposals->proposals_id ?>"><?= lang('accepted') ?></a>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--            *************** Transactions tab start ************-->
            <div class="tab-pane" id="transaction" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('transactions') ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped " cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('date') ?></th>
                                <th><?= lang('account') ?></th>
                                <th><?= lang('type') ?> </th>
                                <th><?= lang('amount') ?> </th>
                                <th><?= lang('action') ?> </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_income = 0;
                            $total_expense = 0;

                            if (!empty($client_transactions)):foreach ($client_transactions as $v_transactions) :
                                $account_info = $this->client_model->check_by(array('account_id' => $v_transactions->account_id), 'tbl_accounts');
                                ?>
                                <tr>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_transactions->date)); ?></td>
                                    <td><?= $account_info->account_name ?></td>
                                    <td><?= $v_transactions->type ?></td>
                                    <td><?= display_money($v_transactions->amount, default_currency()); ?></td>
                                    <td>
                                        <?php

                                        if ($v_transactions->type == 'Income') {
                                            $total_income += $v_transactions->amount;
                                            ?>
                                            <?= btn_edit('admin/transactions/deposit/' . $v_transactions->transactions_id) ?>
                                            <?= btn_delete('admin/transactions/delete_deposit/' . $v_transactions->transactions_id) ?>
                                            <?php
                                        } else {
                                            $total_expense += $v_transactions->amount;
                                            ?>
                                            <?= btn_edit('admin/transactions/expense/' . $v_transactions->transactions_id) ?>
                                            <?= btn_delete('admin/transactions/delete_expense/' . $v_transactions->transactions_id) ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                                ?>

                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <small><strong><?= lang('total_income') ?>:</strong><strong
                                    class="label label-success"><?= display_money($total_income, default_currency()); ?></strong>
                        </small>
                        <small class="text-danger pull-right">
                            <strong><?= lang('total_expense') ?>:</strong>
                            <strong
                                    class="label label-danger"><?= display_money($total_expense, default_currency()); ?></strong>
                        </small>
                    </div>
                </section>
            </div>
            <!--            *************** Project tab start ************-->
            <div class="tab-pane" id="projects" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('project') ?>
                            <?php
                            $pro_created = can_action('57', 'created');
                            $pro_edited = can_action('57', 'edited');
                            if (!empty($pro_created) || !empty($pro_edited)) {
                                ?>
                                <a href="<?= base_url() ?>admin/projects/index/client_project/<?= $client_details->client_id ?>"
                                   class="btn-sm pull-right"><?= lang('new_project') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped " cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('project_name') ?></th>
                                    <th><?= lang('end_date') ?></th>
                                    <th><?= lang('status') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_project)):foreach ($all_project as $v_project):
                                    $progress = $this->items_model->get_project_progress($v_project->project_id);
                                    ?>
                                    <tr>
                                        <td><a class="text-info"
                                               href="<?= base_url() ?>admin/projects/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                            <?php if (strtotime(date('Y-m-d')) > strtotime($v_project->end_date) && $progress < 100) { ?>
                                                <span
                                                        class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                            <?php } ?>

                                            <div class="progress progress-xs progress-striped active">
                                                <div
                                                        class="progress-bar progress-bar-<?php echo ($progress >= 100) ? 'success' : 'primary'; ?>"
                                                        data-toggle="tooltip"
                                                        data-original-title="<?= $progress ?>%"
                                                        style="width: <?= $progress; ?>%"></div>
                                            </div>

                                        </td>
                                        <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                        <td><?php
                                            if (!empty($v_project->project_status)) {
                                                if ($v_project->project_status == 'completed') {
                                                    $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                                } elseif ($v_project->project_status == 'in_progress') {
                                                    $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                                } elseif ($v_project->project_status == 'cancel') {
                                                    $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                                } else {
                                                    $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                                }
                                                echo $status;
                                            }
                                            ?>      </td>
                                    </tr>
                                <?php
                                endforeach;
                                endif;
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <!--            *************** Tickets tab start ************-->
            <?php

            if (!empty($client_details->primary_contact)) {
                $primary_contact = 'c_' . $client_details->primary_contact;
            } else {
                $primary_contact = null;
            }
            ?>
            <div class="tab-pane" id="ticket" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('tickets') ?>
                            <a href="<?= base_url() ?>admin/tickets/index/edit_tickets/<?= $primary_contact ?>"
                               class="btn-sm pull-right"><?= lang('new_ticket') ?></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped " cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('subject') ?></th>
                                    <th class="col-date"><?= lang('date') ?></th>
                                    <?php if ($this->session->userdata('user_type') == '1') { ?>
                                        <th><?= lang('reporter') ?></th>
                                    <?php } ?>
                                    <th><?= lang('status') ?></th>
                                    <th><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_tickets_info)) {
                                    foreach ($all_tickets_info as $v_tickets_info) {
                                        if ($v_tickets_info->reporter != 0) {
                                            $profile_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_account_details')->row();
                                            if (!empty($profile_info->company) && $profile_info->company == $client_details->client_id) {
                                                if ($v_tickets_info->status == 'open') {
                                                    $s_label = 'danger';
                                                } elseif ($v_tickets_info->status == 'closed') {
                                                    $s_label = 'success';
                                                } else {
                                                    $s_label = 'default';
                                                }
                                                ?>
                                                <tr>
                                                    <td><a class="text-info"
                                                           href="<?= base_url() ?>admin/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>"><?= $v_tickets_info->subject ?></a>
                                                    </td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?></td>
                                                    <?php if ($this->session->userdata('user_type') == '1') { ?>

                                                        <td>
                                                            <a class="pull-left recect_task  ">
                                                                <?php if (!empty($profile_info)) {
                                                                    ?>
                                                                    <img style="width: 30px;margin-left: 18px;
                                                         height: 29px;
                                                         border: 1px solid #aaa;"
                                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                                         class="img-circle">
                                                                <?php } ?>

                                                                <?=
                                                                ($profile_info->fullname)
                                                                ?>
                                                            </a>
                                                        </td>

                                                    <?php } ?>
                                                    <?php
                                                    if ($v_tickets_info->status == 'in_progress') {
                                                        $status = 'In Progress';
                                                    } else {
                                                        $status = $v_tickets_info->status;
                                                    }
                                                    ?>
                                                    <td><span
                                                                class="label label-<?= $s_label ?>"><?= ucfirst($status) ?></span>
                                                    </td>
                                                    <td>
                                                        <?= btn_edit('admin/tickets/index/edit_tickets/' . $v_tickets_info->tickets_id) ?>
                                                        <?= btn_delete('admin/tickets/delete/delete_tickets/' . $v_tickets_info->tickets_id) ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <!--            *************** Bugs tab start ************-->
            <div class="tab-pane" id="bugs" style="position: relative;">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('bugs') ?>
                            <?php
                            $bugs_created = can_action('58', 'created');
                            $bugs_edited = can_action('58', 'edited');
                            if (!empty($bugs_created) || !empty($bugs_edited)) {
                                ?>
                                <a href="<?= base_url() ?>admin/bugs/index/<?= $primary_contact ?>"
                                   class="btn-sm pull-right"><?= lang('new_bugs') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped " cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('bug_title') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <th><?= lang('priority') ?></th>
                                    <?php if ($this->session->userdata('user_type') == '1') { ?>
                                        <th><?= lang('reporter') ?></th>
                                    <?php } ?>
                                    <th><?= lang('assigned_to') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_bug_info)) {
                                    foreach ($all_bug_info as $v_bugs) {
                                        $profile = $this->db->where(array('user_id' => $v_bugs->reporter))->get('tbl_account_details')->row();
                                        if (!empty($profile->company) && $profile->company == $client_details->client_id) {
                                            $total_bugs += count($v_bugs->bug_id);
                                            $reporter = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_users')->row();
                                            if ($reporter->role_id == '1') {
                                                $badge = 'danger';
                                            } elseif ($reporter->role_id == '2') {
                                                $badge = 'info';
                                            } else {
                                                $badge = 'primary';
                                            }

                                            if ($v_bugs->bug_status == 'unconfirmed') {
                                                $label = 'warning';
                                            } elseif ($v_bugs->bug_status == 'confirmed') {
                                                $label = 'info';
                                            } elseif ($v_bugs->bug_status == 'in_progress') {
                                                $label = 'primary';
                                            } elseif ($v_bugs->bug_status == 'resolved') {
                                                $label = 'purple';
                                            } else {
                                                $label = 'success';
                                            }
                                            ?>
                                            <tr>
                                                <td><a class="text-info" style="<?php
                                                    if ($v_bugs->bug_status == 'resolve') {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a>
                                                </td>
                                                </td>
                                                <td>
                                                    <span
                                                            class="label label-<?= $label ?>"><?= lang("$v_bugs->bug_status") ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($v_bugs->priority == 'High') {
                                                        $plabel = 'danger';
                                                    } elseif ($v_bugs->priority == 'Medium') {
                                                        $plabel = 'info';
                                                    } else {
                                                        $plabel = 'primary';
                                                    }
                                                    ?>
                                                    <span
                                                            class="badge btn-<?= $plabel ?>"><?= ucfirst($v_bugs->priority) ?></span>
                                                </td>
                                                <td>
                                                    <span
                                                            class="badge btn-<?= $badge ?> "><?= $reporter->username ?></span>
                                                </td>
                                                <td>
                                                    <?php

                                                    if ($v_bugs->permission != 'all') {
                                                        $get_permission = json_decode($v_bugs->permission);

                                                        if (!empty($get_permission)) :
                                                            foreach ($get_permission as $permission => $v_permission) :
                                                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                                if ($user_info->role_id == 1) {
                                                                    $label = 'circle-danger';
                                                                } else {
                                                                    $label = 'circle-success';
                                                                }
                                                                $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                                ?>

                                                                <a href="#" data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="<?= $profile_info->fullname ?>"><img
                                                                            src="<?= base_url() . $profile_info->avatar ?>"
                                                                            class="img-circle img-xs" alt="">
                                                                    <span style="margin: 0px 0 8px -10px;"
                                                                          class="circle <?= $label ?>  circle-lg"></span>
                                                                </a>

                                                            <?php
                                                            endforeach;
                                                        endif;
                                                    } else { ?>
                                                        <strong><?= lang('everyone') ?></strong>
                                                        <i
                                                                title="<?= lang('permission_for_all') ?>"
                                                                class="fa fa-question-circle" data-toggle="tooltip"
                                                                data-placement="top"></i>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php if ($this->session->userdata('user_type') == 1) { ?>
                                                        <span data-placement="top" data-toggle="tooltip"
                                                              title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/bugs/update_users/<?= $v_bugs->bug_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <!--            *************** Bugs tab start ************-->
            <div class="tab-pane <?= $url == 'reminder' ? 'active' : '' ?>" id="reminder">
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#reminder_manage"
                                              data-toggle="tab"><?= lang('reminder') . ' ' . lang('list') ?></a>
                        </li>
                        <li class=""><a href="#reminder_create"
                                        data-toggle="tab"><?= lang('set') . ' ' . lang('reminder') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane active" id="reminder_manage">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= lang('description') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('remind') ?></th>
                                        <th><?= lang('notified') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $all_reminder = $this->db->where(array('module' => 'client', 'module_id' => $client_details->client_id))->get('tbl_reminders')->result();
                                    if (!empty($all_reminder)) {
                                        foreach ($all_reminder as $v_reminder):
                                            $remind_user_info = $this->db->where('user_id', $v_reminder->user_id)->get('tbl_account_details')->row();
                                            ?>
                                            <tr>
                                                <td><?= $v_reminder->description ?></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_reminder->date)) . ' ' . display_time($v_reminder->date) ?></td>
                                                <td>
                                                    <a href="<?= base_url() ?>admin/user/user_details/<?= $v_reminder->user_id ?>"> <?= $remind_user_info->fullname ?></a>
                                                </td>
                                                <td><?= $v_reminder->notified ?></td>
                                                <td>
                                                    <?= btn_delete('admin/invoice/delete_reminder/' . $v_reminder->module . '/' . $v_reminder->module_id . '/' . $v_reminder->reminder_id); ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5"><?= lang('nothing_to_display') ?></td>
                                        </tr>
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="reminder_create">
                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                                  id="form"
                                  action="<?php echo base_url(); ?>admin/invoice/reminder/client/<?= $client_details->client_id ?>/<?php
                                  if (!empty($reminder_info)) {
                                      echo $reminder_info->reminder_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('date_to_notified') ?> <span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                            <input type="text" name="date"
                                                   class="form-control datetimepicker"
                                                   value="<?php
                                                   if (!empty($reminder_info->date)) {
                                                       echo $reminder_info->date;
                                                   } else {
                                                       echo date('Y-m-d h:i');
                                                   }
                                                   ?>"
                                                   data-date-min-date="<?= date('Y-m-d'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- End discount Fields -->
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                                    <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($reminder_info)) {
                                echo $reminder_info->description;
                            }
                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('set_reminder_to') ?> <span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <select class="form-control select_box" name="user_id" style="width: 100%">
                                            <?php

                                            if (!empty($permission_user)) {
                                                foreach ($permission_user as $key => $v_users) {
                                                    ?>
                                                    <option <?php
                                                    if (!empty($reminder_info)) {
                                                        echo $reminder_info->user_id == $v_users->user_id ? 'selected' : null;
                                                    }
                                                    ?> value="<?= $v_users->user_id ?>"><?= $v_users->fullname ?></option>
                                                <?php }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick">
                                                <input type="checkbox" value="Yes"
                                                    <?php if (!empty($reminder_info) && $reminder_info->notify_by_email == 'Yes') {
                                                        echo 'checked';
                                                    } ?> name="notify_by_email">
                                                <span class="fa fa-check"></span>
                                                <?= lang('send_also_email_this_reminder') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <button type="submit" class="btn btn-purple"><?= lang('update') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <link rel="stylesheet"
                      href="<?= base_url() ?>assets/plugins/datetimepicker/jquery.datetimepicker.min.css">
                <?php include_once 'assets/plugins/datetimepicker/jquery.datetimepicker.full.php'; ?>

                <script type="text/javascript">
                    init_datepicker();

                    // Date picker init with selected timeformat from settings
                    function init_datepicker() {
                        var datetimepickers = $('.datetimepicker');
                        if (datetimepickers.length == 0) {
                            return;
                        }
                        var opt_time;
                        // Datepicker with time
                        $.each(datetimepickers, function () {
                            opt_time = {
                                lazyInit: true,
                                scrollInput: false,
                                format: 'Y-m-d H:i',
                            };

                            opt_time.formatTime = 'H:i';
                            // Check in case the input have date-end-date or date-min-date
                            var max_date = $(this).data('date-end-date');
                            var min_date = $(this).data('date-min-date');
                            if (max_date) {
                                opt_time.maxDate = max_date;
                            }
                            if (min_date) {
                                opt_time.minDate = min_date;
                            }
                            // Init the picker
                            $(this).datetimepicker(opt_time);
                        });
                    }
                </script>
            </div>
            <!--            *************** invoice tab start ************-->
            <div class="tab-pane <?= $url == 'filemanager' ? 'active' : '' ?>" id="filemanager">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>
                                <?= lang('filemanager') ?>
                            </strong>
                        </div>
                    </div>
                    <link rel="stylesheet" type="text/css"
                          href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
                    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
                    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
                    <link rel="stylesheet" type="text/css" media="screen"
                          href="<?php echo site_url('assets/plugins/elFinder/css/elfinder.min.css'); ?>">
                    <link rel="stylesheet" type="text/css" media="screen"
                          href="<?php echo site_url('assets/plugins/elFinder/themes/Material/css/theme.css'); ?>">
                    <link rel="stylesheet" type="text/css" media="screen"
                          href="<?php echo site_url('assets/plugins/elFinder/themes/Material/css/theme-light.css'); ?>">

                    <script
                            src="<?php echo site_url('assets/plugins/elFinder/js/elfinder.min.js'); ?>"></script>
                    <script type="text/javascript" charset="utf-8">
                        $().ready(function () {
                            window.setTimeout(function () {
                                var elf = $('#elfinder').elfinder({
                                    // lang: 'ru',             // language (OPTIONAL)
                                    url: '<?= site_url()?>admin/client/elfinder_init/<?= $client_details->client_id?>',  // connector URL (REQUIRED)
                                    height: 600,
                                    uiOptions: {
                                        // toolbar configuration
                                        toolbar: [
                                            ['back', 'forward'],
//                     ['mkdir'],
                                            ['mkdir', 'mkfile', 'upload'],
                                            ['open', 'download', 'getfile'],
                                            ['info'],
                                            ['quicklook'],
                                            ['copy', 'cut', 'paste'],
                                            ['rm'],
                                            ['duplicate', 'rename', 'edit', 'resize'],
                                            ['extract', 'archive'],
                                            ['search'],
                                            ['view'],
                                        ],
                                    }

                                }).elfinder('instance');
                            }, 200);
                        });
                    </script>
                    <div class="">
                        <div id="elfinder"></div>
                    </div>
                </section>
            </div>
            <div class="tab-pane <?= $url == 'map' ? 'active' : '' ?>" id="client_map">
                <section class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('map') ?>
                            <div class="pull-right" style="margin-top: -20px">
                                <?php echo form_open(base_url('admin/client/update_latitude/' . $client_details->client_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
                                <div class="col-md-5">
                                    <label class="text-sm">
                                        <a href="#"
                                           onclick="fetch_lat_long_from_google_cprofile(); return false;"
                                           data-toggle="tooltip"
                                           data-title="<?php echo lang('fetch_from_google') . ' - ' . lang('customer_fetch_lat_lng_usage'); ?>"><i
                                                    id="gmaps-search-icon" class="fa fa-google"
                                                    aria-hidden="true"></i></a>
                                        <?= lang('latitude') . '( ' . lang('google_map') . ' )' ?>
                                    </label>
                                    <div class="">
                                        <input type="text" style="height: 20px" class="form-control text-sm "
                                               value="<?php
                                               if (!empty($client_details->latitude)) {
                                                   echo $client_details->latitude;
                                               }
                                               ?>" name="latitude">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label
                                            class="text-sm"><?= lang('longitude') . '( ' . lang('google_map') . ' )' ?></label>
                                    <div class="">
                                        <input type="text" style="height: 20px" class="form-control input-sm text-sm"
                                               value="<?php
                                               if (!empty($client_details->longitude)) {
                                                   echo $client_details->longitude;
                                               }
                                               ?>" name="longitude">
                                    </div>
                                </div>


                                <textarea class="form-control hidden" name="address"><?php
                                    if (!empty($client_details->address)) {
                                        echo $client_details->address;
                                    }
                                    ?></textarea>
                                <input type="hidden" class="form-control" value="<?php
                                if (!empty($client_details->city)) {
                                    echo $client_details->city;
                                }
                                ?>" name="city">
                                <select name="country" class="form-control hidden"
                                        style="width: 100%">
                                    <option selected
                                            value="<?= $client_details->country ?>"><?= $client_details->country ?></option>
                                </select>
                                <div class="col-md-2" style="margin-top: -6px">
                                    <label
                                            class="" style="visibility: hidden"><?= lang('longitude') ?></label>
                                    <button type="submit"
                                            class="btn btn-sm btn-primary btn-xs"><?= lang('update') ?></button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">


                        <style type="text/css">
                            .client_map {
                                height: 500px;
                            }
                        </style>

                        <?php
                        $google_api_key = config_item('google_api_key');
                        if ($google_api_key !== '') {
                            if ($client_details->longitude == '' && $client_details->latitude == '') {
                                echo lang('map_notice');
                            } else {
                                echo '<div id="map" class="client_map"></div>';
                            } ?>
                            <script>
                                var latitude = '<?= $client_details->latitude?>';
                                var longitude = '<?= $client_details->longitude?>';
                                var marker = '<?= $client_details->name?>';
                            </script>
                            <script src="<?= base_url() ?>assets/plugins/map/map.js"></script>
                            <script async defer
                                    src="https://maps.googleapis.com/maps/api/js?key=<?= $google_api_key ?>&callback=initMap"></script>

                        <?php } else {
                            echo lang('setup_google_api_key_map');
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function fetch_lat_long_from_google_cprofile() {
        var data = {};
        data.address = $('textarea[name="address"]').val();
        data.city = $('input[name="city"]').val();
        data.country = $('select[name="country"] option:selected').text();
        console.log(data);
        $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
        $.post('<?= base_url()?>admin/global_controller/fetch_address_info_gmaps', data).done(function (data) {
            data = JSON.parse(data);
            $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
            if (data.response.status == 'OK') {
                $('input[name="latitude"]').val(data.lat);
                $('input[name="longitude"]').val(data.lng);
            } else {
                if (data.response.status == 'ZERO_RESULTS') {
                    toastr.warning("<?php echo lang('g_search_address_not_found'); ?>");
                } else {
                    toastr.warning(data.response.status);
                }
            }
        });
    }
</script>
