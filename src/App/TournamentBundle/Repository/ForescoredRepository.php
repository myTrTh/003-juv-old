<?php

namespace App\TournamentBundle\Repository;

/**
 * ForescoredRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ForescoredRepository extends \Doctrine\ORM\EntityRepository
{
	public function get_forescored($hash) {
		$dql = "SELECT f.id, f.player, p.first, p.second, p.image, p.position, f.first as scorefirst, f.second as scoresecond, f.three as scorethree
				FROM AppTournamentBundle:Forescored f
				INNER JOIN AppTournamentBundle:Player p
				WHERE f.player = p.id
				WHERE f.hash = :hash
				ORDER BY p.position ASC, p.second ASC";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('hash', $hash);

		$result = $query->execute();

		return $result;
	}

	public function get_forescored_id($hash) {
		$dql = "SELECT f.id, f.player
				FROM AppTournamentBundle:Forescored f
				WHERE f.hash = :hash";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('hash', $hash);

		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {
			$id = $result[$i]['id'];
			$player = $result[$i]['player'];

			$results[$player] = $id;
		}

		return $results;
	}

}
