<div class="panel panel-custom">
    <div class="panel-heading">

        <h4 class="modal-title"
            id="myModalLabel"><?= lang('training_details') ?>
            <div class="pull-right">

                <?= btn_pdf('admin/training/training_pdf/' . $training_info->training_id) ?>
            </div>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="panel-body form-horizontal">
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('employee') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if (!empty($training_info->employment_id)) {
                            echo $training_info->fullname
                            ?> (<?php
                            echo $training_info->employment_id . ' )';
                        }
                        ?></p>
                </div>
            </div>

            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('course_training') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $training_info->training_name; ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('vendor') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $training_info->vendor_name; ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('start_date') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($training_info->start_date)) ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('finish_date') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($training_info->finish_date)) ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('training_cost') ?> :</strong></label>
                </div>
                <?php
                $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                ?>
                <div class="col-sm-8">
                    <?php if (!empty($training_info->training_cost)) { ?>
                        <p class="form-control-static"><?= display_money($training_info->training_cost, $curency->symbol); ?></p>
                    <?php } ?>
                </div>
            </div>
            <?php $show_custom_fields = custom_form_label(15, $training_info->training_id);

            if (!empty($show_custom_fields)) {
                foreach ($show_custom_fields as $c_label => $v_fields) {
                    if (!empty($v_fields)) {
                        ?>
                        <div class="col-md-12 notice-details-margin">
                            <div class="col-sm-4 text-right">
                                <label class="control-label"><strong><?= $c_label ?> :</strong></label>
                            </div>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?= $v_fields ?></p>
                            </div>
                        </div>
                    <?php }
                }
            }
            ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('status') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if ($training_info->status == '0') {
                            echo '<span class="label label-warning">' . lang('pending') . ' </span>';
                        } elseif ($training_info->status == '1') {
                            echo '<span class="label label-info">' . lang('started') . '</span>';
                        } elseif ($training_info->status == '2') {
                            echo '<span class="label label-success"> ' . lang('completed') . ' </span>';
                        } else {
                            echo '<span class="label label-danger"> ' . lang('terminated ') . '</span>';
                        }
                        ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('performance') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if ($training_info->performance == '0') {
                            echo '<span class="label label-warning">' . lang('not_concluded') . ' </span>';
                        } elseif ($training_info->performance == '1') {
                            echo '<span class="label label-info">' . lang('satisfactory') . '</span>';
                        } elseif ($training_info->performance == '2') {
                            echo '<span class="label label-primary"> ' . lang('average') . ' </span>';
                        } elseif ($training_info->performance == '3') {
                            echo '<span class="label label-danger"> ' . lang('poor') . ' </span>';
                        } else {
                            echo '<span class="label label-success"> ' . lang('excellent ') . '</span>';
                        }
                        ?></p>
                </div>
            </div>
            <?php
            $uploaded_file = json_decode($training_info->upload_file);
            if (!empty($uploaded_file)) {
                ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('attachment') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <ul class="mailbox-attachments clearfix mt">
                            <?php
                            if (!empty($uploaded_file)):
                                foreach ($uploaded_file as $v_files):

                                    if (!empty($v_files)):

                                        ?>
                                        <li>
                                            <?php if ($v_files->is_image == 1) : ?>
                                                <span class="mailbox-attachment-icon has-img"><img
                                                        src="<?= base_url() . $v_files->path ?>"
                                                        alt="Attachment"></span>
                                            <?php else : ?>
                                                <span class="mailbox-attachment-icon"><i
                                                        class="fa fa-file-pdf-o"></i></span>
                                            <?php endif; ?>
                                            <div class="mailbox-attachment-info">
                                                <a href="<?= base_url() ?>admin/training/ownload_file/<?= $training_info->training_id . '/' . $v_files->fileName ?>"
                                                   class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                                    <?= $v_files->fileName ?></a>
                        <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> <?= lang('kb') ?>
                            <a href="<?= base_url() ?>admin/training/download_file/<?= $training_info->training_id . '/' . $v_files->fileName ?>"
                               class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                                            </div>
                                        </li>
                                        <?php
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('remarks') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <blockquote style="font-size: 12px"><?php echo $training_info->remarks; ?></blockquote>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
    </div>
</div>
