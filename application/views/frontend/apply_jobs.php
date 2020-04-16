<?php
if (!empty($circular_info->designations_id)) {
    $design_info = $this->db->where('designations_id', $circular_info->designations_id)->get('tbl_designations')->row();
    $designation = $design_info->designations;
} else {
    $designation = '-';
}
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <div class="panel-title">
            <strong><?= $circular_info->job_title . ' ( ' . $designation . ' ) ' ?></strong>
        </div>
    </div>
    <div class="panel-body form-horizontal">
        <form method="post" data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>frontend/save_job_application/<?php echo $circular_info->job_circular_id; ?>"
              class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('name') ?> <span class="required"> *</span></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon" id=""><i class="fa fa-user"></i></span>
                        <input required type="text"  name="name" class="form-control"
                               placeholder="<?= lang('enter') . ' ' . lang('fullname') ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('email') ?> <span class="required"> *</span></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon" id=""><i class="fa fa-envelope"></i></span>
                        <input required type="text" data-parsley-type="email" name="email" class="form-control"
                               placeholder="<?= lang('enter') . ' ' . lang('email') . ' ' . lang('address') ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('mobile') ?> <span class="required"> *</span></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon" id=""><i class="fa fa-phone"></i></span>
                        <input required type="text" data-parsley-type="number" name="mobile" class="form-control"
                               placeholder="<?= lang('enter') . ' ' . lang('mobile') . ' ' . lang('number') ?> ">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('cover_later') ?> </label>
                <div class="col-sm-9">
                    <textarea name="cover_letter" class="form-control textarea_2" rows="5"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?= lang('resume') ?> <span class="required"> *</span></label>
                <div class="col-sm-9">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <span class="btn btn-default btn-file"><span
                                            class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input required type="file" name="resume">
                                    </span>
                        <span class="fileinput-filename"></span>
                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                           style="float: none;">&times;</a>

                    </div>
                </div>
            </div>
            <div class="margin pull-right">
                <button id="btn_emp" type="submit" class="btn btn-primary btn-block"> <?= lang('save') ?></button>
            </div>
        </form>

    </div>
</div>
