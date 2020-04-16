<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('ban_reason') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form role="form" id="ban_reason" data-parsley-validate="" novalidate=""
              action="<?php echo base_url(); ?>admin/user/set_banned/1/<?= $user_id ?>" method="post"
              class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <div class="col-sm-12">
                    <textarea type="text" name="ban_reason" value="" required="" rows="5"
                              class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $("#ban_reason").validate({
            rules: {
                ban_reason: {
                    required: true,
                }
            }
        });
    });</script>
<script src="<?php echo base_url(); ?>asset/js/custom-validation.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>asset/js/jquery.validate.js" type="text/javascript"></script>
