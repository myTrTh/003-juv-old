<?php

namespace App\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\TournamentBundle\Entity\Tournamentusers;
use App\TournamentBundle\Entity\Usercast;
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

            $members = [];            
            for($i=0;$i<count($showtour);$i++) {
                $members[] = $showtour[$i]['uid1'];
                $members[] = $showtour[$i]['uid2'];
            }

            if(in_array($userId, $members))
                $member = 1;
            else
                $member = 0;

            $tourstatus = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->get_tour_status($tournament, $tour);

            return $this->render('AppTournamentBundle:Tournament:show.html.twig',
                   array("tournament" => $tournament,
                         "tour" => $tour,
                         "calendar" => $calendar,
                         "showtour" => $showtour,
                         "member" => $member,
                         "tourstatus" => $tourstatus));
        } else {

            return $this->render('AppTournamentBundle:Tournament:noshow.html.twig',
                   array("tournament" => $tournament));
        }
    }

    public function forecastAction(Request $request) {

        $tr = $request->request->get('tr');
        $tour = $request->request->get('tour');

        $user = $this->getUser();
        if($user)
            $userId = $user->getId();        
        else
            throw $this->createAccessDeniedException();

        $how = 0;
        // add tours
        if($request->isMethod("POST")) {
            if($request->request->has('set_score')) {

                $tr = $request->request->get('htr');
                $tour = $request->request->get('htour');

                $idfore = $request->request->get('id');
                $r1 = $request->request->get('r1');
                $r2 = $request->request->get('r2');

                $em = $this->getDoctrine()->getManager();

                for($i=0;$i<count($idfore);$i++) {
                    $idfore[$i] = (int) $idfore[$i];
                    $r1[$i] = (int) $r1[$i];
                    $r2[$i] = (int) $r2[$i];

                    $cast = $this->getDoctrine()->getRepository('AppTournamentBundle:Usercast')->check_score($idfore[$i], $tr, $tour, $userId);

                    if($cast) {

                        $usercast = $this->getDoctrine()->getRepository('AppTournamentBundle:Usercast')->findOneBy(array('idfore' => $idfore[$i], 'user' => $userId, 'tr' => $tr, 'tour' => $tour));

                        $forecastinfo = $this->getDoctrine()->getRepository('AppTournamentBundle:Forecast')->get_cast_info($idfore[$i]);

                        $now = new \DateTime();
                        $timer = $forecastinfo[0]['timer'];
                        if(strtotime($now->format('d.m.Y H:i')) < strtotime($timer->format('d.m.Y H:i'))) {

                            if($usercast) {
                                $res1 = $usercast->getResult1();
                                $res2 = $usercast->getResult2();
                            } else {
                                $res1 = "";
                                $res2 = "";
                            }

                            if(($r1[$i] != $res1 or $r2[$i] != $res2) and ($r1[$i] != "" and $r2[$i] != "")) {

                                $usercast->setIdfore($idfore[$i]);
                                $usercast->setUser($userId);
                                $usercast->setTr($tr);
                                $usercast->setTour($tour);
                                $usercast->setResult1($r1[$i]);
                                $usercast->setResult2($r2[$i]);

                                $em->persist($usercast);
                                $how += 1;

                            }
                        }

                    } else {

                        if($r1[$i] != "" and $r2[$i] != "") {
                        
                            $usercast = new Usercast();
                            $usercast->setIdfore($idfore[$i]);
                            $usercast->setUser($userId);
                            $usercast->setTr($tr);
                            $usercast->setTour($tour);
                            $usercast->setResult1($r1[$i]);
                            $usercast->setResult2($r2[$i]);

                            $em->persist($usercast);
                            $how += 1;
                        }

                    }   
                }
                if($how > 0)
                    $em->flush();
            }
        }        


        if(empty($tr))
            throw $this->createAccessDeniedException();

        $tournament = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tr);

        $result = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournamentusers")->show_users_for_tournament($tr, $userId);

        $access = $result[1];

        $tourstatus = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->get_tour_status($tr, $tour);

        if($access != 1 and $tourstatus != 1)
            throw $this->createAccessDeniedException();

        $forebridge = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->getForeBridge($tr, $tour);
        if($forebridge) {
            $fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast")->get_forecast($forebridge);

            $preset = $this->getDoctrine()->getRepository("AppTournamentBundle:Usercast")->get_prescore($userId, $fore);
        } else {
            $fore = 0;
            $preset = 0;
        }

        return $this->render('AppTournamentBundle:Tournament:forecast.html.twig',
            array('tr' => $tr, 'tour' => $tour, 'tournament' => $tournament, 'forecast' => $fore, 'preset' => $preset, 'how' => $how));
    }

    public function showgameAction($id) {

        $calendar_info = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_info($id);

        if(!$calendar_info)
            throw $this->createAccessDeniedException();

        $forebridge = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->getForeBridge($calendar_info[0]['tr'], $calendar_info[0]['tour']);

        if($forebridge) {
            $fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast")->get_forecast($forebridge);

        $presetuser1 = $this->getDoctrine()->getRepository("AppTournamentBundle:Usercast")->get_prescore($calendar_info[0]['user1'], $fore);
        $presetuser2 = $this->getDoctrine()->getRepository("AppTournamentBundle:Usercast")->get_prescore($calendar_info[0]['user2'], $fore);

        } else {
            $fore = 0;
            $preset = 0;
        }

        $tournament = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($calendar_info[0]['tr']);        

        return $this->render('AppTournamentBundle:Tournament:showgame.html.twig',
            array('tournament' => $tournament, 'tour' => $calendar_info[0]['tour'],
                'forecast' => $fore, 'calendar' => $calendar_info, 'preset1' => $presetuser1,
                'preset2' => $presetuser2));
    }

}
