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

    public function tournamentsAction($page) {
        $status = 1;
        $nav = 'tournament';

        $result = $this->listAction($page, $status);

        $tournaments = $result[0];
        $count = $result[1];

        return $this->render('AppTournamentBundle:Tournament:list.html.twig',
            array("tournaments" => $tournaments, "countpage" => $count, "nav" => $nav,
                  "page" => $page));        
    }

    public function archivesAction($page) {
        $status = 2;
        $nav = 'archive';

        $result = $this->listAction($page, $status);

        $tournaments = $result[0];
        $count = $result[1];

        return $this->render('AppTournamentBundle:Tournament:list.html.twig',
            array("tournaments" => $tournaments, "countpage" => $count, "nav" => $nav,
                  "page" => $page));
    }

    public function listAction($page, $status) {

        $repository = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament");

        $result = $repository->show_tournaments_list($page, $status);

        return $result;
    }

    public function tournamentAction($id, $tour) {
        $status = 1;
        $nav = 'tournament';

        return $this->showAction($id, $tour, $status, $nav);

    }

    public function archiveAction($id, $tour) {
        $status = 2;
        $nav = 'archive';        

        return $this->showAction($id, $tour, $status, $nav);
    }

    public function showAction($id, $tour, $status, $nav) {

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            $userId = 0;

        $tournament = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show($id, $status);

        if(empty($tournament)) {
            return $this->render('AppTournamentBundle:Tournament:noshow.html.twig',
                   array("tournament" => $tournament));
        }

        if($tournament->getStatus() == 1 or $tournament->getStatus() == 2) {

            if($tour == 0)
                $tour = $this->getDoctrine()->getRepository("AppTournamentBundle:Tablelist")->get_actual_tour($id);

            if($tour == 0)
                $tour = 1;            

            $tourstatus = $this->getDoctrine()->getRepository("AppTournamentBundle:Tablelist")->get_tour_status($id, $tour);

            $info = $tournament->getInfo();
            $schema = $info['schema'];

            $rep_calendar = $this->getDoctrine()->getRepository("AppTournamentBundle:Calendar");
            $showtour = $rep_calendar->get_tour($id, $tour, $schema);
            $calendar = $rep_calendar->get_calendar($id);
            $member = $rep_calendar->get_member($id, $tour, $userId);

            $forebridge = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->getForeBridge($id, $tour);
        if($forebridge) {
            $fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Usercast")->get_balls($id, $tour, $forebridge);
        } else {
            $fore['started'] = 0;
        }

            $table = [];
            if(count($showtour) > 1) {
                for($i=1;$i<=count($showtour);$i++)
                    $table[$i] = $this->getDoctrine()->getRepository('AppTournamentBundle:Tablelist')->show_table($id, $tour, $i);
            } else {
                $table[] = $this->getDoctrine()->getRepository('AppTournamentBundle:Tablelist')->show_table($id, $tour, 0);
            }

            $strickers = $this->getDoctrine()->getRepository('AppTournamentBundle:Tablelist')->show_strickers($id, $tour);

            $groups_name = ['', 'ГРУППА A', 'ГРУППА B', 'ГРУППА C', 'ГРУППА D', 'ГРУППА E', 'ГРУППА F', 'ГРУППА G', 'ГРУППА H', 'ГРУППА I', 'ГРУППА J', 'ГРУППА K', 'ГРУППА L', 'ГРУППА M', 'ГРУППА N', 'ГРУППА O', 'ГРУППА P', 'ГРУППА Q', 'ГРУППА R', 'ГРУППА S', 'ГРУППА T', 'ГРУППА U', 'ГРУППА V', 'ГРУППА W', 'ГРУППА X', 'ГРУППА Y', 'ГРУППА Z'];


            $playoff_name = ['1' => 'ФИНАЛ', '2' => '1/2 ФИНАЛА', '4' => '1/4 ФИНАЛА', '8' => '1/8 ФИНАЛА',
                             '16' => '1/16 ФИНАЛА', '32' => '1/32 ФИНАЛА', '64' => '1/64 ФИНАЛA',
                             '128' => '1/128 ФИНАЛA', '256' => '1/256 ФИНАЛA'];

            // tour name
            $get_tour = $calendar[$tour];
            $off = $get_tour['off'];

            if($off != 0) {
                $printtour = array("tour" => $tour, "name" => $playoff_name[$off]);
                $offstatus = 1;
            } else {
                $printtour = array("tour" => $tour, "name" => $tour." ТУР");
                $offstatus = 0;
            }

            return $this->render('AppTournamentBundle:Tournament:show.html.twig',
                   array("tournament" => $tournament,
                         "user" => $userId,
                         "tour" => $tour,
                         "nav" => $nav,
                         "printtour" => $printtour,
                         "offstatus" => $offstatus,
                         "calendar" => $calendar,
                         "showtour" => $showtour,
                         "member" => $member,
                         "groups" => $groups_name,
                         "playoff" => $playoff_name,
                         "tourstatus" => $tourstatus,
                         "fore" => $fore, "table" => $table, "strickers" => $strickers));

        } else if ($tournament->getStatus() == 3 or $tournament->getStatus() == 4) {

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

                    // return $this->redirect($this->generateUrl('app_tournament_show', array('id'=> $id)));

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

                            if(($r1[$i] != $res1 or $r2[$i] != $res2) and ($r1[$i] !== "" and $r2[$i] !== "")) {

                                $r1[$i] = (int) $r1[$i];
                                $r2[$i] = (int) $r2[$i];

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

        // $tourstatus = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->get_tour_status($tr, $tour);

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

            $sum1 = 0;
            foreach ($presetuser1 as $key) {
                $sum1 += (int) $key['ball'];
            }

            $presetuser2 = $this->getDoctrine()->getRepository("AppTournamentBundle:Usercast")->get_prescore($calendar_info[0]['user2'], $fore);

            $sum2 = 0;
            foreach ($presetuser2 as $key) {
                $sum2 += (int) $key['ball'];
            }

            $summ = [$sum1, $sum2];

        } else {
            $fore = 0;
            $presetuser1 = 0;
            $presetuser2 = 0;

            $summ = [0, 0];
        }

        $tournament = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($calendar_info[0]['tr']);

        if($tournament->getStatus() == 1)
            $nav = "tournament";
        else if ($tournament->getStatus() == 2)
            $nav = "archive";

        $games = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_games_in_pair($calendar_info[0]['user1'], $calendar_info[0]['user2']);

        return $this->render('AppTournamentBundle:Tournament:showgame.html.twig',
            array('tournament' => $tournament, 'tour' => $calendar_info[0]['tour'],
                'forecast' => $fore, 'calendar' => $calendar_info, 'preset1' => $presetuser1,
                'games' => $games, "nav" => $nav,
                'preset2' => $presetuser2, "summ" => $summ));
    }

}
