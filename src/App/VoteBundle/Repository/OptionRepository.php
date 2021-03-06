<?php

namespace App\VoteBundle\Repository;

/**
 * OptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OptionRepository extends \Doctrine\ORM\EntityRepository {
	
	public function show_vote_options($vote, $orderby) {
		$dql = "SELECT o.id as id, o.description, count(r.options) as cnt
				FROM AppVoteBundle:Option o
				LEFT JOIN AppVoteBundle:Result r
				WHERE r.options = o.id
				WHERE o.votes = :votes AND o.status = 1
				GROUP BY o.id
				ORDER BY ".$orderby;

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter('votes', $vote);

		$result = $query->getResult();

		return $result;		
	}
}