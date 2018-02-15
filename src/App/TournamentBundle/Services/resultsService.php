<?php

namespace App\TournamentBundle\Services;
use Doctrine\ORM\EntityManager;
use App\TournamentBundle\Entity\Usercast;
use App\TournamentBundle\Entity\Forescored;
use App\TournamentBundle\Entity\Tablelist;

class resultsService
{
	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

        public function add_howgame($tournament, $tour, $hash) {
                $howgame = $this->em->getRepository('AppTournamentBundle:Forecast')->how_games($hash);

                $this->em->getRepository('AppTournamentBundle:Tablelist')->set_how_games($tournament, $tour, $howgame);
        }

	public function add_tablelist($tr, $tour) {
        $users = $this->em->getRepository('AppTournamentBundle:Calendar')->users_for_tournament($tr, $tour);

        for($i=0;$i<count($users);$i++) {
        	$tablelist = new Tablelist();

        	$tablelist->setTr($tr);
        	$tablelist->setTour($tour);
                $tablelist->setUser($users[$i]['user']);
                $tablelist->setGroups($users[$i]['group']);
        	$tablelist->setOff($users[$i]['off']);
        	$tablelist->setGame(0);
        	$tablelist->setW(0);
        	$tablelist->setN(0);
        	$tablelist->setL(0);
        	$tablelist->setBw(0);
        	$tablelist->setBl(0);
        	$tablelist->setScore(0);

			$this->em->persist($tablelist);
			$this->em->flush();        	
        }
	}

	public function completed_tour($hash) {

	$trs = $this->em->getRepository('AppTournamentBundle:Forebridge')->get_all_trs($hash);

        $tablelist = [];

		for($y=0;$y<count($trs);$y++) {
            if($trs[$y]['types'] == 1) {
        	   $balls = $this->em->getRepository('AppTournamentBundle:Usercast')->table_ball($trs[$y]['tr'], $trs[$y]['tour']);
            } else if ($trs[$y]['types'] == 2) {
               $balls = $this->em->getRepository('AppTournamentBundle:Userscored')->table_ball($trs[$y]['tr'], $trs[$y]['tour']);
            }
        	
        	$calendar = $this->em->getRepository('AppTournamentBundle:Calendar')->get_pair($trs[$y]['tr'], $trs[$y]['tour']);

        	for($i=0;$i<count($calendar);$i++) {
        		$user1 = $calendar[$i]['user1'];
        		$user2 = $calendar[$i]['user2'];

        		if(isset($balls[$user1]))
        			$one = $balls[$user1];
        		else
        			$one = 0;

        		if(isset($balls[$user2]))
        			$two = $balls[$user2];
        		else
        			$two = 0;


                        $cdar = $this->em->getRepository('AppTournamentBundle:Calendar')->find($calendar[$i]['id']);
                        $cdar->setResult1($one);
                        $cdar->setResult2($two);
                        $this->em->persist($cdar);

        		// Сравнение
        		if($one == $two) {

        			$tablelist[] = array("game" => 1, 
        								 "user" => $user1, 
        								 "tr" => $trs[$y]['tr'],
        								 "tour" => $trs[$y]['tour'],
        								 "w" => 0, 
        								 "n" => 1, 
        								 "l" => 0,
        								 "bw" => $one, 
        								 "bl" => $two, 
        								 "score" => 1);

        			$tablelist[] = array("game" => 1, 
        								 "user" => $user2, 
        								 "tr" => $trs[$y]['tr'],
        								 "tour" => $trs[$y]['tour'],
        								 "w" => 0, 
        								 "n" => 1, 
        								 "l" => 0,
        								 "bw" => $two, 
        								 "bl" => $one, 
        								 "score" => 1);
        		} else if ($one > $two) {

        			$tablelist[] = array("game" => 1, 
        								 "user" => $user1, 
        								 "tr" => $trs[$y]['tr'],
        								 "tour" => $trs[$y]['tour'],
        								 "w" => 1, 
        								 "n" => 0, 
        								 "l" => 0,
        								 "bw" => $one, 
        								 "bl" => $two, 
        								 "score" => 3);

        			$tablelist[] = array("game" => 1, 
        								 "user" => $user2, 
        								 "tr" => $trs[$y]['tr'],
        								 "tour" => $trs[$y]['tour'], 
        								 "w" => 0, 
        								 "n" => 0, 
        								 "l" => 1,
        								 "bw" => $two, 
        								 "bl" => $one, 
        								 "score" => 0);
        		} else if ($one < $two) {
        			$tablelist[] = array("game" => 1, 
        								 "user" => $user1, 
        								 "tr" => $trs[$y]['tr'],
        								 "tour" => $trs[$y]['tour'], 
        								 "w" => 0, 
        								 "n" => 0, 
        								 "l" => 1,
        								 "bw" => $one, 
        								 "bl" => $two, 
        								 "score" => 0);

        			$tablelist[] = array("game" => 1, 
        								 "user" => $user2, 
        								 "tr" => $trs[$y]['tr'],
        								 "tour" => $trs[$y]['tour'], 
        								 "w" => 1, 
        								 "n" => 0, 
        								 "l" => 0,
        								 "bw" => $two, 
        								 "bl" => $one, 
        								 "score" => 3);
        		}

        	}

                $this->em->flush();

		}

		for($i=0;$i<count($tablelist);$i++) {

			$tlist = $this->em->getRepository('AppTournamentBundle:Tablelist')->findOneBy(array("user" => $tablelist[$i]['user'],
												"tr" => $tablelist[$i]['tr'], "tour" => $tablelist[$i]['tour']));
                	$tlist->setGame($tablelist[$i]['game']);
                	$tlist->setW($tablelist[$i]['w']);
                	$tlist->setN($tablelist[$i]['n']);
                	$tlist->setL($tablelist[$i]['l']);
                	$tlist->setBw($tablelist[$i]['bw']);
                	$tlist->setBl($tablelist[$i]['bl']);
                	$tlist->setScore($tablelist[$i]['score']);
                	$this->em->persist($tlist);
                }

        $this->em->flush();
	}

	// Расчет результатов по ПРОГРЕССИВНОЙ схеме
	private function annihilation($data){
		# Расскладываем главный счет
		$hline = [];
		# Победитель
		if($data[0] > $data[1]){ 
			$hline[0] = 1;
		} else if($data[0] < $data[1]){ 
			$hline[0] = 2;
		} else if($data[0] == $data[1]){ 
			$hline[0] = 0; }

		# Разница мячей
		$hline[1] = abs($data[0] - $data[1]);

		# Количество голов первой команды
		$hline[2] = $data[0];

		# Сумма голов
		$hline[3] = $data[0] + $data[1];
		
		return $hline;
			
	}

	// Расчет результатов по ПРОГРЕССИВНОЙ схеме
	public function prog_mathem($idfore, $r1, $r2) {

		// получаем все нужные idfore в таблице usercast
		// просчитываем и записываем результаты

		$scores = $this->em->getRepository('AppTournamentBundle:Usercast')->get_scores($idfore);

		for($i=0;$i<count($scores);$i++) {
			$one = $this->annihilation(array($r1, $r2));

            if(is_numeric($scores[$i]['result1']) and is_numeric($scores[$i]['result2'])) {
    			$two = $this->annihilation(array($scores[$i]['result1'], $scores[$i]['result2']));

    			if($one == $two){ 
    				$result = 5;
    			} else if ($one[0] == $two[0] and $one[1] == $two[1] and (abs($one[3] - $two[3]) <= 2)){
    				if($one[0] == 0)
    					$result = 3;
    				else
    					$result = 4;
    			} else if ($one[0] == $two[0] and $one[1] == $two[1] and (abs($one[3] - $two[3]) > 2)){ 
    				$result = 3;
    			} else if ($one[0] == $two[0] and (abs($one[1] - $two[1]) <= 1) and (abs($one[3] - $two[3]) <= 1)) {
    				$result = 3;
    			} else if ($one[0] == $two[0]) {  
    				$result = 2;
    			} else {
    				$result = 0;
    			}
            } else {
                $result = 0;
            }

			$usercast = $this->em->getRepository('AppTournamentBundle:Usercast')->find($scores[$i]['id']);

			$usercast->setBall($result);

			$this->em->persist($usercast);
		}

		$this->em->flush();
	}

    // Расчет результатов для сезона 2017-2018
    public function mathem($idfore, $r1, $r2) {

        // получаем все нужные idfore в таблице usercast
        // просчитываем и записываем результаты

        $scores = $this->em->getRepository('AppTournamentBundle:Usercast')->get_scores($idfore);

        for($i=0;$i<count($scores);$i++) {
            $one = $this->annihilation(array($r1, $r2));

            if(is_numeric($scores[$i]['result1']) and is_numeric($scores[$i]['result2'])) {
                $two = $this->annihilation(array($scores[$i]['result1'], $scores[$i]['result2']));

                $result = $this->processing($one, $two);

            } else {
                $result = 0;
            }

            $usercast = $this->em->getRepository('AppTournamentBundle:Usercast')->find($scores[$i]['id']);

            $usercast->setBall($result);

            $this->em->persist($usercast);
        }

        $this->em->flush();
    }

    // Вычесление балла для сезона 2017-2018
    public function processing($one, $two)
    {
        if($one == $two){ 
            $result = 3;
        } else if ($one[0] == $two[0] and $one[1] == $two[1]){ 
            $result = 2;
        } else if ($one[0] == $two[0]){ 
            $result = 1;
        } else { 
            $result = 0;
        }

        return $result;
    }

    // Расчет результатов по ПРОГРЕССИВНОЙ схеме
    public function OLDmathem($idfore, $r1, $r2) {

        // получаем все нужные idfore в таблице usercast
        // просчитываем и записываем результаты

        $scores = $this->em->getRepository('AppTournamentBundle:Usercast')->get_scores($idfore);

        for($i=0;$i<count($scores);$i++) {
            $one = $this->annihilation(array($r1, $r2));

            if(is_numeric($scores[$i]['result1']) and is_numeric($scores[$i]['result2'])) {
                $two = $this->annihilation(array($scores[$i]['result1'], $scores[$i]['result2']));
        
                if($one == $two){ $result = 3;
                } else if ($one[0] == $two[0] and $one[1] == $two[1]){ 
                    $result = 2;
                    if($one[0] == 0){ $result = 1; }
                } else if ($one[0] == $two[0]){ $result = 1;
                } else { $result = 0;
                }

            } else {
                $result = 0;
            }

            $usercast = $this->em->getRepository('AppTournamentBundle:Usercast')->find($scores[$i]['id']);

            $usercast->setBall($result);

            $this->em->persist($usercast);
        }

        $this->em->flush();
    }    

        public function add_playerslist($tournament, $hash) {
                $getplayers = $this->em->getRepository('AppTournamentBundle:Tournament')->get_players($tournament);

                for($i=0;$i<count($getplayers);$i++) {
                        $score = new Forescored();

                        $score->setPlayer($getplayers[$i]['id']);
                        $score->setHash($hash);
                        $this->em->persist($score);
                }

                $this->em->flush();
        }

        public function setadd_playerslist($tr, $tour, $players) {

            for($i=0;$i<count($players);$i++) {
                $score = new Forescored();

                $score->setTr($tr);
                $score->setTour($tour);
                $score->setPlayer($players[$i]['player']);
                $this->em->persist($score);
            }

                $this->em->flush();
        }        

        public function calc_forescored($tr, $tour, $hash) {

            $forescored = $this->em->getRepository('AppTournamentBundle:Forescored')->findBy(array('hash' => $hash));

            for($i=0;$i<count($forescored);$i++) {

                $foreid = $forescored[$i]->getId();

                $player = $forescored[$i]->getPlayer();

                $position = $this->em->getRepository('AppTournamentBundle:Player')->get_position($player);

                $foreactive = [$forescored[$i]->getFirst(), $forescored[$i]->getSecond(), $forescored[$i]->getThree()];

                // user scored
                $userscoredplayers = $this->em->getRepository('AppTournamentBundle:Userscored')->findBy(array('idfore' => $foreid));

                for($y=0;$y<count($userscoredplayers);$y++) {
                    $ball = 0;
                    $useractive = [$userscoredplayers[$y]->getFirst(), $userscoredplayers[$y]->getSecond(), $userscoredplayers[$y]->getThree()];

                    $scoreball = array_intersect($foreactive, $useractive);
                    $scoreball = array_values($scoreball);

                    if ($position == 1) {

                        for($x=0;$x<count($scoreball);$x++) {        
                            if($scoreball[$x] == 'null' or $scoreball[$x] == 'one' or $scoreball[$x] == 'two' or $scoreball[$x] == 'three') {
                                $ball += 3;
                            } else if ($scoreball[$x] == 'goal') {
                                $ball += 6;
                            } else if ($scoreball[$x] == 'assist') {
                                $ball += 6;
                            } else if ($scoreball[$x] == 'yellow') {
                                $ball += 4;
                            } else if ($scoreball[$x] == 'red') {
                                $ball += 8;
                            }
                        }


                    } else if ($position == 2) {

                        for($x=0;$x<count($scoreball);$x++) {

                            if ($scoreball[$x] == 'goal') {
                                $ball += 6;
                            } else if ($scoreball[$x] == 'assist') {
                                $ball += 6;
                            } else if ($scoreball[$x] == 'yellow') {
                                $ball += 3;
                            } else if ($scoreball[$x] == 'red') {
                                $ball += 8;
                            }
                        }

                    } else if ($position == 3) {

                        for($x=0;$x<count($scoreball);$x++) {
                        
                            if ($scoreball[$x] == 'goal') {
                                $ball += 5;
                            } else if ($scoreball[$x] == 'assist') {
                                $ball += 4;
                            } else if ($scoreball[$x] == 'yellow') {
                                $ball += 3;
                            } else if ($scoreball[$x] == 'red') {
                                $ball += 8;
                            }                                                
                        }

                    } else if ($position == 4) {

                        for($x=0;$x<count($scoreball);$x++) {
                            if ($scoreball[$x] == 'goal') {
                                $ball += 4;
                            } else if ($scoreball[$x] == 'assist') {
                                $ball += 5;
                            } else if ($scoreball[$x] == 'yellow') {
                                $ball += 4;
                            } else if ($scoreball[$x] == 'red') {
                                $ball += 8;
                            }                                                
                        }
                    }

                    $userscoredplayers[$y]->setScore($ball);
                    $this->em->persist($userscoredplayers[$y]);
                }
                $this->em->flush();

            }
    }

    public function getDrawWinner($tr, $tour, $showtour)
    {

        $result = $this->em->getRepository('AppTournamentBundle:Bonus')->getAllBonusGame($tr, $tour);

        for ($i = 0; $i<count($showtour); $i++) {
            if (isset($showtour[$i]['result1']) && isset($showtour[$i]['result2']) && $showtour[$i]['result1'] == $showtour[$i]['result2']) {
                $user1 = $showtour[$i]['uid1'];
                $user2 = $showtour[$i]['uid2'];

                if(isset($result[$user1]['ball']))
                    $ball1 = $result[$user1]['ball'];
                else
                    $ball1 = 0;

                if(isset($result[$user2]['ball']))
                    $ball2 = $result[$user2]['ball'];
                else
                    $ball2 = 0;

                // first step - bonus game
                if ($ball1 > $ball2) {
                    $showtour[$i]['draw'] = $user1;
                    $showtour[$i]['drawstep'] = 1;
                    $showtour[$i]['foreuser1'] = $result[$user1]['fore'];
                    $showtour[$i]['foreuser2'] = $result[$user2]['fore'];
                } else if ($ball1 < $ball2) {
                    $showtour[$i]['draw'] = $user2;
                    $showtour[$i]['drawstep'] = 1;
                    $showtour[$i]['foreuser1'] = $result[$user1]['fore'];
                    $showtour[$i]['foreuser2'] = $result[$user2]['fore'];
                } else {

                    $forebridge = $this->em->getRepository('AppTournamentBundle:Forebridge')->getForeBridge($tr, $tour);

                    $accuracy1 = $this->em->getRepository('AppTournamentBundle:Forecast')->getAccuracy($forebridge, $user1);
 
                    $accuracy2 = $this->em->getRepository('AppTournamentBundle:Forecast')->getAccuracy($forebridge, $user2);

                    if ($accuracy1 > $accuracy2) {
                        $showtour[$i]['draw'] = $user1;
                        $showtour[$i]['drawstep'] = 2;
                    } else if ($accuracy1 < $accuracy2) {
                        $showtour[$i]['draw'] = $user2;
                        $showtour[$i]['drawstep'] = 2;
                    } else {
                        $showtour[$i]['drawstep'] = 3;
                    }
                }
            }
        }

        return $showtour;
    }

}