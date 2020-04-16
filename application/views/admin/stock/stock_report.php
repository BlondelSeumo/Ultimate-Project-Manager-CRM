<?php include_once 'asset/admin-ajax.php'; ?>
<div class="well">
    <form role="form" id="sales_report" class="form-horizontal form-groups-bordered"
          action="<?php echo base_url() ?>admin/stock/report" method="post">
        <div class="form-group">
            <label for="field-1" class="col-sm-3 control-label"><?= lang('search_type') ?> <span
                    class="required"> *</span></label>

            <div class="col-sm-5">
                <select required name="search_type" id="search_type" class="form-control ">
                    <option value=""><?= lang('select') . ' ' . lang('search_type') ?></option>
                    <option value="period" <?php if (!empty($search_type)) {
                        echo $search_type == 'period' ? 'selected' : '';
                    } ?>><?php echo lang('by') . ' ' . lang('period') ?></option>

                    <option value="employee" <?php if (!empty($search_type)) {
                        echo $search_type == 'employee' ? 'selected' : '';
                    } ?>><?php echo lang('by') . ' ' . lang('categories') ?></option>
                </select>
            </div>
        </div>

        <div class="by_employee"
             style="display: <?= !empty($search_type) && $search_type == 'employee' ? 'block' : 'none' ?>">
            <div class="form-group ">
                <label class="control-label col-sm-3"><?= lang('stock_category') ?><span
                        class="required">*</span></label>
                <div class="col-sm-5">

                    <select name="stock_sub_category_id" style="width: 100%"
                            class="form-control select_box"
                            onchange="get_item_name_by_id(this.value)">
                        <option
                            value=""><?= lang('select') . ' ' . lang('stock_category') ?></option>
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
                                            <option
                                                value="<?php echo $sub_category->stock_sub_category_id; ?>"
                                                <?php
                                                if (!empty($stock_sub_category_id)) {
                                                    echo $sub_category->stock_sub_category_id == $stock_sub_category_id ? 'selected' : '';
                                                }
                                                ?>><?php echo $sub_category->stock_sub_category ?></option>
                                            <?php
                                        }
                                    endforeach;
                                    ?>
                                </optgroup>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('item_name') ?><span
                        class="required">*</span></label>

                <div class="col-sm-5">
                    <select class="form-control" name="stock_id" id="item_name">
                        <option value=""><?= lang('select') . ' ' . lang('item_name') ?></option>
                        <?php if (!empty($stock_info)): ?>
                            <?php foreach ($stock_info as $v_stock_info): ?>
                                <option value="<?php echo $v_stock_info->stock_id ?>" <?php
                                if (!empty($stock_id)) {
                                    echo $v_stock_info->stock_id == $stock_id ? 'selected' : '';
                                }
                                ?>><?php echo $v_stock_info->item_name ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="by_period"
             style="display: <?= !empty($search_type) && $search_type == 'period' ? 'block' : 'none' ?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('start_date') ?> <span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <div class="input-group">
                        <input type="text" value="<?php if (!empty($date['start_date'])) {
                            echo $date['start_date'];
                        } ?>" class="form-control start_date" name="start_date"
                               data-format="yyyy-mm-dd">

                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('end_date') ?> <span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <div class="input-group">
                        <input type="text" value="<?php if (!empty($date['end_date'])) {
                            echo $date['end_date'];
                        } ?>" class="form-control end_date" name="end_date"
                               data-format="yyyy-mm-dd">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="field-1" class="col-sm-3 control-label"></label>
            <div class="col-sm-5">
                <button type="submit" name="flag" value="1" data-toggle="tooltip" data-placement="top"
                        title="<?= lang('search') ?>" class="btn btn-primary"><i
                        class="fa fa-search fa-2x"></i></button>
            </div>
        </div>

    </form>
</div>


<style type="text/css">
    .custom-bg {
        background: #f0f0f0;
    }
</style>

<?php
if (!empty($assign_stock) && !empty($purchase_stock)) {
    $col = 'col-md-12';
} else {
    $col = 'col-md-12';
}
if (!empty($assign_stock) || !empty($purchase_stock)): ?>
    <div id="printReport">
        <div class="show_print">
            <div style="width: 100%; border-bottom: 2px solid black;">
                <table style="width: 100%; vertical-align: middle;">
                    <tr>
                        <td style="width: 50px; border: 0px;">
                            <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                                 src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                        </td>

                        <td style="border: 0px;">
                            <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                        </td>

                    </tr>
                </table>
            </div>
            <br/>
            <div style="background: #E0E5E8;padding: 5px;">
                <!-- Default panel contents -->
                <?php if (!empty($search_type) && $search_type == 'employee') {
                    $url = 'name/'.$stock_id;
                    ?>
                    <div
                        style="font-size: 15px;padding: 5px 0px 0px 0px"><?= lang('items_name') ?>
                        :<strong>
                        <?php
                        $items_name = $this->db->where('stock_id', $stock_id)->get('tbl_stock')->row();
                        echo $items_name->item_name; ?></div>
                <?php } else {
                    $url = $date['start_date'] . '/' . $date['end_date'];
                    ?>
                    <div
                        style="font-size: 15px;padding: 5px 0px 0px 0px"><?= lang('stock') . ' ' . lang('report_from') ?>
                        :<strong>
                        <?= strftime(config_item('date_format'), strtotime($date['start_date'])); ?></div>
                    <div
                        style="font-size: 15px;padding: 0px 0px 0px 0px"></strong><?= lang('stock') . ' ' . lang('report_to') ?>
                        : <strong><?= strftime(config_item('date_format'), strtotime($date['end_date'])); ?></strong>
                    </div>
                    <?php
                } ?>
            </div>
            <br/>
        </div>
        <div class="panel panel-custom">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong class="hidden-print"><?= lang('report_list') ?></strong>
                    <div class="pull-right hidden-print">
                        <span><?php echo btn_pdf('admin/stock/assign_report_pdf/' . $url); ?></span>
                        <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger"
                           data-toggle="tooltip"
                           data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
                    </div>


                </div>
            </div>
            <div class="row">
                <?php if (!empty($purchase_stock)) { ?>
                    <div class="mt-lg <?= $col ?>">
                        <div class="custom-bg p text-center"><strong><?= lang('stock_list') ?></strong></div>
                        <table class="table table-bordered table-hover">
                            <?php foreach ($purchase_stock as $item_name => $v_purchase_stoc) :
                            ?>
                            <thead>
                            <tr class="color-black heading_print" style="background: #e7f0f5">
                                <th colspan="3"><strong><?php echo $item_name; ?></strong></th>
                            </tr>

                            <tr>
                                <th><?= lang('item_name') ?></th>
                                <th><?= lang('inventory') ?></th>
                                <th><?= lang('buying_date') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_inventory = 0;
                            if (!empty($v_purchase_stoc)): foreach ($v_purchase_stoc as $v_stock) :
                                ?>

                                <tr class="custom-tr custom-font-print">
                                    <td class="vertical-td"><?php echo $v_stock->item_name ?></td>
                                    <td class="vertical-td"><?php echo $v_stock->inventory; ?> </td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_stock->purchase_date)); ?></td>
                                </tr>
                                <?php
                                $total_inventory += $v_stock->inventory;
                            endforeach; ?>
                                <tr class="custom-bg">
                                    <th style="text-align: right;">
                                        <strong><?= lang('total') . ' ' . lang('assigned') . ': ' ?></strong>
                                    </th>
                                    <td><?= $total_inventory - $v_stock->total_stock ?></td>
                                    <td align="">
                                <span class="pull-right"><?= lang('available_stock') ?>
                                    :<strong> <?php echo $v_stock->total_stock; ?></strong></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>

                <?php if (!empty($assign_stock)) { ?>
                    <div class="mt-lg <?= $col ?>">
                        <div class="custom-bg p text-center"><strong><?= lang('assign_stock_list') ?></strong></div>
                        <table class="table table-bordered table-hover">
                            <?php foreach ($assign_stock as $item_name => $v_assign_report) :
                            ?>
                            <thead>
                            <tr class="color-black heading_print" style="background: #e7f0f5">
                                <th colspan="3"><strong><?php echo $item_name; ?></strong></th>
                            </tr>

                            <tr>
                                <th><?= lang('assigned_user') ?></th>
                                <th><?= lang('assign_date') ?></th>
                                <th><?= lang('assign_quantity') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_assign_inventory = 0;
                            if (!empty($v_assign_report)): foreach ($v_assign_report as $v_report) :
                                ?>

                                <tr class="custom-tr custom-font-print">
                                    <td class="vertical-td"><?php echo $v_report->fullname ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_report->assign_date)); ?></td>
                                    <td class="vertical-td"><?php echo $v_report->assign_inventory; ?> </td>
                                    <?php
                                    $total_assign_inventory += $v_report->assign_inventory;
                                    ?>
                                </tr>
                            <?php endforeach; ?>
                                <tr class="custom-bg">
                                    <th style="text-align: right;" colspan="2">
                                        <strong><?= lang('total') ?> <?php echo $v_report->item_name ?>
                                            :</strong>
                                    </th>
                                    <td align=""><?php
                                        echo $total_assign_inventory;
                                        ?>
                                        <span class="pull-right"><?= lang('available_stock') ?>
                                            :<strong> <?php echo $v_report->total_stock; ?></strong></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">

    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
