<link rel="stylesheet" type="text/css"
      href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo site_url('assets/plugins/elFinder/css/elfinder.min.css'); ?>">
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo site_url('assets/plugins/elFinder/themes/Material/css/theme.css'); ?>">
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo site_url('assets/plugins/elFinder/themes/Material/css/theme-light.css'); ?>">

<script src="<?php echo site_url('assets/plugins/elFinder/js/elfinder.min.js'); ?>"></script>
<?php
$languages = $this->db->where('name', config_item('default_language'))->get('tbl_languages')->row();
?>
<script type="text/javascript" charset="utf-8">
    $().ready(function () {
        window.setTimeout(function () {
            var locale = "<?= $languages->code;?>";
            var _locale = locale;
            if (locale == 'pt') {
                _locale = 'pt_BR';
            }
            var elf = $('#elfinder').elfinder({
                // lang: 'ru',             // language (OPTIONAL)
                url: '<?= site_url()?>admin/filemanager/elfinder_init',  // connector URL (REQUIRED)
                lang: _locale,
                height: 700,
                uiOptions: {
                    toolbar: [
                        ['back', 'forward'],
//                     ['mkdir'],
                        ['mkdir', 'mkfile', 'upload'],
                        ['open', 'download', 'getfile'],
                        ['info'],
                        ['quicklook'],
                        ['copy', 'cut', 'paste'],
                        ['rm'],
                        ['duplicate', 'rename', 'edit', 'resize'],
                        ['extract', 'archive'],
                        ['search'],
                        ['view'],
                    ],
                }

            }).elfinder('instance');
        }, 200);
    });
</script>

<!-- Element where elFinder will be created (REQUIRED) -->
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('filemanager') ?></div>
    </div>
    <div class="">
        <div id="elfinder"></div>
    </div>

</div>
