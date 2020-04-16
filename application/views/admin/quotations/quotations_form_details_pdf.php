<!DOCTYPE html>
<html>
<head>
    <title><?= lang('overtime_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>
    <style type="text/css">
        .table_tr1 {
            background-color: rgb(224, 224, 224);
        }

        .table_tr1 td {
            padding: 7px 0px 7px 8px;
            font-weight: bold;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr2 td {
            padding: 7px 0px 7px 8px;
            border: 1px solid black;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .total_amount td {
            padding: 7px 8px 7px 0px;
            border: 1px solid black;
            font-size: 15px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }
    </style>
</head>
<body style="min-width: 100%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<br/>
<?php
$img = ROOTPATH . '/' . config_item('company_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('company_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= $img ?>" alt="" class="img-circle"/>
            </td>
            <td style="border: 0px;">
                <h4 style="margin: 0;padding: 0"><?= config_item('company_name') ?></h4>
                <?= lang('address') . ': ' . config_item('company_address') ?>
                <?= lang('city') . ': ' . config_item('company_city') ?>,
                <?= lang('country') . ': ' . config_item('company_country') . '-' . config_item('company_zip_code') ?>
                ,<?= lang('phone') ?> : <?= config_item('company_phone') ?>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="width: 100%;">
    <div style="width: 100%; background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;">
        <table style="width: 100%;">
            <tr style="font-size: 20px;  text-align: center">
                <td style="padding: 10px;"><?= $quotationforms_info->quotationforms_title ?></td>
            </tr>
        </table>
    </div>
    <br/>
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
                            $height = "<br/><br/><br/>";
                        } elseif ($field_options['size'] == 'medium') {
                            $height = "<br/><br/><br/><br/><br/>";
                        } else {
                            $height = "<br/><br/><br/><br/><br/><br/><br/><br/>";
                        }
                        echo $height;
                        ?>
                    </div>
                    <br/>
                <?php endif; ?>
                <?php if ($field_type == 'dropdown'): ?>
                    <label
                        class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                            <span class="text-danger">*</span><?php } ?></label>
                    <div class="">
                        <?php
                        $options = $field_options['options'];
                        foreach ($options as $dr => $v_option) {
                            if ($v_option['checked'] == 1) {
                                $checked = 'selected';
                            } else {
                                $checked = '';
                            }
                            echo $dr + 1 . '.' . $v_option['label'] . '  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            ?>
                        <?php } ?>
                    </div>
                    <br/>
                <?php endif; ?>
                <?php if ($field_type == 'text'): ?>
                    <label
                        class="control-label"><?php echo $value['label'] ?> <?php if (!empty($required)) { ?>
                            <span class="text-danger">*</span><?php } ?></label>
                    <div class="">
                        <?php $height = "<br/><br/><br/>";
                        echo $height;
                        ?>
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
                        foreach ($options as $ch => $v_option) {
                            if ($v_option['checked'] == 1) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }
                            echo $ch + 1 . '.' . $v_option['label'] . '  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            ?>

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
                        foreach ($options as $r => $v_option) {
                            if ($v_option['checked'] == 1) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }
                            echo $r + 1 . '.' . $v_option['label'] . '  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            ?>
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

</body>
</html>