<?php

namespace App\TournamentBundle\Repository;

/**
 * BonusRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BonusRepository extends \Doctrine\ORM\EntityRepository
{
	public function check_bonus($tr, $tour, $userId)
	{
		$dql = "SELECT b.foreid FROM AppTournamentBundle:Bonus b
				WHERE b.tr = :tr AND b.tour = :tour AND b.user = :user";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('tr', $tr)
					  ->SetParameter('tour', $tour)
					  ->SetParameter('user', $userId);

		$result = $query->execute();

		if($result)
			return $result[0]['foreid'];
		else
			return 0;
	}

	public function get_bonus($tr, $tour, $userId)
	{
		$bonus = $this->check_bonus($tr, $tour, $userId);

		if($bonus){
			$dql = "SELECT f.team1, f.team2 FROM AppTournamentBundle:Forecast f
					WHERE f.id = :id";

			$query = $this->getEntityManager()->createQuery($dql)
						  ->SetParameter('id', $bonus);

			$result = $query->execute();

			if($result)
				return $result[0]['team1']." - ".$result[0]['team2'];

		} else {
			return '';
		}
	}

	public function getAllBonusGame($tr, $tour)
	{
		$dql = "SELECT b.foreid, b.user, u.ball FROM AppTournamentBundle:Bonus b
				LEFT JOIN AppTournamentBundle:Usercast u
				WHERE b.foreid = u.idfore AND b.user = u.user
				WHERE b.tr = :tr AND b.tour = :tour";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('tr', $tr)
					  ->SetParameter('tour', $tour);

		$result = $query->execute();

		$bonus_user = [];
		foreach ($result as $user => $bonus) {
			$us = $bonus['user'];
			$bo = $bonus['foreid'];
			$ball = (int) $bonus['ball'];

			$bonus_user[$us] = ["fore" => $bo, "ball" => $ball];
		}

		return $bonus_user;
	}
}
