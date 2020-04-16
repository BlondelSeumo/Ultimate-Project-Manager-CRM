<?php
echo message_box('success');
echo message_box('error');
?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <?= lang('items') . ' ' . lang('group') ?>
    </header>
    <?php echo form_open(base_url('admin/items/update_group'), array('id' => 'group_modal', 'class' => 'form-horizontal')); ?>
    <div class="form-group">
        <label
            class="col-sm-3 control-label"><?= lang('group') . ' ' . lang('name') ?></label>
        <div class="col-sm-5">
            <input type="text" name="customer_group" class="form-control"
                   placeholder="<?= lang('enter') . ' ' . lang('group') . ' ' . lang('name') ?>" required>
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
</div>

<script type="text/javascript">
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/items/update_group')?>') {
            event.preventDefault();
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            }).done(function (response) {
                response = JSON.parse(response);
                if (response.status == 'success') {
                    if (typeof(response.id) != 'undefined') {
                        var groups = $('select[name="customer_group_id"]');
                        groups.prepend('<option selected value="' + response.id + '">' + response.group + '</option>');
                        var select2Instance = groups.data('select2');
                        var resetOptions = select2Instance.options.options;
                        groups.select2('destroy').select2(resetOptions)
                    }
                    toastr[response.status](response.message);
                }
                $('#myModal').modal('hide');
            });
        }
    });
</script>