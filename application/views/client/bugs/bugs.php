<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<style>

</style>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"
                                                                   data-toggle="tab"><?= lang('all_bugs') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"
                                                                   data-toggle="tab"><?= lang('new_bugs') ?></a></li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                        <div class="box-body">
                            <!-- Table -->
                            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('issue_#') ?></th>
                                    <th><?= lang('bug_title') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <th><?= lang('priority') ?></th>
                                    <?php $show_custom_fields = custom_form_table(6, null);
                                    if (!empty($show_custom_fields)) {
                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                            if (!empty($c_label)) {
                                                ?>
                                                <th><?= $c_label ?> </th>
                                            <?php }
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        list = base_url + "client/bugs/bugsList";
                                        $('.filtered > .dropdown-toggle').on('click', function () {
                                            if ($('.group').css('display') == 'block') {
                                                $('.group').css('display', 'none');
                                            } else {
                                                $('.group').css('display', 'block')
                                            }
                                        });
                                        $('.filter_by').on('click', function () {
                                            $('.filter_by').removeClass('active');
                                            $('.group').css('display', 'block');
                                            $(this).addClass('active');
                                            var filter_by = $(this).attr('id');
                                            if (filter_by) {
                                                filter_by = filter_by;
                                            } else {
                                                filter_by = '';
                                            }
                                            table_url(base_url + "client/bugs/bugsList/" + filter_by);
                                        });
                                    });
                                </script>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Stock Category tab Starts -->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                        <div class="panel-body">
                            <form data-parsley-validate="" novalidate=""
                                  action="<?php echo base_url() ?>client/bugs/save_bug/<?php if (!empty($bug_info->bug_id)) echo $bug_info->bug_id; ?>"
                                  method="post" class="form-horizontal">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('issue_#') ?><span
                                            class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" style="width:260px" value="<?php
                                        $this->load->helper('string');
                                        if (!empty($bug_info)) {
                                            echo $bug_info->issue_no;
                                        } else {
                                            echo strtoupper(random_string('alnum', 7));
                                        }
                                        ?>" name="issue_no">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('bug_title') ?><span
                                            class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="bug_title" required class="form-control"
                                               value="<?php if (!empty($bug_info->bug_title)) echo $bug_info->bug_title; ?>"/>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="field-1"
                                           class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('project') ?>
                                        <span
                                            class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <select name="project_id" style="width: 100%" class="select_box"
                                                required>
                                            <?php
                                            $client_id = $this->session->userdata('client_id');
                                            $all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();
                                            if (!empty($all_project)) {
                                                foreach ($all_project as $v_project) {
                                                    ?>
                                                    <option value="<?= $v_project->project_id ?>" <?php
                                                    if (!empty($bug_info->project_id)) {
                                                        echo $v_project->project_id == $bug_info->project_id ? 'selected' : '';
                                                    }
                                                    ?>><?= $v_project->project_name ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('priority') ?> <span
                                            class="text-danger">*</span> </label>
                                    <div class="col-lg-5">
                                        <div class=" ">
                                            <select name="priority" class="form-control">
                                                <?php
                                                $priorities = $this->db->get('tbl_priority')->result();
                                                if (!empty($priorities)) {
                                                    foreach ($priorities as $v_priorities):
                                                        ?>
                                                        <option value="<?= $v_priorities->priority ?>" <?php
                                                        if (!empty($bug_info) && $bug_info->priority == $bug_info->priority) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?= ($v_priorities->priority) ?></option>
                                                        <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('description') ?> <span
                                            class="required">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control " name="bug_description" id="ck_editor"
                                                  required><?php if (!empty($bug_info->bug_description)) echo $bug_info->bug_description; ?></textarea>
                                        <?php echo display_ckeditor($editor['ckeditor']); ?>
                                    </div>
                                </div>
                                <?php
                                if (!empty($bug_info)) {
                                    $bug_id = $bug_info->bug_id;
                                } else {
                                    $bug_id = null;
                                }
                                ?>
                                <?= custom_form_Fields(6, $bug_id); ?>
                                <div class="">
                                    <div class="col-sm-offset-3 col-sm-5">
                                        <button type="submit" id="sbtn"
                                                class="btn btn-primary"><?= lang('save') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

