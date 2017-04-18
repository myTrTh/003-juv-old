<?php

namespace App\TournamentBundle\Repository;

/**
 * UsercastRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsercastRepository extends \Doctrine\ORM\EntityRepository
{

	public function check_score($idfore, $tr, $tour, $userId) {

		$dql = "SELECT u.idfore FROM AppTournamentBundle:Usercast u
				WHERE u.tr = :tr AND u.tour = :tour AND u.user = :user AND u.idfore = :fore";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tr)
					  ->SetParameter("tour", $tour)
					  ->SetParameter("user", $userId)
					  ->SetParameter("fore", $idfore);

		$result = $query->execute();

		return $result;
	}

	public function get_prescore($userId, $fore) {

		$ids = [];
		for($i=0;$i<count($fore);$i++) {
			$ids[] = $fore[$i]->getId();
		}

		$dql = "SELECT u.idfore, u.result1, u.result2, u.ball FROM AppTournamentBundle:Usercast u
				WHERE u.user = :user AND u.idfore IN (".implode(', ', $ids).")";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("user", $userId);

		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {

			$idfore = $result[$i]['idfore'];
			$r1 = $result[$i]['result1'];
			$r2 = $result[$i]['result2'];
			$ball = $result[$i]['ball'];

			$results[$idfore] = ['result1' => $r1, 'result2' => $r2, 'ball' => $ball];
		}

		return $results;
	}

	public function get_scores($idfore) {

		$dql = "SELECT u.id, u.result1, u.result2 FROM AppTournamentBundle:Usercast u
				WHERE u.idfore = :idfore";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('idfore', $idfore);

		$result = $query->execute();

		return $result;
	}

	public function get_balls($tr, $tour, $forebridge) {
		$dql = "SELECT f.id FROM AppTournamentBundle:Forecast f WHERE f.hash = :hash AND (f.result1 IS NOT NULL OR f.result2 IS NOT NULL)";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("hash", $forebridge);

		$result = $query->execute();

		if(empty($result))
			$res['started']	= 0;
		else
			$res['started'] = 1;

		$dql = "SELECT u.user, sum(u.ball) as summ FROM AppTournamentBundle:Usercast u WHERE u.tr = :tr AND u.tour = :tour
		        GROUP BY u.user";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tr)
					  ->SetParameter("tour", $tour);

		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {
			$user = $result[$i]['user'];
			$sum = $result[$i]['summ'];

			$results[$user] = $sum;
		}

		$res['members'] = $results;

		return $res;

	}

	public function table_ball($tr, $tour) {
		$dql = "SELECT u.user, sum(u.ball) as summ FROM AppTournamentBundle:Usercast u WHERE u.tr = :tr AND u.tour = :tour
		        GROUP BY u.user";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tr)
					  ->SetParameter("tour", $tour);

		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {
			$user = $result[$i]['user'];
			$sum = $result[$i]['summ'];

			$results[] = ["user" => $user, "sum" => $sum];
		}

		return $results;
	}
}