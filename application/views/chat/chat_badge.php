<div class="chat_badge custom-bg" onclick="open_chat_box(<?php echo $chats->private_chat_id ?>)"
     id="open_chat_box_<?php echo $chats->private_chat_id ?>">
    <?php if ($chats->unread) : ?><span
        class="badge-chat small-text"><?php echo lang("new") ?></span><?php endif; ?><span
        class="chat_title"><?php echo $chats->title ?></span>
</div>