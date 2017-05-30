<?php

namespace App\UserBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;

class admintextService {

	public function adminreplace_text($message){
		// bb tag for admin: rule and attention

		$patternB = "/\[h1\](.*?)\[\/h1\]/s";
		$message = preg_replace($patternB, "<span class='h2'>$1</span>", $message);
		
		$patternB = "/\[h2\](.*?)\[\/h2\]/s";
		$message = preg_replace($patternB, "<span class='h3'>$1</span>", $message);	

		$patternB = "/\[red\](.*?)\[\/red\]/s";
		$message = preg_replace($patternB, "<span class='red'>$1</span>", $message);

		$patternB = "/\[img\](.*?)\[\/img\]/s";
		$message = preg_replace($patternB, '<img class="content-img" src="$1">', $message);	

		return $message;
	}
}
