<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('82', 'created');
$edited = can_action('82', 'edited');
$deleted = can_action('82', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="btn-group pull-right btn-with-tooltip-group filtered" data-toggle="tooltip"
     data-title="<?php echo lang('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu group animated zoomIn"
        style="width:300px;">
        <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
        <li class="divider"></li>

        <li class="dropdown-submenu pull-left  " id="by_sub_category">
            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('sub_category'); ?></a>
            <ul class="dropdown-menu dropdown-menu-left by_sub_category"
                style="">
                <?php
                if (!empty($all_category_info)) {
                    foreach ($all_category_info as $cate_name => $v_category_info) { ?>
                        <?php if (!empty($v_category_info)) {
                            if (!empty($cate_name)) {
                                $cate_name = $cate_name;
                            } else {
                                $cate_name = lang('undefined_category');
                            }
                            foreach ($v_category_info as $sub_category) {
                                if (!empty($sub_category->stock_sub_category)) {
                                    ?>
                                    <li class="filter_by" id="<?= $sub_category->stock_sub_category_id ?>"
                                        search-type="by_sub_category">
                                        <a href="#"><?php echo $sub_category->stock_sub_category; ?></a>
                                    </li>
                                    <?php
                                }
                            }
                        }
                    }
                }
                ?>
            </ul>
        </li>
        <div class="clearfix"></div>
        <li class="dropdown-submenu pull-left " id="by_item_name">
            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('item_name'); ?></a>
            <ul class="dropdown-menu dropdown-menu-left by_item_name"
                style="">
                <?php
                $all_items = get_result('tbl_stock');
                if (!empty($all_items)) { ?>
                    <?php foreach ($all_items as $v_items) {
                        ?>
                        <li class="filter_by" id="<?= $v_items->stock_id ?>" search-type="by_item_name">
                            <a href="#"><?php echo $v_items->item_name; ?></a>
                        </li>
                    <?php }
                }
                ?>
            </ul>
        </li>
        <div class="clearfix"></div>
        <li class="dropdown-submenu pull-left " id="by_employee">
            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('employee') . ' ' . lang('name'); ?></a>
            <ul class="dropdown-menu dropdown-menu-left by_employee"
                style="">
                <?php
                if (!empty($all_employee)) { ?>
                    <?php foreach ($all_employee as $dept_name => $v_all_employee) {
                        if (!empty($v_all_employee)) {
                            foreach ($v_all_employee as $v_employee) {
                                ?>
                                <li class="filter_by" id="<?= $v_employee->user_id ?>" search-type="by_employee">
                                    <a href="#"><?php echo $v_employee->fullname . ' ( ' . $v_employee->designations . ' )'; ?></a>
                                </li>
                            <?php }
                        }
                    }
                }
                ?>
            </ul>
        </li>
    </ul>


</div>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs" style="margin-top: 1px">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"
                                                               data-toggle="tab"><?= lang('assign_stock_list') ?></a>
            </li>
            <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"
                                                               data-toggle="tab"><?= lang('assign_stock') ?></a>
            </li>
        </ul>
        <div class="tab-content bg-white">
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
                <?php } else { ?>
                <div class="panel panel-custom">
                    <header class="panel-heading ">
                        <div class="panel-title"><strong><?= lang('assign_stock_list') ?></strong></div>
                    </header>
                    <?php } ?>
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th class="col-sm-1"><?= lang('sl') ?></th>
                            <th><?= lang('item_name') ?></th>
                            <th><?= lang('stock_category') ?></th>
                            <th><?= lang('assign_quantity') ?></th>
                            <th><?= lang('assign_date') ?></th>
                            <th><?= lang('assigned_user') ?></th>
                            <?php if (!empty($deleted) || !empty($edited)) { ?>
                                <th class="col-sm-1 hidden-print"><?= lang('action') ?></th>
                            <?php } ?>

                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "admin/stock/assign_stockList";
                                $('.filtered > .dropdown-toggle').on('click', function () {
                                    if ($('.group').css('display') == 'block') {
                                        $('.group').css('display', 'none');
                                    } else {
                                        $('.group').css('display', 'block')
                                    }
                                });
                                $('.all_filter').on('click', function () {
                                    $('.to_account').removeAttr("style");
                                    $('.from_account').removeAttr("style");
                                });
                                $('.by_sub_category li').on('click', function () {
                                    if ($('.by_employee').css('display') == 'block') {
                                        $('.by_employee').removeAttr("style");
                                        $('.by_sub_category').css('display', 'block');
                                    } else {
                                        $('.by_sub_category').css('display', 'block')
                                    }
                                    if ($('.by_item_name').css('display') == 'block') {
                                        $('.by_item_name').removeAttr("style");
                                    }
                                });

                                $('.by_employee li').on('click', function () {
                                    if ($('.by_sub_category').css('display') == 'block') {
                                        $('.by_sub_category').removeAttr("style");
                                        $('.by_employee').css('display', 'block');
                                    } else {
                                        $('.by_employee').css('display', 'block');
                                    }
                                    if ($('.by_item_name').css('display') == 'block') {
                                        $('.by_item_name').removeAttr("style");
                                    }
                                });

                                $('.by_item_name li').on('click', function () {
                                    if ($('.by_sub_category').css('display') == 'block') {
                                        $('.by_sub_category').removeAttr("style");
                                        $('.by_item_name').css('display', 'block');
                                    } else {
                                        $('.by_item_name').css('display', 'block');
                                    }
                                    if ($('.by_employee').css('display') == 'block') {
                                        $('.by_employee').removeAttr("style");
                                    }
                                });

                                $('.filter_by').on('click', function () {
                                    $('.filter_by').removeClass('active');
                                    $('.group').css('display', 'block');
                                    $(this).addClass('active');
                                    var filter_by = $(this).attr('id');
                                    var search_type = $(this).attr('search-type');
                                    if (filter_by) {
                                        filter_by = filter_by;
                                    } else {
                                        filter_by = '';
                                    }
                                    if (search_type) {
                                        search_type = '/' + search_type;
                                    } else {
                                        search_type = '';
                                    }
//                                    alert(base_url + "admin/stock/assign_stockList/" + filter_by + search_type);
                                    table_url(base_url + "admin/stock/assign_stockList/" + filter_by + search_type);
                                });
                            });
                        </script>

                        </tbody>
                    </table>
                </div>
                <?php if (!empty($created) || !empty($edited)){ ?>
                <!-- Add Stock Category tab Starts -->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task" style="position: relative;">
                    <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                          action="<?php echo base_url() ?>admin/stock/set_assign_stock/<?php
                          if (!empty($assign_item->assign_item_id)) {
                              echo $assign_item->assign_item_id;
                          }
                          ?>" method="post" class="form-horizontal form-groups-bordered">
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
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('item_name') ?><span
                                    class="required">*</span></label>

                            <div class="col-sm-5">
                                <select required class="form-control" name="stock_id" id="item_name">
                                    <option value=""><?= lang('select') . ' ' . lang('item_name') ?></option>
                                    <?php if (!empty($stock_info)): ?>
                                        <?php foreach ($stock_info as $v_stock_info): ?>
                                            <option value="<?php echo $v_stock_info->stock_id ?>" <?php
                                            if (!empty($assign_item->stock_id)) {
                                                echo $v_stock_info->stock_id == $assign_item->stock_id ? 'selected' : '';
                                            }
                                            ?>><?php echo $v_stock_info->item_name ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('employee') . ' ' . lang('name') ?>
                                <span
                                    class="required"> *</span></label>

                            <div class="col-sm-5">
                                <select required class="form-control select_box" style="width: 100%" name="user_id">
                                    <option value=""><?= lang('select_employee') ?>...</option>
                                    <?php if (!empty($all_employee)): ?>
                                        <?php foreach ($all_employee as $dept_name => $v_all_employee) : ?>
                                            <optgroup label="<?php echo $dept_name; ?>">
                                                <?php if (!empty($v_all_employee)):foreach ($v_all_employee as $v_employee) : ?>
                                                    <option value="<?php echo $v_employee->user_id; ?>"
                                                        <?php
                                                        if (!empty($assign_item->user_id)) {
                                                            echo $v_employee->user_id == $assign_item->user_id ? 'selected' : '';
                                                        }
                                                        ?>><?php echo $v_employee->fullname . ' ( ' . $v_employee->designations . ' )' ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= lang('assign_quantity') ?><span
                                    class="required"> *</span></label>

                            <div class="col-sm-5">
                                <input required type="text" data-parsley-type="number" name="assign_inventory"
                                       placeholder=" <?= lang('enter') . ' ' . lang('assign_quantity') ?>"
                                       class="form-control" value="<?php
                                if (!empty($assign_item->assign_inventory)) {
                                    echo $assign_item->assign_inventory;
                                }
                                ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= lang('assign_date') ?><span
                                    class="required">*</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input required type="text" name="assign_date"
                                           placeholder="<?= lang('enter') . ' ' . lang('assign_date') ?>"
                                           class="form-control datepicker" value="<?php
                                    if (!empty($assign_item->assign_date)) {
                                        echo $assign_item->assign_date;
                                    }
                                    ?>" data-format="dd-mm-yyyy">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <?php
                            if (!empty($assign_item)) { ?>
                                <button type="submit" id="sbtn"
                                        class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                <button type="button" onclick="goBack()"
                                        class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                            <?php } else {
                                ?>
                                <button type="submit" id="sbtn"
                                        class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                            <?php }
                            ?>
                        </div>

                    </form>
                </div>
            </div>
            <?php }else{ ?>
        </div>
        <?php } ?>
    </ul>
</div>