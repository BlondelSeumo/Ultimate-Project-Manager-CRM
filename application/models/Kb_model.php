<?php

/**
 * Description of Knowledge base model
 *
 * @author NaYeM
 */
class Kb_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_kb_info($type = null, $id = null, $frontend = null)
    {
        $this->db->select('tbl_knowledgebase.*', FALSE);
        $this->db->select('tbl_kb_category.kb_category_id', FALSE);
        $this->db->from('tbl_knowledgebase');
        $this->db->join('tbl_kb_category', 'tbl_kb_category.kb_category_id = tbl_knowledgebase.kb_category_id', 'left');
        $this->db->where('tbl_kb_category.status', 1);
        $this->db->where('tbl_knowledgebase.status', 1);
        if (!empty($frontend)) {
            $this->db->where('tbl_knowledgebase.for_all', 'No');
        }
        if (!empty($type) && $type == 'articles') {
            $this->db->where('tbl_knowledgebase.kb_id', $id);
        } elseif (!empty($type) && $type == 'category') {
            $this->db->where('tbl_kb_category.kb_category_id', $id);
        }
        $query_result = $this->db->get();
        if (!empty($type) && $type == 'articles') {
            $result = $query_result->row();
        } else {
            $result = $query_result->result();
        }
        return $result;
    }

    function increase_total_view($id)
    {
        $tbl_knowledgebase = $this->db->dbprefix('tbl_knowledgebase');

        $sql = "UPDATE $tbl_knowledgebase
        SET total_view = total_view+1
        WHERE $tbl_knowledgebase.kb_id=$id";

        return $this->db->query($sql);
    }

    function get_suggestions($search, $frontend = null)
    {
        $tbl_knowledgebase = $this->db->dbprefix('tbl_knowledgebase');
        $tbl_kb_category = $this->db->dbprefix('tbl_kb_category');

        if (!empty($frontend)) {
            $sql = "SELECT $tbl_knowledgebase.kb_id, $tbl_knowledgebase.title
        FROM $tbl_knowledgebase
        LEFT JOIN $tbl_kb_category ON $tbl_kb_category.kb_category_id=$tbl_knowledgebase.kb_category_id
        WHERE $tbl_knowledgebase.for_all='No' AND $tbl_knowledgebase.status='1' AND $tbl_kb_category.status='1'
            AND $tbl_knowledgebase.title LIKE '%$search%'
        ORDER BY $tbl_knowledgebase.title ASC
        LIMIT 0, 10";
        } else {
            $sql = "SELECT $tbl_knowledgebase.kb_id, $tbl_knowledgebase.title
        FROM $tbl_knowledgebase
        LEFT JOIN $tbl_kb_category ON $tbl_kb_category.kb_category_id=$tbl_knowledgebase.kb_category_id
        WHERE $tbl_knowledgebase.status='1' AND $tbl_kb_category.status='1'
            AND $tbl_knowledgebase.title LIKE '%$search%'
        ORDER BY $tbl_knowledgebase.title ASC
        LIMIT 0, 10";
        }
        $result = $this->db->query($sql)->result();

        $result_array = array();
        foreach ($result as $value) {
            $result_array[] = array("value" => $value->kb_id, "label" => $value->title);
        }

        return $result_array;
    }
}
