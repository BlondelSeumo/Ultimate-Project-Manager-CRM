<form method="post" action="<?= base_url() ?>admin/settings/update_menu_allocation" enctype="multipart/form-data"
      class="form-horizontal">
    <div class="row">
        <div class="col-sm-12" data-spy="scroll" data-offset="0">
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <div class="panel-title">
                        <?= lang('menu_allocation'); ?>
                        <div class="pull-right">
                            <button type="submit"
                                    class="btn btn-sm btn-primary"></i> <?= lang('submit') ?></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="box-heading">
                            <div class="box-title">
                                <h4><?= lang('active_menu'); ?></h4>
                            </div>
                        </div>
                        <div id="nestable" class="dd">
                            <?php echo $active_menu ?>
                        </div>
                        <textarea id="nestable-output" name="all_active_menu" class="form-control hidden"></textarea>
                    </div>

                    <div class="col-md-6">
                        <div class="box-heading">
                            <div class="box-title">
                                <h4><?= lang('inactive_menu'); ?></h4>
                            </div>
                        </div>
                        <div id="nestable2" class="dd">
                            <?= !empty($inactive_menu) ? $inactive_menu : '' ?>
                        </div>
                        <textarea id="nestable2-output" name="all_inactive_menu" class="form-control hidden"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url(); ?>assets/plugins/nestable/jquery.nestable.js"></script>
<script type="text/javascript">
    // Nestable demo
    // -----------------------------------
    (function (window, document, $, undefined) {

        $(function () {

            var updateOutput = function (e) {
                var list = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };
            // activate Nestable for list 1
            $('#nestable').nestable({
                    group: 1
                })
                .on('change', updateOutput);

            // activate Nestable for list 2
            $('#nestable2').nestable({
                    group: 1
                })
                .on('change', updateOutput);
            // output initial serialised data
            updateOutput($('#nestable').data('output', $('#nestable-output')));
            updateOutput($('#nestable2').data('output', $('#nestable2-output')));
            $('.js-nestable-action').on('click', function (e) {
                var target = $(e.target),
                    action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });

        });

    })(window, document, window.jQuery);
</script>
