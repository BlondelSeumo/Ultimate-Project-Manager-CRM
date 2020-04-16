<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><strong><?= lang('delete') ?> <?= $client_info->name ?> </strong>

        </div>
    </div>
    <div class="panel-body ">
        <form role="form" enctype="multipart/form-data" id="form"
              action="<?php echo base_url(); ?>admin/client/delete_client/<?= $client_info->client_id ?>/yes"
              method="post"
              class="form-horizontal  ">
            <p style="font-weight: bold; color: Red"><?= lang('delete_note_1') . ' <span style="color:#000"> ' . $client_info->name . ' </span> ' . lang('delete_note_2') ?></p>
            <p>
                <strong> <?= lang('delete_note_3') . ' <span class="text-danger">' . $client_info->name . '</span> ' . lang('delete_note_4') ?></strong>
            </p>

            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('primary_contact') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('all_users') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('messages') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('mailbox') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('expense') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('payments') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('quotations') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('tickets') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('project') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('milestones') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('invoices') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('estimates') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('bugs') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('leads') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('call') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('mettings') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('comment') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . lang('files') . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class=""> </label>
                <div class="col-md-4 ml-lg">
                    <button type="submit" name="submit" value="1"
                            class="btn-block btn btn-danger"><?= lang('proceed_anyway') ?></button>
                </div>
                <div class="col-md-1">
                    <a href="<?= base_url() ?>admin/client/manage_client"
                       class="btn btn-primary"><?= lang('cancel') ?></a>
                </div>
            </div>
        </form>

    </div>
</div>