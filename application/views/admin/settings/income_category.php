<?= message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('123', 'created');
$edited = can_action('123', 'edited');
$deleted = can_action('123', 'deleted');
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('income_category') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                <tr>

                    <th><?= lang('income_category') ?></th>
                    <th><?= lang('description') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                if (!empty($all_income_category)) {
                    foreach ($all_income_category as $income_category) {
                        $where = array('type' => 'Income', 'category_id' => $income_category->income_category_id);
                        $total_income = $this->db->select_sum('amount')->where($where)->get('tbl_transactions')->result()[0]->amount;

                        ?>
                        <tr id="income_category_<?= $income_category->income_category_id?>">
                            <td><?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $income_category->income_category_id) { ?>
                                <form method="post"
                                      action="<?= base_url() ?>admin/settings/income_category/update_income_category/<?php
                                      if (!empty($income_category_info)) {
                                          echo $income_category_info->income_category_id;
                                      }
                                      ?>" class="form-horizontal">
                                    <input type="text" name="income_category" value="<?php
                                    if (!empty($income_category_info)) {
                                        echo $income_category_info->income_category;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('income_category') ?>" required>
                                <?php } else {
                                    echo $income_category->income_category . '<p class="text-sm text-info m0 p0">' . lang('total') . ' ' . lang('income') . ' : ' . display_money($total_income, $currency->symbol) . '</p>';
                                }
                                ?></td>
                            <td><?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $income_category->income_category_id) { ?>
                                    <textarea name="description" rows="1" class="form-control"><?php
                                        if (!empty($income_category_info)) {
                                            echo $income_category_info->description;
                                        }
                                        ?></textarea>
                                <?php } else {
                                    echo $income_category->description;
                                }
                                ?></td>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <td>
                                    <?php
                                    $id = $this->uri->segment(5);
                                    if (!empty($id) && $id == $income_category->income_category_id) { ?>
                                        <?= btn_update() ?>
                                        </form>
                                        <?= btn_cancel('admin/settings/income_category/') ?>
                                    <?php } else {
                                        if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/settings/income_category/edit_income_category/' . $income_category->income_category_id) ?>
                                        <?php }
                                        if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/settings/delete_income_category/" . $income_category->income_category_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#income_category_" . $income_category->income_category_id)); ?>
                                        <?php }
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php }
                }
                if (!empty($created) || !empty($edited)) { ?>
                    <form method="post" action="<?= base_url() ?>admin/settings/income_category/update_income_category"
                          class="form-horizontal" data-parsley-validate="" novalidate="">
                        <tr>
                            <td><input type="text" name="income_category" class="form-control"
                                       placeholder="<?= lang('income_category') ?>" required></td>
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