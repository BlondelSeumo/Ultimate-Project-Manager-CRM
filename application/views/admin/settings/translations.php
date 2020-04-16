<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<?php if (!empty($active_language)) : ?>

    <div class="panel panel-custom">
        <header class="panel-heading  "><?= lang('translations') ?></header>
        <div class="row">
            <div class="panel-body">
                <form action="<?php echo base_url() ?>admin/settings/add_language" method="post" class="form-inline">
                    <div class="pull-right" style="margin-right: 5px;">
                        <select class="form-control select_box" name="language">
                            <?php if (!empty($availabe_language)): foreach ($availabe_language as $v_availabe_language) : ?>
                                <option
                                    value="<?= str_replace(" ", "_", $v_availabe_language->language) ?>"><?= ucwords($v_availabe_language->language) ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button type="submit" id="add-translation"
                                class="btn btn-dark"><?= lang('add_translation') ?></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="datatable_action" class="table table-striped">
                    <thead>
                    <tr>
                        <th class="col-xs-1  "><?= lang('icon') ?></th>
                        <th class="col-xs-2"><?= lang('language') ?></th>
                        <th class="col-xs-4"><?= lang('progress') ?></th>
                        <th class="col-xs-1"><?= lang('done') ?></th>
                        <th class="col-xs-1"><?= lang('total') ?></th>
                        <th class="col-options   col-xs-2"><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($active_language)):
                        foreach ($active_language as $v_language) :
                            $st = $translation_stats;
                            $total_data = $st[$v_language->name]['total'];
                            $translated_data = $st[$v_language->name]['translated'];

                            $view_status = intval(($translated_data / $total_data) * 1000) / 10;
                            ?>
                            <tr>
                                <td class=""><img src="<?= base_url('asset/images/flags/' . $v_language->icon) ?>.gif"/>
                                </td>
                                <td class=""><a
                                        href="<?= base_url() ?>admin/settings/translations/<?= $v_language->name ?>"><?= ucwords(str_replace("_", " ", $v_language->name)) ?></a>
                                </td>
                                <td>
                                    <div class="progress">
                                        <?php
                                        $status = 'danger';
                                        if ($view_status > 20) {
                                            $status = 'warning';
                                        }
                                        if ($view_status > 50) {
                                            $status = 'primary';
                                        }
                                        if ($view_status > 80) {
                                            $status = 'success';
                                        }
                                        ?>
                                        <div class="progress-bar progress-bar-<?= $status ?>" role="progressbar"
                                             aria-valuenow="<?= $view_status ?>" aria-valuemin="0" aria-valuemax="100"
                                             style="width: <?= $view_status ?>%;">
                                            <?= $view_status ?>%
                                        </div>
                                    </div>
                                </td>
                                <td class=""><?= $translated_data ?></td>
                                <td class=""><?= $total_data ?></td>
                                <?php
                                if ($v_language->active == 1) {
                                    $status = 1;
                                } else {
                                    $status = 0;
                                }
                                ?>
                                <td class="">
                                    <a data-toggle="tooltip"
                                       title="<?= ($v_language->active == 1 ? lang('deactivate') : lang('activate')) ?>"
                                       class="active-translation btn btn-xs btn-<?= ($v_language->active == 0 ? 'default' : 'success') ?>"
                                       href="<?= base_url() ?>admin/settings/translations_status/<?= $v_language->name ?>/<?= ($v_language->active == 1 ? 0 : 1) ?>"><i
                                            class="fa fa-check"></i></a>
                                    <a data-toggle="tooltip" title="<?= lang('edit') ?>" class="btn btn-xs btn-primary"
                                       href="<?= base_url() ?>admin/settings/translations/<?= $v_language->name ?>"><i
                                            class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif (!empty($language_files)) : ?>
    <section class="panel panel-custom">
        <header class="panel-heading font-bold"><i class="fa fa-cogs"></i><?= lang('translations') ?>
            - <?= ucwords($language) ?></header>
        <div class="table-responsive">
            <table id="table-translations-files" class="table table-striped b-t b-light AppendDataTables">
                <thead>
                <tr>
                    <th class="col-xs-2 no-sort"><?= lang('type') ?></th>
                    <th class="col-xs-3"><?= lang('file') ?></th>
                    <th class="col-xs-4"><?= lang('progress') ?></th>
                    <th class="col-xs-1"><?= lang('done') ?></th>
                    <th class="col-xs-1"><?= lang('total') ?></th>
                    <th class="col-options no-sort col-xs-1"><?= lang('options') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($language_files as $file => $altpath) :
                    $shortfile = str_replace("_lang.php", "", $file);
                    $st = $translation_stats[$language]['files'][$shortfile];
                    $fn = ucwords(str_replace("_", " ", $shortfile));
                    $total = $st['total'];
                    $translated = $st['translated'];
                    $pc = intval(($translated / $total) * 1000) / 10;
                    ?>
                    <tr>
                        <td class=""><?= ($altpath == './system/' ? 'System' : 'Application') ?></td>
                        <td class=""><a
                                href="<?= base_url() ?>admin/settings/edit_translations/<?= $language ?>/<?= $shortfile ?>"><?= $fn ?></a>
                        </td>
                        <td>
                            <div class="progress">
                                <?php $bar = 'danger';
                                if ($pc > 20) {
                                    $bar = 'warning';
                                }
                                if ($pc > 50) {
                                    $bar = 'info';
                                }
                                if ($pc > 80) {
                                    $bar = 'success';
                                } ?>
                                <div class="progress-bar progress-bar-<?= $bar ?>" role="progressbar"
                                     aria-valuenow="<?= $pc ?>" aria-valuemin="0" aria-valuemax="100"
                                     style="width: <?= $pc ?>%;">
                                    <?= $pc ?>%
                                </div>
                            </div>
                        </td>
                        <td class=""><?= $translated ?></td>
                        <td class=""><?= $total ?></td>
                        <td class="">
                            <a class="btn btn-xs btn-default"
                               href="<?= base_url() ?>admin/settings/edit_translations/<?= $language ?>/<?= $shortfile ?>"><i
                                    class="fa fa-edit"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php elseif (!empty($current_languages)) : ?>
    <?php $attributes = array('class' => 'bs-example form-horizontal', 'id' => 'form-strings');
    echo form_open_multipart('admin/settings/set_translations/' . $current_languages . '/' . $active_language_files, $attributes); ?>
    <input type="hidden" name="_language" value="<?= $current_languages ?>">
    <input type="hidden" name="_file" value="<?= $active_language_files ?>">

    <section class="panel panel-custom">
        <header class="panel-heading font-bold"><i class="fa fa-cogs"></i>
            <?php
            $fn = ucwords(str_replace("_", " ", $active_language_files));
            $total = count($english);
            $translated = 0;
            if ($language == 'english') {
                $percent = 100;
            } else {
                foreach ($english as $key => $value) {
                    if (isset($translation[$key]) && $translation[$key] != $value) {
                        $translated++;
                    }
                }
                $percent = intval(($translated / $total) * 100);
            }
            ?>
            <?= lang('translations') ?> | <a style="color: red"
                                             href="<?= base_url() ?>admin/settings/translations/<?= $current_languages ?>"><?= ucwords(str_replace("_", " ", $current_languages)) ?></a>
            | <?= $percent ?>% <?= mb_strtolower(lang('done')) ?>
            <button type="submit" id="save-translation"
                    class="btn btn-xs btn-primary pull-right"><?= lang('save_translation') ?></button>
        </header>
        <div class="table-responsive">
            <table id="table-strings" class="table table-striped b-t b-light AppendDataTables">
                <thead>
                <tr>
                    <th class="col-xs-5">English</th>
                    <th class="col-xs-7"><?= ucwords(str_replace("_", " ", $language)) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($english as $key => $value) : ?>
                    <tr>
                        <td><?= $value ?></td>
                        <td><input class="form-control" width="100%" type="text"
                                   value="<?= (isset($translation[$key]) ? $translation[$key] : $value) ?>"
                                   name="<?= $key ?>"/></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- End details -->
    </section>
    </form>
<?php endif; ?>
<script type="text/javascript">

    $('#save-translation').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: base_url + 'admin/settings/set_translations/',
            type: 'POST',
            data: {json: JSON.stringify($('#form-strings').serializeArray())},
            success: function () {
                toastr.success("Translation Updated Successfully", "Response Status");
//                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + JSON.stringify(xhr));
            }
        });
    });
    $(document).ready(function () {
        $('#Transation_DataTables').dataTable({
            paging: false
        });
    });
</script>