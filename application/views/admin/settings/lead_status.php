<?php
echo message_box('success');
echo message_box('error');
$created = can_action('127', 'created');
$edited = can_action('127', 'edited');
$deleted = can_action('127', 'deleted');
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('lead_status') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                <tr>

                    <th><?= lang('lead_status') ?></th>
                    <th><?= lang('lead_type') ?></th>
                    <th><?= lang('order_no') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php

                if (!empty($all_lead_status)) {
                    foreach ($all_lead_status as $lead_status) {
                        $total_lead_status = count($this->db->where('lead_status_id', $lead_status->lead_status_id)->get('tbl_leads')->result());
                        ?>
                        <tr id="lead_status_<?= $lead_status->lead_status_id?>">
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $lead_status->lead_status_id) { ?>
                                <form method="post"
                                      action="<?= base_url() ?>admin/settings/lead_status/update_lead_status/<?php
                                      if (!empty($lead_status_info)) {
                                          echo $lead_status_info->lead_status_id;
                                      }
                                      ?>" class="form-horizontal">
                                    <input type="text" name="lead_status" value="<?php
                                    if (!empty($lead_status_info)) {
                                        echo $lead_status_info->lead_status;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('lead_status') ?>" required>
                                    <?php } else {
                                        echo $lead_status->lead_status. '<p class="text-sm text-info m0 p0">' . lang('total') .' '. lang('leads') .' : '. $total_lead_status . '</p>';
                                    } ?>
                            </td>
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $lead_status->lead_status_id) { ?>
                                    <select name="lead_type" class="form-control">
                                        <option value=""><?= lang('none') ?></option>
                                        <option
                                            value="close" <?= !empty($lead_status_info) && $lead_status_info->lead_type == 'close' ? 'selected' : '' ?>><?= lang('close') ?></option>
                                        <option
                                            value="open" <?= !empty($lead_status_info) && $lead_status_info->lead_type == 'open' ? 'selected' : '' ?>><?= lang('open') ?></option>
                                    </select>
                                <?php } else {
                                    echo lang($lead_status->lead_type);
                                } ?>
                            </td>
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $lead_status->lead_status_id) { ?>
                                    <input type="text" name="order_no" value="<?php
                                    if (!empty($lead_status_info)) {
                                        echo $lead_status_info->order_no;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('order_no') ?>" required>
                                <?php } else {
                                    echo $lead_status->order_no;
                                } ?>
                            </td>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <td>
                                    <?php
                                    $id = $this->uri->segment(5);
                                    if (!empty($id) && $id == $lead_status->lead_status_id) { ?>
                                        <?= btn_update() ?>
                                        </form>
                                        <?= btn_cancel('admin/settings/lead_status/') ?>
                                    <?php } else {
                                        if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/settings/lead_status/edit_lead_status/' . $lead_status->lead_status_id) ?>
                                        <?php }
                                        if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/settings/delete_lead_status/" . $lead_status->lead_status_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#lead_status_" . $lead_status->lead_status_id)); ?>
                                        <?php }
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                }
                if (!empty($created) || !empty($edited)) { ?>
                    <form method="post" action="<?= base_url() ?>admin/settings/lead_status/update_lead_status"
                          class="form-horizontal"  data-parsley-validate="" novalidate="">
                        <tr>
                            <td><input type="text" name="lead_status" class="form-control"
                                       placeholder="<?= lang('lead_status') ?>" required></td>
                            <td>
                                <select name="lead_type" class="form-control">
                                    <option value=""><?= lang('none') ?></option>
                                    <option value="close"><?= lang('close') ?></option>
                                    <option
                                        value="open"><?= lang('open') ?></option>
                                </select>
                            </td>
                            <td><input type="text" name="order_no" class="form-control"
                                       placeholder="<?= lang('order_no') ?>" required></td>
                            <td>
                                <button type="submit" class="btn btn-sm btn-info"><?= lang('add') ?></button>
                            </td>
                        </tr>
                    </form>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>