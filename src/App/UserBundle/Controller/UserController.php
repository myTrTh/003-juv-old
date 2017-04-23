<?php

namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\ImageType;
use App\UserBundle\Form\TimezoneType;

class UserController extends Controller
{
	# show user profile
    public function showAction($id) {

        $user = $this->getDoctrine()->getRepository('AppUserBundle:User')->find($id);    	
        $repository_champion = $this->getDoctrine()->getRepository('AppGuestbookBundle:Champion');
        $champions = $repository_champion->champions_for_user($id);

        $repository_number = $this->getDoctrine()->getRepository('AppGuestbookBundle:Number');
        $number = $repository_number->number_for_user($id);

        $tournament_player = $this->getDoctrine()->getRepository('AppTournamentBundle:Tournamentusers')->get_tournaments($id);        
        $games = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_games($id);

        return $this->render('AppUserBundle:User:show.html.twig',
        	array("user" => $user, 'number' => $number,
                  "tournaments" => $tournament_player,
                  "games" => $games,
                  "champions" => $champions));
    }

    # show all users
    public function listAction($page, $sort) {

        $repository = $this->getDoctrine()->getRepository('AppUserBundle:User');
        $users = $repository->show_all_users($page, $sort);

        $repository_achives = $this->getDoctrine()->getRepository('AppGuestbookBundle:Achive');
        $achives = $repository_achives->achives_for_guestbook();

        $repository_number = $this->getDoctrine()->getRepository('AppGuestbookBundle:Number');
        $numbers = $repository_number->show_numbers();

        return $this->render('AppUserBundle:User:list.html.twig',
        	array("users" => $users,
                  "achives" => $achives,
                  "numbers" => $numbers,
                  "sort" => $sort
                  ));
    }

    # delete avator
    public function delete_imageAction() {

        $user = $this->getUser();
        if($user) {

            $image = $user->getImage();

            if($image)
            {
                $this->get('app.image_uploader')->image_delete($image, 'public/images/users');
                $em = $this->getDoctrine()->getManager();                
                $user->setImage(NULL);
                $em->persist($user);
                $em->flush();       
            }
        }
        return $this->redirect($this->generateUrl('app_user_settings'));
    }

    # setting user profile
    public function settingsAction() {

        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $request = Request::createFromGlobals();

        # image. show, add and delete
        $image = new User();
        $form_image = $this->createForm(ImageType::class, $image);
        $form_image->handleRequest($request);
        $oldimage = $user->getImage();

        # upload image
        if($form_image->isSubmitted() && $form_image->isValid()) {

            $file = $image->getImage();
            # upload file
            $fileName = $this->get('app.image_uploader')->image_upload($file, 'public/images/users', 50000, 220, 220);

            # if file is upload, write in db
            if($fileName) {

                # delete old image
                if($oldimage)
                    $this->get('app.image_uploader')->image_delete($oldimage, 'public/images/users');

                $em = $this->getDoctrine()->getManager();

                # set new image in db
                $user->setImage($fileName);
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('app_user_settings'));                
            }
        }

        # set timezone user
        $timezone = new User();
        $options = $user->getOptions();
        $oldtimezone = $options['timezone'];
        $form_date = $this->createForm(TimezoneType::class, $timezone, array('data' => array($oldtimezone)));
        $form_date->handleRequest($request);

        # upload image
        if($form_date->isSubmitted() && $form_date->isValid()) {

            $new = $form_date->getData();

            $new_tz = $new['timezone'];

            $em = $this->getDoctrine()->getManager();
            $options['timezone'] = $new_tz;
            $user->setOptions($options);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_user_settings'));              
        }

        # FOR EXAMPLE
        $date = $this->get('app.date_mode')->replace_date(date("d.m.Y H:i"));

        return $this->render('AppUserBundle:User:settings.html.twig',
            array("user" => $user,
                  "form_image" => $form_image->createView(),
                  "form_date" => $form_date->createView(),
                  "date" => $date
                ));
    }

    public function notificationAction(Request $request) {
        $user = $this->getUser();
        if(!$user)
            return new Response(json_encode(array("error" => 1, "text" => "Вы не авторизированы.")));

        $userId = $user->getId();

        if(!$request->request->get('notification'))
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));

        if(!$request->request->get('status'))
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));        

        $notification = $request->request->get('notification');
        $status = $request->request->get('status');

        $order_not = array('notification_guestbook', 'notification_vote');
        if(!in_array($notification, $order_not))
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));             

        $options = $user->getOptions();
        $options['notification'][$notification] = $status;



        $user->setOptions($options);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response(json_encode(array("error" => 0, "text" => "GOOD")));
    }
}