<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('from_items') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form role="form" id="from_items"
              action="<?php echo base_url(); ?>admin/credit_note/add_insert_items/<?= $credit_note_id ?>" method="post"
              class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>
                            <div class="checkbox c-checkbox needsclick">
                                <label class="needsclick" data-toggle="tooltip"
                                       title="<?= lang('select') . ' ' . lang('all') ?>">
                                    <input id="parent_present" type="checkbox" lass="needsclick">
                                    <span class="fa fa-check"></span> </label>

                            </div>
                        </th>
                        <th><?= lang('item') ?></th>
                        <?php
                        $invoice_view = config_item('invoice_view');
                        if (!empty($invoice_view) && $invoice_view == '2') {
                            ?>
                            <th><?= lang('hsn_code') ?></th>
                        <?php } ?>
                        <th class="col-sm-1"><?= lang('qty') ?></th>
                        <th class="col-sm-1"><?= lang('unit_price') ?></th>
                        <th class="col-sm-2"><?= lang('tax') ?></th>
                        <th><?= lang('total') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $saved_items = $this->credit_note_model->get_all_items();
                    if (!empty($saved_items)) {
                        $saved_items = array_reverse($saved_items, true);
                        foreach ($saved_items as $group_id => $v_saved_items) {
                            if ($group_id != 0) {
                                $group = $this->db->where('customer_group_id', $group_id)->get('tbl_customer_group')->row()->customer_group;
                            } else {
                                $group = '';
                            }
                            ?>
                            <tr>
                            <th colspan="5" style="font-size: 16px"><?= $group ?></th>
                            <?php
                            if (!empty($v_saved_items)) {
                                foreach ($v_saved_items as $v_item) { ?>
                                    <tr>
                                        <td>
                                            <div class="checkbox c-checkbox needsclick">
                                                <label class="needsclick">
                                                    <input class="child_present" type="checkbox" name="saved_items_id[]"
                                                           value="<?= $v_item->saved_items_id ?>"/>
                                                    <span class="fa fa-check"></span></label>

                                            </div>


                                        </td>
                                        <td><strong class="block"><?= $v_item->item_name ?></strong>
                                            <?= strip_html_tags(mb_substr($v_item->item_desc, 0, 200)) . '...'; ?>
                                        </td>
                                        <?php
                                        $invoice_view = config_item('invoice_view');
                                        if (!empty($invoice_view) && $invoice_view == '2') {
                                            ?>
                                            <td><?= $v_item->hsn_code ?></td>
                                        <?php } ?>
                                        <td><?= $v_item->quantity . '   &nbsp' . $v_item->unit_type ?></td>
                                        <td><?= display_money($v_item->unit_cost) ?></td>
                                        <td><?php
                                            if (!is_numeric($v_item->tax_rates_id)) {
                                                $tax_rates = json_decode($v_item->tax_rates_id);
                                            } else {
                                                $tax_rates = null;
                                            }
                                            if (!empty($tax_rates)) {
                                                foreach ($tax_rates as $key => $tax_id) {
                                                    $taxes_info = $this->db->where('tax_rates_id', $tax_id)->get('tbl_tax_rates')->row();
                                                    if (!empty($taxes_info)) {
                                                        echo $key + 1 . '. ' . $taxes_info->tax_rate_name . '&nbsp;&nbsp; (' . $taxes_info->tax_rate_percent . '% ) <br>';
                                                    }
                                                }
                                            }
                                            ?></td>
                                        <td><?= display_money($v_item->total_cost) ?></td>
                                    </tr>
                                <?php }
                            }
                            ?>
                            </tr>
                        <?php }
                    }; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    /*
     * Select All select
     */
    $(function () {
        $('#parent_present').on('change', function () {
            $('.child_present').prop('checked', $(this).prop('checked'));
        });
        $('.child_present').on('change', function () {
            $('.child_present').prop($('.child_present:checked').length ? true : false);
        });
    });
    $(document).ready(function () {
        $("#from_items").validate({
            rules: {
                saved_items_id: {
                    required: true,
                }
            }
        });
    });</script>
<script src="<?php echo base_url(); ?>asset/js/custom-validation.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>asset/js/jquery.validate.js" type="text/javascript"></script>
