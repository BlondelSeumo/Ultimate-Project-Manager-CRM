<!-- Modal -->
<script src="<?php echo base_url() ?>assets/plugins/parsleyjs/parsley.min.js"></script>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('hide.bs.modal', '#myModal', function () {
        $('#myModal').removeData('bs.modal');
        location.reload();
    });
</script>