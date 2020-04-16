<?php
echo message_box('success');
echo message_box('error');
$created = can_action('122', 'created');
$edited = can_action('122', 'edited');
?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <?= lang('leave_category') ?>
    </header>
    <?php if (!empty($created) || !empty($edited)) { ?>
        <?php echo form_open(base_url('admin/leave_management/update_leave_category'), array('id' => 'update_leave_category', 'class' => 'form-horizontal')); ?>
        <div class="form-group">
            <label
                class="col-sm-3 control-label"><?= lang('leave_category') ?></label>
            <div class="col-sm-5">
                <input type="text" name="leave_category" class="form-control"
                       placeholder="<?= lang('leave_category') ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label
                class="col-sm-3 control-label"><?= lang('leave_quota') ?></label>
            <div class="col-sm-5">
                <input type="text" name="leave_quota" class="form-control"
                       placeholder="<?= lang('days') . ' / ' . lang('years') ?>" required>
            </div>
        </div>
        <div class="form-group mt">
            <label class="col-lg-3"></label>
            <div class="col-lg-3">
                <button type="submit"
                        class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } ?>
</div>

<script type="text/javascript">

    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/leave_management/update_leave_category')?>') {
            event.preventDefault();
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            }).done(function (response) {
                response = JSON.parse(response);
                console.log(response);
                if (response.status == 'success') {
                    if (typeof(response.id) != 'undefined') {
                        var groups = $('select[name="leave_category_id"]');
                        groups.prepend('<option selected value="' + response.id + '">' + response.leave_category + '</option>');
                        var select2Instance = groups.data('select2');
                        var resetOptions = select2Instance.options.options;
                        groups.select2('destroy').select2(resetOptions)
                    }
                    toastr[response.status](response.message);
                }
                $('#myModal').modal('hide');
            }).fail(function () {
                alert('There was a problem with AJAX');
            });
        }
    });
</script>