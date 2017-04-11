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
use App\TournamentBundle\Form\TeamType;
use App\TournamentBundle\Entity\Tournament;
use App\TournamentBundle\Entity\Team;
use App\TournamentBundle\Entity\Forecast;

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
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
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

    public function setroleAction(Request $request) {
    	/* if no admin */
	    if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
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
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

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

            $content->setTitle($type);
            $content->setDescription($message);
            $content->setAuthor($userId);

            $em->persist($content);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_content', array('type'=> $type)));
        }

        $repository = $this->getDoctrine()->getRepository('AppTournamentBundle:Content');
        $description = $repository->get_content($type);

        return $this->render('AppAdminBundle:Admin:content.html.twig', array('form'=>$form->createView(), 'description' => $description, 'type' => $type));
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

        $result = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->show_tournaments_list($page);

        $tournaments = $result[0];
        $count = $result[1];

        return $this->render("AppAdminBundle:Tournament:tournaments.html.twig",
                array("tournaments" => $tournaments));
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

        if(in_array($userId, $tr['access']))
            $access = 1;
        else
            $access = 0;

        if($tr['status'] == 1 or $tr['status'] == 3 or $tr['status'] == 4) {

            return $this->render("AppAdminBundle:Tournament:tournament.html.twig",
                    array("tournament" => $tr, 'form_name' => $form1->createView(),
                          "form_pos" => $form_pos->createView(),
                          "form_archive" => $form_archive->createView(),
                          "form_delete" => $form_delete->createView(),
                          "access" => $access
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
            $tournament->setAccess(array($userId));
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

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

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

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

        if($tournamentshow->getStatus() == 3 or $tournamentshow->getStatus() == 4) {

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

                    $info = $tournamentshow->getInfo();
                    $tr_id = $tournamentshow->getId();


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

                        $tournamentshow->setStatus(1);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($tournamentshow);
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

        } else if($tournamentshow->getStatus() == 1) {
            return $this->render('AppAdminBundle:Tournament:replace.html.twig',
                    array("tournament" => $tournamentshow,
                          "preusers" => $preusers,
                          "form" => $form->createView()));
        }
    }

    public function completedAction($tournament) {

        $tournamentshow = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

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

                $info = $tournamentshow->getInfo();

                if(!empty($info))
                    $result = array_merge($info, $data);
                else
                    $result = $data;
                $tournamentshow->setInfo($result);
                $tournamentshow->setStatus(3);

                $em = $this->getDoctrine()->getManager();
                $em->persist($tournamentshow);
                $em->flush();

                return $this->redirect($this->generateUrl('app_admin_replaceuser', array('tournament'=> $tournament)));
            }
        }

            return $this->render('AppAdminBundle:Tournament:completed.html.twig',
                    array("tournament" => $tournamentshow,
                          "form" => $form->createView()));
    }    

    public function toursAction($tournament, $tour) {

        if($tour == 0)
            $tour = 1;

        $tr = $this->getDoctrine()->getRepository("AppTournamentBundle:Tournament")->find($tournament);

        $rep_calendar = $this->getDoctrine()->getRepository("AppTournamentBundle:Calendar");
        $calendar = $rep_calendar->get_calendar($tournament);

        $rep_forecas = $this->getDoctrine()->getRepository("AppTournamentBundle:Forecast");
        $page = $rep_forecas->get_forecast($tournament, $tour);

                $field = $this->getDoctrine()->getRepository("AppTournamentBundle:Team");

                // $forecast = new Forecast();
                // $form = $this->createForm(ForecastType::class, $forecast, array('data' => $field->getTeams($tournament)));
                //
                // $request = Request::createFromGlobals();
                // $form->handleRequest($request);
                //
        // if($form->isSubmitted()) {
                //
        //     $data = $form->getData();
                //
                //      print "<pre>";
                //      print_r($data);
                //      print "</pre>";
                //
        // }

        return $this->render("AppAdminBundle:Tournament:tours.html.twig",
                array("calendar" => $calendar,
                      "tournament" => $tr, "tour" => $tour,
                      "forecast" => $page));
    }    

}