<!-- FONT AWESOME-->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome/css/fontawesome-iconpicker.min.css">
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/fontawesome/js/fontawesome-iconpicker.js"></script>

<div class="panel panel-custom" data-collapsed="0">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('add') . ' ' . lang('menu') ?></strong>
        </div>
    </div>
    <div class="panel-body">

        <form role="form" id="form"
              action="<?php echo base_url(); ?>admin/navigation/save_navigation/<?php if (!empty($menu_info->menu_id)) {
                  echo $menu_info->menu_id;
              } ?>" method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label">Label<span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <input type="text" name="label" value="<?php echo $menu_info->label ?>"
                           class="form-control" placeholder="Menu Label"/>
                </div>
            </div>

            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label">Icon<span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <input class="form-control icon-picker" name="icon" value="<?php echo $menu_info->icon ?>" type="text"/>
                    <!--                                <input type="text" name="icon" value="-->
                    <?php //echo $menu_info->icon ?><!--" class="form-control" placeholder="Menu Icon"/>-->
                </div>
            </div>

            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label">Parent</label>
                <div class="col-sm-5">
                    <select name="parent" class="form-control select_box">
                        <option value="">Select Parent...</option>

                        <?php if (count($nav)): foreach ($nav as $v_nav) : ?>
                            <option value="<?php echo $v_nav->menu_id ?>"
                                <?php echo $menu_info->parent == $v_nav->menu_id ? 'selected' : '' ?>>
                                <?php echo $v_nav->label ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label">Sort<span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <input type="text" name="sort" value="<?php echo $menu_info->sort ?>"
                           class="form-control" placeholder="Menu Sorting"/>
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-5 pull-right">
                    <button type="submit" id="sbtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    $(document).ready(function () {
        _MenuIconInput();
        $('.icon-picker').iconpicker()
            .on({
                'iconpickerSetSourceValue': function (e) {
                    _MenuIconInput(e);
                }
            })

    });
    function _MenuIconInput(e) {
        if (typeof(e) == 'undefined') {
            return;
        }
        var _input = $(e.target);
        if (!_input.val().match(/^fa /)) {
            _input.val(
                'fa ' + _input.val()
            );
        }
    }


</script>

