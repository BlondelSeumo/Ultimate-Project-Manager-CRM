<div class="search-box">
    <h2 class="text-center"><?= lang('how_can_we_help') ?></h2>
    <?php $this->load->view("frontend/kb/search_box"); ?>
</div>

<?php
if (!empty($all_kb_category)) {
    foreach ($all_kb_category as $kb_category) {
        $total_kb = count($this->db->where(array('kb_category_id' => $kb_category->kb_category_id, 'status' => 1, 'for_all' => 'No'))->get('tbl_knowledgebase')->result());
        ?>
        <div class="col-lg-4 kb">
            <a href="<?= base_url('frontend/kb_details/kb_category/' . $kb_category->kb_category_id) ?>"
               style="text-decoration: none;color: inherit"><!-- START widget-->
                <div class="panel widget">
                    <div class="row row-table row-flush">
                        <div class="panel-body text-center">
                            <h4><?= $kb_category->category ?></h4>
                            <p><?= $kb_category->description ?>
                            </p>
                            <p>
                                <strong> <?= !empty($total_kb) ? $total_kb : lang('no');
                                    echo ' ' . lang('articles') ?> </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </a><!-- END widget-->
        </div>
    <?php }
} ?>
