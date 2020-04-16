<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php
$created = can_action('149', 'created');
$edited = can_action('149', 'edited');
$deleted = can_action('149', 'deleted');
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('allowed_ip') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                <tr>
                    <th><?= lang('allowed_ip') ?></th>
                    <th><?= lang('status') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $all_allowed_ip = $this->db->get('tbl_allowed_ip')->result();
                if (!empty($all_allowed_ip)) {
                    foreach ($all_allowed_ip as $allowed_ip) {
                        ?>
                        <tr id="allowed_ip_<?= $allowed_ip->allowed_ip_id ?>">
                            <td><?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $allowed_ip->allowed_ip_id) { ?>
                                <form method="post"
                                      action="<?= base_url() ?>admin/settings/allowed_ip/update_allowed_ip/<?php
                                      if (!empty($allowed_ip_info)) {
                                          echo $allowed_ip_info->allowed_ip_id;
                                      }
                                      ?>" class="form-horizontal">
                                    <input type="text" name="allowed_ip" value="<?php
                                    if (!empty($allowed_ip_info)) {
                                        echo $allowed_ip_info->allowed_ip;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('allowed_ip') ?>" required>
                                <?php } else {
                                    echo $allowed_ip->allowed_ip;
                                }
                                ?></td>
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $allowed_ip->allowed_ip_id) { ?>
                                    <select name="status" class="form-control">
                                        <?php
                                        if (!empty($allowed_ip_info) && $allowed_ip_info->status == 'pending') {
                                            ?>
                                            <option
                                                value="pending" <?= !empty($allowed_ip_info) && $allowed_ip_info->status == 'pending' ? 'selected' : '' ?>><?= lang('pending') ?></option>
                                        <?php } ?>
                                        <option
                                            value="active" <?= !empty($allowed_ip_info) && $allowed_ip_info->status == 'active' ? 'selected' : '' ?>><?= lang('active') ?></option>
                                        <option
                                            value="reject" <?= !empty($allowed_ip_info) && $allowed_ip_info->status == 'reject' ? 'selected' : '' ?>><?= lang('reject') ?></option>
                                    </select>
                                <?php } else { ?>

                                    <?php $status = 'danger';
                                    $action = '<a class="btn btn-xs btn-success" data-toggle="tooltip" title="' . lang('click_to') . ' ' . lang('active') . '" href="' . base_url("admin/settings/allowed_ip/change_status/active/$allowed_ip->allowed_ip_id") . '"><i class="fa fa-check"></i></a>';
                                    if ($allowed_ip->status == 'active') {
                                        $status = 'success';
                                        $action = '<a class="btn btn-xs btn-danger" data-toggle="tooltip" title="' . lang('click_to') . ' ' . lang('reject') . '" href="' . base_url("admin/settings/allowed_ip/change_status/reject/$allowed_ip->allowed_ip_id") . '"><i class="fa fa-times"></i></a>';

                                    } else if ($allowed_ip->status == 'pending') {
                                        $status = 'warning';
                                        $action = '<a class="btn btn-xs btn-success" data-toggle="tooltip" title="' . lang('click_to') . ' ' . lang('active') . '" href="' . base_url("admin/settings/allowed_ip/change_status/active/$allowed_ip->allowed_ip_id") . '"><i class="fa fa-check"></i></a>' . ' ' . '<a class="btn btn-xs btn-danger" data-toggle="tooltip" title="' . lang('click_to') . ' ' . lang('reject') . '" href="' . base_url("admin/settings/allowed_ip/change_status/reject") . '"><i class="fa fa-times"></i></a>';
                                    }
                                    ?>
                                    <strong
                                        class="label label-<?= $status ?>"><?= lang($allowed_ip->status); ?></strong>
                                <?php } ?>
                            </td>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <td>
                                    <?= $action ?>
                                    <?php
                                    $id = $this->uri->segment(5);
                                    if (!empty($id) && $id == $allowed_ip->allowed_ip_id) { ?>
                                        <?= btn_update() ?>
                                        </form>
                                        <?= btn_cancel('admin/settings/allowed_ip/') ?>
                                    <?php } else { ?>
                                        <?php if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/settings/allowed_ip/edit_allowed_ip/' . $allowed_ip->allowed_ip_id) ?>
                                        <?php } ?>
                                        <?php if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/settings/allowed_ip/delete_allowed_ip/" . $allowed_ip->allowed_ip_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#allowed_ip_" . $allowed_ip->allowed_ip_id)); ?>
                                        <?php }
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                }
                ?>
                <?php if (!empty($created) || !empty($edited)) { ?>
                    <form method="post" action="<?= base_url() ?>admin/settings/allowed_ip/update_allowed_ip"
                          class="form-horizontal" data-parsley-validate="" novalidate="">
                        <tr>
                            <td><input type="text" name="allowed_ip" class="form-control"
                                       placeholder="<?= lang('allowed_ip') ?>" required></td>
                            <td>
                                <select name="status" class="form-control">
                                    <option value="active"><?= lang('active') ?></option>
                                    <option
                                        value="reject"><?= lang('reject') ?></option>
                                </select>
                            </td>
                            <td><?= btn_add() ?></td>
                        </tr>
                    </form>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>