<?php

namespace App\UserBundle\Twig;
use App\UserBundle\Services\admintextService;

class ModeAdminTextExtension extends \Twig_Extension
{
	protected $service;

	public function __construct(admintextService $service){
		$this->service = $service;
	}	

    public function getName()
    {
        return 'ModeAdminTextExtension';
    }

	// public function getFunctions() {
	// 	return array(
	// 		'adminreplace_text' => new \Twig_Function_Method($this, 'adminreplace_text')
	// 	);
	// }

	public function getFunctions() {
		return array(
            new \Twig_SimpleFunction('adminreplace_text', array($this, 'adminreplace_text'))
		);
	}

	public function adminreplace_text($message) {
		return $this->service->adminreplace_text($message);
	}
}