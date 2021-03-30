<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'/libraries/PHPMailer/src/PHPMailer.php';
include_once APPPATH.'/libraries/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailer_Library
{
    public function __construct()
    {
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function load()
    {
        //require_once(APPPATH."third_party/phpmailer/PHPMailerAutoload.php");
		
        $objMail = new PHPMailer();
        return $objMail;
    }
}
