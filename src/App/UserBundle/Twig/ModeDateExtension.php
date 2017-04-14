<?php

namespace App\UserBundle\Twig;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\UserBundle\Services\dateService;

class ModeDateExtension extends \Twig_Extension
{
	protected $service;

	public function __construct(TokenStorageInterface $token_storage, dateService $service){
		$this->service = $service;
        $this->token_storage = $token_storage;		
	}

	public function getFunctions() {
		return array(
			'replace_date' => new \Twig_Function_Method($this, 'replace_date'),
			'utc_to_locale' => new \Twig_Function_Method($this, 'utc_to_locale'),
			'locale_to_utc' => new \Twig_Function_Method($this, 'locale_to_utc'),
		);
	}

	public function replace_date($date) {
		return $this->service->replace_date($date);
	}

	public function utc_to_locale($date) {
		return $this->service->utc_to_locale($date);
	}

	public function locale_to_utc($date) {
		return $this->service->locale_to_utc($date);
	}		
}