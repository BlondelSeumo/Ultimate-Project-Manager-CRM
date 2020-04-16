<a href="#" data-toggle="dropdown">
    <em class="icon-bell"></em>
    <?php
    if ($unread_notifications > 0) { ?>
        <div class="label label-danger unraed-total icon-notifications"><?php echo $unread_notifications; ?></div>
    <?php } ?>
</a>
<!-- START Dropdown menu-->
<ul class="dropdown-menu animated zoomIn notifications-list" data-total-unread="<?php echo $unread_notifications; ?>"
    style="width: 350px">
    <li class="text-sm text-right" style="border-bottom: 1px solid rgb(238, 238, 238)">
        <a href="#" class="list-group-item"
           onclick="mark_all_as_read(); return false;"><?php echo lang('mark_all_as_read'); ?></a>
    </li>
    <?php
    $user_notifications = $this->global_model->get_user_notifications(false);
    if (!empty($user_notifications)) {
        foreach ($user_notifications as $notification) { ?>
            <li class="notification-li"
                data-notification-id="<?php echo $notification->notifications_id; ?>">
                <!-- list item-->
                <!-- list item-->
                <?php if (!empty($notification->link)) {
                    $link = base_url() . $notification->link;
                } else {
                    $link = '#';
                }
                ?>
                <a href="<?php echo base_url() . $notification->link; ?>"
                   class="n-top n-link list-group-item <?php if ($notification->read_inline == 0) {
                       echo ' unread';
                   } ?>">
                    <div class="n-box media-box ">
                        <div class="pull-left">
                            <?php
                            if ($notification->from_user_id != 0) {
                                $img = base_url() . staffImage($notification->from_user_id);
                            } else {
                                $img = 'https://raw.githubusercontent.com/encharm/Font-Awesome-SVG-PNG/master/black/png/128/' . $notification->icon . '.png';
                            } ?>
                            <img src="<?= $img ?>" alt="Avatar"
                                 width="40"
                                 height="40" class="img-thumbnail img-circle n-image">
                        </div>
                        <div class="media-box-body clearfix">
                            <?php
                            $description = lang($notification->description, $notification->value);
                            if ($notification->from_user_id != 0) {
                                $description = fullname($notification->from_user_id) . ' - ' . $description;
                            }
                            echo '<span class="n-title text-sm block">' . $description . '</span>'; ?>
                            <small class="text-muted pull-left" style="margin-top: -4px"><i
                                    class="fa fa-clock-o"></i> <?php echo time_ago($notification->date); ?></small>
                            <?php if ($notification->read_inline == 0) { ?>
                                <span class="text-muted pull-right mark-as-read-inline"
                                      onclick="read_inline(<?php echo $notification->notifications_id; ?>);"
                                      data-placement="top"
                                      data-toggle="tooltip" data-title="<?php echo lang('mark_as_read'); ?>">
                                    <small><i class="fa fa-circle-thin"></i></small>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            </li>
        <?php }
    }
    ?>
    <li class="text-center">
        <?php if (count($user_notifications) > 0) { ?>
            <a href="<?php echo base_url(); ?>admin/user/user_details/<?= $this->session->userdata('user_id') ?>/notifications"><?php echo lang('view_all_notifications'); ?></a>
        <?php } else { ?>
            <?php echo lang('no_notification'); ?>
        <?php } ?>
    </li>
    <!-- END list group-->
</ul>
<!-- END Dropdown menu-->
