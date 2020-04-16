<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['email_must_be_array']='Phương thức xác nhận email phải được thông qua 1 mảng';
$lang['email_invalid_address']='Địa chỉ email không hợp lệ: %s';
$lang['email_attachment_missing']='Không thể định vị  được email  đính kèm: %s';
$lang['email_attachment_unreadable']='Không thể mở tệp định kèm này: %s';
$lang['email_no_from']='Không thể gửi mail không có tiêu đề :"From"';
$lang['email_no_recipients']='Bạn phải bao gồm người nhận :To, Cc, or Bcc';
$lang['email_send_failure_phpmail']='Không thể gửi mail sử dụng PHP mail(). Máy chủ của bạn không thể sử dụng phương pháp này để gửi';
$lang['email_send_failure_sendmail']='Không thể gửi mail sử dụng PHP Sendmail. Máy chủ của bạn không thể sử dụng phương pháp này để gửi';
$lang['email_send_failure_smtp']='Không thể gửi mail sử dụng PHP SMTP. Máy chủ của bạn không thể sử dụng phương pháp này để gửi';
$lang['email_sent']='Tin nhắn của bạn đã được gửi thành công sử dụng giao thức sau: %s';
$lang['email_no_socket']='Không thể mở socket to Sendmail.Vui lòng kiểm tra cài đặt';
$lang['email_no_hostname']='Bạn đã không ghi rõ tên chủ SMTP';
$lang['email_smtp_error']='Lỗi gặp lỗi SMTP sau: %s';
$lang['email_no_smtp_unpw']='Lỗi: Bạn phải nhập tên người dùng và mật khẩu SMTP';
$lang['email_failed_smtp_login']='Không thể gửi lệnh AUTH LOG. Lỗi: %s';
$lang['email_smtp_auth_un']='Không thể xác nhận tên người dùng.Lỗi: %s';
$lang['email_smtp_auth_pw']='Không thể xác nhận mật khẩu. Lỗi: %s';
$lang['email_smtp_data_failure']='Không thể gửi dữ liệu: %s';
$lang['email_exit_status']='Mã trạng thái tồn tại: %s';
