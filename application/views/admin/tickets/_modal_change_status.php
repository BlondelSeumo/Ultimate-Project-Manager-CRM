<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('comments') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <form role="form"
              action="<?php echo base_url(); ?>admin/tickets/index/changed_ticket_status/<?= $id ?>/<?= $status ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group">
                <div class="col-sm-12">
                    <textarea type="text" name="body" class="form-control textarea-md"></textarea>
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
    $('.textarea-md').summernote({
        height: 90,
        codemirror: {// codemirror options
            theme: 'monokai'
        }
    });
    $('.note-insert,.note-toolbar .note-fontsize,.note-toolbar .note-help,.note-toolbar .note-fontname,.note-toolbar .note-height,.note-toolbar .note-table').remove();
</script>