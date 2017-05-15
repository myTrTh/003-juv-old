<?php

namespace App\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function toolsAction($first, $second, Request $request) {

    	$users = $this->getDoctrine()->getRepository('AppUserBundle:User')->show_users();

        if($request->request->get('show_games')) {
	       	if(empty($request->request->get('user1')) || empty($request->request->get('user2'))) {
	           $session = $this->get('session');
	            $session
	                ->getFlashBag()
	                ->add('error', 'Выберите пользователей.');

	           	return $this->redirect($this->generateUrl('app_tournament_toolgames'));
	       	}

        	$user1 = $request->request->get('user1');
        	$user2 = $request->request->get('user2');

        	if($user1 == $user2) {
               $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Выберите разных пользователей.');

            	return $this->redirect($this->generateUrl('app_tournament_toolgames'));

            }

			return $this->redirect($this->generateUrl('app_tournament_toolgames', array('first' => $user1, 'second' => $user2)));
		}

		if($first != 0 and $second != 0)
        	$games = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_games_tools($first, $second);
        else
        	$games = [];

            $playoff_name = ['1' => 'ФИНАЛ', '2' => '1/2 ФИНАЛА', '4' => '1/4 ФИНАЛА', '8' => '1/8 ФИНАЛА',
                             '16' => '1/16 ФИНАЛА', '32' => '1/32 ФИНАЛА', '64' => '1/64 ФИНАЛA',
                             '128' => '1/128 ФИНАЛA', '256' => '1/256 ФИНАЛA'];

        $us1 = $this->getDoctrine()->getRepository('AppUserBundle:User')->get_user_info($first);
        $us2 = $this->getDoctrine()->getRepository('AppUserBundle:User')->get_user_info($second);

    	return $this->render('AppTournamentBundle:Pages:games.html.twig',
    		array('users' => $users, 'games' => $games, 'first' => $first, 'second' => $second, 'playoff' => $playoff_name, 'us1' => $us1, 'us2' => $us2));
    }
}
