<?php 
require_once('src/phpmailer.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = new PHPMailer();
$email->From      = 'haris@indokids.co.id';
$email->FromName  = 'Indokids';
$email->Subject   = 'Message Subject';
$email->Body      = 'deskripsi message';
$email->AddAddress( 'adehasbiasidik@gmail.com' );

//$file_to_attach = 'PATH_OF_YOUR_FILE_HERE';

//$email->AddAttachment( $file_to_attach , 'NameOfFile.pdf' );

return $email->Send();

?>
