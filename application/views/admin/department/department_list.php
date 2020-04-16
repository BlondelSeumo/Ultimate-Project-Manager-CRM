<?= message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('70', 'created');
$edited = can_action('70', 'edited');
$deleted = can_action('70', 'deleted');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <?= lang('all') . ' ' . lang('department') ?>
        <?php if (!empty($created)) { ?>
            <div class="pull-right">
                <a href="<?= base_url() ?>admin/departments/details"><?= lang('new_department') ?></a>
            </div>
        <?php } ?>
    </div>
    <div class="panel-body">
        <!-- NESTED-->
        <div class="box" style="" data-collapsed="0">
            <div class="box-body">
                <!-- Table -->
                <div class="row">
                    <?php
                    if (!empty($all_department_info)): foreach ($all_department_info as $akey => $v_department_info) : ?>
                        <?php if (!empty($v_department_info)):
                            if (!empty($all_dept_info[$akey]->deptname)) {
                                $deptname = $all_dept_info[$akey]->deptname;
                            } else {
                                $deptname = lang('undefined_department');
                            }
                            ?>
                            <div class="col-sm-6 mb-lg" id="table_department_<?= $all_dept_info[$akey]->departments_id ?>">
                            <div class="box-heading">
                                <div class="box-title">
                                    <h4 class="m0"><?php echo $deptname ?>
                                        <?php if (!empty($edited)) { ?>
                                            <div class="pull-right">
                                                    <span data-toggle="tooltip" data-placement="top"
                                                          title="<?= lang('edit') ?>">
                                                    <a href="<?= base_url() ?>admin/departments/edit_departments/<?= $all_dept_info[$akey]->departments_id ?>"
                                                       class="btn btn-primary btn-xs" data-toggle="modal"
                                                       data-placement="top" data-target="#myModal"><span
                                                            class="fa fa-pencil-square-o"></span></a>
                                                        </span>
                                                <?php echo ajax_anchor(base_url("admin/departments/delete_department/" . $all_dept_info[$akey]->departments_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_department_" . $all_dept_info[$akey]->departments_id)); ?>
                                            </div>
                                        <?php } ?>
                                    </h4>
                                    <?php if (!empty($all_dept_info[$akey]->department_head_id)) {
                                        echo '<span class="text-sm"><strong>' . lang('department_head') . '</strong>: <a href="' . base_url('admin/user/user_details/' . $all_dept_info[$akey]->department_head_id) . '">' . fullname($all_dept_info[$akey]->department_head_id) . '</a></span>';

                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Table -->
                            <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td class="text-bold col-sm-1">#</td>
                                <td class="text-bold"><?= lang('designation') ?></td>
                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                    <td class="text-bold col-sm-2"><?= lang('action') ?></td>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($v_department_info as $key => $v_department) :
                            if (!empty($v_department->designations)) {
                                $total_employee = count($this->db->where('designations_id', $v_department->designations_id)->get('tbl_account_details')->result());
                                ?>

                                <tr id="table_designation_<?= $v_department->designations_id ?>">
                                    <td><?php echo $key + 1 ?></td>
                                    <td>
                                        <a data-toggle="tooltip" data-placement="top"
                                           title="<?= lang('set_full_permission') ?>"
                                           href="<?= base_url() ?>admin/departments/details/<?= $v_department->designations_id ?>"> <?php echo $v_department->designations ?></a>
                                        <p class="m0">
                                            <a data-toggle="modal" data-target="#myModal" style="color:#656565"
                                               href="<?= base_url() ?>admin/departments/user_by_designation/<?= $v_department->designations_id ?>">
                                                <strong>
                                                    <small><?= lang('total') . ' ' . lang('users') . ': ' . $total_employee ?></small>
                                                </strong>
                                            </a>
                                        </p>
                                    </td>
                                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                                        <td>
                                            <?php if (!empty($edited)) { ?>
                                                <?php echo btn_edit('admin/departments/details/' . $v_department->designations_id); ?>
                                            <?php }
                                            if (!empty($deleted)) { ?>
                                                <?php echo ajax_anchor(base_url("admin/departments/delete_designations/" . $v_department->designations_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_designation_" . $v_department->designations_id)); ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php
                            } else {
                                ?>
                                <tr>
                                    <td colspan="3"><?= lang('no_designation_create_yet') ?></td>
                                <tr></tr>
                            <?php }
                        endforeach;
                            ?>
                        <?php endif; ?>
                        </tbody>
                        </table>
                        </div>

                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
