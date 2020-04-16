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

$lang['db_invalid_connection_str'] = 'Không thể xác định cài đặt cơ sở dữ liệu dựa trên kết nối mà bạn đánh giá';
$lang['db_unable_to_connect'] = 'Không thể kết nối với máy chủ cơ sở dữ liệu sử dụng cài đặt được cung cấp';
$lang['db_unable_to_select'] = 'Không thể chọn một cở sở dũ liệu rõ ràng: %s';
$lang['db_unable_to_create'] = 'Không thể tạo một cơ sở dữ liệu rõ ràng: %s';
$lang['db_invalid_query'] = 'Câu  hỏi bạn đưa ra không hợp lí';
$lang['db_must_set_table'] = 'Bạn phải cài bảng cơ sở dữ liệu đã được dùng với câu hỏi';
$lang['db_must_use_set'] = 'Bạn phải dùng phương thức "set" để cập nhật lối vào';
$lang['db_must_use_index'] = 'Bạn phải chỉ định một danh mục để khớp với cập nhật hàng loạt';
$lang['db_batch_missing_index'] = 'Một hoặc nhiều hàng được gửi đến cập nhật hàng loạt thì bị thiếu chỉ mục chỉ định';
$lang['db_must_use_where'] = 'Bản cập nhật thì không được cho phép chỉ khi có chứa mệnh đề "where"';
$lang['db_del_must_use_where'] = 'Việc xóa thì không được cho phép chỉ khi có chứa một mệnh đề "where" hoặc mệnh đề "like"';
$lang['db_field_param_missing'] = 'Tìm nạp các trường để yêu cầu tên bảng như là một tham số';
$lang['db_unsupported_function'] = 'Tính năng này thì không có sẵn cho cơ sở dữ liệu mà bạn sử dụng';
$lang['db_transaction_failure'] = 'Giao dịch thất bại :Quay lại trình chiếu';
$lang['db_unable_to_drop'] = 'Không thể bỏ vào cơ sở dữ liệu rõ ràng';
$lang['db_unsupported_feature'] = 'Không hỗ trợ tính năng của nền tảng cơ sở dữ liệu bạn đang sử dụng';
$lang['db_unsupported_compression'] = 'Định dạng file nén bạn chọn thì không được hỗ trợ bởi máy chủ của bạn';
$lang['db_filepath_error'] = 'Không thể nhập dữ liệu đến trường mà bạn đã đưa ra';
$lang['db_invalid_cache_path'] = 'Tiền bộ nhớ mà bạn đưa ra thì không hợp lệ hoặc không thể nhập được';
$lang['db_table_name_required'] = 'Yêu cầu tên bảng cho thao tác này';
$lang['db_column_name_required'] = 'Yêu cầu tên cột cho thao tác này';
$lang['db_column_definition_required'] = 'Yêu cầu định dạng cột cho thao tác này';
$lang['db_unable_to_set_charset'] = 'Không thể cài bộ ký tự kết nối khách hàng: %s';
$lang['db_error_heading'] = 'Đã xảy ra một lỗi cơ sở dữ liệu';
