<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('user_documents') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form data-parsley-validate="" novalidate="" enctype="multipart/form-data"
              action="<?php echo base_url() ?>admin/user/update_documents/<?php if (!empty($profile_info->account_details_id)) echo $profile_info->account_details_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <!-- CV Upload -->
            <div class="form-group mb0">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('resume') ?></label>

                <input type="hidden" name="resume_path" value="<?php
                if (!empty($document_info->resume_path)) {
                    echo $document_info->resume_path;
                }
                ?>">
                <input type="hidden" name="document_id" value="<?php
                if (!empty($document_info->document_id)) {
                    echo $document_info->document_id;
                }
                ?>">
                <div class="col-sm-8">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php if (!empty($document_info->resume)): ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new"
                                                                         style="display: none"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"
                                              style="display: block"><?= lang('change') ?></span>
                                        <input type="hidden" name="resume" value="<?php echo $document_info->resume ?>">
                                        <input type="file" name="resume">
                                    </span>
                            <span class="fileinput-filename"> <?php echo $document_info->resume_filename ?></span>
                        <?php else: ?>
                            <span class="btn btn-default btn-file"><span
                                    class="fileinput-new"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                        <input type="file" name="resume">
                                    </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        <?php endif; ?>

                    </div>
                    <div id="msg_pdf" style="color: #e11221"></div>
                </div>
            </div>

            <!-- Offer Letter Upload -->
            <div class="form-group mb0">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('offer_latter') ?></label>
                <input type="hidden" name="offer_letter_path" value="<?php
                if (!empty($document_info->offer_letter_path)) {
                    echo $document_info->offer_letter_path;
                }
                ?>">
                <div class="col-sm-8">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php if (!empty($document_info->offer_letter)): ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new"
                                                                         style="display: none"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"
                                              style="display: block"><?= lang('change') ?></span>
                                        <input type="hidden" name="offer_letter"
                                               value="<?php echo $document_info->offer_letter ?>">
                                        <input type="file" name="offer_letter">
                                    </span>
                            <span class="fileinput-filename"> <?php echo $document_info->offer_letter_filename ?></span>
                        <?php else: ?>
                            <span class="btn btn-default btn-file"><span
                                    class="fileinput-new"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                        <input type="file" name="offer_letter">
                                    </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        <?php endif; ?>

                    </div>
                    <div id="msg_pdf" style="color: #e11221"></div>
                </div>
            </div>

            <!-- Joining Letter Upload -->
            <div class="form-group mb0">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('joining_latter') ?></label>
                <input type="hidden" name="joining_letter_path" value="<?php
                if (!empty($document_info->joining_letter_path)) {
                    echo $document_info->joining_letter_path;
                }
                ?>">
                <div class="col-sm-8">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php if (!empty($document_info->joining_letter)): ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new"
                                                                         style="display: none"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"
                                              style="display: block"><?= lang('change') ?></span>
                                        <input type="hidden" name="joining_letter"
                                               value="<?php echo $document_info->joining_letter ?>">
                                        <input type="file" name="joining_letter">
                                    </span>
                            <span class="fileinput-filename"> <?php echo $document_info->offer_letter_filename ?></span>
                        <?php else: ?>
                            <span class="btn btn-default btn-file"><span
                                    class="fileinput-new"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                        <input type="file" name="joining_letter">
                                    </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        <?php endif; ?>

                    </div>
                    <div id="msg_pdf" style="color: #e11221"></div>
                </div>
            </div>

            <!-- Contract Paper Upload -->
            <div class="form-group mb0">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('contract_paper') ?></label>
                <input type="hidden" name="contract_paper_path" value="<?php
                if (!empty($document_info->contract_paper_path)) {
                    echo $document_info->contract_paper_path;
                }
                ?>">
                <div class="col-sm-8">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php if (!empty($document_info->contract_paper)): ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new"
                                                                         style="display: none"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"
                                              style="display: block"><?= lang('change') ?></span>
                                        <input type="hidden" name="contract_paper"
                                               value="<?php echo $document_info->contract_paper ?>">
                                        <input type="file" name="contract_paper">
                                    </span>
                            <span class="fileinput-filename"> <?php echo $document_info->offer_letter_filename ?></span>
                        <?php else: ?>
                            <span class="btn btn-default btn-file"><span
                                    class="fileinput-new"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                        <input type="file" name="contract_paper">
                                    </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        <?php endif; ?>

                    </div>
                    <div id="msg_pdf" style="color: #e11221"></div>
                </div>
            </div>

            <!-- ID / Proff Upload -->
            <div class="form-group mb0">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('id_prof') ?></label>
                <input type="hidden" name="id_proff_path" value="<?php
                if (!empty($document_info->id_proff_path)) {
                    echo $document_info->id_proff_path;
                }
                ?>">
                <div class="col-sm-8">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <?php if (!empty($document_info->id_proff)): ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new"
                                                                         style="display: none"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"
                                              style="display: block"><?= lang('change') ?></span>
                                        <input type="hidden" name="id_proff"
                                               value="<?php echo $document_info->id_proff ?>">
                                        <input type="file" name="id_proff">
                                    </span>
                            <span class="fileinput-filename"> <?php echo $document_info->offer_letter_filename ?></span>
                        <?php else: ?>
                            <span class="btn btn-default btn-file"><span
                                    class="fileinput-new"><?= lang('select_file') ?></span>
                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                        <input type="file" name="id_proff">
                                    </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        <?php endif; ?>

                    </div>
                    <div id="msg_pdf" style="color: #e11221"></div>
                </div>
            </div>

            <!-- Medical Upload -->
            <div id="add_new">
                <div class="form-group mb0" style="margin-bottom: 0px">
                    <label for="field-1"
                           class="col-sm-4 control-label"><?= lang('other_document') ?></label>
                    <div class="col-sm-5">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <?php
                            if (!empty($document_info->other_document)) {
                                $uploaded_file = json_decode($document_info->other_document);
                            }
                            if (!empty($uploaded_file)):foreach ($uploaded_file as $v_files_image): ?>
                                <div class="">
                                    <input type="hidden" name="path[]"
                                           value="<?php echo $v_files_image->path ?>">
                                    <input type="hidden" name="fileName[]"
                                           value="<?php echo $v_files_image->fileName ?>">
                                    <input type="hidden" name="fullPath[]"
                                           value="<?php echo $v_files_image->fullPath ?>">
                                    <input type="hidden" name="size[]"
                                           value="<?php echo $v_files_image->size ?>">
                                    <input type="hidden" name="is_image[]"
                                           value="<?php echo $v_files_image->is_image ?>">
                                    <span class=" btn btn-default btn-file">
                                    <span class="fileinput-filename"> <?php echo $v_files_image->fileName ?></span>
                                    <a href="javascript:void(0);" class="remCFile" style="float: none;">×</a>
                                    </span>
                                    <strong>
                                        <a href="javascript:void(0);" class="RCF"><i
                                                class="fa fa-times"></i>&nbsp;Remove</a></strong>
                                    <p></p>
                                </div>

                            <?php endforeach; ?>
                            <?php else: ?>
                                <span class="btn btn-default btn-file"><span
                                        class="fileinput-new"><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="other_document[]">
                                                        </span>
                                <span class="fileinput-filename"></span>
                                <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                                   style="float: none;">&times;</a>
                            <?php endif; ?>
                        </div>
                        <div id="msg_pdf" style="color: #e11221"></div>
                    </div>
                    <div class="col-sm-3">
                        <strong><a href="javascript:void(0);" id="add_more" class="addCF "><i
                                    class="fa fa-plus"></i>&nbsp;<?= lang('add_more') ?>
                            </a></strong>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {

            var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-4 control-label"><?= lang('other_document') ?></label>\n\
        <div class="col-sm-5">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" ><?= lang('select_file') ?></span><span class="fileinput-exists" >Change</span><input type="file" name="other_document[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;<?= lang('remove')?></a></strong></div>');
            maxAppend++;
            $("#add_new").append(add_new);

        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>
