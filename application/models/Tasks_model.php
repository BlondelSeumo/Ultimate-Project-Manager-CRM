<?php

class Tasks_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function set_progress($id)
    {
        $project_info = $this->check_by(array('project_id' => $id), 'tbl_project');
        if ($project_info->calculate_progress != '0') {
            if ($project_info->calculate_progress == 'through_tasks') {
                $done_task = $this->db->where(array('project_id' => $id, 'task_status' => 'completed'))->get('tbl_task')->result();
                $total_tasks = $this->db->where(array('project_id' => $id))->get('tbl_task')->result();
                if (count($done_task) > 0) {
                    $done_task = count($done_task);
                } else {
                    $done_task = 0;
                }
                if (count($total_tasks) > 0) {
                    $total_tasks = count($total_tasks);
                } else {
                    $total_tasks = 0;
                }
                $progress = round(($done_task / $total_tasks) * 100);
                if ($progress > 100) {
                    $progress = 100;
                }
            }
        } else {
            $progress = $project_info->progress;
        }
        if (empty($progress)) {
            $progress = 0;
        } else if ($progress >= 100) {
            $progress = 100;
            $p_data['project_status'] = 'completed';
        }
        $p_data['progress'] = $progress;

        $this->_table_name = "tbl_project"; //table name
        $this->_primary_key = "project_id";
        $this->save($p_data, $id);
        return true;
    }

    function get_task_progress($id)
    {
        $project_info = $this->check_by(array('task_id' => $id), 'tbl_task');
        if ($project_info->task_status == 'completed') {
            $progress = 100;
        } else {
            if (!empty($project_info->calculate_progress) && $project_info->calculate_progress != '0') {
                if ($project_info->calculate_progress == 'through_sub_tasks') {
                    $estimate_hours = $project_info->task_hour;
                    $percentage = $this->get_estime_time($estimate_hours);
                    if ($percentage != 0) {
                        $task_time = $this->task_spent_time_by_id($id);
                        if ($percentage != 0) {
                            $progress = round(($task_time / $percentage) * 100);
                        }
                    }
                } else {
                    $done_task = $this->db->where(array('project_id' => $id, 'task_status' => 'completed'))->get('tbl_task')->result();
                    $total_tasks = $this->db->where(array('project_id' => $id))->get('tbl_task')->result();
                    if (count($done_task) > 0) {
                        $done_task = count($done_task);
                    } else {
                        $done_task = 0;
                    }
                    if (count($total_tasks) > 0) {
                        $total_tasks = count($total_tasks);
                    } else {
                        $total_tasks = 0;
                    }
                    if ($total_tasks != 0) {
                        $progress = round(($done_task / $total_tasks) * 100);
                    }
                }
            } else {
                $progress = $project_info->task_progress;
            }
            if (empty($progress)) {
                $progress = 0;
            } else {
                if ($progress > 100) {
                    $progress = 100;
                }
            }
        }

        return $progress;
    }

    function set_task_progress($id)
    {
        $project_info = $this->check_by(array('task_id' => $id), 'tbl_task');

        if (!empty($project_info->calculate_progress) && $project_info->calculate_progress != '0') {
            if ($project_info->calculate_progress == 'through_tasks_hours') {
                $task_hour = $project_info->task_hour;
                $percentage = $this->get_estime_time($task_hour);
                $task_time = $this->task_spent_time_by_id($id);
                if ($percentage != 0) {
                    $progress = round(($task_time / $percentage) * 100);
                }
            } else {
                $done_task = $this->db->where(array('project_id' => $id, 'task_status' => 'completed'))->get('tbl_task')->result();
                $total_tasks = $this->db->where(array('project_id' => $id))->get('tbl_task')->result();
                if (count($done_task) > 0) {
                    $done_task = count($done_task);
                } else {
                    $done_task = 0;
                }
                if (count($total_tasks) > 0) {
                    $total_tasks = count($total_tasks);
                } else {
                    $total_tasks = 0;
                }
                if (empty($total_tasks) || empty($done_task)) {
                    $progress = 0;
                } else {
                    $progress = round(($done_task / $total_tasks) * 100);
                }

                if ($progress > 100) {
                    $progress = 100;
                }
            }
        } else {
            $progress = $project_info->task_progress;
        }
        if (empty($progress)) {
            $progress = 0;
        } else if ($progress >= 100) {
            $progress = 100;
            $t_data['task_status'] = 'completed';
        }
        $t_data['task_progress'] = $progress;

        $this->_table_name = "tbl_task"; //table name
        $this->_primary_key = "task_id";
        $this->save($t_data, $id);
    }


    public function get_statuses()
    {
        $statuses = array(
            array(
                'id' => 1,
                'value' => 'not_started',
                'name' => lang('not_started'),
                'order' => 1,
            ),
            array(
                'id' => 2,
                'value' => 'in_progress',
                'name' => lang('in_progress'),
                'order' => 2,
            ),
            array(
                'id' => 3,
                'value' => 'completed',
                'name' => lang('completed'),
                'order' => 3,
            ),
            array(
                'id' => 4,
                'value' => 'deferred',
                'name' => lang('deferred'),
                'order' => 4,
            ),
            array(
                'id' => 5,
                'value' => 'waiting_for_someone',
                'name' => lang('waiting_for_someone'),
                'order' => 5,
            )
        );
        return $statuses;
    }

    public function get_tasks($filterBy)
    {
        $tasks = array();
        $all_tasks = array_reverse($this->get_permission('tbl_task'));
        if (empty($filterBy)) {
            return $all_tasks;
        } else {
            foreach ($all_tasks as $v_tasks) {
                if ($v_tasks->task_status == $filterBy) {
                    array_push($tasks, $v_tasks);
                }
            }
        }
        return $tasks;
    }
}
