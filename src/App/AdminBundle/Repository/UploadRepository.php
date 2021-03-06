<?php

namespace App\AdminBundle\Repository;

/**
 * UploadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UploadRepository extends \Doctrine\ORM\EntityRepository
{
	public function show_upload() {

		$dql = "SELECT u.id, u.folder, u.image FROM AppAdminBundle:Upload u
				WHERE u.status = 1
				ORDER BY u.id DESC";

		$query = $this->getEntityManager()->createQuery($dql);

		$result = $query->execute();

		$images = [];
		for($i=0;$i<count($result);$i++) {
			$id = $result[$i]['id'];
			$folder = $result[$i]['folder'];
			$img = $result[$i]['image'];

			$images[$folder][] = [$id, $img];
		}
		return $images;
	}

	public function get_upload($id) {
		$dql = "SELECT u.folder, u.image FROM AppAdminBundle:Upload u
				WHERE u.id = :id";

		$query = $this->getEntityManager()->createQuery($dql)
						->SetParameter("id", $id);

		$result = $query->execute();

		if(!empty($result))
			return $result[0];
		else
			return 0;
	}

	public function remove_image($id) {
		$dql = "DELETE FROM AppAdminBundle:Upload u
				WHERE u.id = :id";

		$query = $this->getEntityManager()->createQuery($dql)
						->SetParameter("id", $id);
		$query->execute();
	}

	public function order($id) {

		$order = 0;

		$dql = "SELECT a.id FROM AppGuestbookBundle:Achive a
				WHERE a.image = :id";
		$query = $this->getEntityManager()->createQuery($dql)
							->SetParameter("id", $id);
		$achive = $query->execute();

		if(!empty($achive))
			$order += (int) $achive[0]['id'];

		$dql = "SELECT a.id FROM AppGuestbookBundle:Champion a
				WHERE a.image = :id";
		$query = $this->getEntityManager()->createQuery($dql)
							->SetParameter("id", $id);
		$champion = $query->execute();

		if(!empty($champion))
			$order += (int) $champion[0]['id'];

		return $order;
	}

	public function getUpload($folder) {
		$dql = "SELECT u.id, u.image FROM AppAdminBundle:Upload u
				WHERE u.folder = :folder AND u.status = 1
				ORDER BY u.id DESC";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter('folder', $folder);
		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {
			$id = $result[$i]['id'];
			$image = $result[$i]['image'];
			$results[$image] = $id;
		}

		return $results;
	}

	public function get_files($folder, $page, $posts) {
        $list = $page - 1;
        if($list > 0)
            $list = $list * $posts;

		$dql = "SELECT u.id, u.image FROM AppAdminBundle:Upload u
				WHERE u.folder = :folder AND u.status = 1
				ORDER BY u.id DESC";

		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter('folder', $folder)
                	->SetFirstResult($list)
                	->SetMaxResults($posts);

		$result = $query->execute();

		$results = [];
		for($i=0;$i<count($result);$i++) {
			$id = $result[$i]['id'];
			$image = $result[$i]['image'];
			$results[] = ["id" => $id, "image" => $image];
		}

        $pagedql = "SELECT count(u) FROM AppAdminBundle:Upload u WHERE u.folder = 'upload'";
        $pagequery = $this->getEntityManager()->createQuery($pagedql);

        $result = $query->execute();
        $rowcountpage = $pagequery->execute();
        $countpage = ceil($rowcountpage[0][1]/$posts);

        $r = array($results, $countpage);

		return $r;
	}

	public function update_image_status($id, $status) {
		$dql = "UPDATE AppAdminBundle:Upload i SET i.status = :status
				WHERE i.id = :id";
		$query = $this->getEntityManager()->createQuery($dql)
					->SetParameter("status", $status)
					->SetParameter("id", $id);

		$query->execute();

	}

	public function order_img($id, $image) {

		$type = $image['folder'];
		$name = $image['image'];

		if($type == 'logo') {
			$bundle = 'AppTournamentBundle:Tournament i';
		} else if ($type == 'cup') {
			$bundle = 'AppGuestbookBundle:Champion i';
			$name = $id;
		} else if ($type == 'achive') {
			$bundle = 'AppGuestbookBundle:Achive i';
			$name = $id;
		}

		$dql = "SELECT i.id FROM ".$bundle."
		WHERE i.image = :image";

		$query = $this->getEntityManager()->createQuery($dql)
				->SetParameter('image', $name);

		$result = $query->execute();

		if(empty($result))
			return 0;
		else
			return 1;

	}
}
