<?php include_once 'assets/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$user_id = $this->session->userdata('user_id');
$profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();

$user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
$languages = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
$locales = $this->db->order_by('name')->get('tbl_locales')->result();
?>
<style type="text/css">
    #id_error_msg {
        display: none;
    }

    .form-groups-bordered > .form-group {
        padding-bottom: 0px
    }
</style>
<div class="row">
    <div class="col-sm-6 wrap-fpanel">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('update_profile') ?></strong>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" id="update_profile" enctype="multipart/form-data" style="display: initial"
                      action="<?php echo base_url(); ?>admin/settings/profile_updated" method="post"
                      class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('full_name') ?></strong> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="fullname"
                                   value="<?= $profile_info->fullname ?>" required>
                        </div>
                    </div>
                    <input type="hidden" id="user_id" class="form-control" value="<?= my_id() ?>">
                    <?php
                    if ($profile_info->company > 0) {
                        $client_info = $this->db->where('client_id', $profile_info->company)->get('tbl_client')->row();

                        $email = $client_info->email;
                        $company_address = $client_info->address;
                        $company_vat = $client_info->vat;
                        $company_name = $client_info->name;
                        ?>
                        <div class="form-group">
                            <label class="col-lg-4 control-label"><strong>
                                    <?= (lang('company')) ?> </strong></label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="name" value="<?= $company_name ?>"
                                       required>
                                <input type="hidden" class="form-control" name="client_id"
                                       value="<?= $client_info->client_id ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-lg-4 control-label"><strong><?= (lang('company_email')) ?></strong>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="email" value="<?= $email ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label
                                class="col-lg-4 control-label"><strong><?= (lang('company_address')) ?></strong>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="address" value="<?= $company_address ?>"
                                       required>
                            </div>
                        </div>

                    <?php } ?>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('phone') ?></strong></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="phone" value="<?= $profile_info->phone ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('language') ?></strong></label>
                        <div class="col-lg-8">
                            <select name="language" class="form-control">

                                <?php foreach ($languages as $lang) : ?>
                                    <option
                                        value="<?= $lang->name ?>"<?= ($profile_info->language == $lang->name ? ' selected="selected"' : '') ?>><?= ucfirst($lang->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('locale') ?></strong></label>
                        <div class="col-lg-8">
                            <select class="  form-control" name="locale">

                                <?php foreach ($locales as $loc) : ?>
                                    <option
                                        value="<?= $loc->locale ?>"<?= ($profile_info->locale == $loc->locale ? ' selected="selected"' : '') ?>><?= $loc->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('profile_photo') ?></strong></label>
                        <div class="col-lg-7">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 210px;">
                                    <?php if ($profile_info->avatar != '') : ?>
                                        <img src="<?php echo base_url() . $profile_info->avatar; ?>">
                                    <?php else: ?>
                                        <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">
                                    <?php endif; ?>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="avatar" value="upload"
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
                        <label class="col-lg-4 control-label"></label>
                        <div class="col-lg-8">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('update_profile') ?></button>
                        </div>
                    </div>
                </form>

                <h1 class="page-header" style="font-size: 16px;font-weight: bold"><?= lang('change_email') ?></h1>
                <form data-parsley-validate="" novalidate="" role="form" style="display: initial"
                      action="<?php echo base_url(); ?>admin/settings/change_email" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('password') ?></strong></label>
                        <div class="col-lg-8">
                            <input type="password" id="change_email_password" class="form-control" name="password"
                                   placeholder="<?= lang('password_current_password') ?>" required>
                            <span class="required" id="email_password"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('new_email') ?></strong></label>
                        <div class="col-lg-8">
                            <input type="email" id="check_email_addrees" class="form-control" name="email"
                                   placeholder="<?= lang('new_email') ?>"
                                   required>
                            <span id="email_addrees_error" class="required"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"></label>
                        <div class="col-lg-8">
                            <button type="submit" id="new_uses_btn"
                                    class="btn btn-sm btn-primary"><?= lang('change_email') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-6 wrap-fpanel">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('change_password') ?></strong>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" data-parsley-validate="" novalidate=""
                      action="<?php echo base_url(); ?>admin/settings/set_password"
                      method="post" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?= lang('old_password') ?><span
                                class="required"> *</span></label>
                        <div class="col-sm-7">
                            <input type="password" id="old_password"
                                   name="old_password" value="" class="form-control"
                                   placeholder="<?= lang('enter_your') . ' ' . lang('old_password') ?>"/>
                            <span class="required" id="old_password_error"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?= lang('new_password') ?><span
                                class="required"> *</span></label>
                        <div class="col-sm-7">
                            <input type="password"
                                   name="new_password" id="new_password" value="" class="form-control"
                                   placeholder="Enter Your <?= lang('new_password') ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-4 control-label"><?= lang('confirm_password') ?> <span
                                class="required"> *</span></label>
                        <div class="col-sm-7">
                            <input type="password" id="confirm_password" data-parsley-equalto="#new_password"
                                   name="confirm_password" value="" class="form-control"
                                   placeholder="Enter Your <?= lang('confirm_password') ?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-5">
                            <button id="old_password_button" type="submit"
                                    class="btn btn-primary"><?= lang('change_password') ?></button>
                        </div>
                    </div>
                </form>
                <h1 class="page-header" style="font-size: 16px;font-weight: bold"><?= lang('change_username') ?></h1>
                <form role="form" data-parsley-validate="" novalidate="" style="display: initial"
                      action="<?php echo base_url(); ?>admin/settings/change_username" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('password') ?></strong></label>
                        <div class="col-lg-8">
                            <input type="password" id="change_username"
                                   class="form-control" name="password"
                                   placeholder="<?= lang('password_current_password') ?>" required>
                            <span class="required" id="username_error"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"><strong><?= lang('new_username') ?></strong></label>
                        <div class="col-lg-8">
                            <input type="text" id="check_username" class="form-control" name="username"
                                   placeholder="<?= lang('new_username') ?>" required>
                            <span class="required" id="check_username_error"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"></label>
                        <div class="col-lg-8">
                            <button type="submit" id="change_username_btn"
                                    class="btn btn-sm btn-primary"><?= lang('change_username') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>   