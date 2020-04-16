<?php
defined('BASEPATH') || exit('No direct script access allowed');
$config['encrypto'] = 'ssl';
$config['validate'] = true;
$config['host'] = 'mail.codexcube.com';
$config['port'] = 993;
$config['username'] = 'support@codexcube.com';
$config['password'] = 'Ab!Q@W#E$R56';
$config['folders'] = [
    'inbox' => 'INBOX',
    'sent' => 'Sent',
    'trash' => 'Trash',
    'spam' => 'Spam',
    'drafts' => 'Drafts',
];
$config['expunge_on_disconnect'] = false;
$config['cache'] = [
    'active' => false,
    'adapter' => 'file',
    'backup' => 'file',
    'key_prefix' => 'imap:',
    'ttl' => 60,
];