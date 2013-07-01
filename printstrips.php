<?php

/**
 * 
 * @version $Id$
 * @copyright 2004
 */
// get some bookkeeping out of the way

define('FPDF_FONTPATH', '/usr/local/share/fpdf/font/');
require "/usr/local/share/fpdf/fpdf.php";
require "cellz.php";

$color      = $_POST['color'];
$background = $_POST['background'];
$titlefont  = $_POST['titlefont'];
$titlesize  = $_POST['titlesize'];


$publisherfont  = "Helvetica";
$largefontsize  = "16";
$mediumfontsize = "12";
$smallfontsize  = "9";
// start building pdf
$pdf            = new ZPDF('P', 'pt', 'Letter');
$pdftitle       = 'Jukebox Title Strips';
$author         = 'Simple Stripper';

$pdf->SetTitle($pdftitle);
$pdf->SetAuthor($author);
$pdf->SetLeftMargin(18);
$pdf->SetTopMargin(18);
$pdf->AddPage();
switch ($titlesize) {
    case small:
        $fontsize = $smallfontsize;
        break;
    case medium:
        $fontsize = $mediumfontsize;
        break;
    case large:
        $fontsize = $largefontsize;
        break;
}
switch ($color) {
    case red:
        $stripr = 255;
        $stripg = 0;
        $stripb = 0;
        if ($background) {
            $stripbgr = 255;
            $stripbgg = 192;
            $stripbgb = 203;
        }
        break;
    case blue:
        $stripr = 0;
        $stripg = 0;
        $stripb = 255;
        if ($background) {
            $stripbgr = 173;
            $stripbgg = 216;
            $stripbgb = 230;
        }
        break;
    case green:
        $stripr = 0;
        $stripg = 190;
        $stripb = 0;
        if ($background) {
            $stripbgr = 144;
            $stripbgg = 238;
            $stripbgb = 144;
        }
        break;
    case purple:
        $stripr = 128;
        $stripg = 0;
        $stripb = 128;
        if ($background) {
            $stripbgr = 221;
            $stripbgg = 160;
            $stripbgb = 221;
        }
        break;
    default:
        $stripr = 0;
        $stripg = 0;
        $stripb = 255;
        if ($background) {
            $stripbgr = 0;
            $stripbgg = 0;
            $stripbgb = 127;
        }
} // switch
$pdf->SetTextColor(0);
if ($background) {
    $pdf->SetLineWidth(6);
    $pdf->SetDrawColor($stripbgr, $stripbgg, $stripbgb);
    $pdf->SetFillColor($stripbgr, $stripbgg, $stripbgb);
    $pdf->Rect(18, 18, 216, 720, FD);
    $pdf->Rect(306, 18, 216, 720, FD);
}
$pdf->SetDrawColor($stripr, $stripg, $stripb);
// Heavy lifting
// Do it twice for two columns
for ($horiz = 0; $horiz <= 1; $horiz++) {
    // and do it nine times for ten rows
    for ($vert = 0; $vert <= 9; $vert++) {
        $pdf->SetFont($titlefont, '', $fontsize);
        $pdf->SetLineWidth(6);
        // top and bottom lines
        $pdf->Line(18 + $horiz * 288, 18 + $vert * 72, 234 + $horiz * 288, 18 + $vert * 72);
        $pdf->Line(18 + $horiz * 288, 90 + $vert * 72, 234 + $horiz * 288, 90 + $vert * 72);
        // box for artist
        $pdf->SetLineWidth(1);
        $pdf->Rect(39 + $horiz * 288, 45 + $vert * 72, 168, 18);
        // bars on side of artist box
        $pdf->SetFillColor($stripr, $stripg, $stripb);
        $pdf->Rect(15 + $horiz * 288, 50 + $vert * 72, 24, 9, FD);
        $pdf->Rect(207 + $horiz * 288, 50 + $vert * 72, 30, 9, FD);
        // next line picks which record to print based on which cell we are working on
		// and gets the details from the post variables
        $recordtoprint = (($horiz * 10) + $vert + 1);
        $titlea[$recordtoprint]      = urldecode($_POST['titlea'][$recordtoprint]);
        $titleb[$recordtoprint]      = urldecode($_POST['titleb'][$recordtoprint]);
        $artista[$recordtoprint]     = urldecode($_POST['artista'][$recordtoprint]);
        $artistb[$recordtoprint]     = urldecode($_POST['artistb'][$recordtoprint]);
        $publisher[$recordtoprint]   = urldecode($_POST['publisher'][$recordtoprint]);
        $publisherid[$recordtoprint] = urldecode($_POST['publisherid'][$recordtoprint]);
        $titlea[$recordtoprint]      = stripslashes($titlea[$recordtoprint]);
        $titleb[$recordtoprint]      = stripslashes($titleb[$recordtoprint]);
        $publisherinfo               = "$publisher[$recordtoprint] $publisherid[$recordtoprint]";
        // combine artist a and b into one string if needed
        if ($artista[$recordtoprint] && $artistb[$recordtoprint]) {
            if ($artista[$recordtoprint] == $artistb[$recordtoprint]) {
                $combinedartist = ("$artista[$recordtoprint]");
            } else {
                $combinedartist = ("$artista[$recordtoprint]/$artistb[$recordtoprint]");
            }
        } elseif ($artista[$recordtoprint]) {
            $combinedartist = ("$artista[$recordtoprint]");
        } elseif ($artistb[$recordtoprint]) {
            $combinedartist = ("$artistb[$recordtoprint]");
        } else {
            $combinedartist = ("");
        }
        // next lines sets top left corner of box. Extra y offset to get off
        // of lines. line after that prints title of a side
        $pdf->SetXY(18 + $horiz * 288, 30 + $vert * 72);
        $pdf->CellZ(216, 0, $titlea[$recordtoprint], '', '', 'C');
        // set position and print title of b side
        $pdf->SetXY(18 + $horiz * 288, 73 + $vert * 72);
        $pdf->CellZ(216, 0, $titleb[$recordtoprint], '', '', 'C');
        $pdf->SetXY(39 + $horiz * 288, 54 + $vert * 72);
        $pdf->CellZ(168, 0, $combinedartist, '', '', 'C');
        $pdf->SetFont('Helvetica', '', 6);
        $pdf->SetXY(174 + $horiz * 288, 84 + $vert * 72);
        $pdf->CellZ(60, 0, $publisherinfo, '', '', 'L');
        
    }
    
}

// we're done. Send it out
$pdf->Output('titlestrips.pdf', I);
?>
