<?php

namespace App\TournamentBundle\Repository;
use Doctrine\ORM\EntityManager;

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

	public function users_for_tournament($tr) {

		$dql = "SELECT p.user, u.username 
		FROM AppTournamentBundle:Tournamentusers p
		INNER JOIN AppUserBundle:User u
		WHERE u.id = p.user
		WHERE p.tournament = :tournament AND p.status = 1
		ORDER BY u.username ASC";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("tournament", $tr);

		$result = $query->execute();

		return $result;
	}

	public function users_without_tournament($tr) {
		$dql = "SELECT u.id, u.username 
		FROM AppUserBundle:User u
		WHERE u.id NOT IN (SELECT tu.user FROM AppTournamentBundle:Tournamentusers tu WHERE tu.tournament = ".$tr." AND tu.status = 1) ORDER BY u.username ASC";


		$query = $this->getEntityManager()->createQuery($dql);

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

	public function replace($tr, $inuser, $newuser) {

        $em = $this->getEntityManager();
        $params = array('newuser' => $newuser, 'inuser' => $inuser, 'tr' => $tr);

        $sql = "UPDATE tournamentusers SET user = :newuser WHERE user = :inuser AND tournament = :tr";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);

        $sql = "UPDATE usercast SET user = :newuser WHERE user = :inuser AND tr = :tr";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);

        $sql = "UPDATE tablelist SET user = :newuser WHERE user = :inuser AND tr = :tr";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);

        $sql = "UPDATE calendar SET user1 = :newuser WHERE user1 = :inuser AND tr = :tr";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);

        $sql = "UPDATE calendar SET user2 = :newuser WHERE user2 = :inuser AND tr = :tr";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);        

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

	public function get_tournaments($id) {

		$dql = "SELECT t.id, t.name
				FROM AppTournamentBundle:Tournamentusers tu
				INNER JOIN AppTournamentBundle:Tournament t
				WHERE tu.tournament = t.id
				WHERE tu.user = :user
				ORDER BY t.id DESC";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("user", $id);

		$result = $query->execute();

		return $result;
	}

	public function get_member($tr, $userId) {
		$dql = "SELECT t.user FROM AppTournamentBundle:Tournamentusers t
				WHERE t.tournament = :tr and t.user = :user AND t.status = 1";

		$query = $this->getEntityManager()->createQuery($dql)
					  ->SetParameter("tr", $tr)
					  ->SetParameter("user", $userId);

		$result = $query->execute();

		if(empty($result))
			$res = 0;
		else
			$res = 1;

		return $res;
	}
}
