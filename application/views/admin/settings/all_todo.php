<?php
echo message_box('success');
echo message_box('error');
$kanban = $this->session->userdata('todo_kanban');
$uri_segment = $this->uri->segment(4);
if (!empty($kanban)) {
    $todo = 'kanban';
} elseif ($uri_segment == 'kanban') {
    $todo = 'kanban';
} else {
    $todo = 'list';
}

if ($todo == 'kanban') {
    $text = 'list';
    $btn = 'purple';
} else {
    $text = 'kanban';
    $btn = 'danger';
}
if ($this->session->userdata('user_type') == 1) {
    $all_users = $this->db->where(array('role_id !=' => 2, 'activated' => 1))->get('tbl_users')->result();
    ?>
    <div class="well">
        <div class="row">
            <div class="col-sm-12">
                <form role="form" action="<?= base_url() ?>admin/dashboard/all_todo"
                      method="post">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="control-label"><?= lang('select') . ' ' . lang('users') ?> <span
                                    class="required"> *</span></label>
                            <select name="user_id" style="width: 100%" id="employee" required
                                    class="form-control select_box">
                                <option value=""><?= lang('select') . ' ' . lang('users') ?>...</option>
                                <?php if (!empty($all_users)): ?>
                                    <?php foreach ($all_users as $v_user) :
                                        $user_profile = $this->db->where(array('user_id' => $v_user->user_id))->get('tbl_account_details')->row();
                                        ?>
                                        <option value="<?php echo $v_user->user_id; ?>"
                                            <?php
                                            if (!empty($user_id)) {
                                                $user_id = $user_id;
                                            } else {
                                                $user_id = $this->session->userdata('user_id');
                                            }
                                            if (!empty($user_id)) {
                                                echo $v_user->user_id == $user_id ? 'selected' : '';
                                            }
                                            ?>><?php echo $user_profile->fullname ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label"></label>
                            <div class="">
                                <button type="submit" name="flag" value="1" data-toggle="tooltip" data-placement="top"
                                        title="" class="btn btn-purple" data-original-title="Search"><i
                                        class="fa fa-search fa-2x"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<div class="mb-lg ">
    <a href="<?= base_url() ?>admin/dashboard/all_todo/<?= $text ?>"
       class="btn btn-xs btn-<?= $btn ?>"
       data-toggle="tooltip"
       data-placement="top" title="<?= lang('switch_to_' . $text) ?>">
        <i class="fa fa-undo"> </i><?= ' ' . lang('switch_to_' . $text) ?>
    </a>
</div>
<div class="panel panel-custom">
    <header class="panel-heading mb0">
        <h3 class="panel-title">
            <span><?= lang('to_do') . ' ' . lang('list') ?></span>
            <div class="pull-right " style="padding-top: 0px;padding-bottom: 8px;margin-top: -4px">
                <a href="<?= base_url() ?>admin/dashboard/new_todo"
                   class="btn btn-xs btn-success" data-toggle="modal" data-placement="top"
                   data-target="#myModal_lg"><?= lang('add_new') ?></a>
            </div>
        </h3>
    </header>
    <div class="">
        <?php if ($todo == 'kanban') { ?>
            <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/kanban/kan-app.css"/>
            <div class="app-wrapper">
                <p class="total-card-counter" id="totalCards"></p>
                <div class="board" id="board"></div>
            </div>
            <?php include_once 'assets/plugins/kanban/todo_kan-app.php'; ?>
        <?php } else { ?>
            <table class="table todo-preview table-striped m-b-none text-sm items">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th><?= lang('what') . ' ' . lang('to_do') ?></th>
                    <th><?= lang('status') ?></th>
                    <th><?= lang('end_date') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <style type="text/css">
                    .dragger {
                        background: url(../../../assets/img/dragger.png) 0px 11px no-repeat;
                        cursor: pointer;
                    }

                    .table > tbody > tr > td {
                        vertical-align: initial;
                    }
                </style>
                <?php
                $t_where = array('user_id' => $user_id);
                if (!empty($where)) {
                    $t_where = array_merge($t_where, $where);
                }
                $my_todo_list = $this->db->where($t_where)->order_by('order', 'ASC')->get('tbl_todo')->result();
                if (!empty($my_todo_list)):foreach ($my_todo_list as $tkey => $my_todo):

                    if ($my_todo->status == 3) {
                        $todo_label = '<small style="font-size:10px;padding:2px;" class="label label-success ">' . lang('done') . '</small>';
                    } elseif ($my_todo->status == 2) {
                        $todo_label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">' . lang('on_hold') . '</small>';
                    } else {
                        $todo_label = '<small style="font-size:10px;padding:2px;" class="label label-warning">' . lang('in_progress') . '</small>';
                    }
                    if (!empty($my_todo->due_date)) {
                        $due_date = $my_todo->due_date;
                    } else {
                        $due_date = date('D-M-Y');
                    }
                    ?>
                    <tr class="sortable item" data-item-id="<?= $my_todo->todo_id ?>">
                        <td class="item_no dragger pl-lg pr-lg"><?= $tkey + 1 ?></td>
                        <td>
                            <div class="complete-todo checkbox c-checkbox ">
                                <label>
                                    <input type="checkbox" data-id="<?= $my_todo->todo_id ?>"
                                           style="position: absolute;" <?php
                                    if ($my_todo->status == 3) {
                                        echo 'checked';
                                    }
                                    ?>>
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <a <?php
                            if ($my_todo->status == 3) {
                                echo 'style="text-decoration: line-through;"';
                            }
                            ?> class="text-info" data-toggle="modal" data-target="#myModal_lg"
                               href="<?= base_url() ?>admin/dashboard/new_todo/<?= $my_todo->todo_id ?>">
                                <?php echo $my_todo->title; ?></a>
                            <?php if (!empty($my_todo->assigned) && $my_todo->assigned != 0) {
                                $a_userinfo = $this->db->where('user_id', $my_todo->assigned)->get('tbl_account_details')->row();
                                ?>
                                <small class="block" data-toggle="tooltip"
                                       data-placement="top"><?= lang('assign_by') ?><a
                                        class="text-danger"
                                        href="<?= base_url() ?>admin/user/user_details/<?= $my_todo->assigned ?>"> <?= $a_userinfo->fullname ?></a>
                                </small>
                            <?php } ?>
                        </td>

                        <td>
                            <?= $todo_label ?>
                            <div class="btn-group">
                                <button style="font-size:10px;padding:0px;margin-top: -1px"
                                        class="btn btn-xs btn-success dropdown-toggle"
                                        data-toggle="dropdown">
                                    <?= lang('change_status') ?>
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu animated zoomIn">
                                    <li>
                                        <a href="<?= base_url() ?>admin/dashboard/change_todo_status/<?= $my_todo->todo_id . '/1' ?>"><?= lang('in_progress') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url() ?>admin/dashboard/change_todo_status/<?= $my_todo->todo_id . '/2' ?>"><?= lang('on_hold') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url() ?>admin/dashboard/change_todo_status/<?= $my_todo->todo_id . '/3' ?>"><?= lang('done') ?></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <strong data-toggle="tooltip" data-placement="top"
                                    title="<?= strftime(config_item('date_format'), strtotime($due_date)) ?>"><?= date("l", strtotime($due_date)) ?>
                                <span class="block"><?= daysleft($due_date) ?></span>

                            </strong>

                        </td>
                        <td><?= btn_edit_modal('admin/dashboard/new_todo/' . $my_todo->todo_id) ?>
                            <?= btn_delete('admin/dashboard/delete_todo/' . $my_todo->todo_id) ?></td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        <?php } ?>
    </div><!-- ./box-body -->
</div>
<?php include_once 'assets/js/sales.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        init_items_sortable(true);
    });
</script>
