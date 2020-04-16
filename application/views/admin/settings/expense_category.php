<?php
echo message_box('success');
echo message_box('error');
$created = can_action('124', 'created');
$edited = can_action('124', 'edited');
$deleted = can_action('124', 'deleted');
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('expense_category') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                <tr>

                    <th><?= lang('expense_category') ?></th>
                    <th><?= lang('description') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                if (!empty($all_expense_category)) {
                    foreach ($all_expense_category as $expense_category) {
                        $where = array('type' => 'Expense', 'category_id' => $expense_category->expense_category_id);
                        $total_expense = $this->db->select_sum('amount')->where($where)->get('tbl_transactions')->result()[0]->amount;
                        ?>
                        <tr id="expense_category_<?= $expense_category->expense_category_id?>">
                            <td><?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $expense_category->expense_category_id) { ?>
                                <form method="post"
                                      action="<?= base_url() ?>admin/settings/expense_category/update_expense_category/<?php
                                      if (!empty($expense_category_info)) {
                                          echo $expense_category_info->expense_category_id;
                                      }
                                      ?>" class="form-horizontal">
                                    <input type="text" name="expense_category" value="<?php
                                    if (!empty($expense_category)) {
                                        echo $expense_category->expense_category;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('expense_category') ?>" required>
                                <?php } else {
                                    echo $expense_category->expense_category . '<p class="text-sm text-info m0 p0">' . lang('total') . ' ' . lang('expense') . ' : ' . display_money($total_expense, $currency->symbol) . '</p>';;;
                                }
                                ?></td>
                            <td><?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $expense_category->expense_category_id) { ?>
                                    <textarea name="description" rows="1" class="form-control"><?php
                                        if (!empty($expense_category)) {
                                            echo $expense_category->description;
                                        }
                                        ?></textarea>
                                <?php } else {
                                    echo $expense_category->description;
                                }
                                ?></td>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <td>
                                    <?php
                                    $id = $this->uri->segment(5);
                                    if (!empty($id) && $id == $expense_category->expense_category_id) { ?>
                                        <?= btn_update() ?>
                                        </form>
                                        <?= btn_cancel('admin/settings/expense_category/') ?>
                                    <?php } else {
                                        if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/settings/expense_category/edit_expense_category/' . $expense_category->expense_category_id) ?>
                                        <?php }
                                        if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/settings/delete_expense_category/" . $expense_category->expense_category_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#expense_category_" . $expense_category->expense_category_id)); ?>
                                        <?php }
                                    } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php }
                }
                if (!empty($created) || !empty($edited)) { ?>
                    <form method="post"
                          action="<?= base_url() ?>admin/settings/expense_category/update_expense_category"
                          class="form-horizontal">
                        <tr>
                            <td><input type="text" name="expense_category" class="form-control"
                                       placeholder="<?= lang('expense_category') ?>" required></td>
                            <td>
                                <textarea name="description" rows="1" class="form-control"></textarea>
                            </td>
                            <td><?= btn_add() ?></td>
                        </tr>
                    </form>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
