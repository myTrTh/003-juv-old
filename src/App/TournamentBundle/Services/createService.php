<?php

namespace App\TournamentBundle\Services;
use Doctrine\ORM\EntityManager;
use App\TournamentBundle\Entity\Calendar;

class createService
{
	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function createCalendar($tr_id, $circle, $users, $param){

		shuffle($users);
		$lenght = count($users)-1;
		$len = count($users);
		$pollen = count($users)/2;
		$new = [];
		for($i=0; $i<$lenght; $i++){
			$first = array_shift($users);
			$last = array_pop($users);
			array_unshift($users, $first, $last);
			$new[] = $users;
		}
		$tours = [];
		foreach ($new as $key){
			$tour = [];
			for($i=0;$i<$pollen;$i++){
				$tour[] = $key[$i];
				$tour[] = $key[$lenght-$i];
			}
			$tours[] = $tour;
		}

		$all = [];
		$i=0;
		foreach ($tours as $tour) {
			if(($i%2)==0){
				$all[] = $tour;
			} else {
				$first = array_shift($tour);
				$second = array_shift($tour);
				array_unshift($tour, $second, $first);
				$all[] = $tour;
			}
			$i++;
		}

		shuffle($all);

		$rall = array_reverse($all);

		if($circle > 1){
			$twoall = [];
			foreach ($rall as $a) {
				$twoall[] = array_reverse($a);
			}

			$call = [];
			for($i=1;$i<=$circle;$i++){
				if(($i%2)!=0){
					$call[] = $all;
				} else {
					$call[] = $twoall;
				}
			}

			$called = [];
			foreach ($call as $ca) {
				foreach ($ca as $c) {
					$called[] = $c;
				}
			}
		}

		if(isset($called)){
			$all = $called;
		}

		$y=1;
		foreach ($all as $al) {
			for($i=0;$i<count($al);$i+=2) {

				$calendar = new Calendar();

				$calendar->setTr($tr_id);
				$calendar->setTour($y);
				$calendar->setUser1($al[$i]);
				$calendar->setUser2($al[$i+1]);
				$calendar->setGroups($param);

				$this->em->persist($calendar);
			}

			$y++;
		}

		$this->em->flush();
	}

	# Обновляем плей-офф
	public function updatePlayOff($tr_id, $users, $tour) {

        $id_calendar = $this->em->getRepository('AppTournamentBundle:Calendar')->get_tour_of_playoff($tr_id, $tour);

        $players = [];

        foreach ($users as $user) {
        	$players[] = $user;
        }

        if(count($players) != (count($id_calendar)*2))
        	return 0;

        $y = 0;
        for($i=0;$i<count($id_calendar);$i++) {
        	$calendar = $this->em->getRepository('AppTournamentBundle:Calendar')->find($id_calendar[$i]['id']);

        	$calendar->setUser1($players[$y]);
        	$y++;
        	$calendar->setUser2($players[$y]);
        	$y++;

			$this->em->persist($calendar);        	
        }

        $this->em->flush();

        return 1;
	}

	# Создаем расписание для плей-офф
	public function createPlayOff($tr_id, $users, $add){
		$members = count($users);
		$users = array_values($users);

		$round = $members/2;
		$first = $members/2;
		$newmembers = $members;
		$null = 0;

		if($add != 0){
			$trID = $tr_id;
			$startTour = $add + 1;
		} else {
			$trID = $tr_id;
			$startTour = 1;
		}

		$y = $startTour;
		while($round >= 1){

			for($i=0;$i<$newmembers;$i+=2){

				$calendar = new Calendar();

				$calendar->setTr($trID);
				$calendar->setTour($y);

				if($round == $first){
					$calendar->setUser1($users[$i]);
					$calendar->setUser2($users[$i+1]);
				} else {
					$calendar->setUser1($null);
					$calendar->setUser2($null);

				}

				$calendar->setGroups($null);
				$calendar->setOff($round);

				$this->em->persist($calendar);
				
			}
			$round = $round/2;
			$newmembers = $newmembers/2;
			$y++;
		}
		$this->em->flush();
	}	

}