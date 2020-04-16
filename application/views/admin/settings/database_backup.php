<?php echo message_box('success') ?>
<?php echo message_box('error') ?>

<section class="panel panel-custom">
    <header class="panel-heading  "> <?= lang('database_backup') ?>
        <div class="pull-right">
            <a href="<?= base_url() ?>admin/settings/restore_database" class="btn btn-xs btn-primary"
               data-toggle="modal" data-placement="top" data-target="#myModal">
                <i class="fa fa-upload "></i> <?= lang('restore_database') ?>
            </a>
            <a href="<?= base_url() ?>admin/settings/db_backup" class="btn btn-xs btn-purple"><i
                    class="fa fa-download"></i> <?= lang('database_backup') ?></a>
        </div>
    </header>
    <div class="panel-body">
        <table id="backup" class="table" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th><?= lang('date'); ?></th>
                <th><?= lang('file_name'); ?></th>
                <th><?= lang('action'); ?></th>
            </tr>
            </thead>
            <?php
            if (isset($backups)) {
                if (!empty($backups)) {
                    arsort($backups);
                    foreach ($backups as $file):
                        $filename = explode("_", $file);
                        ?>
                        <tr>
                            <td><?php echo str_replace('.zip', ' &nbsp ', $filename[1]); ?><?php echo str_replace('.zip', ' &nbsp  ', $filename[2]); ?></td>
                            <td><?php echo str_replace('-', ' &nbsp ', $filename[0]); ?></td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" class="btn btn-purple btn-xs"
                                   href="<?= base_url() ?>admin/settings/download_backup/<?= $file ?>"
                                   title="<?= lang('download'); ?>"><i
                                        class="fa fa-download"></i></a>
                                <?= btn_delete('admin/settings/delete_backup/' . $file) ?>
                            </td>
                        </tr>

                    <?php endforeach;
                }
            } else { ?>
                <tr>
                    <td colspan="4"><?= $this->lang->line('application_no_backups'); ?></td>
                </tr>
            <?php } ?>
        </table>

    </div>
</section>