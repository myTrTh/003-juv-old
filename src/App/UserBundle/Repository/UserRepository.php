<?php

namespace App\UserBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{

    public function show_all_users($page, $sort)
    {

        if($sort == 'alpha_asc')
            $orber_by = "username ASC";
        else if ($sort == 'alpha_desc')
            $orber_by = "username DESC";
        else if ($sort == 'since_asc')
            $orber_by = "id ASC";
        else if ($sort == 'since_desc')
            $orber_by = "id DESC";

        $list = $page - 1;
        $how = 50;
        if($list > 0)
            $list = $list * $how;

        $dql = 'SELECT u.id, u.username, u.email, u.image, n.number, u.created
            FROM AppUserBundle:User u
            LEFT JOIN AppGuestbookBundle:Number n
            WHERE u.id = n.user AND n.status = 1
            WHERE u.enabled = 1
            ORDER BY u.'.$orber_by;

        $query = $this->getEntityManager()->createQuery($dql)
                ->SetFirstResult($list)
                ->SetMaxResults($how);        

        $result = $query->execute();

        for($i=0;$i<count($result);$i++) {
            if($result[$i]['image'] != '') {
                $info = getimagesize("public/images/users/".$result[$i]['image']);
                if($info[1] >= $info[0]) {
                    $result[$i]['resize'] = 0;
                } else {
                    $result[$i]['resize'] = 1;
                }
            } else {
                $result[$i]['resize'] = 0;
            }
        }

        $pagedql = 'SELECT count(u) FROM AppUserBundle:User u';
        $pagequery = $this->getEntityManager()->createQuery($pagedql);

        $rowcountpage = $pagequery->execute();
        $countpage = ceil($rowcountpage[0][1]/$how);

        $res['users'] = $result;
        $res['countpage'] = $countpage;

        return $res;
    }       

	public function show_roles() {
        $dql = 'SELECT u.id, u.roles
            FROM AppUserBundle:User u';

        $query = $this->getEntityManager()->createQuery($dql);

        $result = $query->execute();

        $roles = [];

        for($i=0;$i<count($result);$i++) {
        	$id = $result[$i]['id'];
        	$ban = $result[$i]['roles'];

        	$roles[$id] = $ban;
        }

        return $roles;
	}

    public function get_users() {
        $dql = "SELECT u.id, u.username FROM AppUserBundle:User u ORDER BY u.username ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->execute();

        $results = [];
        for($i=0;$i<count($result);$i++) {
            $id = $result[$i]['id'];
            $username = $result[$i]['username'];
            $results[$username] = $id;
        }
        return $results;
    }

    public function show_users() {
        $dql = "SELECT u.id, u.username FROM AppUserBundle:User u ORDER BY u.username ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->execute();

        return $result;
    }    

    public function get_admins($type, $assistant, $userId ) {

        $dql = "SELECT u.id, u.username, u.roles FROM AppUserBundle:User u 
        WHERE u.id != :author ORDER BY u.username ASC";

        $query = $this->getEntityManager()->createQuery($dql)
            ->SetParameter('author', $userId);
        $result = $query->execute();

        $results = [];

        if($type == 'partner')
            $needle = 'ROLE_TOURNAMENTS';
        else if($type == 'assistant')
            $needle = 'ROLE_TOURS';

        for($i=0;$i<count($result);$i++) {
            $id = $result[$i]['id'];
            $username = $result[$i]['username'];
            $roles = $result[$i]['roles'];

            if(in_array($needle, $roles)) {
                if(in_array($id, $assistant))
                    $access = 1;
                else
                    $access = 0;

                $results[] = ['id' => $id, 'username' => $username, 'access' => $access];
            }
        }

        return $results;
    }

    public function get_user_info($user) {
        $dql = "SELECT u.id, u.username
                FROM AppUserBundle:User u
                WHERE u.id = :user";

        $query = $this->getEntityManager()->createQuery($dql)
                      ->SetParameter('user', $user);

        $result = $query->execute();

        return $result;
    }    
}