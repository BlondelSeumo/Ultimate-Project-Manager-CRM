<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= $status . ' ' . lang('list') ?></a>
        </li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new') . ' ' . $status ?></a>
        </li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables" id="DataTables">
                    <thead>
                    <tr>
                        <th><?= $status ?></th>
                        <th class="col-options no-sort"><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if (!empty($all_status)) {
                        foreach ($all_status as $v_status):

                            $v_status->id = (!empty($v_status->status) ? $v_status->status_id : $v_status->priority_id);

                            ?>
                            <tr>
                                <td><?= !empty($v_status->status) ? $v_status->status : $v_status->priority ?></td>
                                <td>
                                    <?= btn_delete('admin/settings/delete_status/' . $status . '/' . $v_status->id); ?>
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
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
                  action="<?php echo base_url(); ?>admin/settings/manage_status/<?= $status ?>/<?php
                  if (!empty($reminder_info)) {
                      echo $reminder_info->reminder_id;
                  }
                  ?>" method="post" class="form-horizontal  ">
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= $status ?> <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="text" name="status" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"></label>
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-purple"><?= lang('upload') ?></button>
                        <button type="button" class="btn btn-primary pull-right"
                                data-dismiss="modal"><?= lang('close') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>