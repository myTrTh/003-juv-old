<?php

namespace App\TournamentBundle\Repository;

/**
 * TeamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TeamRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('name' => 'ASC'));
    }

    public function getTeams($tournament) {

      $dql = "SELECT t
              FROM AppTournamentBundle:Team t
              WHERE t.tournament = :tr
              ORDER BY t.name ASC";

      $query = $this->getEntityManager()->createQuery($dql)
                    ->SetParameter("tr", $tournament);

      $result = $query->execute();

      $results = [];
      for ($i=0; $i < count($result); $i++) {
        $id = $result[$i]->getId();
        $name = $result[$i]->getName();
        $results[] = ['id' => $id, 'name' => $name];
      }

      return $results;
    }
}
