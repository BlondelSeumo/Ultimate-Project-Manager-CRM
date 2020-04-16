<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h3 class="panel-title"><?= lang('new') . ' ' . lang('to_do') ?></h3>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/dashboard/save_todo/<?php
              if (!empty($todo_info->todo_id)) {
                  echo $todo_info->todo_id;
              }
              ?>" method="post" class="form-horizontal">
            <?php
            if ($this->session->userdata('user_type') == 1) {
                $all_users = $this->db->where(array('role_id !=' => 2, 'activated' => 1))->get('tbl_users')->result();
                ?>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('users') ?> <span
                            class="required">*</span></label>
                    <div class="col-sm-7">
                        <select name="user_id" style="width: 100%" id="employee" required
                                class="form-control select_box">
                            <option value=""><?= lang('select_employee') ?>...</option>
                            <?php if (!empty($all_users)): ?>
                                <?php foreach ($all_users as $v_user) :
                                    $user_profile = $this->db->where(array('user_id' => $v_user->user_id))->get('tbl_account_details')->row();
                                    ?>
                                    <option value="<?php echo $v_user->user_id; ?>"
                                        <?php
                                        if (!empty($todo_info->user_id)) {
                                            $user_id = $todo_info->user_id;
                                        } else {
                                            $user_id = $this->session->userdata('user_id');
                                        }
                                        if (!empty($user_id)) {
                                            echo $v_user->user_id == $user_id ? 'selected' : '';
                                        }
                                        ?>><?php echo $user_profile->fullname ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('what') . ' ' . lang('to_do') ?>
                    <span class="required">*</span></label>

                <div class="col-sm-7">
                    <textarea name="title" class="form-control " rows="3"><?php
                        if (!empty($todo_info->title)) {
                            echo $todo_info->title;
                        }
                        ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1"
                       class="col-sm-3 control-label"><?= lang('status') ?></label>

                <div class="col-sm-7">
                    <?php
                    if (!empty($todo_info->status)) {
                        $todo_status = $todo_info->status;
                    } else {
                        $todo_status = null;
                    }
                    $options = array(
                        '0' => lang('in_progress'),
                        '2' => lang('on_hold'),
                        '1' => lang('done'),
                    );
                    echo form_dropdown('status', $options, $todo_status, 'style="width:100%" class="form-control"'); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('due_date') ?> <span
                        class="required">*</span></label>

                <div class="col-sm-7">
                    <div class="input-group">
                        <input required type="text" name="due_date"
                               placeholder="<?= lang('enter') . ' ' . lang('due_date') ?>"
                               class="form-control datepicker" value="<?php
                        if (!empty($todo_info->due_date)) {
                            echo $todo_info->due_date;
                        }
                        ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-7">
                    <button type="submit" id="sbtn" name="sbtn" value="1"
                            class="btn btn-primary"><?= lang('save') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
