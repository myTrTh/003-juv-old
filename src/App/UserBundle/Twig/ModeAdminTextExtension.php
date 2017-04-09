<?php

namespace App\UserBundle\Twig;

class ModeAdminTextExtension extends \Twig_Extension
{
	public function getName()
	{
		return 'mode_admintext';
	}

  public function getFunctions()
  {
      $function = function($message) {
          $result = $this->adminreplace_text($message);
          return $result;
      };
      return array(
          new \Twig_SimpleFunction('adminreplace_text', $function),
      );
  }


	public function adminreplace_text($message){
		// bb tag for admin: rule and attention
		$patternB = "/\[h1\](.*?)\[\/h1\]/s";
		$message = preg_replace($patternB, "<span class='h2'>$1</span>", $message);
		
		$patternB = "/\[h2\](.*?)\[\/h2\]/s";
		$message = preg_replace($patternB, "<span class='h3'>$1</span>", $message);	

		$patternB = "/\[red\](.*?)\[\/red\]/s";
		$message = preg_replace($patternB, "<span class='red'>$1</span>", $message);				

		return $message;
	}
}