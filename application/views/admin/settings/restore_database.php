<div class="panel panel-custom">
    <div class="panel-heading">

        <h4 class="modal-title"
            id="myModalLabel"><?= lang('restore_database') ?>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form"
              action="<?php echo base_url() ?>admin/settings/restore_database"
              method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="panel-body">
                <div class="alert alert-warning"><?= lang('restore_notice'); ?></div>
                <br/>
                <div class="form-group" style="margin-bottom: 0px">
                    <label for="field-1"
                           class="col-sm-6 control-label"><?= lang('upload') . ' ' . lang('database_backup') . ' ' . lang('zipped_file') ?></label>
                    <div class="col-sm-5">
                        <div class="fileinput fileinput-new"
                             data-provides="fileinput">

                            <span class="btn btn-default btn-file"><span
                                    class="fileinput-new"><?= lang('choose_file') ?></span>
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                            <input type="file" name="upload_file">
                                                        </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists"
                               data-dismiss="fileinput"
                               style="float: none;">&times;</a>
                        </div>
                        <div id="msg_pdf" style="color: #e11221"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="submit" name="send" class="btn btn-primary"
                           value="<?= lang('upload'); ?>"/>
                    <a class="btn" data-dismiss="modal"><?= lang('close'); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>