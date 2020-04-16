<div class="row">

    <div class="col-sm-12">
        <div class="panel panel-custom">
            <div class="panel-heading">  <?= $quotationforms_info->quotationforms_title ?></div>
            <div class="panel-body">
                <form method="post" action="<?= base_url() ?>client/tickets/create_tickets/<?php
                if (!empty($tickets_info)) {
                    echo $tickets_info->tickets_id;
                }
                ?>" enctype="multipart/form-data">
                    <?php
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
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>

