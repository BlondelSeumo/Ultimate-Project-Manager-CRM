<?php

class Bugs_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function get_time_spent_result($seconds) {
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        if ($minutes >= 60) {
            return round($hours, 2) . ' ' . lang('hours');
        } elseif ($seconds >= 60) {
            return round($minutes, 2) . ' ' . lang('minutes');
        } else {
            return $seconds . ' ' . lang('seconds');
        }
    }
    public function get_statuses()
    {
        $statuses = array(
            array(
                'id' => 1,
                'value' => 'unconfirmed',
                'name' => lang('unconfirmed'),
                'order' => 1,
            ),
            array(
                'id' => 2,
                'value' => 'confirmed',
                'name' => lang('confirmed'),
                'order' => 2,
            ),
            array(
                'id' => 3,
                'value' => 'in_progress',
                'name' => lang('in_progress'),
                'order' => 3,
            ),
            array(
                'id' => 4,
                'value' => 'resolved',
                'name' => lang('resolved'),
                'order' => 4,
            ),
            array(
                'id' => 5,
                'value' => 'verified',
                'name' => lang('verified'),
                'order' => 5,
            )
        );
        return $statuses;
    }

    public function get_bugs($filterBy)
    {
        $bugs = array();
        $all_bugs = array_reverse($this->get_permission('tbl_bug'));
        if (empty($filterBy)) {
            return $all_bugs;
        } else {
            foreach ($all_bugs as $v_bugs) {
                if ($v_bugs->bug_status == $filterBy) {
                    array_push($bugs, $v_bugs);
                }
            }
        }
        return $bugs;
    }

}
