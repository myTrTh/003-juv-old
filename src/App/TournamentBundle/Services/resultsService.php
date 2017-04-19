<?php

namespace App\TournamentBundle\Services;
use Doctrine\ORM\EntityManager;
use App\TournamentBundle\Entity\Usercast;
use App\TournamentBundle\Entity\Tablelist;

class resultsService
{
	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function add_tablelist($tr, $tour) {
        $users = $this->em->getRepository('AppTournamentBundle:Tournamentusers')->users_for_tournament($tr);

        for($i=0;$i<count($users);$i++) {
        	$tablelist = new Tablelist();

        	$tablelist->setTr($tr);
        	$tablelist->setTour($tour);
        	$tablelist->setUser($users[$i]['user']);
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

		for($i=0;$i<count($trs);$i++) {
        	$balls = $this->em->getRepository('AppTournamentBundle:Usercast')->table_ball($trs[$i]['tr'], $trs[$i]['tour']);
        	
        	$calendar = $this->em->getRepository('AppTournamentBundle:Calendar')->get_pair($trs[$i]['tr'], $trs[$i]['tour']);

        	for($i=0;$i<count($calendar);$i++) {
        		$user1 = $calendar[$i]['user1'];
        		$user2 = $calendar[$i]['user2'];

        		// Сравнение
        		if($balls[$user1] == $balls[$user2]) {
        			$tablelist[] = array("game" => 1, "user" => $user1, "tr" => $trs[$i]['tr'],
        							"tour" => $trs[$i]['tour'], "w" => 0, "n" => 1, "l" => 0,
        							"bw" => $balls[$user1], "bl" => $balls[$user2], "score" => 1);

        			$tablelist[] = array("game" => 1, "user" => $user2, "tr" => $trs[$i]['tr'],
        							"tour" => $trs[$i]['tour'], "w" => 0, "n" => 1, "l" => 0,
        							"bw" => $balls[$user2], "bl" => $balls[$user1], "score" => 1);
        		} else if ($balls[$user1] > $balls[$user2]) {
        			$tablelist[] = array("game" => 1, "user" => $user1, "tr" => $trs[$i]['tr'],
        							"tour" => $trs[$i]['tour'], "w" => 1, "n" => 0, "l" => 0,
        							"bw" => $balls[$user1], "bl" => $balls[$user2], "score" => 3);

        			$tablelist[] = array("game" => 1, "user" => $user2, "tr" => $trs[$i]['tr'],
        							"tour" => $trs[$i]['tour'], "w" => 0, "n" => 0, "l" => 1,
        							"bw" => $balls[$user2], "bl" => $balls[$user1], "score" => 0);
        		} else if ($balls[$user1] < $balls[$user2]) {
        			$tablelist[] = array("game" => 1, "user" => $user1, "tr" => $trs[$i]['tr'],
        							"tour" => $trs[$i]['tour'], "w" => 0, "n" => 0, "l" => 1,
        							"bw" => $balls[$user1], "bl" => $balls[$user2], "score" => 0);

        			$tablelist[] = array("game" => 1, "user" => $user2, "tr" => $trs[$i]['tr'],
        							"tour" => $trs[$i]['tour'], "w" => 1, "n" => 0, "l" => 0,
        							"bw" => $balls[$user2], "bl" => $balls[$user1], "score" => 3);
        		}
        	}

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
	public function mathem($idfore, $r1, $r2) {

		// получаем все нужные idfore в таблице usercast
		// просчитываем и записываем результаты

		$scores = $this->em->getRepository('AppTournamentBundle:Usercast')->get_scores($idfore);

		for($i=0;$i<count($scores);$i++) {
			$one = $this->annihilation(array($r1, $r2));
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

			$usercast = $this->em->getRepository('AppTournamentBundle:Usercast')->find($scores[$i]['id']);

			$usercast->setBall($result);

			$this->em->persist($usercast);
		}

		$this->em->flush();
	}

	// Расчет результатов по классической схеме
	private function OLDannihilation($data){
		# Расскладываем главный счет
		$hline = [];
		# Победитель
		if($data[0] > $data[1]){ $hline[0] = 1;
		} else if($data[0] < $data[1]){ $hline[0] = 2;
		} else if($data[0] == $data[1]){ $hline[0] = 0; }

		# Разница мячей
		$hline[1] = abs($data[0] - $data[1]);

		# Количество голов первой команды
		$hline[2] = $data[0];
		
		return $hline;
			
	}

	// Расчет результатов по классической схеме
	public function OLDmathem($head, $plus){
		$one = $this->OLDannihilation($head);
		$two = $this->OLDannihilation($plus);

		if($one == $two){ $result = 3;
		} else if ($one[0] == $two[0] and $one[1] == $two[1]){ 
			$result = 2;
			if($one[0] == 0){ $result = 1; }
		} else if ($one[0] == $two[0]){ $result = 1;
		} else { $result = 0;
		}
		return $result; 
	}

}