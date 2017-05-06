<?php

namespace App\TournamentBundle\Repository;

/**
 * TournamentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TournamentRepository extends \Doctrine\ORM\EntityRepository
{

	public function show($tr, $status) {

	    $append = $this->getStatus($status);

		$dql = "SELECT t FROM AppTournamentBundle:Tournament t
				WHERE t.id = :tr AND (".$append.")";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tr);

		$result = $query->execute();

		if($result)
			return $result[0];
		else
			return 0;

	}

	public function getStatus($status) {
	    if($status == 2)
	    	$append = "t.status = 2";
	    else if ($status == 1) {
	    	$append = "t.status = 1 OR t.status = 3 OR t.status = 4";
	    }

	    return $append;
	}

	public function show_tournaments_list($page, $status) {
	    $list = $page - 1;
	    $how = 10;
	    if($list > 0)
	        $list = $list * $how;

	    $append = $this->getStatus($status);

		$dql = "SELECT t.id, t.name, t.image, t.status
				FROM AppTournamentBundle:Tournament t
				WHERE ".$append." ORDER BY t.id DESC";

		$query = $this->getEntityManager()->createQuery($dql)
                ->SetFirstResult($list)
                ->SetMaxResults($how);

		$tournament = $query->execute();

	    $dql = 'SELECT count(t) 
	    		FROM AppTournamentBundle:Tournament t
	    		WHERE '.$append;
	    
	    $query = $this->getEntityManager()->createQuery($dql);

	    $rowcount = $query->execute();
	    $count = ceil($rowcount[0][1]/$how);

		return array($tournament, $count);

	}

	public function show_tournament_for_admin($tournament) {

		$dql = "SELECT t.id, t.types, t.name, t.access, t.image, t.info, t.status FROM AppTournamentBundle:Tournament t
			WHERE t.id = :id";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("id", $tournament);

		$result = $query->execute();

		if(!empty($result)) {
			$result = $result[0];

			$result['info'] = unserialize($result['info']);
			$result['access'] = unserialize($result['access']);
		}

		return $result;
	}

	public function get_players($tournament) {

		print "here";
		$dql = "SELECT p.id FROM AppTournamentBundle:Player p
				INNER JOIN AppTournamentBundle:Trteam t
				WHERE p.team = t.team
				INNER JOIN AppTournamentBundle:Tournament tr
				WHERE t.tr = tr.id
				WHERE tr.id = :tr AND p.status = 1";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('tr', $tournament);

		$result = $query->execute();

		return $result;
	}

}
