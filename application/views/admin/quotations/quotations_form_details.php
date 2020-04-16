<link href="<?= base_url() ?>plugins/formbuilder/formbuilder.css" rel="stylesheet"/>
<style>
    .fb-main {
        background-color: #fff;
        min-height: 600px;
    }

    input[type=text] {
        height: 26px;
        margin-bottom: 3px;
    }

    select {
        margin-bottom: 5px;
    }
</style>
<?= message_box('success'); ?>
<form class="form-horizontal" action="<?= base_url() ?>admin/quotations/add_form/<?php
if (!empty($quotationforms_info)) {
    echo $quotationforms_info->quotationforms_id;
}
?>" method="post" id="addQuotationForm">
    <!-- Sidebar ends -->
    <!-- Main bar -->
    <div class="">
        <div class="col-md-12 mb-lg">
            <input type="text" class="form-control" name="quotationforms_title" autocomplete="off"
                   value="<?= $quotationforms_info->quotationforms_title ?>" placeholder="<?= lang('form_title') ?>">
        </div>
    </div>
    <!--WI_QUOTATION_TITLE-->
    <div class="">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="fb-main"></div>
            </div>
            <div class="panel-footer">
                <div class="col-sm-3">
                    <select class="form-control" name="quotationforms_status">
                        <option value="enabled" <?php
                        if ($quotationforms_info->quotationforms_status == 'enabled') {
                            echo 'selected';
                        }
                        ?>><?= lang('enabled') ?></option>
                        <option value="disabled" <?php
                        if ($quotationforms_info->quotationforms_status == 'disabled') {
                            echo 'selected';
                        }
                        ?>><?= lang('disabled') ?></option>
                    </select>
                </div>
                <div class="pull-right">
                    <input class="btn btn-primary" type="submit" value="<?= lang('save') ?>" id="" name="submit">
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <input type="hidden" name="quotationforms_code" id="quotationforms_code"
           value='<?= $quotationforms_info->quotationforms_code ?>'>
</form>
<script src="<?= base_url() ?>asset/vendor/js/vendor.js"></script>
<script src="<?= base_url() ?>plugins/formbuilder/formbuilder.js"></script>

<script>
    $(function () {
        fb = new Formbuilder({
            selector: '.fb-main',
            bootstrapData: [
                <?php
                foreach ($formbuilder_data as $value) {
                if (!empty($value)) {
                $field_options = $value['field_options'];
                if ($value['required'] == 1) {
                    $required = 'true';
                } else {
                    $required = 'false';
                }
                ?>
                {
                    "label": "<?php echo $value['label'] ?>",
                    "field_type": "<?php echo $value['field_type'] ?>",
                    "required": <?php echo $required; ?>,
                    "field_options": {
                        <?php if (!empty($field_options['options'])) { ?>
                        "options": [
                            <?php
                            $options = $field_options['options'];
                            foreach ($options as $v_option) {
                            if ($v_option['checked'] == 1) {
                                $checked = 'true';
                            } else {
                                $checked = 'false';
                            }
                            ?>
                            {
                                "label": "<?= $v_option['label'] ?>",
                                "checked": <?= $checked ?>
                            },
                            <?php } ?>],
                        "include_other_option": true
                        <?php } else { ?>
                        "size": "<?= $field_options['size']; ?>"
                        <?php } ?>
                    },
                    "cid": "<?php echo $value['cid'] ?>"
                },
                <?php
                }
                }
                ?>
            ]
        });
        fb.on('save', function (payload) {
            console.log(payload);
            $('#quotationforms_code').val(payload);
        })
    });
</script>
<!-- Mainbar ends -->
<div class="clearfix"></div>