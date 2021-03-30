<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 include_once APPPATH.'/libraries/M_pdf/mpdf.php';

class M_report_p {

    public $pdf;

    public function __construct()
    {
        $this->pdf = new mPDF('utf-8', array(210,297), 0, '', 10, 10, 10, 10);
    }
}
