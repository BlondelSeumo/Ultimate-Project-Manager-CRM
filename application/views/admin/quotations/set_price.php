<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('quotations') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form data-parsley-validate="" novalidate=""
              action="<?php echo base_url(); ?>admin/quotations/set_price_quotations/<?= $quotations_id ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label"><?= lang('amount') ?></label>
                    <input type="number" min="0" name="quotations_amount" value="<?php
                    if (!empty($quotations_info->quotations_amount)) {
                        echo $quotations_info->quotations_amount;
                    }
                    ?>" required="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label"><?= lang('notes') ?></label>
                    <textarea name="notes" value="" required="" rows="5" class="form-control"><?php
                        if (!empty($quotations_info->notes)) {
                            echo $quotations_info->notes;
                        }
                        ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label"><?= lang('send_email') ?></label>
                    <div class="">
                        <label>
                            <input type="checkbox" checked="true" name="send_email">
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('updates') ?></button>
            </div>
        </form>
    </div>
</div>