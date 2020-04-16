<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('clone') . ' ' . lang('credit_note') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" id="from_items"  data-parsley-validate="" novalidate=""
              action="<?php echo base_url(); ?>admin/credit_note/cloned_credit_note/<?= $credit_note_info->credit_note_id ?>"
              method="post"
              class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('client') ?> <span
                        class="text-danger">*</span>
                </label>
                <div class="col-lg-7">
                    <select class="form-control select_box" style="width: 100%" name="client_id" required>
                        <?php
                        if (!empty($all_client)) {
                            foreach ($all_client as $v_client) {
                                ?>
                                <option value="<?= $v_client->client_id ?>"
                                    <?php
                                    if (!empty($credit_note_info)) {
                                        $credit_note_info->client_id == $v_client->client_id ? 'selected' : '';
                                    }
                                    ?>
                                ><?= ucfirst($v_client->name) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label
                    class="col-lg-3 control-label"><?= lang('credit_note_date') ?></label>
                <div class="col-lg-7">
                    <div class="input-group">
                        <input required type="text" name="credit_note_date"
                               class="form-control datepicker"
                               value="<?php
                               if (!empty($credit_note_info->credit_note_date)) {
                                   echo $credit_note_info->credit_note_date;
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
