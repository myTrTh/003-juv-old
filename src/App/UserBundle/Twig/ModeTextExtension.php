<?php

namespace App\UserBundle\Twig;
use App\UserBundle\Twig\ModeDateExtension;
use App\UserBundle\Services\textService;

class ModeTextExtension extends \Twig_Extension
{

	protected $mode_date;
	protected $service;

	public function __construct(ModeDateExtension $mode_date, textService $service) {
		$this->mode_date = $mode_date;
		$this->service = $service;
	}

	public function getFunctions() {
		return array(
			'replace_text' => new \Twig_Function_Method($this, 'replace_text')
		);
	}		
	
	public function replace_text($message) {
		return $this->service->replace_text($message);
	}

}