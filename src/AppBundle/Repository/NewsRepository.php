<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NewsRepository

 * repository methods below.
 */
class NewsRepository extends EntityRepository
{


    public function downloadPdf($news_details, $pdf, $docRoot, $imagePath) {

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($news_details->getUser()->getEmail());
        $pdf->SetTitle($news_details->getTitle());
        $pdf->SetSubject('test');
        $pdf->SetKeywords('News, PDF');

// set default header data
        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, $news_details->getTitle(),  $news_details->getUser()->getFirstName().' '.$news_details->getUser()->getLastName(), array(0,64,255), array(0,64,128));
//        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, 'test','test', array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------

// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

// set text shadow effect
        $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
        $image_url = '';
        if(!empty($news_details->getImageName())) {
            $image_url = '<img src="'.$docRoot.$imagePath.$news_details->getImageName().'"> <br><br>';
        }

        $description = $news_details->getDescription();
        $html = <<<EOD
           $image_url
          $description
EOD;
// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $name = str_replace(' ','_',$news_details->getTitle());
        $name = trim($name,"_");
        $pdf->Output($name.'.pdf', 'I');
    }


}
