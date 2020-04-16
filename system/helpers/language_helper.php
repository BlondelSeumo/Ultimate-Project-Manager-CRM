<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
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
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');

/**
 * CodeIgniter Language Helpers
 *
 * @package        CodeIgniter
 * @subpackage    Helpers
 * @category    Helpers
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/helpers/language_helper.html
 */

// ------------------------------------------------------------------------

if (!function_exists('lang')) {
    /**
     * Lang
     *
     * Fetches a language variable and optionally outputs a form label
     *
     * @param    string $line The language line
     * @param    string $for The "for" value (id of the form element)
     * @param    array $attributes Any additional HTML attributes
     * @return    string
     */
    function lang($line, $label = '', $attributes = array())
    {

        $CI = &get_instance();
        $CI->load->library('encoding_lib');
        if (is_array($label) && count($label) > 0) {
            $_line = vsprintf($CI->lang->line(trim($line), $attributes), $label);
        } else {
            $_line = @sprintf($CI->lang->line(trim($line), $attributes), $label);
        }
        if ($_line != '') {
            if (preg_match('/"/', $_line) && !is_html($_line)) {
                $_line = html_escape($_line);
            }
            return ForceUTF8\Encoding::toUTF8($_line);
        }

        if (mb_strpos($line, '_db_') !== false) {
            return 'db_translate_not_found';
        }

        return ForceUTF8\Encoding::toUTF8($line);
    }
}
