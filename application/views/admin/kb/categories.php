<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('142', 'created');
$edited = can_action('142', 'edited');
$deleted = can_action('142', 'deleted');
?>
<div class="panel panel-custom" style="border: none;" data-collapsed="0">
    <div class="panel-heading">
        <div class="panel-title">
            <?= lang('category') ?>
            <?php if (!empty($created)) { ?>
                <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                    <a href="<?= base_url() ?>admin/knowledgebase/new_categories" class="btn btn-xs btn-info"
                       data-toggle="modal"
                       data-placement="top" data-target="#myModal">
                        <i class="fa fa-plus "></i> <?= ' ' . lang('new') . ' ' . lang('categories') ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('categories') ?></th>
                <th><?= lang('description') ?></th>
                <th class="col-sm-1"><?= lang('active') ?></th>
                <th class="col-sm-1"><?= lang('order') ?></th>
                <th><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                list = base_url + "admin/knowledgebase/categoriesList";
            </script>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).on("click", '.change_kb_category input[type="checkbox"]', function () {
            var kb_category_id = $(this).data().id;
            var status = $(this).is(":checked");
            if (status == true) {
                status = 1;
            } else {
                status = 2;
            }
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/knowledgebase/change_status/' + status + '/' + kb_category_id, // the url where we want to POST
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    if (res) {
                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })
    });
</script>