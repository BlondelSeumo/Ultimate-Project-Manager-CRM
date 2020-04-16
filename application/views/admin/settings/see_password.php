<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('see_password') ?></h4>
    </div>
    <div class="modal-body form-horizontal">
        <div class="form-group">
            <div class="col-lg-12">
                <input type="password" class="form-control"
                       placeholder="<?= lang('enter') . ' ' . lang('your') . ' ' . lang('current') . ' ' . lang('password') ?>"
                       name="my_password">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" id="check_current_password" class="btn btn-primary"><?= lang('update') ?></button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        setTimeout(function () {
            $('#show_password').fadeOut('fast');
        }, 10000);

        $('#check_current_password').on('click', function () {
            var my_password = $('input[name="my_password"]').val();
            var encrypt_password = '<?= $password ?>';
            var ids = '<?= $ids ?>';
            $.ajax({
                url: base_url + "admin/global_controller/check_current_password/",
                type: "POST",
                data: {
                    name: my_password,
                    encrypt_password: encrypt_password,
                },
                dataType: 'json',
                success: function (res) {
                    if (res.error) {
                        if (ids) {
                            handle_error("#hosting_password_" + ids, res.error);
                        } else {
                            handle_error("#hosting_password", res.error);
                        }
//                        disable_button("#" + btn);
                        return;
                    } else {
                        if (ids) {
                            remove_error("#hosting_password_" + ids);
                            handle_error("#show_password" + ids, res.password);
                        } else {
                            remove_error("#hosting_password");
                            handle_error("#show_password", res.password);
                        }
//                        disable_remove("#" + btn);
                        $('#myModal').modal('hide');
                        return;
                    }
                }
            });
        });
    });
</script>
