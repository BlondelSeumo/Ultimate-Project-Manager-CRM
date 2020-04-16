<?php

class Navigation_Model extends MY_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key;
    
    public function get_new_menuInfo() {
        $post = new stdClass();
        $post->label = '';
        $post->link = '';
        $post->icon = '';
        $post->sort = '';
        $post->parent = '';
        $post->id = '';
        return $post;
    }
    
}