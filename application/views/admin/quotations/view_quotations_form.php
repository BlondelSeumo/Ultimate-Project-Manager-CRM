<div class="row" id="printableArea">
    <div class="col-sm-12">
        <div class="row show_print">
            <div class="col-md-1 pull-left">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= base_url() . config_item('company_logo') ?>">
            </div>
            <div class="col-md-10 pull-right ml0 pl0">
                <h4 class="m0 p0"><?= config_item('company_name') ?></h4>
                <?= lang('address') . ': ' . config_item('company_address') ?>
                <?= lang('city') . ': ' . config_item('company_city') ?>,
                <?= lang('country') . ': ' . config_item('company_country') . '-' . config_item('company_zip_code') ?>
                ,<?= lang('phone') ?> : <?= config_item('company_phone') ?>
            </div>
        </div>
        <div class="row hidden">
            <div class="col-md-2 text-center visible-md visible-lg">
                <img style="width: 100%"
                     src="<?= base_url() . config_item('company_logo') ?>">
            </div>
            <div class="col-md-10">
                <h4 class="ml-sm"><?= config_item('company_legal_name') ?></h4>
                <address></address><?= config_item('company_address') ?>
                <br><?= config_item('company_city') ?>
                , <?= config_item('company_zip_code') ?>
                <br><?= config_item('company_country') ?>
                <br/><?= lang('phone') ?> : <?= config_item('company_phone') ?>
            </div>
        </div>
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title">
                    <?= $quotationforms_info->quotationforms_title ?>
                    <div class="pull-right hidden-print mr-lg" >
                        <div class="pull-left mr-sm">
                            <button class="btn btn-xs pull-left btn-danger btn-print" type="button"
                                    data-toggle="tooltip"
                                    title="Print" onclick="printDiv('printableArea')"><i class="fa fa-print"></i></button>
                        </div>
                        <div class="pull-left">
                            <a href="<?= base_url() ?>admin/quotations/quotations_form_details_pdf/<?= $quotationforms_info->quotationforms_id ?>"
                               class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top"
                               title="PDF" style="margin-top: -6px;"><i class="fa fa-file-pdf-o"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <?php
                if (!empty($formbuilder_data)) {
                    foreach ($formbuilder_data as $value) {
                        if (!empty($value)) {
                            $field_type = $value['field_type'];
                            $field_options = $value['field_options'];
                            if ($value['required'] == 1) {
                                $required = 'true';
                            }
                            ?>

                            <?php if ($field_type == 'paragraph'): ?>
                                <label
                                    class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                                        <span class="text-danger">*</span><?php } ?></label>
                                <div class="">
                                    <?php
                                    if ($field_options['size'] == 'small') {
                                        $height = 'min-height:60px';
                                    } elseif ($field_options['size'] == 'medium') {
                                        $height = 'min-height:100px';
                                    } else {
                                        $height = 'min-height:200px';
                                    }
                                    ?>
                                    <textarea style="<?= $height ?>" class="form-control"></textarea>
                                </div>
                                <br/>
                            <?php endif; ?>
                            <?php if ($field_type == 'dropdown'): ?>
                                <label
                                    class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                                        <span class="text-danger">*</span><?php } ?></label>
                                <div class="">
                                    <select class="form-control">
                                        <?php
                                        $options = $field_options['options'];
                                        foreach ($options as $v_option) {
                                            if ($v_option['checked'] == 1) {
                                                $checked = 'selected';
                                            } else {
                                                $checked = '';
                                            }
                                            ?>
                                            <option <?= $checked ?>> <?= $v_option['label'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <br/>
                            <?php endif; ?>
                            <?php if ($field_type == 'text'): ?>
                                <label
                                    class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                                        <span class="text-danger">*</span><?php } ?></label>
                                <div class="">
                                    <input type="text" class="form-control"/>
                                </div>
                                <br/>
                            <?php endif; ?>
                            <?php if ($field_type == 'checkboxes'): ?>
                                <label
                                    class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                                        <span class="text-danger">*</span><?php } ?></label>
                                <div class="">
                                    <?php
                                    $options = $field_options['options'];
                                    foreach ($options as $v_option) {
                                        if ($v_option['checked'] == 1) {
                                            $checked = 'checked';
                                        } else {
                                            $checked = '';
                                        }
                                        ?>
                                        <input type="checkbox"
                                               style="width: 15px;height: 15px;margin-right: 5px"  <?= $checked ?> ><?= $v_option['label'] ?>
                                    <?php } ?>
                                </div>
                                <br/>
                            <?php endif; ?>
                            <?php if ($field_type == 'radio'): ?>
                                <label
                                    class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                                        <span class="text-danger">*</span><?php } ?></label>
                                <div class="">
                                    <?php
                                    $options = $field_options['options'];
                                    foreach ($options as $v_option) {
                                        if ($v_option['checked'] == 1) {
                                            $checked = 'checked';
                                        } else {
                                            $checked = '';
                                        }
                                        ?>
                                        <input type="radio"
                                               style="width: 15px;height: 15px;margin-right: 5px"  <?= $checked ?> ><?= $v_option['label'] ?>
                                    <?php } ?>
                                </div>
                                <br/>
                            <?php endif; ?>

                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function printDiv(printableArea) {
        var printContents = document.getElementById(printableArea).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
