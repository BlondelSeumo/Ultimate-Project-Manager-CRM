<?php include_once 'asset/admin-ajax.php'; ?>
<?php
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
<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('all_users') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_user') ?></a>
        </li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">

                <thead>
                <tr>
                    <th class="col-sm-1"><?= lang('photo') ?></th>
                    <th><?= lang('name') ?></th>
                    <th><?= lang('username') ?></th>
                    <th><?= lang('phone') ?></th>
                    <th><?= lang('mobile') ?></th>
                    <th><?= lang('skype_id') ?></th>
                    <th><?= lang('last_login') ?> </th>
                    <th class="col-sm-1"><?= lang('status') ?></th>
                </tr>
                </thead>
                <tbody>
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "client/user/usersList";
                    });
                </script>
                </tbody>
            </table>
        </div>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
            <form role="form" id="userform" enctype="multipart/form-data"
                  action="<?php echo base_url(); ?>client/user/save_user" method="post"
                  class="form-horizontal form-groups-bordered">
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        if (!empty($login_info->user_id)) {
                            $profile_info = $this->user_model->check_by(array('user_id' => $login_info->user_id), 'tbl_account_details');
                        }
                        ?>
                        <input type="hidden" id="username_flag" value="">
                        <input type="hidden" name="user_id" value="<?php
                        if (!empty($login_info)) {
                            echo $login_info->user_id;
                        }
                        ?>">
                        <input type="hidden" name="account_details_id" value="<?php
                        if (!empty($profile_info)) {
                            echo $profile_info->account_details_id;
                        }
                        ?>">

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('full_name') ?> </strong><span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->fullname;
                                }
                                ?>" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_name') ?>" name="fullname"
                                       required>
                            </div>
                        </div>
                        <?php if (empty($login_info->user_id)) { ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><strong> <?= lang('username'); ?></strong><span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="username"
                                           placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_username') ?>"
                                           value="<?php
                                           if (!empty($login_info)) {
                                               echo $login_info->username;
                                           }
                                           ?>" class="input-sm form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label"><strong><?= lang('password') ?> </strong><span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" value="" id="password" placeholder="<?= lang('password') ?>"
                                           name="password" class="input-sm form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-sm-4 control-label"><strong><?= lang('confirm_password') ?> </strong><span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" placeholder="<?= lang('confirm_password') ?>"
                                           name="confirm_password" class="input-sm form-control">
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="username"
                                   placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_username') ?>" value="<?php
                            if (!empty($login_info)) {
                                echo $login_info->username;
                            }
                            ?>" class="input-sm form-control" required>
                        <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('email') ?> </strong><span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="email"
                                       placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_email') ?>"
                                       name="email" value="<?php
                                if (!empty($login_info)) {
                                    echo $login_info->email;
                                }
                                ?>" class="input-sm form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('locale') ?></strong></label>
                            <div class="col-lg-8">
                                <select class="  form-control select_box" style="width: 100%" name="locale">

                                    <?php
                                    $locales = $this->db->get('tbl_locales')->result();
                                    foreach ($locales as $loc) :
                                        ?>
                                        <option lang="<?= $loc->code ?>" value="<?= $loc->locale ?>" <?php
                                        if (!empty($profile_info)) {
                                            if ($profile_info->locale == $loc->locale) {
                                                echo 'selected';
                                            }
                                        } else {
                                            echo($this->config->item('locale') == $loc->locale ? 'selected="selected"' : '');
                                        }
                                        ?>><?= $loc->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('language') ?></strong></label>
                            <div class="col-sm-8">
                                <select name="language" class="form-control select_box" style="width: 100%">
                                    <?php foreach ($languages as $lang) : ?>
                                        <option value="<?= $lang->name ?>"<?php
                                        if (!empty($profile_info)) {
                                            if ($profile_info->language == $lang->name) {
                                                echo 'selected';
                                            }
                                        } else {
                                            echo($this->config->item('language') == $lang->name ? ' selected="selected"' : '');
                                        }
                                        ?>><?= ucfirst($lang->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('phone') ?> </strong></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->phone;
                                }
                                ?>" name="phone" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_phone') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('mobile') ?> </strong></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->mobile;
                                }
                                ?>" name="mobile"
                                       placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_mobile') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('skype_id') ?> </strong></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->skype;
                                }
                                ?>" name="skype" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_skype') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label"><strong><?= lang('profile_photo') ?></strong><span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 210px;">
                                        <?php
                                        if (!empty($profile_info)) :
                                            ?>
                                            <img src="<?php echo base_url() . $profile_info->avatar; ?>">
                                        <?php else: ?>
                                            <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">
                                        <?php endif; ?>
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                         style="width: 210px;"></div>
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
                            <label class="col-sm-4"></label>
                            <div class="col-sm-8">
                                <button type="submit" id="sbtn"
                                        class="btn btn-primary"><?php echo !empty($user_id) ? lang('update_user') : lang('create_user') ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>

    $().ready(function () {

        // validate signup form on keyup and submit
        $("#userform").validate({
            rules: {
                user_name: "required",
                name: "required",
                departments_id: "required",
                user_name: {
                    required: true,
                    minlength: 4
                },
                password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password",
                },
                email: {
                    required: true,
                    email: true
                }

            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            messages: {
                user_name: {
                    required: "Please enter a username",
                    minlength: "Your username must consist of at least 4 characters"
                },
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                },
                email: "Please enter a valid email address",
                name: "Please enter your Name"

            }

        });
    });
</script>