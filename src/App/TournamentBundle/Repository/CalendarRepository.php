<?php

namespace App\TournamentBundle\Repository;

/**
 * CalendarRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CalendarRepository extends \Doctrine\ORM\EntityRepository
{

	public function get_calendar($id) {
		$dql = "SELECT max(c.tour)
				FROM AppTournamentBundle:Calendar c
				WHERE c.tr = :tr AND c.off IS NULL";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $id);

		$result = $query->execute();

		if(!empty($result))
			$result = $result[0][1];
		else
			$result = 0;

		return $result;
	}

	public function get_tour($id, $tour, $schema, $playoff) {
		$dql = "SELECT c.id, c.user1 as uid1, c.user2 as uid2, u1.username AS user1, u2.username AS user2, c.result1, c.result2, c.groups
				FROM AppTournamentBundle:Calendar c
				INNER JOIN AppUserBundle:User u1
				WHERE u1.id = c.user1
				INNER JOIN AppUserBundle:User u2
				WHERE u2.id = c.user2
				WHERE c.tr = :tr AND c.tour = :tour";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $id)
					  ->SetParameter("tour", $tour);

		$result = $query->execute();

		return $result;
	}	

	public function get_max_tour($tournament) {
		$dql = "SELECT max(c.tour) FROM AppTournamentBundle:Calendar c
				WHERE c.tr = :tr";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tournament);

		$result = $query->execute();
		if(!empty($result))
			$result = $result[0][1];
		else
			$result = 0;

		return $result;
	}

	public function get_info($id) {
		$dql = "SELECT c.tr, c.tour, c.user1, c.user2 FROM AppTournamentBundle:Calendar c
				WHERE c.id = :id";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("id", $id);

		$result = $query->execute();

		return $result;
	}
}
