<?php

namespace App\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller
{

    public function rulesAction() {

        $repository = $this->getDoctrine()->getRepository('AppTournamentBundle:Content');
        $rules = $repository->get_content('rules');

        return $this->render('AppTournamentBundle:Pages:rules.html.twig', array('rules' => $rules));
    }    
}
