<?php

namespace App\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\GuestbookBundle\Controller\GuestbookController;
use App\AdminBundle\Entity\Adminbook;
use App\AdminBundle\Form\AdminbookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminbookController extends GuestbookController
{

    protected $bundle;
    protected $entity;
    protected $class;
    protected $form;
    protected $url;
    protected $posts;

    public function __construct() {
        $this->bundle = 'AppAdminBundle:Adminbook';
        $this->entity = new Adminbook();
        $this->class = 'Adminbook';
        $this->form = AdminbookType::class;
        $this->url = 'app_admin_guestbook';
        $this->posts = 20;
    }

}