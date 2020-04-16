<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
           font-family: Verdana, Geneva, sans-serif;
           font-size:14px;
       }
       .wrapper {
        margin:0 auto;
        display:block;
        background:#f0f0f0;
        width:600px;
        border:1px solid #e4e4e4;
        padding:20px;
        border-radius:4px;
        margin-top:50px;
        text-align:center;
    }
    .wrapper h1 {
        text-align:center;
        font-size:24px;
        color:red;
        margin-top:0px;
    }
    .wrapper .upgrade_now {
        text-transform:uppercase;
        background:#82b440;
        color:#fff;
        padding: 15px 25px;
        border-radius:3px;
        text-decoration:none;
        text-align:center;
        border:0px;
        outline:0px;
        cursor:pointer;
        font-size: 15px;
    }
    .wrapper .upgrade_now:hover,.wrapper .upgrade_now:active {
        background:#73a92d;
    }
    .upgrade_now_wrapper {
        margin:0 auto;
        width:100%;
        text-align:center;
        margin-top:40px;
        margin-bottom:40px;
    }
    .note {
        color:#636363;
    }
    .bold {
        font-weight:bold;
    }
</style>
</head>
<body>
    <div class="wrapper">
     <h1>
        Database upgrade is required.
    </h1>
    <p>You need to perform an database upgrade before proceed using Ultimate Project Manager CRM PRO. Your files version is <?php echo wordwrap($this->config->item('migration_version'),1,'.',true); ?> and database version is <?php echo wordwrap($this->_current_version,1,'.',true); ?></p>
    <p class="bold">Please make sure that you have backup of your database before perform an upgrade.</p>
    <div class="upgrade_now_wrapper">
        <form action="<?php echo $this->config->site_url($this->uri->uri_string()); ?>" method="post" accept-charset="utf-8">
            <button type="submit" class="upgrade_now" value="true" name="upgrade_database">Upgrade now</button>
            <?php echo form_close(); ?>
        </div>
        <small class="note">This message may shown if you uploaded files from newer version downloaded from CodeCanyon to your existing installation.</small>
    </div>
</body>
</html>
