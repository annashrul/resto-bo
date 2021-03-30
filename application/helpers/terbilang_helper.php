<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Terbilang Helper
 *
 * @package	CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author	Gede Lumbung
 * @link	http://gedelumbung.com
 */

if ( ! function_exists('number_to_words'))
{
    function number_to_words($number)
    {
        $before_comma = trim(to_word($number));
        /*$after_comma = trim(comma($number));*/
        return ucwords($results = $before_comma.' rupiah '/*.$after_comma*/);
    }
}

function to_word($number){
	$number = abs($number);
	$words = "";
	$arr_number = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");

	if($number<12){
		$words = " ".$arr_number[$number];
	} else if($number<20){
		$words = to_word($number-10)." belas";
	} else if($number<100){
		$words = to_word($number/10)." puluh ".to_word($number%10);
	} else if($number<200){
		$words = "seratus ".to_word($number-100);
	} else if($number<1000) {
		$words = to_word($number/100)." ratus ".to_word($number%100);
	} else if($number<2000){
		$words = "seribu ".to_word($number-1000);
	} else if($number<1000000){
		$words = to_word($number/1000)." ribu ".to_word($number%1000);
	} else if($number<1000000000){
		$words = to_word($number/1000000)." juta ".to_word($number%1000000);
	} else if($number <1000000000000) {
		$words = to_word($number/1000000000)." milyar ".to_word(fmod($number,1000000000));
	} else if($number <1000000000000000) {
		$words = to_word($number/1000000000000)." trilyun ".to_word(fmod($number,1000000000000));
	} else {
		$words = "undefined";
	}
	return $words;
}
	
function comma($number){
	$after_comma = stristr($number,'.');
	$arr_number = array("nol","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan");
	$results = "";
	$length = strlen($after_comma);
	$i = 1;
	while($i<$length)
	{
		if($i==1){ $results .= ' koma'; }
		$get = substr($after_comma,$i,1);
		$results .= " ".$arr_number[$get];
		$i++;
	}
	return $results;
}
	
function terbilang($x, $style=3){
	if($x<0){
		//$hasil = "minus " . trim($this->to_word($x) . $this->comma($x) . ' rupiah');
		$hasil = "minus " . trim($this->to_word($x) . $this->comma($x));
	} else {
		//$hasil = trim($this->to_word($x) . $this->comma($x) . ' rupiah');
		$hasil = trim($this->to_word($x) . $this->comma($x));
	}     
	switch ($style) {
		case 1: $hasil = strtoupper($hasil); break;
		case 2: $hasil = strtolower($hasil); break;
		case 3:	$hasil = ucwords($hasil); break;
		default: $hasil = ucfirst($hasil); break;
	}     
	return $hasil;
}
	
function to_say($number){
	return $this->convert_number_to_words($number);
}
	
function convert_number_to_words($number) {
	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
		0                   => 'zero',
		1                   => 'one',
		2                   => 'two',
		3                   => 'three',
		4                   => 'four',
		5                   => 'five',
		6                   => 'six',
		7                   => 'seven',
		8                   => 'eight',
		9                   => 'nine',
		10                  => 'ten',
		11                  => 'eleven',
		12                  => 'twelve',
		13                  => 'thirteen',
		14                  => 'fourteen',
		15                  => 'fifteen',
		16                  => 'sixteen',
		17                  => 'seventeen',
		18                  => 'eighteen',
		19                  => 'nineteen',
		20                  => 'twenty',
		30                  => 'thirty',
		40                  => 'fourty',
		50                  => 'fifty',
		60                  => 'sixty',
		70                  => 'seventy',
		80                  => 'eighty',
		90                  => 'ninety',
		100                 => 'hundred',
		1000                => 'thousand',
		1000000             => 'million',
		1000000000          => 'billion',
		1000000000000       => 'trillion',
		1000000000000000    => 'quadrillion',
		1000000000000000000 => 'quintillion'
	);
	
	if (!is_numeric($number)) {
		return false;
	}
	
	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
			'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . $this->convert_number_to_words(abs($number));
	}
	
	$string = $fraction = null;
	
	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}
	
	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . $this->convert_number_to_words($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= $this->convert_number_to_words($remainder);
			}
			break;
	}
	
	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}
	
	return $string;
}


