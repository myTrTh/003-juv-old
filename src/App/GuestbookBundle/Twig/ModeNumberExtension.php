<?php

namespace App\GuestbookBundle\Twig;

class ModeNumberExtension extends \Twig_Extension
{
	public function getName()
	{
		return 'mode_numbers';
	}

  public function getFunctions()
  {
      $function = function($number) {
          $result = $this->set_number($number);
          return $result;
      };
      return array(
          new \Twig_SimpleFunction('set_number', $function),
      );
  }


	public function set_number($number)
	{
    if($number < 10){
      $shirt = "<img class='number-player' src='/public/images/numbers/".$number.".png'>";
    } else {
      $first = substr($number, 0, 1);
      $shirt_first = "<img class='number-player' src='/public/images/numbers/".$first.".png'>";
      $second = substr($number, 1, 1);
      $shirt_second = "<img class='number-player' src='/public/images/numbers/".$second.".png'>";
      $shirt = $shirt_first.$shirt_second;
    }
		return $shirt; 
	}
}