<?php include_once 'assets/admin-ajax.php'; ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('reset_password') ?></h4>
    </div>
    <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
          action="<?php echo base_url(); ?>admin/user/reset_password/<?php
          if (!empty($user_info->user_id)) {
              echo $user_info->user_id;
          }
          ?>" method="post" class="form-horizontal  ">
        <div class="modal-body form-horizontal">
            <div class="form-group">
                <div class="col-lg-12">
                    <input type="password" class="form-control" id="change_email_password"
                           placeholder="<?= lang('enter') . ' ' . lang('your') . ' ' . lang('current') . ' ' . lang('password') ?>"
                           name="my_password">
                    <span class="required" id="email_password"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-12">
                    <input type="password" class="form-control" id="new_password"
                           placeholder="<?= lang('enter') . ' ' . lang('new') . ' ' . lang('password') . ' ' . lang('for') . ' ' . fullname($user_info->user_id) ?>"
                           name="password">
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-12">
                    <input type="password" class="form-control" data-parsley-equalto="#new_password"
                           placeholder="<?= lang('enter') . ' ' . lang('confirm_password') . ' ' . lang('for') . ' ' . fullname($user_info->user_id) ?>"
                           name="password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" id="new_uses_btn" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </div>
    </form>
</div>

