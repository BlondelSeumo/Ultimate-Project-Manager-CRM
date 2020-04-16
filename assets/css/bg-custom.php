<?php
$uri = $this->uri->segment(3);
if ($uri == 'theme') {
    $im = null;
} else {
    $im = '!important';
}
?>
<style type="text/css">
    /* ========================================================================
       Component: layout
     ========================================================================== */
    body,
    .wrapper > section {
        background-color: <?= config_item('body_background').$im?>;;
    }

    #loader-wrapper {
        background-color: <?= config_item('body_background').$im?>;;
    }

    .wrapper > .aside {
        background-color: #3a3f51;
    }

    /* ========================================================================
       Component: top-navbar
     ========================================================================== */
    .topnavbar {
        background-color: #fff;
    }

    .topnavbar .navbar-header {
        background: <?= config_item('navbar_logo_background').$im?>;
    }

    @media only screen and (min-width: 768px) {
        .topnavbar .navbar-header {
            background-image: none;
        }
    }

    .topnavbar .navbar-nav > li > a,
    .topnavbar .navbar-nav > .open > a {
        color: <?= config_item('top_bar_color').$im?>;
    }

    .topnavbar .navbar-nav > li > a:hover,
    .topnavbar .navbar-nav > .open > a:hover,
    .topnavbar .navbar-nav > li > a:focus,
    .topnavbar .navbar-nav > .open > a:focus {
        color: <?= config_item('top_bar_color').$im?>;
    }

    .topnavbar .navbar-nav > .active > a,
    .topnavbar .navbar-nav > .open > a,
    .topnavbar .navbar-nav > .active > a:hover,
    .topnavbar .navbar-nav > .open > a:hover,
    .topnavbar .navbar-nav > .active > a:focus,
    .topnavbar .navbar-nav > .open > a:focus {
        background-color: transparent;
    }

    .topnavbar .navbar-nav > li > [data-toggle='navbar-search'] {
        color: #ffffff;
    }

    .topnavbar .nav-wrapper {
        background: <?= config_item('top_bar_background').$im?>;
    }

    @media only screen and (min-width: 768px) {
        .topnavbar {
            background: <?= config_item('top_bar_background').$im?>;
        }

        .topnavbar .navbar-nav > .open > a,
        .topnavbar .navbar-nav > .open > a:hover,
        .topnavbar .navbar-nav > .open > a:focus {
            box-shadow: 0 -3px 0 rgba(255, 255, 255, 0.5) inset;
        }

        .topnavbar .navbar-nav > li > a,
        .topnavbar .navbar-nav > .open > a {
            color: <?= config_item('top_bar_color').$im?>;
        }

        .topnavbar .navbar-nav > li > a:hover,
        .topnavbar .navbar-nav > .open > a:hover,
        .topnavbar .navbar-nav > li > a:focus,
        .topnavbar .navbar-nav > .open > a:focus {
            color: <?= config_item('top_bar_color').$im?>;
        }
    }

    /* ========================================================================
       Component: sidebar
     ========================================================================== */
    .sidebar {
        background: <?= config_item('sidebar_background').$im?>;
    }

    .sidebar .nav-heading {
        color: <?= config_item('sidebar_color')?> !important;;
    }

    .sidebar .nav > li > a,
    .sidebar .nav > li > .nav-item, .user-block .user-block-info .user-block-name, .user-block .user-block-info .user-block-role {
        color: <?= config_item('sidebar_color').$im?>;
    }

    .sidebar .nav > li > a:focus,
    .sidebar .nav > li > .nav-item:focus,
    .sidebar .nav > li > a:hover,
    .sidebar .nav > li > .nav-item:hover {
        color: <?= config_item('sidebar_active_background').$im?>;
    }

    .sidebar .nav > li > a > em,
    .sidebar .nav > li > .nav-item > em {
        color: inherits;
    }

    .sidebar .nav > li.active,
    .sidebar .nav > li.open,
    .sidebar .nav > li.active > a,
    .sidebar .nav > li.open > a,
    .sidebar .nav > li.active .nav,
    .sidebar .nav > li.open .nav {
        background-color: <?= config_item('sidebar_active_background').$im?>;
        color: <?= config_item('sidebar_active_color').$im?>;
    }

    .sidebar .nav > li.active > a > em,
    .sidebar .nav > li.open > a > em {
        color: <?= config_item('sidebar_active_color').$im?>;
    }

    .sidebar .nav > li.active {
        border-left-color: <?= config_item('sidebar_active_color').$im?>;
    }

    .sidebar-subnav {
        background-color: <?= config_item('submenu_open_background').$im?>;
    }

    .sidebar-subnav > .sidebar-subnav-header {
        color: #e1e2e3;
    }

    .sidebar-subnav > li > a,
    .sidebar-subnav > li > .nav-item {
        color: #e1e2e3;
    }

    .sidebar-subnav > li > a:focus,
    .sidebar-subnav > li > .nav-item:focus,
    .sidebar-subnav > li > a:hover,
    .sidebar-subnav > li > .nav-item:hover {
        color: <?= config_item('sidebar_active_color').$im?>;
    }

    .sidebar-subnav > li.active > a,
    .sidebar-subnav > li.active > .nav-item {
        color: <?= config_item('sidebar_active_color').$im?>;
    }

    .sidebar-subnav > li.active > a:after,
    .sidebar-subnav > li.active > .nav-item:after {
        border-color: <?= config_item('sidebar_active_color').$im?>;
        /*background-color: #2b957a;*/
    }

    /* ========================================================================
       Component: offsidebar
     ========================================================================== */
    .offsidebar {
        border-left: 1px solid #cccccc;
        background-color: #ffffff;
        color: #515253;
    }

    .nav-pills > li.active > a,
    .nav-pills > li.active > a:hover,
    .nav-pills > li.active > a:focus, li.user-header {
        background-color: <?= config_item('active_background').$im?>;
        color: <?= config_item('active_color').$im?>;
    }

    .pinned li.nav-heading .badge {
        font-size: 11px;
        padding: 3px 6px;
        margin-top: 2px;
        border-radius: 10px;
        background-color: <?= config_item('active_background').$im?>;
    }

    .panel-custom .panel-heading {
        border-bottom: 2px solid <?= config_item('top_bar_background')?>;
    }

    .custom-bg, .fc-state-default {
        background: <?= config_item('top_bar_background')?>;
        color: <?= config_item('top_bar_color').$im?>;
    }

    .nav-tabs > li.active > a {
        border-top: 2px solid <?= config_item('top_bar_background')?>;;
    }

    .timeline-2, .timeline-2 .time-item:after, .time-item, .time-item:after {
        border-color: <?= config_item('top_bar_background')?>;;
    }

    .sub-active {
        border-left-color: <?= config_item('top_bar_background')?>;
    }


</style>