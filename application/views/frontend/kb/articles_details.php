<div class="row">
    <div class="col-sm-3">
        <?php $this->load->view("frontend/kb/search_box"); ?>
        <div class="mt">
            <ul class="nav nav-pills nav-stacked">
                <?php
                if (!empty($all_kb_category)) {
                    foreach ($all_kb_category as $kb_category) {
                        if (!empty($articles_info->kb_category_id)) {
                            $category_id = $articles_info->kb_category_id;
                        } else {
                            $category_id = $this->uri->segment(4);
                        }
                        $total_kb = count($this->db->where(array('kb_category_id' => $kb_category->kb_category_id, 'status' => 1, 'for_all' => 'No'))->get('tbl_knowledgebase')->result());
                        ?>
                        <li class="<?= !empty($category_id) && $category_id == $kb_category->kb_category_id ? 'sub-active' : '' ?>">
                            <a href="<?= base_url('frontend/kb_details/kb_category/' . $kb_category->kb_category_id) ?>">
                                <?= $kb_category->category ?> <span
                                        class="label label-primary pull-right mt-sm"><?= (!empty($total_kb) && $total_kb != 0 ? $total_kb : '') ?></span>
                            </a>
                        </li>
                    <?php }
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-sm-9 ">
        <div class="panel panel-custom" style="border: none;" data-collapsed="0">
            <div class="panel-heading m0" style="border-bottom-width: 1px ">
                <div class="panel-title">
                    <?php
                    $category = $this->uri->segment(3);
                    if ($category == 'kb_category') {
                        $category_info = get_row('tbl_kb_category', array('kb_category_id' => $category_id, 'status' => 1));
                        echo $category_info->category;
                        echo '<small class="block" style="font-size: 12px">' . $category_info->description . '</small>';
                    } else {
                        if (!empty($articles_info)) {
                            echo $articles_info->title;
                        }
                    } ?>
                </div>
            </div>
            <!-- Table -->
            <div class="p">
                <?php
                if (!empty($articles_by_category)) {
                    foreach ($articles_by_category as $key => $by_category) {
                        ?>
                        <div class="panel-group" style="margin:8px 0px;" role="tablist"
                             aria-multiselectable="true">
                            <div class="box box-info" style="border-radius: 0px ">
                                <div class="p pb-sm" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion"
                                           href="#<?php echo $key ?>" aria-expanded="true"
                                           aria-controls="collapseOne">
                                            <strong
                                                    class="text-alpha-inverse"><i
                                                        class="fa fa-hand-o-right"></i> <?php echo $by_category->title; ?>
                                            </strong>
                                        </a>
                                    </h4>
                                </div>
                                <div id="<?php echo $key ?>" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="headingOne">
                                    <div class="content p">
                                        <?php
                                        $attachments = null;
                                        $uploaded_file = json_decode($by_category->attachments);
                                        if (!empty($uploaded_file)) {
                                            $attachments = true;
                                        }
                                        echo read_more($by_category->description, 500, 'frontend/kb_details/articles/' . $by_category->kb_id, $attachments);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else {
                    if (!empty($articles_info)) {
                        echo html_entity_decode($articles_info->description);
                        $uploaded_file = json_decode($articles_info->attachments);
                        if (!empty($uploaded_file)):
                            ?>
                            <hr/>
                            <ul class="mailbox-attachments clearfix mt">
                                <?php
                                foreach ($uploaded_file as $v_files):
                                    if (!empty($v_files)):
                                        ?>
                                        <li>
                                            <?php if ($v_files->is_image == 1) : ?>
                                                <span class="mailbox-attachment-icon has-img"><img
                                                            src="<?= base_url() . $v_files->path ?>"
                                                            alt="Attachment"></span>
                                            <?php else : ?>
                                                <span class="mailbox-attachment-icon"><i
                                                            class="fa fa-file-pdf-o"></i></span>
                                            <?php endif; ?>
                                            <div class="mailbox-attachment-info">
                                                <a href="<?= base_url() ?>frontend/kb_download/<?= $articles_info->kb_id . '/' . $v_files->fileName ?>"
                                                   class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                                    <?= $v_files->fileName ?></a>
                                                <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> <?= lang('kb') ?>
                            <a href="<?= base_url() ?>frontend/kb_download/<?= $articles_info->kb_id . '/' . $v_files->fileName ?>"
                               class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                                            </div>
                                        </li>
                                    <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        <?php endif;
                    }
                } ?>
            </div>
        </div>
    </div>
</div>
