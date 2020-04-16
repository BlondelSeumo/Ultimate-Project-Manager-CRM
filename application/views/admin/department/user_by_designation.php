<?php
if (!empty($users_info)) {
    ?>
    <div class="panel panel-custom">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <h4 class="modal-title"
                id="myModalLabel"><?= lang('users') . ' ' . lang('list') . ' ' . lang('by') . ' ' . lang('designation') . ':' . $designation_info->designations ?></h4>
        </div>
        <div class="modal-body wrap-modal wrap">
            <div id="scroll-500">
                <table class="table">
                    <?php foreach ($users_info as $key => $v_user) {
                        ?>
                        <tr>
                            <td><?= $key + 1 . '. <a href="' . base_url('admin/user/user_details/' . $v_user->user_id) . '">' . fullname($v_user->user_id) ?>
                                </a></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        initScrollbar("#scroll-500", {setHeight: 500});
    });
</script>