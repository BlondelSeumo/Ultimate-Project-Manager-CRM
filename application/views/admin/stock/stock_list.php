<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('81', 'created');
$edited = can_action('81', 'edited');
$deleted = can_action('81', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs" style="margin-top: 1px">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"
                                                               data-toggle="tab"><?= lang('all') . ' ' . lang('stock') ?></a>
            </li>
            <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"
                                                               data-toggle="tab"><?= lang('new') . ' ' . lang('stock') ?></a>
            </li>
        </ul>
        <div class="tab-content bg-white">
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
                <?php } else { ?>
                <div class="panel panel-custom">
                    <header class="panel-heading ">
                        <div class="panel-title"><strong><?= lang('all') . ' ' . lang('stock') ?></strong></div>
                    </header>
                    <?php } ?>
                    <div class="row">
                        <?php $key = 0 ?>
                        <?php if (!empty($all_stock_info)) : ?>
                            <?php foreach ($all_stock_info as $category => $v_stock_info):
                                if (!empty($category)) {
                                    $category = $category;
                                } else {
                                    $category = lang('undefined_category');
                                }
                                ?>
                                <div class="col-sm-6">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                                <?php if (!empty($v_stock_info)): ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" class="collapsed" data-parent="#accordion"
                                               href="#<?php echo $key ?>" aria-expanded="false"
                                               aria-controls="collapseOne">
                                                <i class="fa fa-plus"> </i> <?php echo $category; ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="<?php echo $key ?>" class="panel-collapse collapse" role="tabpanel"
                                         aria-labelledby="headingOne">
                                        <?php foreach ($v_stock_info as $sub_category => $v_stock): ?>
                                            <div class="panel-body">
                                                <table class="table table-bordered" style="margin-bottom: 0px;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="3"
                                                            style="background-color: #E3E5E6;color: #000000 ">
                                                            <strong><?php echo $sub_category; ?></strong></th>
                                                    </tr>
                                                    <tr style="font-size: 13px;color: #000000">
                                                        <th><?= lang('item_name') ?></th>
                                                        <th><?= lang('total_stock') ?></th>
                                                        <?php if (!empty($deleted) || !empty($edited)) { ?>
                                                            <th class="col-sm-2"><?= lang('action') ?></th>
                                                        <?php } ?>
                                                    </tr>
                                                    </thead>
                                                    <tbody
                                                        style="margin-bottom: 0px;background: #FFFFFF;font-size: 12px;">
                                                    <?php foreach ($v_stock as $stock) : ?>
                                                        <tr>
                                                            <td><?php echo $stock->item_name; ?></td>
                                                            <td><?php echo $stock->total_stock ?></td>
                                                            <?php if (!empty($deleted) || !empty($edited)) { ?>
                                                                <td>
                                                                    <?php if (!empty($edited)) { ?>
                                                                        <?php echo btn_edit('admin/stock/stock_list/' . $stock->stock_id); ?>
                                                                    <?php }
                                                                    if (!empty($deleted)) { ?>
                                                                        <?php echo btn_delete('admin/stock/delete_stock/' . $stock->stock_id); ?>
                                                                    <?php } ?>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php endforeach; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                </div>
                                </div>
                            <?php endif; ?>
                                <?php $key++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($created) || !empty($edited)) { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task" style="position: relative;">
                    <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                          action="<?php echo base_url() ?>admin/stock/save_stock/<?php
                          if (!empty($stock_info->item_history_id)) {
                              echo $stock_info->item_history_id;
                          }
                          ?>" method="post" class="form-horizontal form-groups-bordered">

                        <div class="form-group ">
                            <label class="control-label col-sm-3"><?= lang('stock_category') ?><span
                                    class="required">*</span></label>
                            <div class="col-sm-5">

                                <select name="stock_sub_category_id" style="width: 100%"
                                        class="form-control select_box">
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
                                                        <option
                                                            value="<?php echo $sub_category->stock_sub_category_id; ?>"
                                                            <?php
                                                            if (!empty($stock_info->stock_sub_category_id)) {
                                                                echo $sub_category->stock_sub_category_id == $stock_info->stock_sub_category_id ? 'selected' : '';
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
                            <label for="field-1" class="control-label col-sm-3 "><?= lang('buying_date') ?><span
                                    class="required">*</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input required type="text" class="form-control   datepicker" name="purchase_date"
                                           value="<?php
                                           if (!empty($stock_info->purchase_date)) {
                                               echo $stock_info->purchase_date;
                                           }
                                           ?>" data-format="yyyy/mm/dd">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $direction = $this->session->userdata('direction');
                        if (!empty($direction) && $direction == 'rtl') {
                            $RTL = 'on';
                        } else {
                            $RTL = config_item('RTL');
                        }
                        ?>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('item_name') ?><span
                                    class="required"> * </span></label>

                            <div class="col-sm-5">
                                <input required type="text" <?php
                                if (!empty($RTL)) { ?>
                                    dir="rtl"
                                <?php }
                                ?> name="item_name" class="form-control" placeholder=""
                                       id="query"
                                       value="<?php
                                       if (!empty($stock_info->item_name)) {
                                           echo $stock_info->item_name;
                                       }
                                       ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php if (!empty($from_history) || empty($stock_info)) { ?>
                                <label for="field-1" class="col-sm-3 control-label"><?= lang('inventory') ?> <span
                                            class="required">*</span></label>

                                <div class="col-sm-5">
                                    <input required type="text" data-parsley-type="number" name="inventory"
                                           placeholder=""
                                           class="form-control"
                                           value="<?php
                                           if (!empty($stock_info->inventory)) {
                                               echo $stock_info->inventory;
                                           }
                                           ?>">
                                </div>
                            <?php } elseif (!empty($stock_info)) { ?>
                                <label for="field-1" class="col-sm-3 control-label"><?= lang('total_stock') ?> <span
                                            class="required">*</span></label>

                                <div class="col-sm-5">
                                    <input required type="text" readonly
                                           placeholder=""
                                           class="form-control"
                                           value="<?php
                                           if (!empty($stock_info->total_stock)) {
                                               echo $stock_info->total_stock;
                                           }
                                           ?>">
                                </div>
                            <?php } ?>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <?php
                            if (!empty($stock_info)) { ?>
                                <button type="submit" id="i_submit"
                                        class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                <button type="button" onclick="goBack()"
                                        class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                            <?php } else {
                                ?>
                                <button type="submit" id="i_submit"
                                        class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                            <?php }
                            ?>
                        </div>

                        <!-- Hidden input field-->
                        <input type="hidden" name="item_history_id" value="<?php
                        if (!empty($stock_info->item_history_id)) {
                            echo $stock_info->item_history_id;
                        }
                        ?>">
                    </form>
                </div>
            </div>
            <?php }else{ ?>
        </div>
        <?php } ?>
    </ul>
</div>
<link href="<?php echo base_url() ?>assets/plugins/typehead/typehead.css" rel="stylesheet"/>
<script src="<?php echo base_url() ?>assets/plugins/typehead/typehead.js"></script>

<?php $all_stock = $this->db->get('tbl_stock')->result(); ?>
<script type="text/javascript">
    $('#query').typeahead({
        local: [<?php if(!empty($all_stock)){ foreach($all_stock as $v_stock){?>"<?= $v_stock->item_name ?>",<?php }}?>]
    });

    $('.tt-query').css('background-color', '#fff');


</script>

