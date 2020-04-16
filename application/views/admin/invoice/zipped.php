<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('zip_invoice') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" id="from_items"
              action="<?php echo base_url(); ?>admin/invoice/zipped/<?= $module ?>" method="post"
              class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <label
                    class="col-lg-3 control-label"><?= lang('status') ?></label>
                <div class="col-lg-7">
                    <label class="radio c-radio">
                        <input id="inlineradio10" type="radio" name="invoice_status" value="all"
                               checked="">
                        <span class="fa fa-check"></span><?= lang('all') ?>
                    </label>
                    <?php
                    if ($module == 'invoice') {
                        $invoiceFilter = $this->invoice_model->get_invoice_filter();
                    }
                    if ($module == 'estimate') {
                        $invoiceFilter = $this->estimates_model->get_invoice_filter();
                    }
                    if ($module == 'credit_note') {
                        $invoiceFilter = $this->credit_note_model->get_credit_note_filter();
                    }
                    if ($module == 'proposal') {
                        $invoiceFilter = $this->proposal_model->get_invoice_filter();
                    }
                    if ($module == 'payment') {
                        $invoiceFilter = $this->invoice_model->get_invoice_payment();
                    }
                    if (!empty($invoiceFilter)) {
                        foreach ($invoiceFilter as $v_Filter) { ?>
                            <label class="radio c-radio">
                                <input id="inlineradio10" type="radio" name="invoice_status"
                                       value="<?= $v_Filter['value'] ?>">
                                <span class="fa fa-check"></span><?= $v_Filter['name'] ?></label>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="period">
                <div class="form-group">
                    <label
                        class="col-lg-3 control-label"><?= lang('from_date') ?></label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input class="form-control datepicker period" type="text"
                                   name="from_date"
                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label
                        class="col-lg-3 control-label"><?= lang('to_date') ?></label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input class="form-control datepicker period" type="text"
                                   name="to_date"
                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if (!empty($client_id)) {
                ?>
                <input type="hidden" name="client_id" value="<?= $client_id ?>">
            <?php } ?>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-purple"><?= lang('zipped') ?></button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('[name="invoice_status"]').change(function () {
            var val = $(this).val();
            var year = val.split('_');
            if (val == 'last_month' || val == 'this_months' || $.isNumeric(year[1])) {
                $('.period').hide().attr('disabled', 'disabled');
            } else {
                $('.period').show().removeAttr('disabled');
            }
        });
    });

</script>