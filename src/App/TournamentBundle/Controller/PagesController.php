<?php

namespace App\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller
{

    public function contentAction($content) {

        $repository = $this->getDoctrine()->getRepository('AppTournamentBundle:Content');
        $bodycontent = $repository->get_content($content);

        return $this->render('AppTournamentBundle:Pages:content.html.twig', array('content' => $bodycontent));
    }

    public function rankingAction() {
    	$ranking = $this->getDoctrine()->getRepository('AppTournamentBundle:Tablelist')->get_ranking();

    	$last_date = $this->getDoctrine()->getRepository('AppTournamentBundle:Tablelist')->get_updated();

		$pre = $this->getDoctrine()->getRepository('AppTournamentBundle:Tablelist')->get_last_tour();

    	return $this->render('AppTournamentBundle:Pages:ranking.html.twig', 
    		array('ranking' => $ranking, 'lastdate' => $last_date, 'pre' => $pre));
    }

    public function toolsAction($tools, $first, $second) {
    	return $this->render('AppTournamentBundle:Pages:tools.html.twig',
    		array('tools' => $tools));
    }
}
