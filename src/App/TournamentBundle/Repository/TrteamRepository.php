<?php

namespace App\TournamentBundle\Repository;

/**
 * TrteamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TrteamRepository extends \Doctrine\ORM\EntityRepository
{
	public function get_team($tr) {
		$dql = "SELECT t.team, h.name FROM AppTournamentBundle:Trteam t
				INNER JOIN AppTournamentBundle:Headteam h
				WHERE t.team = h.id
				WHERE t.tr = :tr AND t.status = 1";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tr);

		$result = $query->execute();

		if(empty($result))
			return 0;
		else
			return $result[0];
	}

	public function get_teams($tr) {
		$team = $this->get_team($tr);

		if($team == 0)
			$id = 0;
		else
			$id = $team['team'];

		$dql = "SELECT h.id, h.name FROM AppTournamentBundle:Headteam h
				WHERE h.status = 1 AND h.id != :id
				ORDER BY h.name ASC";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("id", $id);

		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {
			$ind = $result[$i]['id'];
			$name = $result[$i]['name'];
			$results[$name] = $ind;
		}

		return $results;

	}	

}
