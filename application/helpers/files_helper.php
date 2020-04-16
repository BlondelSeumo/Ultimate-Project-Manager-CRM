<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function access($attr, $path, $data, $volume)
{
    return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
        ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
        : null;                                    // else elFinder decide it itself
}
/**
 * Copy directory and all contents
 * @since  Version 1.0.2
 * @param  string $source string
 * @param  string $dest destionation
 * @param  integer $permissions folder permissions
 * @return boolean
 */
function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }
    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }
    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }
    // Clean up
    $dir->close();
    return true;
}

/**
 * Delete directory
 * @param  string $dirPath dir
 * @return boolean
 */
function delete_dir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }
    return false;
}

/**
 * Is file image
 * @param  string $path file path
 * @return boolean
 */
function is_image($path)
{
    $image = @getimagesize($path);
    $image_type = $image[2];
    if (in_array($image_type, array(
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_BMP
    ))) {
        return true;
    }
    return false;
}

/**
 * Get file extension by filename
 * @param  string $file_name file name
 * @return mixed
 */
function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

/**
 * Unique filename based on folder
 * @since  Version 1.0.1
 * @param  string $dir directory to compare
 * @param  string $filename filename
 * @return string           the unique filename
 */
function unique_filename($dir, $filename)
{
    // Separate the filename into a name and extension.
    $info = pathinfo($filename);
    $ext = !empty($info['extension']) ? '.' . $info['extension'] : '';
    $filename = sanitize_file_name($filename);
    $number = '';
    $new_number = '';
    // Change '.ext' to lower case.
    if ($ext && strtolower($ext) != $ext) {
        $ext2 = strtolower($ext);
        $filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);
        // Check for both lower and upper case extension or image sub-sizes may be overwritten.
        while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
            $filename = str_replace(array(
                "-$number$ext",
                "$number$ext"
            ), "-$new_number$ext", $filename);
            $filename2 = str_replace(array(
                "-$number$ext2",
                "$number$ext2"
            ), "-$new_number$ext2", $filename2);
            $number = $new_number;
        }
        return $filename2;
    }
    while (file_exists($dir . "/$filename")) {
        if ('' == "$number$ext") {
            $filename = "$filename-" . ++$number;
        } else {
            $filename = str_replace(array(
                "-$number$ext",
                "$number$ext"
            ), "-" . ++$number . $ext, $filename);
        }
    }
    return $filename;
}

/**
 * Sanitize file name
 * @param  string $filename filename
 * @return mixed
 */
function sanitize_file_name($filename)
{
    $special_chars = array(
        "?",
        "[",
        "]",
        "/",
        "\\",
        "=",
        "<",
        ">",
        ":",
        ";",
        ",",
        "'",
        "\"",
        "&",
        "$",
        "#",
        "*",
        "(",
        ")",
        "|",
        "~",
        "`",
        "!",
        "{",
        "}",
        "%",
        "+",
        chr(0)
    );
    $filename = str_replace($special_chars, '', $filename);
    $filename = str_replace(array(
        '%20',
        '+'
    ), '-', $filename);
    $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);
    // Return if only one extension
    if (count($parts) <= 2) {
        return $filename;
    }
    // Process multiple extensions
    $filename = array_shift($parts);
    $extension = array_pop($parts);

    $filename .= '.' . $extension;
    $CI = &get_instance();
    $filename = $CI->security->sanitize_filename($filename);
    return $filename;
}

/**
 * Get mime class by mime - admin system function
 * @param  string $mime file mime type
 * @return string
 */
function get_mime_class($mime)
{
    if (empty($mime) || is_null($mime)) {
        return 'mime mime-file';
    }
    $_temp_mime = explode('/', $mime);
    $part1 = $_temp_mime[0];
    $part2 = $_temp_mime[1];
    // Image
    if ($part1 == 'image') {
        if (strpos($part2, 'photoshop') !== false) {
            return 'mime mime-photoshop';
        };
        return 'mime mime-image';
    } // Audio
    else if ($part1 == 'audio') {
        return 'mime mime-audio';
    } // Video
    else if ($part1 == 'video') {
        return 'mime mime-video';
    } // Text
    else if ($part1 == 'text') {
        return 'mime mime-file';
    } // Applications
    else if ($part1 == 'application') {
        // Pdf
        if ($part2 == 'pdf') {
            return 'mime mime-pdf';
        } // Ilustrator
        else if ($part2 == 'illustrator') {
            return 'mime mime-illustrator';
        } // Zip
        else if ($part2 == 'zip' || $part2 == 'gzip' || strpos($part2, 'tar') !== false || strpos($part2, 'compressed') !== false) {
            return 'mime mime-zip';
        } // PowerPoint
        else if (strpos($part2, 'powerpoint') !== false || strpos($part2, 'presentation') !== false) {
            return 'mime mime-powerpoint ';
        } // Excel
        else if (strpos($part2, 'excel') !== false || strpos($part2, 'sheet') !== false) {
            return 'mime mime-excel';
        } // Word
        else if ($part2 == 'msword' || $part2 == 'rtf' || strpos($part2, 'document') !== false) {
            return 'mime mime-word';
        } // Else
        else {
            return 'mime mime-file';
        }
    } // Else
    else {
        return 'mime mime-file';
    }
}

/**
 * Convert bytes of files to readable seize
 * @param  string $path file path
 * @param  string $filesize file path
 * @return mixed
 */
function bytesToSize($path, $filesize = '')
{

    if (!is_numeric($filesize)) {
        $bytes = sprintf('%u', filesize($path));
    } else {
        $bytes = $filesize;
    }
    if ($bytes > 0) {
        $unit = intval(log($bytes, 1024));
        $units = array(
            'B',
            'KB',
            'MB',
            'GB'
        );
        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }
    return $bytes;
}

/**
 * List folder on a specific path
 * @param  stirng $path
 * @return array
 */
function list_folders($path)
{
    $folders = array();
    foreach (new DirectoryIterator($path) as $file) {
        if ($file->isDot())
            continue;
        if ($file->isDir()) {
            array_push($folders, $file->getFilename());
        }
    }
    return $folders;
}

/**
 * List files in a specific folder
 * @param  string $dir directory to list files
 * @return array
 */
function list_files($dir)
{
    $ignored = array(
        '.',
        '..',
        '.svn',
        '.htaccess',
        'index.html'
    );
    $files = array();
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored))
            continue;
        $files[$file] = filectime($dir . '/' . $file);
    }
    arsort($files);
    $files = array_keys($files);
    return ($files) ? $files : array();
}

// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $max_size = parse_upload_size(ini_get('post_max_size'));

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_upload_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function parse_upload_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}
