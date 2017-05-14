<?php

namespace App\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\TournamentBundle\Entity\Content;
use App\TournamentBundle\Form\ContentType;
use App\GuestbookBundle\Entity\Number;
use App\GuestbookBundle\Form\NumberType;
use App\GuestbookBundle\Entity\Nach;
use App\GuestbookBundle\Form\NachType;
use App\GuestbookBundle\Entity\Achive;
use App\GuestbookBundle\Form\AchiveType;
use App\GuestbookBundle\Entity\Champion;
use App\GuestbookBundle\Form\ChampionType;
use App\AdminBundle\Entity\Upload;
use App\AdminBundle\Form\UploadType;
use App\TournamentBundle\Form\LogoType;
use App\TournamentBundle\Form\StarttournamentType;
use App\TournamentBundle\Form\NameType;
use App\TournamentBundle\Form\ForecastType;
use App\TournamentBundle\Form\ForebridgeType;
use App\TournamentBundle\Form\TeamType;
use App\TournamentBundle\Form\HeadteamType;
use App\TournamentBundle\Form\PlayerType;
use App\TournamentBundle\Form\ChangeplayerType;
use App\TournamentBundle\Form\StatusplayerType;
use App\TournamentBundle\Form\ImageplayerType;
use App\TournamentBundle\Form\TrteamType;
use App\TournamentBundle\Entity\Tournament;
use App\TournamentBundle\Entity\Team;
use App\TournamentBundle\Entity\Trteam;
use App\TournamentBundle\Entity\Headteam;
use App\TournamentBundle\Entity\Player;
use App\TournamentBundle\Entity\Forecast;
use App\TournamentBundle\Entity\Forebridge;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminController extends Controller
{
	private $ban_status;

	public function __construct() {
		$this->ban_status = ['ROLE_BANNED_0', 'ROLE_BANNED_1', 'ROLE_BANNED_2'];
	}

    public function usersAction()
    {
        $user = $this->getUser();
        if($user) {
            $userId = $user->getId();
        } 

        # users activity
        $rep_activity = $this->getDoctrine()->getRepository('AppUserBundle:Activity');
        $users = $rep_activity->show_activity();

        # user roles
        $rep_users = $this->getDoctrine()->getRepository('AppUserBundle:User');
        $roles = $rep_users->show_roles();

        return $this->render('AppAdminBundle:Admin:users.html.twig', array('users' => $users, 'roles' => $roles));
    }

    public function deleteguestbookAction($id_message, Request $request) {
        
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODERATE')) {
	        throw $this->createAccessDeniedException();
        }

        $referer = $request->headers->get('referer');

        $guestbook = strstr($referer, "guestbook");
        $bundle = "AppGuestbookBundle:Guestbook";
        $url = "app_guestbook_show";
        if($guestbook != "guestbook") {

            $guestbook = strstr($referer, "adminbook");
            $bundle = "AppAdminBundle:Adminbook";
            $url = "app_admin_guestbook";
            if($guestbook != "adminbook")
                throw $this->createAccessDeniedException();
        }

        $message = $this->getDoctrine()->getRepository($bundle)->find($id_message);

        $message->setStatus(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->redirect($this->generateUrl($url));

    }

    public function setbanAction(Request $request) {
    	/* if no admin */
	    if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODERATE')) {
	        throw $this->createAccessDeniedException();
	    }

	    /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        if($request->request->get('id') && $request->request->get('role') && $request->request->get('old'))
        {
            $id = (int) $request->request->get('id');
            $role = trim($request->request->get('role'));
            $old = trim($request->request->get('old'));

            /* real role check */
            if(!in_array($role, $this->ban_status))
            	return false;

            /* old role check */
            if(!in_array($old, $this->ban_status))
            	return false;

			$userManager = $this->get('fos_user.user_manager');
			$user_role = $userManager->findUserBy(array('id' => $id));

            $user_role->removeRole($old);
            $user_role->addRole($role);
            $userManager->updateUser($user_role);

            return new Response(json_encode(1));
    	}
    }    

    public function contentAction($type)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODERATE')) {
            throw $this->createAccessDeniedException();
        }

        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        $repository = $this->getDoctrine()->getRepository('AppTournamentBundle:Content');
        $bodycontent = $repository->get_content($type);

        if($form->isSubmitted() && $form->isValid())
        {

            $user = $this->getUser();
            if($user)
                $userId = $user->getId();
            else
                $userId = 0;

            if(!$userId)
                throw new AccessDeniedException('Вам запрещен доступ на эту страницу.');

            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $message = strip_tags($data->getDescription());

            $content->setTitle($bodycontent['title']);
            $content->setChapter($type);
            $content->setDescription($message);
            $content->setAuthor($userId);

            $em->persist($content);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_content', array('type'=> $type)));
        }

        return $this->render('AppAdminBundle:Admin:content.html.twig', array('form'=>$form->createView(), 'content' => $bodycontent, 'type' => $type));
    }    

   public function uploadAction() {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $upload = new Upload();
        $form = $this->createForm(UploadType::class, $upload);

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $file = $data->getImage();
            $folder = $data->getFolder();

            $user = $this->getUser();
            if($user)
                $userId = $user->getId();
            else
                return false;

            $fileName = $this->get('app.image_uploader')->image_upload($file, 'public/images/custom/'.$folder, 50000, 150, 150);

            if($fileName)
            {
                $em = $this->getDoctrine()->getManager();
                $upload->setFolder($folder);
                $upload->setImage($fileName);
                $upload->setAuthor($userId);
                $upload->setStatus(1);
                $em->persist($upload);
                $em->flush();

                return $this->redirect($this->generateUrl('app_admin_upload'));
            }


        }

        $repository = $this->getDoctrine()->getRepository('AppAdminBundle:Upload');
        $images = $repository->show_upload();

        return $this->render('AppAdminBundle:Admin:upload.html.twig',
                        array('form' => $form->createView(),
                              'images' => $images));
    }

    public function deleteuploadAction($id) {
        $repository = $this->getDoctrine()->getRepository('AppAdminBundle:Upload');
        $image = $repository->get_upload($id);

        if($image) {

            // $order = $repository->order($id);
            $order = 0;

            if($order) {
                $repository->update_image_status($id, 0);
            } else {

                $path = "public/images/custom/".$image['folder']."/";
                $this->get('app.image_uploader')->image_delete($image['image'], $path);
                $repository->remove_image($id);
            }

        }

        return $this->redirect($this->generateUrl('app_admin_upload'));
    }

    public function numbersAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $number = new Number();

        $form = $this->createForm(NumberType::class, $number);
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        $repository = $this->getDoctrine()->getRepository('AppGuestbookBundle:Number');

        if($form->isSubmitted() && $form->isValid())
        {

            $user = $this->getUser();
            if($user)
                $userId = $user->getId();
            else
                $userId = 0;

            if(!$userId)
                throw new AccessDeniedException();

            $data = $form->getData();
            $number_id = $data->getNumber();
            $user_id = $data->getUser()->getId();

            $result = $repository->get_numbers($user_id, $number_id);
            if(empty($result)){
                $em = $this->getDoctrine()->getManager();

                $number->setUser($user_id);
                $number->setNumber($number_id);
                $number->setAuthor($userId);
                $em->persist($number);
                $em->flush();
            } else {
               $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Этот номер занят или пользователь уже имеет другой номер.');
            }

            return $this->redirect($this->generateUrl('app_admin_numbers'));
        }

        $number_users = $repository->show_users_numbers();

        return $this->render('AppAdminBundle:Admin:numbers.html.twig',
            array('form' => $form->createView(), 'number_users' => $number_users));
    }    

    public function deleteAction($content, $numb)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if($numb){
            $repository = $this->getDoctrine()->getRepository('AppGuestbookBundle:'.$content);
            $number = $repository->findOneById($numb);
            if($number){
                $em = $this->getDoctrine()->getManager();
                $em->remove($number);
                $em->flush();
            }
        }

        if($content == 'Number')
            return $this->redirect($this->generateUrl('app_admin_numbers'));
        else if ($content == 'Nach')
            return $this->redirect($this->generateUrl('app_admin_nach'));
        else if ($content == 'Achive')
            return $this->redirect($this->generateUrl('app_admin_achives'));
        else if ($content == 'Champion')
            return $this->redirect($this->generateUrl('app_admin_champions'));
    }    

    public function nachAction(){
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $nach = new Nach();

        $form = $this->createForm(NachType::class, $nach);
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        $repository = $this->getDoctrine()->getRepository('AppGuestbookBundle:Nach');

        if($form->isSubmitted() && $form->isValid())
        {

            $user = $this->getUser();
            if($user)
                $userId = $user->getId();
            else
                $userId = 0;

            if(!$userId)
                throw new AccessDeniedException();

            $data = $form->getData();
            $number_id = $data->getNumber();
            $title = $data->getTitle();
            $description = $data->getDescription();

            $result = $repository->order_nach($number_id);
            if(empty($result)){
                $em = $this->getDoctrine()->getManager();

                $nach->setTitle($title);
                $nach->setDescription($description);
                $nach->setNumber($number_id);
                $nach->setAuthor($userId);
                $em->persist($nach);
                $em->flush();
            } else {
               $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Нах под этим номером уже есть.');
            }

            return $this->redirect($this->generateUrl('app_admin_nach'));
        }

        $allnach = $repository->show_nach();

        return $this->render('AppAdminBundle:Admin:nach.html.twig', array('form' => $form->createView(), 'nachs' => $allnach));
    }

    public function achivesAction() {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;


        $achive = new Achive();

        $field = $this->getDoctrine()->getRepository('AppAdminBundle:Upload');
        $users = $this->getDoctrine()->getRepository('AppUserBundle:User');

        $form = $this->createForm(AchiveType::class, $achive, array('data' => array($field->getUpload('achive'),
                                                                    $users->get_users())));
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $id_user = $data['user'];
            $description = $data['description'];
            $image = $data['image'];

            if(!$image) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Выберите изображение.');
                return $this->redirect($this->generateUrl('app_admin_achives'));
            }

            $em = $this->getDoctrine()->getManager();
            $achive->setUser($id_user);
            $achive->setDescription($description);
            $achive->setImage($image);
            $achive->setAuthor($userId);
            $em->persist($achive);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_achives'));
        }

        $rep = $this->getDoctrine()->getRepository('AppGuestbookBundle:Achive');
        $achives = $rep->get_achives();

        return $this->render('AppAdminBundle:Admin:achives.html.twig',
            array('form' => $form->createView(), 'achives' => $achives));
    }

    public function championsAction() {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;


        $champion = new Champion();

        $field = $this->getDoctrine()->getRepository('AppAdminBundle:Upload');
        $users = $this->getDoctrine()->getRepository('AppUserBundle:User');

        $form = $this->createForm(ChampionType::class, $champion, array('data' => array($field->getUpload('cup'),
                                                                    $users->get_users())));
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $id_user = $data['user'];
            $year = $data['year'];
            $description = $data['description'];
            $image = $data['image'];

            if(!$image) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Выберите изображение.');
                return $this->redirect($this->generateUrl('app_admin_champions'));
            }

            $em = $this->getDoctrine()->getManager();
            $champion->setUser($id_user);
            $champion->setYear($year);
            $champion->setDescription($description);
            $champion->setImage($image);
            $champion->setAuthor($userId);
            $em->persist($champion);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_champions'));
        }

        $rep = $this->getDoctrine()->getRepository('AppGuestbookBundle:Champion');
        $champions = $rep->get_champions();

        return $this->render('AppAdminBundle:Admin:champions.html.twig',
            array('form' => $form->createView(), 'champions' => $champions));
    }    

    public function tournamentsAction($page) {
        $status = 1;

        $result = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournaments_list($page, $status);

        $tournaments = $result[0];
        $count = $result[1];

        return $this->render("AppAdminBundle:Tournament:tournaments.html.twig",
                array("tournaments" => $tournaments, 'countpage' => $count, 'page' => $page));
    }

    public function tournamentAction($tournament) {

        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;

        $name = new Tournament();

        /* name of tournament */
        $form1 = $this->createForm(NameType::class, $name);

        $request = Request::createFromGlobals();
        $form1->handleRequest($request);

        if($form1->isSubmitted() && $form1->isValid()) {
            $newname = $form1->getData()->getName();

            $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);
            $tr->setName($newname);

            $em = $this->getDoctrine()->getManager();
            $em->persist($tr);
            $em->flush();

            return $this->redirect($this->generateUrl("app_admin_tournament", array("tournament" => $tournament)));


        }

        /* position of tournament */
        $form_pos = $this->createFormBuilder()
                    ->add("start", TextType::class, array("attr" => array('NotBlank')))
                    ->add("end", TextType::class, array("attr" => array('NotBlank')))
                    ->add('save_position', SubmitType::class, array('label' => 'Установить'))
                    ->getForm();

        if($request->isMethod("POST")) {
            $form_pos->handleRequest($request);

            if($form_pos->get('save_position')->isClicked()) {
                $data = $form_pos->getData();

                $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

                $info = $tr->getInfo();

                $info['start'] = (int) $data['start'];
                $info['end'] = (int) $data['end'];

                $tr->setInfo($info);

                $em = $this->getDoctrine()->getManager();
                $em->persist($tr);
                $em->flush();
            }
        }

        /* set archive of tournament */
        $form_archive = $this->createFormBuilder()
            ->add('save_archive', SubmitType::class, array('label' => 'Перевести турнир в архив'))
            ->getForm();

        /* set delete of tournament */
        $form_delete = $this->createFormBuilder()
            ->add('save_delete', SubmitType::class, array('label' => 'Удалить турнир'))
            ->getForm();

        if($request->isMethod("POST")) {
            $form_archive->handleRequest($request);

            $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

            if($tournamentshow['access']['creator'] != $userId)
                throw $this->createAccessDeniedException();            

            if($form_archive->get('save_archive')->isClicked()) {
                $data = $form_archive->getData();

                $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);
                $tr->setStatus(2);

                $em = $this->getDoctrine()->getManager();
                $em->persist($tr);
                $em->flush();
            }

            $form_delete->handleRequest($request);

            if($form_delete->get('save_delete')->isClicked()) {
                $data = $form_delete->getData();

                $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);
                $tr->setStatus(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($tr);
                $em->flush();

                return $this->redirect($this->generateUrl("app_admin_tournaments"));
            }
        }


        $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if(empty($tr))
            return $this->redirect($this->generateUrl("app_admin_tournaments"));

        if($userId == $tr['access']['creator'])
            $access = 2;
        else if(in_array($userId, $tr['access']['assistant']))
            $access = 1;
        else
            $access = 0;

        $headteam = $this->getDoctrine()->getRepository('AppTournamentBundle:Trteam')->get_team($tournament);

        if($tr['status'] == 1 or $tr['status'] == 3 or $tr['status'] == 4) {

            return $this->render("AppAdminBundle:Tournament:tournament.html.twig",
                    array("tournament" => $tr, 'form_name' => $form1->createView(),
                          "form_pos" => $form_pos->createView(),
                          "form_archive" => $form_archive->createView(),
                          "form_delete" => $form_delete->createView(),
                          "access" => $access, 'headteam' => $headteam,
                          ));

        } else {

            return $this->render("AppAdminBundle:Tournament:notournament.html.twig",
                    array("tournament" => $tr));
        }
    }

    public function createAction() {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if($user) {
            $userId = $user->getId();
        } 

        $tournament = new Tournament;

        $form = $this->createForm(StarttournamentType::class, $tournament);

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $name = strip_tags($data->getName());
            $types = $data->getTypes();

            if(!$name) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Введите название турнира.');
                return $this->redirect($this->generateUrl('app_admin_create'));
            }

            $tournament = new Tournament();
            $tournament->setName($name);
            $tournament->setTypes($types);
            $tournament->setAccess(array('creator' => $userId, 'assistant' => array(0)));
            $tournament->setStatus(4);

            $em = $this->getDoctrine()->getManager();
            $em->persist($tournament);
            $em->flush();

            $tournament_id = $tournament->getId();

            return $this->redirect($this->generateUrl('app_admin_tournament', array("tournament" => $tournament_id)));

        }

        return $this->render("AppAdminBundle:Tournament:create.html.twig",
            array("form" => $form->createView()));
    }

    public function logotypeAction($tournament) {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;

       $id = $user->getId();

        $em = $this->getDoctrine()->getManager();

        $image = new Tournament();

        $field = $this->getDoctrine()->getRepository('AppAdminBundle:Upload');

        $form = $this->createForm(LogoType::class, $image, array('data' => array($field->getUpload('logo'))));

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $image_for_tournament = $data['image'];

            $img_obj = $this->getDoctrine()->getRepository("AppAdminBundle:Upload")->find($image_for_tournament);
            $img_name = $img_obj->getImage();

            if(!$image) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Выберите изображение.');
                return $this->redirect($this->generateUrl('app_admin_logo', array("tournament" => $tournament)));
            }

            $img = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);
            $img->setImage($img_name);
            $em->persist($img);
            $em->flush();

            return $this->redirect($this->generateUrl("app_admin_logo", array("tournament" => $tournament)));
        }

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            throw $this->createAccessDeniedException();

        return $this->render('AppAdminBundle:Tournament:logo.html.twig',
            array('form' => $form->createView(), 'tournament' => $tournamentshow));
    }    

    public function teamsAction($tournament) {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;

       $id = $user->getId();

        $em = $this->getDoctrine()->getManager();

        $teams = new Team();
        $form = $this->createForm(TeamType::class, $teams);

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $team = trim($data->getName());

            if(!$team) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Введите название команды.');
                return $this->redirect($this->generateUrl('app_admin_addteam', array("tournament" => $tournament)));
            }

            $teams = new Team();

            $teams->setName($team);
            $teams->setTournament($tournament);
            $teams->setStatus(1);
            $teams->setAuthor($userId);

            $em = $this->getDoctrine()->getManager();
            $em->persist($teams);
            $em->flush();

            return $this->redirect($this->generateUrl("app_admin_addteam", array("tournament" => $tournament)));
        }

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            throw $this->createAccessDeniedException();

        $teams = $this->getDoctrine()->getRepository("AppTournamentBundle:Team")->getTeams($tournament);

        return $this->render('AppAdminBundle:Tournament:teams.html.twig',
            array('form' => $form->createView(), 'teams' => $teams,
                'tournament' => $tournamentshow));
    }

    public function deleteteamAction($tournament, $numb)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if($numb){
            $repository = $this->getDoctrine()->getRepository('AppTournamentBundle:Team');
            $number = $repository->findOneById($numb);
            if($number){
                $em = $this->getDoctrine()->getManager();
                $em->remove($number);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('app_admin_addteam', array("tournament" => $tournament)));
    }

    public function replaceusersAction($tournament) {

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            throw $this->createAccessDeniedException();

        if($tournamentshow['status'] == 3 or $tournamentshow['status'] == 4) {

            /* if no user */
            $user = $this->getUser();
            if(!$user)
                $userId = 0;
            else
                $userId = $user->getId();

            $repo = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournamentusers");
            $res = $repo->show_users_for_tournament($tournament, $userId);
            $preusers = $res[0];

            $form = $this->createFormBuilder()
              ->add('user', CollectionType::class, array(
                    'entry_type' => HiddenType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'constraints' => new NotBlank(),
                    ))
              ->add('save', SubmitType::class, array('label' => 'Создать турнир'))
              ->getForm();

            $request = Request::createFromGlobals();
            if($request->isMethod("POST")) {
                $form->handleRequest($request);

                if($form->get('save')->isClicked()) {
                    $data = $form->getData();

                    $info = $tournamentshow['info'];
                    $tr_id = $tournamentshow['id'];


                    if($info['users'] == count($data['user'])) {
                        $users = $data['user'];

                        if($info['schema'] == 1){
                            $this->get("app.create_tournament")->createCalendar($tr_id, $info['circle'], $users, 0);
                        } else if($info['schema'] == 2){
                            $this->get("app.create_tournament")->createPlayOff($tr_id, $users, 0);
                        } else if($info['schema'] == 3){
                            $chunkusers = array_chunk($users, $info['usergroups']);
                            for($i=0;$i<count($chunkusers);$i++)
                                $this->get("app.create_tournament")->createCalendar($tr_id, $info['circle'], $chunkusers[$i], $i+1);
                        }

                        // set active users //
                        $repo->set_new_status($tournament, $users);

                        $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

                        $tr->setStatus(1);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($tr);
                        $em->flush();

                        return $this->redirect($this->generateUrl("app_admin_tournament", array("tournament" => $tournament)));

                    } else {
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Турнир не может быть создан. Повторите шаги снова.');

                        return $this->generateUrl("app_admin_replaceuser", array("tournament" => $tournament));
                    }
                }
            }

            return $this->render('AppAdminBundle:Tournament:preliminaryusers.html.twig',
                    array("tournament" => $tournamentshow,
                          "preusers" => $preusers,
                          "form" => $form->createView()));

        } else if($tournamentshow['status'] == 1) {

            $request = Request::createFromGlobals();
            if($request->isMethod("POST")) {
                if($request->request->get('replace_user')) {

                    $inuser = (int) $request->request->get('inuser');
                    $newuser = (int) $request->request->get('newuser');

                    if($inuser && $newuser) {

                        $this->getDoctrine()->getRepository('AppTournamentBundle:Tournamentusers')->replace($tournament, $inuser, $newuser);

                    } else {
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Нужно выбрать пользователей.');
                    }

                }
            }


            $users = $this->getDoctrine()->getRepository('AppTournamentBundle:Tournamentusers')->users_for_tournament($tournament);
            
            $newusers = $this->getDoctrine()->getRepository('AppTournamentBundle:Tournamentusers')->users_without_tournament($tournament);
            return $this->render('AppAdminBundle:Tournament:replace.html.twig',
                    array("tournament" => $tournamentshow,
                          "users" => $users,
                          "newusers" => $newusers
                          ));
        }
    }

    public function accessAction($tournament) {

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;

        $repository = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament");
        $admin_repository = $this->getDoctrine()->getRepository("AppUserBundle:User");

        $tournamentshow = $repository->show_tournament_for_admin($tournament);

        $admins = $admin_repository->get_admins($tournamentshow['access']['assistant'], $userId);

        if($tournamentshow['access']['creator'] != $userId)
            throw $this->createAccessDeniedException();

            return $this->render('AppAdminBundle:Tournament:access.html.twig',
                array('tournament' => $tournamentshow, 'admins' => $admins));
    }

    public function setaccessAction(Request $request) {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            throw $this->createAccessDeniedException();

        if($request->request->get('tr') && $request->request->get('user') && $request->request->get('status'))
        {
            $tr = (int) $request->request->get('tr');
            $accessuser = (int) $request->request->get('user');
            $status = $request->request->get('status');

            $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tr);

            if($tournamentshow['access']['creator'] != $userId)
                throw $this->createAccessDeniedException();

            if($status == 'true') {
                if(!in_array($accessuser, $tournamentshow['access']['assistant']))
                    $tournamentshow['access']['assistant'][] = $accessuser;
            } else if ($status == 'false') {
                if(($key = array_search($accessuser, $tournamentshow['access']['assistant'])) == true)
                    unset($tournamentshow['access']['assistant'][$key]);
            } else {
                throw $this->createAccessDeniedException();
            }

            $newaccess = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tr);

            $newaccess->setAccess($tournamentshow['access']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newaccess);
            $em->flush();           

            return new Response(json_encode(1));
        }        

    }

    public function completedAction($tournament) {

        /* if no user */
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();
        else
            return false;

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            throw $this->createAccessDeniedException();


        $form = $this->createFormBuilder()
                    ->add("schema", ChoiceType::class, array("attr" => array('NotBlank'),
                        "choices" => array(
                                "Лига" => 1,
                                "Плей-офф" => 2,
                                "Групповой этап + плей-офф" => 3,
                            ),
                        "expanded" => true,
                        "data" => 1))
                    ->add("users", TextType::class, array("attr" => array('NotBlank')))
                    ->add("circle", TextType::class)
                    ->add("groups", TextType::class)
                    ->add("usergroups", TextType::class)
                    ->add('create_tournament', SubmitType::class, array('label' => 'Продолжить',
                        'attr' => array("formnovalidate" => "formnovalidate")))
                    ->getForm();

        $request = Request::createFromGlobals();

        if($request->isMethod("POST")) {
            $form->handleRequest($request);

            if($form->get('create_tournament')->isClicked()) {
                $data = $form->getData();

                $schema = (int) $data['schema'];

                $error = 0;

                if($schema < 1 or $schema > 3) {
                    $error += 1;
                    $session = $this->get('session');
                    $session
                        ->getFlashBag()
                        ->add('error', 'Неправильный тип турнира.');
                    // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                }

                $users = (int) $data['users'];

                if($schema == 1) {
                    $circle = (int) $data['circle'];

                    if($circle < 1) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Должен быть как минимум один круг');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }

                    if($users < 2 or ($users % 2) != 0) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Неправильное количество участников.');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }
                }

                if($schema == 2) {
                    $users_for_schema2 = [2, 4, 8, 16, 32, 64, 128, 256];

                    if(!in_array($users, $users_for_schema2)) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Неправильное количество участников. Можно выбрать 2, 4, 8, 16, 32 и т.д. участников.');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }
                }

                if($schema == 3) {
                    $groups = (int) $data['groups'];
                    $usergroups = (int) $data['usergroups'];
                    $circle = (int) $data['circle'];

                    if($circle < 1) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Должен быть как минимум один круг');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }

                    if($users < 2 or ($users % 2) != 0) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Неправильное количество участников.');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }

                    if($groups < 1) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Должна быть как минимум одна группа.');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }

                    if(($groups * $usergroups) != $users) {
                        $error += 1;
                        $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Неправильное соотношение групп и участников.');
                        // return $this->redirect($this->generateUrl("app_admin_completed", array("tournament" => $tournament)));
                    }
                }
            }

            if(!$error) {

                $info = $tournamentshow['info'];

                if(!empty($info))
                    $result = array_merge($info, $data);
                else
                    $result = $data;

                $tr = $this->getDoctrine()->getRepository('AppTournamentBundle:Tournament')->find($tournament);
                $tr->setInfo($result);
                $tr->setStatus(3);

                $em = $this->getDoctrine()->getManager();
                $em->persist($tr);
                $em->flush();

                return $this->redirect($this->generateUrl('app_admin_replaceuser', array('tournament'=> $tournament)));
            }
        }

            return $this->render('AppAdminBundle:Tournament:completed.html.twig',
                    array("tournament" => $tournamentshow,
                          "form" => $form->createView()));
    }    

    public function toursAction($tournament, $tour, Request $request) {

        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            if(!in_array($userId, $tournamentshow['access']['assistant']))
                throw $this->createAccessDeniedException();

        if($tour == 0)
            $tour = $this->getDoctrine()->getRepository("AppTournamentBundle:Tablelist")->get_actual_tour($tournament);

        if($tour == 0)
            $tour = 1;            

        $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

        $rep_calendar = $this->getDoctrine()->getRepository("AppTournamentBundle:Calendar");
        $calendar = $rep_calendar->get_calendar($tournament);

        $forebridge = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->getForeBridge($tournament, $tour);
        if($forebridge)
            $fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast")->get_forecast($forebridge);
        else
            $fore = 0;

        $teams = $this->getDoctrine()->getRepository("AppTournamentBundle:Team")->getTeams($tournament);

        // add tours
        if($request->isMethod("POST")) {
            if($request->request->has('create_score')) {

                $date = $request->request->get('date');
                $team1 = $request->request->get('team1');
                $team2 = $request->request->get('team2');

                if($forebridge) {
                    $hash = $forebridge;
                    $br = 1;
                } else {
                    $hash = substr(sha1(uniqid()), 0, 9);
                    $br = 0;
                }

                $em = $this->getDoctrine()->getEntityManager();
                for($i=0;$i<count($date);$i++) {

                    if(empty($date[$i]) or empty($team1[$i]) or empty($team2[$i])) {
                       $session = $this->get('session');
                        $session
                            ->getFlashBag()
                            ->add('error', 'Заполните все поля');

                        return $this->redirect($this->generateUrl('app_admin_tours', array('tournament'=> $tournament, 'tour' => $tour)));
                    }

                    $date[$i] = $this->get('app.date_mode')->locale_to_utc($date[$i]);
                    $team1[$i] = trim(strip_tags($team1[$i]));
                    $team2[$i] = trim(strip_tags($team2[$i]));

                    $forecast = new Forecast();
                    $forecast->setHash($hash);
                    $forecast->setTeam1($team1[$i]);
                    $forecast->setTeam2($team2[$i]);
                    $forecast->setTimer($date[$i]);
                    $em->persist($forecast);
                }

                if(!$br) {
                    $forebridge = new Forebridge();
                    $forebridge->setHash($hash);
                    $forebridge->setTr($tournament);
                    $forebridge->setTour($tour);
                    $em->persist($forebridge);

                    $this->get('app.results_tournament')->add_tablelist($tournament, $tour);

                }
                $em->flush();

                if($tournamentshow['types'] == 2) {
                    $this->get('app.results_tournament')->add_playerslist($tournament, $hash);
                }                
                
                $this->get('app.results_tournament')->add_howgame($tournament, $tour, $hash);

                return $this->redirect($this->generateUrl('app_admin_tours', array('tournament'=> $tournament, 'tour' => $tour)));
            }

        // update tours
            if($request->request->has('update_score')) {

                $id_fore = $request->request->get('fore');
                $date = $request->request->get('upd_date');
                $team1 = $request->request->get('upd_team1');
                $team2 = $request->request->get('upd_team2');

                $em = $this->getDoctrine()->getEntityManager();
                for($i=0;$i<count($date);$i++) {

                    $id_fore[$i] = $id_fore[$i];
                    $date[$i] = $this->get('app.date_mode')->locale_to_utc($date[$i]);
                    $team1[$i] = trim(strip_tags($team1[$i]));
                    $team2[$i] = trim(strip_tags($team2[$i]));

                    $upd_fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast")->find($id_fore[$i]);

                    $upd_fore->setTeam1($team1[$i]);
                    $upd_fore->setTeam2($team2[$i]);
                    $upd_fore->setTimer($date[$i]);

                    $em->persist($upd_fore);
                }

                $em->flush();

                return $this->redirect($this->generateUrl('app_admin_tours', array('tournament'=> $tournament, 'tour' => $tour)));
            }
        }        

        $playoff_name = ['1' => 'ФИНАЛ', '2' => '1/2 ФИНАЛА', '4' => '1/4 ФИНАЛА', '8' => '1/8 ФИНАЛА',
                             '16' => '1/16 ФИНАЛА', '32' => '1/32 ФИНАЛА', '64' => '1/64 ФИНАЛA',
                             '128' => '1/128 ФИНАЛA', '256' => '1/256 ФИНАЛA'];

        // tour name
        $get_tour = $calendar[$tour];
        $off = $get_tour['off'];

        if($off != 0)
            $printtour = array("tour" => $tour, "name" => $playoff_name[$off]);
        else
            $printtour = array("tour" => $tour, "name" => $tour." ТУР");

        $offstatus = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_off_status($tr, $tour);

        return $this->render("AppAdminBundle:Tournament:tours.html.twig",
                array("calendar" => $calendar,
                      "tournament" => $tr, "tour" => $tour,
                      "printtour" => $printtour,
                      "off" => $offstatus,
                      "calendar" => $calendar,
                      "playoff" => $playoff_name,                      
                      "forecast" => $fore, 'teams' => $teams));
    }    

    public function addtoursAction($tournament, $tour, Request $request) {
        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            if(!in_array($userId, $tournamentshow['access']['assistant']))
                throw $this->createAccessDeniedException();

        if($tour == 0)
            $tour = 1;

        // add tours
        if($request->isMethod("POST")) {
            if($request->request->has('addtours')) {

                $forebridge = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->getForeBridge($tournament, $tour);

                if($forebridge) {
                   $session = $this->get('session');
                    $session
                        ->getFlashBag()
                        ->add('error', 'Невозможно добавить тур.');

                    return $this->redirect($this->generateUrl('app_admin_addtours', array('tournament'=> $tournament, 'tour' => $tour)));                        
                }

                $hash = $request->request->get('hash');

                $em = $this->getDoctrine()->getEntityManager();

                $forebridge = new Forebridge();
                $forebridge->setHash($hash);
                $forebridge->setTr($tournament);
                $forebridge->setTour($tour);
                $em->persist($forebridge);
                $em->flush();

                $this->get('app.results_tournament')->add_tablelist($tournament, $tour);

                return $this->redirect($this->generateUrl('app_admin_tours', array('tournament'=> $tournament, 'tour' => $tour)));
            }
        }    

        $forecast = $this->getDoctrine()->getRepository('AppTournamentBundle:Forecast')->getActiveTours($tournamentshow['types']);

        

        return $this->render("AppAdminBundle:Tournament:addtours.html.twig", array(
                "tournament" => $tournamentshow, "forecast" => $forecast, "tour" => $tour
            ));        
    }

    public function tourcompletedAction($tournament, $tour, Request $request) {

        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tournament);

        if($tournamentshow['access']['creator'] != $userId)
            if(!in_array($userId, $tournamentshow['access']['assistant']))
                throw $this->createAccessDeniedException();

        if($tour == 0)
            $tour = 1;

        // add fore
        if($request->isMethod("POST")) {
            if($request->request->has('set_fore')) {

                $idfore = $request->request->get('id');
                $r1 = $request->request->get('r1');
                $r2 = $request->request->get('r2');
                $fore_end = $request->request->get('fore_end');

                $em = $this->getDoctrine()->getManager();

                for($i=0;$i<count($idfore);$i++) {
                    $idfore[$i] = (int) $idfore[$i];

                    $forecast = $this->getDoctrine()->getRepository('AppTournamentBundle:Forecast')->find($idfore[$i]);
                    $hash = $forecast->getHash();
                    if($r1[$i] !== "" or $r2[$i] !== "") {
                        $r1[$i] = (int) $r1[$i];
                        $r2[$i] = (int) $r2[$i];

                        $forecast->setResult1($r1[$i]);
                        $forecast->setResult2($r2[$i]);
                        $em->persist($forecast);

                        $this->get('app.results_tournament')->mathem($idfore[$i], $r1[$i], $r2[$i]);

                    }
                }
                $em->flush();

                // Вносим результат второго турнира
                if($tournamentshow['types'] == 2) {

                    $player = $request->request->get('player');
                    $first = $request->request->get('first');
                    $second = $request->request->get('second');
                    $three = $request->request->get('three');

                    for($i=0;$i<count($player);$i++) {

                        $scoreid = $this->getDoctrine()->getRepository('AppTournamentBundle:Forescored')->findOneBy(array('hash' => $hash, 'player' => $player[$i]));

                        $scoreid->setFirst($first[$i]);
                        $scoreid->setSecond($second[$i]);
                        $scoreid->setThree($three[$i]);

                        $em->persist($scoreid);
                    }

                    $em->flush();
                    
                    $this->get('app.results_tournament')->calc_forescored($tournament, $tour, $hash);
                }

                // Рассчитать турнир
                if($fore_end)
                    $this->get('app.results_tournament')->completed_tour($hash);

            }
        }

        $forebridge = $this->getDoctrine()->getRepository("AppTournamentBundle:Forebridge")->getForeBridge($tournament, $tour);

        if($forebridge) {

            if($tournamentshow['types'] == 1) {
                $fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast")->get_forecast($forebridge);
            } else if ($tournamentshow['types'] == 2) {

                $fore = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast")->get_forecast($forebridge);

                $scored = $this->getDoctrine()->getRepository("AppTournamentBundle:Forescored")->get_forescored($forebridge);

            }

        } else {
            $fore = 0;
        }

        if($tournamentshow['types'] == 1) {
            return $this->render('AppAdminBundle:Tournament:tourcompleted.html.twig',
                array('tour' => $tour, 'tournament' => $tournamentshow, "forecast" => $fore));
        } else if ($tournamentshow['types'] == 2) {

            return $this->render('AppAdminBundle:Tournament:scoredcompleted.html.twig',
                array('tour' => $tour, 'tournament' => $tournamentshow, "forecast" => $fore, "scored" => $scored));
        }
    }    

   public function playoffAction($tr, $tour) {

        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tr);

        if($tournamentshow['access']['creator'] != $userId)
            if(!in_array($userId, $tournamentshow['access']['assistant']))
                throw $this->createAccessDeniedException();

        $tournament = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tr);

        $repo = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournamentusers");
        $res = $repo->show_playoff_users($tr, $userId);
        $preusers = $res[0];

            $form = $this->createFormBuilder()
              ->add('user', CollectionType::class, array(
                    'entry_type' => HiddenType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'constraints' => new NotBlank(),
                    ))
              ->add('save', SubmitType::class, array('label' => 'Создать'))
              ->getForm();

            $info = $tournamentshow['info'];

            if($info['schema'] != 3 and $info['schema'] != 2) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Плей-офф не доступен для этого турнира.');

                    return $this->redirect($this->generateUrl("app_admin_playoff", array("tr" => $tr)));                    
            }


            $request = Request::createFromGlobals();
            if($request->isMethod("POST")) {
                $form->handleRequest($request);

                if($form->get('save')->isClicked()) {
                    $data = $form->getData();

                    $tr_id = $tournament->getId();

                    $repository_calendar = $this->getDoctrine()->getRepository("AppTournamentBundle:Calendar");

                    $is_playoff = $repository_calendar->is_playoff($tr);

                    if($is_playoff) {

                        if($tour) {

                            $users = $data['user'];
                            $result_added = $this->get("app.create_tournament")->updatePlayOff($tr_id, $users, $tour);

                            if(!$result_added) {
                                $session = $this->get('session');
                                $session
                                    ->getFlashBag()
                                    ->add('error', 'Неправильно количество участников.');

                                return $this->redirect($this->generateUrl("app_admin_playoff", array("tr" => $tr, 'tour' => $tour)));
                            }


                        } else {

                            $session = $this->get('session');
                            $session
                                ->getFlashBag()
                                ->add('error', 'Плей-офф уже добавлен для этого турнира.');

                        return $this->redirect($this->generateUrl("app_admin_playoff", array("tr" => $tr)));
                        }

                    } else {

                        $max_tour = $repository_calendar->get_max_tour($tr);

                        $users = $data['user'];
                        $this->get("app.create_tournament")->createPlayOff($tr_id, $users, $max_tour);

                        $info['playoff'] = 1;

                        $tournament->setInfo($info);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($tournament);
                        $em->flush();

                    }
                    return $this->redirect($this->generateUrl("app_admin_tournament", array("tournament" => $tr)));

                }
            }

            $playtours = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_tours_with_playoff($tr);

            return $this->render('AppAdminBundle:Tournament:playoff.html.twig',
                array("tournament" => $tournamentshow, "preusers" => $preusers,
                          "form" => $form->createView()));

    }    

    public function playtours($tournament, $tour) {
        return $this->render('AppAdminBundle:Tournament:playtours.html.twig');
            // array("tournament" => $tournamentshow));
    }

    public function headteamAction($tr) {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournament_for_admin($tr);

        if($tournamentshow['access']['creator'] != $userId)
            if(!in_array($userId, $tournamentshow['access']['assistant']))
                throw $this->createAccessDeniedException();

        if($tournamentshow['types'] != 2)
                throw $this->createAccessDeniedException();

        $team = $this->getDoctrine()->getRepository('AppTournamentBundle:Trteam')->get_team($tr);
        $other = $this->getDoctrine()->getRepository('AppTournamentBundle:Trteam')->get_teams($tr);

        $newteam = new Trteam();
        $form = $this->createForm(TrteamType::class, $newteam, array('data' => $other));

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $newheadteam = (int) $data['team'];
            
            $thisteam = $this->getDoctrine()->getRepository('AppTournamentBundle:Trteam')->findOneBy(array('tr' => $tr, 'team' => $team));

                $em = $this->getDoctrine()->getManager();
            if($thisteam) {
                $thisteam->setTeam($newheadteam);
                $em->persist($thisteam);
            } else {
                $newteam->setTr($tr);
                $newteam->setTeam($newheadteam);
                $newteam->setStatus(1);
                $newteam->setAuthor($userId);
                $em->persist($newteam);
            }
                $em->flush();

                return $this->redirect($this->generateUrl("app_admin_headteam", array("tr" => $tr)));
        }        

        return $this->render('AppAdminBundle:Tournament:headteam.html.twig', 
            array('team' => $team, 'form' => $form->createView(), 'tournament' => $tournamentshow));
    }

    public function headteamsAction() {
        /* if no admin */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if($user)
            $userId = $user->getId();

        $teams = new Headteam();
        $form = $this->createForm(HeadteamType::class, $teams);

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $team = trim($data->getName());

            if(!$team) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Введите название команды.');
                return $this->redirect($this->generateUrl('app_admin_headteams'));
            }

            $result = $this->getDoctrine()->getRepository('AppTournamentBundle:Headteam')->showHeadTeam($team);

            if($result) {
                $session = $this->get('session');
                $session
                    ->getFlashBag()
                    ->add('error', 'Команда уже существует.');
                return $this->redirect($this->generateUrl('app_admin_headteams'));
            }
            
            $em = $this->getDoctrine()->getManager();
            $teams->setName($team);
            $teams->setAuthor($userId);
            $teams->setStatus(1);
            $em->persist($teams);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_headteams'));            

        }

        $heads = $this->getDoctrine()->getRepository('AppTournamentBundle:Headteam')->get_teams();

        return $this->render('AppAdminBundle:Tournament:headteams.html.twig',
            array('form' => $form->createView(), 'teams' => $heads));
    }

    public function teamAction($team) {

        $info = $this->getDoctrine()->getRepository('AppTournamentBundle:Headteam')->get_team($team);

        $players = $this->getDoctrine()->getRepository('AppTournamentBundle:Player')->get_players($team);

        $player = new Player();

        $form = $this->createForm(PlayerType::class, $player);

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $first = mb_strtolower($data->getFirst());
            $first = ucfirst($first);
            $second = mb_strtoupper($data->getSecond());
            $position = $data->getPosition();

            $em = $this->getDoctrine()->getManager();
            $player->setTeam($team);
            $player->setFirst($first);
            $player->setSecond($second);
            $player->setPosition($position);
            $player->setStatus(1);
            $em->persist($player);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_team', array('team' => $team))); 
        }

        return $this->render('AppAdminBundle:Tournament:team.html.twig',
            array('team' => $info, 'players' => $players, 'form' => $form->createView()));
    }

    public function playerAction($team, $player, Request $request) {

        $footballer = $this->getDoctrine()->getRepository('AppTournamentBundle:Player')->find($player);

        if(empty($footballer))
            throw $this->createAccessDeniedException();
             
        $pl = new Player();
        $pl->setFirst(ucfirst($footballer->getFirst()));
        $pl->setSecond(mb_strtoupper($footballer->getSecond()));
        $pl->setPosition($footballer->getPosition());

        $oldimage = $footballer->getImage();

        $form = $this->createForm(ChangeplayerType::class, $pl);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $footballer->setFirst(mb_strtolower($pl->getFirst()));
            $footballer->setSecond(mb_strtolower($pl->getSecond()));
            $footballer->setPosition($pl->getPosition());
            $em->persist($footballer);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_player', array('team' => $team, 'player' => $player))); 
        }

        $fimage = new Player();

        $formimage = $this->createForm(ImageplayerType::class, $fimage);

        $formimage->handleRequest($request);

        if($formimage->isSubmitted() && $formimage->isValid()) {

            $file = $fimage->getImage();
        //     # upload file
            $fileName = $this->get('app.image_uploader')->image_upload($file, 'public/images/players', 50000, 150, 150);

        //     # if file is upload, write in db
            if($fileName) {

                # delete old image
                if($oldimage)
                    $this->get('app.image_uploader')->image_delete($oldimage, 'public/images/players');

                $em = $this->getDoctrine()->getManager();

                # set new image in db
                $footballer->setImage($fileName);
                $em->persist($footballer);
                $em->flush();

                return $this->redirect($this->generateUrl('app_admin_player', array('team' => $team, 'player' => $player)));
            }

        }

        $playerstatus = new Player();
        $formstatus = $this->createForm(StatusplayerType::class, $playerstatus);
        $formstatus->handleRequest($request);

        if($formstatus->isSubmitted() && $formstatus->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $footballer->setStatus(0);
            $em->persist($footballer);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_team', array('team' => $team))); 
        }        

        $p = $this->getDoctrine()->getRepository('AppTournamentBundle:Player')->show_player($player);

        return $this->render('AppAdminBundle:Tournament:player.html.twig',
            array('player' => $p, 'image' => $oldimage, 'form' => $form->createView(), 'team' => $team,
                  'formimage' => $formimage->createView(), 'formstatus' => $formstatus->createView()));
    }

}