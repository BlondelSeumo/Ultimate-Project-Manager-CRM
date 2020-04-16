<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('from_items') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form role="form" id="from_items" action="<?php echo base_url(); ?>admin/settings/new_currency/save"
              method="post" class="form-horizontal form-groups-bordered">
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('currency_code') ?></label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="Please Enter Currency Code"
                           name="code">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('name') ?> </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="Please Enter Currency Name"
                           name="name">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('currency_symbol') ?> </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" placeholder="Please Enter Currency Symbol"
                           name="symbol">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"></label>
                <div class="col-lg-7">
                    <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
