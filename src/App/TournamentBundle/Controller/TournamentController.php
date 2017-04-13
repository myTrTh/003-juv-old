<?php

namespace App\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\TournamentBundle\Entity\Tournamentusers;
use App\TournamentBundle\Form\PreliminaryType;
use Symfony\Component\HttpFoundation\Request;

class TournamentController extends Controller
{
    public function rulesAction() {

        $user = $this->getUser();
        if($user)
            $tz = $user->getTimezone();            
        else
        	$tz = 100;

        $repository = $this->getDoctrine()->getRepository('AppTournamentBundle:Content');
        $rules = $repository->get_content('rules');

        return $this->render('AppTournamentBundle:Pages:rules.html.twig', array('rules' => $rules));
    }        

    public function listAction($page) {

        $repository = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament");

        $result = $repository->show_tournaments_list($page);

        $tournaments = $result[0];
        $count = $result[1];

        return $this->render('AppTournamentBundle:Tournament:list.html.twig',
            array("tournaments" => $tournaments, "count" => $count,
                  "page" => $page));
    }

    public function showAction($id, $tour) {

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            $userId = 0;

        $tournament = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($id);

        if(empty($tournament)) {
            return $this->render('AppTournamentBundle:Tournament:noshow.html.twig',
                   array("tournament" => $tournament));
        }


        if($tournament->getStatus() == 3 or $tournament->getStatus() == 4) {

            $tournamentusers = new Tournamentusers();

            $form_preliminary = $this->createForm(PreliminaryType::class, $tournamentusers);

            $request = Request::createFromGlobals();

            $form_preliminary->handleRequest($request);

            if($form_preliminary->isSubmitted() && $form_preliminary->isValid()) {

                $tr = (int) $form_preliminary->getData()->getTournament();

                $repository = $this->getDoctrine()->getRepository("AppTournamentBundle:TOurnamentusers");

                $order = $repository->order_preliminary($id, $userId);

                if(!$order) {

                    $tournamentusers->setUser($userId);
                    $tournamentusers->setTournament($tr);
                    $tournamentusers->setStatus(2);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($tournamentusers);
                    $em->flush();

                    return $this->redirect($this->generateUrl('app_tournament_show', array('id'=> $id)));

                } else {

                   $session = $this->get('session');
                    $session
                        ->getFlashBag()
                        ->add('error', 'Вы уже заявлены на турнир.');

                }

            }

            $repository_users = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournamentusers");

            $result = $repository_users->show_users_for_tournament($id, $userId);

            $tournamentusers = $result[0];
            $access = $result[1];

            return $this->render('AppTournamentBundle:Tournament:preliminary.html.twig',
            array("tournament" => $tournament, 'form' => $form_preliminary->createView(),
                  "tournamentusers" => $tournamentusers, 'access' => $access));

        } else if ($tournament->getStatus() == 1) {

            if($tour == 0)
                $tour = 1;

            $info = $tournament->getInfo();
            $schema = $info['schema'];
            if(isset($info['playoff']))
                $playoff = $info['playoff'];
            else
                $playoff = 0;

            $rep_calendar = $this->getDoctrine()->getRepository("AppTournamentBundle:Calendar");
            $showtour = $rep_calendar->get_tour($id, $tour, $schema, $playoff);
            $calendar = $rep_calendar->get_calendar($id);

            return $this->render('AppTournamentBundle:Tournament:show.html.twig',
                   array("tournament" => $tournament,
                         "tour" => $tour,
                         "calendar" => $calendar,
                         "showtour" => $showtour));
        } else {

            return $this->render('AppTournamentBundle:Tournament:noshow.html.twig',
                   array("tournament" => $tournament));
        }
    }       
}
