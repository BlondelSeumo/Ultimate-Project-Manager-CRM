<?php
$created = can_action('70', 'created');
$edited = can_action('70', 'edited');
if (!empty($created) || !empty($edited)) {
    ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('edit') . ' ' . lang('departments') ?></h4>
        </div>
        <?php if (!empty($department_info->departments_id)) {
            $departments_id = $department_info->departments_id;
        } elseif (!empty($inline)) {
            $departments_id = 'inline/true';
        } ?>
        <div class="modal-body wrap-modal wrap">
            <form data-parsley-validate="" novalidate=""
                  action="<?php echo base_url() ?>admin/departments/edit_departments/<?= $departments_id; ?>"
                  method="post" class="form-horizontal form-groups-bordered">

                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-4 control-label"><?= lang('departments') . ' ' . lang('name') ?>
                        <span
                            class="required">*</span></label>
                    <div class="col-sm-5">
                        <input
                            type="text" name="deptname" required class="form-control"
                            value="<?= (!empty($department_info->deptname) ? $department_info->deptname : '') ?>"/>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php }
if (!empty($inline)) {
    ?>
    <script type="text/javascript">
        $(document).on("submit", "form", function (event) {
            var form = $(event.target);
            var id = form.attr('id');
            if (form.attr('action') == '<?= base_url('admin/departments/edit_departments/' . $departments_id)?>') {
                event.preventDefault();
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize()
                }).done(function (response) {
                    response = JSON.parse(response);
                    if (response.status == 'success') {
                        if (typeof (response.id) != 'undefined') {
                            var groups = $('select[name="departments_id"]');
                            groups.prepend('<option selected value="' + response.id + '">' + response.deptname + '</option>');
                            var select2Instance = groups.data('select2');
                            var resetOptions = select2Instance.options.options;
                            groups.select2('destroy').select2(resetOptions)
                        }
                    }
                    toastr[response.status](response.message);
                    $('#myModal').modal('hide');
                }).fail(function () {
                    console.log('There was a problem with AJAX')
                });
            }
        });
    </script>
<?php } ?>