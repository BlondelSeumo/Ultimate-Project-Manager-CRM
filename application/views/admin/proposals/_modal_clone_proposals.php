<?php include_once 'assets/admin-ajax.php'; ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('clone') . ' ' . lang('proposal') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" data-parsley-validate="" novalidate=""
              action="<?php echo base_url(); ?>admin/proposals/cloned_proposals/<?= $proposals_info->proposals_id ?>"
              method="post"
              class="form-horizontal form-groups-bordered">

            <div class="form-group" id="border-none">
                <label for="field-1"
                       class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
                <div class="col-sm-7">
                    <select name="module" class="form-control select_box"
                            id="check_related" required
                            onchange="get_related_moduleName(this.value,true)" style="width: 100%">
                        <option> <?= lang('none') ?> </option>
                        <option
                            value="leads" <?= (!empty($leads_id) ? 'selected' : '') ?>> <?= lang('leads') ?> </option>
                        <option
                            value="client" <?= (!empty($client_id) ? 'selected' : '') ?>> <?= lang('client') ?> </option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="related_to">

            </div>
            <div class="form-group">
                <label
                    class="col-lg-3 control-label"><?= lang('proposal_date') ?></label>
                <div class="col-lg-7">
                    <div class="input-group">
                        <input type="text" name="proposal_date"
                               class="form-control datepicker"
                               value="<?php
                               if (!empty($proposals_info->proposal_date)) {
                                   echo $proposals_info->proposal_date;
                               } else {
                                   echo date('Y-m-d');
                               }
                               ?>"
                               data-date-format="<?= config_item('date_picker_format'); ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('expire_date') ?></label>
                <div class="col-lg-7">
                    <div class="input-group">
                        <input type="text" name="due_date"
                               class="form-control datepicker"
                               value="<?php
                               if (!empty($proposals_info->due_date)) {
                                   echo $proposals_info->due_date;
                               } else {
                                   echo date('Y-m-d');
                               }
                               ?>"
                               data-date-format="<?= config_item('date_picker_format'); ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('clone') ?></button>
            </div>
        </form>
    </div>
</div>
