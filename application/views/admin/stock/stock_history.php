<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('select') . ' ' . lang('stock_category') ?></strong>
                </div>
            </div>
            <div class="panel-body">

                <form id="form" action="<?php echo base_url() ?>admin/stock/stock_history" method="post"
                      class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label for="field-1"
                               class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('stock_category') ?> <span
                                class="required">*</span></label>

                        <div class="col-sm-5">
                            <select name="stock_sub_category_id" class="form-control select_box">
                                <option value=""><?= lang('select') . ' ' . lang('stock_category') ?></option>
                                <?php if (!empty($all_category_info)): foreach ($all_category_info as $cate_name => $v_category_info) : ?>
                                    <?php if (!empty($v_category_info)):
                                        if (!empty($cate_name)) {
                                            $cate_name = $cate_name;
                                        } else {
                                            $cate_name = lang('undefined_category');
                                        }
                                        ?>
                                        <optgroup label="<?php echo $cate_name; ?>">
                                            <?php foreach ($v_category_info as $sub_category) :
                                                if (!empty($sub_category->stock_sub_category)) {
                                                    ?>
                                                    <option value="<?php echo $sub_category->stock_sub_category_id; ?>"
                                                        <?php
                                                        if (!empty($sub_category_id)) {
                                                            echo $sub_category->stock_sub_category_id == $sub_category_id ? 'selected' : '';
                                                        }
                                                        ?>><?php echo $sub_category->stock_sub_category ?></option>
                                                    <?php
                                                }
                                            endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" id="sbtn" value="1" name="flag"
                                    class="btn btn-primary"><?= lang('go') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br/>
        <?php if (!empty($flag)): ?>

            <?php if (!empty($item_history_info)): foreach ($item_history_info as $sub_category => $v_item_history) : ?>
                <?php if (!empty($v_item_history)): ?>
                    <div class="row">
                    <div class="col-sm-12" data-offset="0">
                    <div class="panel panel-custom">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><?php echo $sub_category; ?></strong>
                        </div>
                    </div>
                    <?php foreach ($v_item_history as $item_name => $item_history) : ?>
                        <div class="box-heading">
                            <div class="box-title" style="border-bottom: 1px solid #a0a0a0;padding-bottom:5px;">
                                <strong><?php echo $item_name ?></strong>
                            </div>
                        </div>
                        <!-- Table -->
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-sm-1"><?= lang('sl') ?></th>
                                <th><?= lang('item_name') ?></th>
                                <th><?= lang('inventory') ?></th>
                                <th><?= lang('buying_date') ?></th>
                                <th class="col-sm-2"><?= lang('action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($item_history as $key => $v_item) : ?>

                                <tr id="table_history_<?= $v_item->item_history_id ?>">
                                    <td><?php echo $key + 1 ?></td>
                                    <td><?php echo $v_item->item_name ?></td>
                                    <td><?php echo $v_item->inventory ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_item->purchase_date)); ?></td>
                                    <td>
                                        <?php echo btn_edit('admin/stock/stock_list/' . $v_item->item_history_id . '/h'); ?>
                                        <?php echo ajax_anchor(base_url("admin/stock/delete_stock_history/" . $v_item->stock_id . '/' . $v_item->item_history_id . '/' . $sub_category_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_history_" . $v_item->item_history_id)); ?>
                                    </td>

                                </tr>
                                <?php
                            endforeach;
                            ?>
                            </tbody>
                        </table>
                        <?php
                    endforeach;
                    ?>
                <?php endif; ?>
                <?php
            endforeach;
                ?>
            <?php else : ?>
                <div class="panel-body">
                    <strong>There is no data to display</strong>
                </div>
            <?php endif; ?>
            </div>
            </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>
<script type="text/javascript">
    function assign_stock(assign_stock) {
        var printContents = document.getElementById(assign_stock).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>


