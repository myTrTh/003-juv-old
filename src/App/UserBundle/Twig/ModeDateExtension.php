<?php

namespace App\UserBundle\Twig;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ModeDateExtension extends \Twig_Extension
{
    private $token_storage;

	public function __construct(TokenStorageInterface $token_storage) {

        $this->token_storage = $token_storage;

	}

	public function getName()
	{
		return 'mode_date';
	}

  public function getFunctions()
  {
      $function = function($message) {
          $result = $this->replace_date($message);
          return $result;
      };
      return array(
          new \Twig_SimpleFunction('replace_date', $function),
      );
  }


	public function replace_date($date){

		if(!is_object($date)) {

			# if no date format, return row value
			if(!strtotime($date))
				return $date;

			# get datetime object
			$date = new \DateTime($date);
		}

		# russians month name
		$month = ['','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];


        $token = $this->token_storage->getToken();
        $user = $token ? $token->getUser() : null;
        
        if($user && $user != 'anon.') {
        	$options = $user->getOptions();
        	$tz = $options['timezone'];
        } else {
        	$tz = 100;
        }

		# extract timezone
		if($tz == 100) {
			if(isset($_COOKIE['timezone']))
				$timezone = (int) $_COOKIE['timezone'];
			else
				$timezone = 0;
		} else {
			$timezone = $tz;
		}

		$now = new \DateTime();

		if($timezone <= 0)
			$point = "+".abs($timezone)." hours";
		else
			$point = "-".$timezone." hours";

		$date->modify($point);
		$now->modify($point);

			if(strtotime($now->format("d.m.Y")) == strtotime($date->format("d.m.Y")))
				return "Сегодня, в ".$date->format("H:i");
			if(strtotime($now->modify("-1 day")->format("d.m.Y")) == strtotime($date->format("d.m.Y")))
				return "Вчера, в ".$date->format("H:i");
			else {
				$d = $date->format("d");
				$m = $date->format("n");
				$y = $date->format("Y");

				$ny = $now->format("Y");
				if($y == $ny)
					$date = $d." ".$month[$m].", в ".$date->format("H:i");
				else
					$date = $d." ".$month[$m]." ".$y.", в ".$date->format("H:i");
				return $date;
			}
	}
}