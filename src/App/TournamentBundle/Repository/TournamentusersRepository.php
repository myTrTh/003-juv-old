<?php

namespace App\TournamentBundle\Repository;

/**
 * TournamentusersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TournamentusersRepository extends \Doctrine\ORM\EntityRepository
{
	public function order_preliminary($id, $userId) {
		$dql = "SELECT p.id FROM AppTournamentBundle:Tournamentusers p
		WHERE p.tournament = :tournament AND p.user = :user";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("tournament", $id)
					->SetParameter("user", $userId);

		$result = $query->execute();

		if(!empty($result))
			$result = $result[0]['id'];
		else
			$result = 0;

		return $result;
	}

	public function show_users_for_tournament($id, $user)	{

		$dql = "SELECT p.user, u.username 
		FROM AppTournamentBundle:Tournamentusers p
		INNER JOIN AppUserBundle:User u
		WHERE u.id = p.user
		WHERE p.tournament = :tournament AND p.status > 0";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("tournament", $id);

		$result = $query->execute();

		$access = 0;
		foreach ($result as $k => $v) {
			if($user === $v['user'])
				$access += 1;
		}

		return array($result, $access);
	}

	public function users_for_tournament($tr)	{

		$dql = "SELECT p.user, u.username 
		FROM AppTournamentBundle:Tournamentusers p
		INNER JOIN AppUserBundle:User u
		WHERE u.id = p.user
		WHERE p.tournament = :tournament AND p.status = 1";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("tournament", $tr);

		$result = $query->execute();

		return $result;
	}	

	public function show_playoff_users($id, $user)	{
		$dql = "SELECT p.user, u.username 
		FROM AppTournamentBundle:Tournamentusers p
		INNER JOIN AppUserBundle:User u
		WHERE u.id = p.user
		WHERE p.tournament = :tournament AND p.status = 1";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("tournament", $id);

		$result = $query->execute();

		$access = 0;
		foreach ($result as $k => $v) {
			if($user === $v['user'])
				$access += 1;
		}

		return array($result, $access);
	}	

	public function delete_users($tournament) {
		$dql = "DELETE FROM AppTournamentBundle:Tournamentusers tu
				WHERE tu.tournament = :tr";

		$query = $this->getEntityManager()->createQuery($dql)
				->SetParameter("tr", $tournament);

		$query->execute();
	}

	public function set_new_status($tournament, $users) {
		$dq = $this->createQueryBuilder('t');
		$q = $dq->update('AppTournamentBundle:Tournamentusers', 't')
		        ->set('t.status', 1)
		        ->where('t.tournament = :tr')
		        ->andWhere('t.user IN (:user)')
		        ->SetParameter('tr', $tournament)
		        ->SetParameter('user', $users)
		        ->getQuery();

		$q->execute();
	}
}
