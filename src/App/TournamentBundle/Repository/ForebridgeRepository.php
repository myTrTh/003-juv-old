<?php

namespace App\TournamentBundle\Repository;

/**
 * ForebridgeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ForebridgeRepository extends \Doctrine\ORM\EntityRepository
{
	public function getForeBridge($tournament, $tour) {
		$dql = "SELECT f.hash FROM AppTournamentBundle:Forebridge f WHERE f.tr = :tr AND f.tour = :tour";

		$query = $this->getEntityManager()->createQuery($dql)
			->SetParameter('tr', $tournament)
			->SetParameter('tour', $tour);

		$result = $query->execute();

		if(empty($result))
			$result = 0;
		else
			$result = $result[0]['hash'];

		return $result;
	}

	public function get_all_trs($hash) {
		$dql = "SELECT f.tr, f.tour, t.types FROM AppTournamentBundle:Forebridge f
				INNER JOIN AppTournamentBundle:Tournament t
				WHERE t.id = f.tr
				WHERE f.hash = :hash";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("hash", $hash);

		$result = $query->execute();

		return $result;
	}
}
