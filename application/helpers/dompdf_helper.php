<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dompdf_helper
 *
 * @author Parimal
 */
require_once("dompdf/autoload.inc.php");
use Dompdf\Dompdf;
use Dompdf\Options;

function pdf_create($html, $filename = '', $stream = TRUE, $set_paper = '', $attach = null, $folder_name = null)
{

    $options = new Options();
    $options->set('defaultFont', 'dejavusanscondensed');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);

    if ($set_paper != '') {
        $dompdf->setPaper(array(0, 0, 900, 841), 'portrait');
    } else {
        $dompdf->setPaper("a4", "portrait");
    }

    $dompdf->render();

    if ($stream) {
        $pdf_string = $dompdf->output();
        if (!empty($attach)) {
            if (!empty($folder_name)) {
                $folder = "uploads/" . $folder_name . '/' . $filename . ".pdf";;
            } else {
                $folder = "uploads/" . $filename . ".pdf";;
            }
            file_put_contents($folder, $pdf_string);
        } else {
            $dompdf->stream($filename . ".pdf");
        }
    } else {
        return $dompdf->output();
    }
}

//require_once("dompdf/autoload.inc.php");
//use Dompdf\Dompdf;
//use Dompdf\Options;
//
//function pdf_create($html, $filename = '', $stream = TRUE, $set_paper = '', $attach = null, $folder_name = null)
//{
//
//    $options = new Options();
//    $options->set('defaultFont', 'dejavusanscondensed');
//    $dompdf = new Dompdf($options);
//    $dompdf->loadHtml($html,'UTF-8');
//
//    if ($set_paper != '') {
//        $dompdf->setPaper(array(0, 0, 900, 841), 'portrait');
//    } else {
//        $dompdf->setPaper("a4", "landscape");
//    }
//    $dompdf->render();
//    if ($stream) {
//        $pdf_string = $dompdf->output();
//        if (!empty($attach)) {
//            if (!empty($folder_name)) {
//                $folder = "uploads/" . $folder_name . '/' . $filename . ".pdf";;
//            } else {
//                $folder = "uploads/" . $filename . ".pdf";;
//            }
//            file_put_contents($folder, $pdf_string);
//        } else {
//            $dompdf->stream($filename . ".pdf");
//        }
//    } else {
//        return $dompdf->output();
//    }
//}
