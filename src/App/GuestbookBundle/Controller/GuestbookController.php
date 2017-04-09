<?php

namespace App\GuestbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\GuestbookBundle\Entity\Guestbook;
use App\GuestbookBundle\Entity\Rate;
use App\GuestbookBundle\Form\GuestbookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestbookController extends Controller
{

    protected $bundle;
    protected $entity;
    protected $class;
    protected $form;
    protected $url;
    protected $posts;

    public function __construct() {
        $this->bundle = 'AppGuestbookBundle:Guestbook';
        $this->entity = new Guestbook();
        $this->class = 'Guestbook';
        $this->form = GuestbookType::class;
        $this->url = 'app_guestbook_show';
        $this->posts = 20;
    }

    public function showAction($page)
    {
        $guestbook = $this->entity;

        $form = $this->createForm($this->form, $guestbook);
        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        $user = $this->getUser();
        if($user) {
            $userId = $user->getId();
            $username = $user->getUsername();
            $timezone = $user->getTimezone();
        } else {
            $userId = 0;
            $username = 0;
            $timezone = 100;
        }

        if($form->isSubmitted()) {

            if($form->isValid()) {

                /* if not user */
                if(!$userId)
                    return $this->redirect($this->generateUrl('fos_user_security_login'));

                /* if banned user */
    	        if ($this->get('security.authorization_checker')->isGranted('ROLE_BANNED_2')) {
                    $this->get('session')->getFlashBag()->add('error', 'Вам запрещено писать сообщения на неопределенный срок. Приносим свои извинения.');
                    return $this->redirect($this->generateUrl($this->url));
                }

                $em = $this->getDoctrine()->getManager();

                $data = $form->getData();
                $message = strip_tags(trim($data->getMessage()));

                /* if empty message */
                if($message == ''){
                    $this->get('session')->getFlashBag()->add('error', 'Пожалуйста, заполните пустое поле.');
                    return $this->redirect($this->generateUrl($this->url));
                }

                /* duplicate message */
                $repository = $this->getDoctrine()->getRepository($this->bundle);
                $last_message = $repository->last_message($userId, $message);
                if($last_message) {
                    $this->get('session')->getFlashBag()->add('error', 'Вы уже отправили это сообщение.');
                    return $this->redirect($this->generateUrl($this->url));
                }

                $guestbook->setUser($userId);
                $guestbook->setMessage($message);
                $guestbook->setStatus(1);

                $em->persist($guestbook);
                $em->flush();

            } else {
                    $this->get('session')->getFlashBag()->add('error', 'Пожалуйста, заполните пустое поле.');
            }

            return $this->redirect($this->generateUrl($this->url));
        }        

        $repository = $this->getDoctrine()->getRepository($this->bundle);
        $result = $repository->show_guestbook($page, $this->posts);

        /* guestbook content */
        $guestbook_content = $result[0];
        /* all messages for page navigation */
        $countpage = $result[1];        

        /* count users message */
        $count_message = $repository->count_message();

        /* rate */
        $repository_rate = $this->getDoctrine()->getRepository('AppGuestbookBundle:Rate');
        $rate = $repository_rate->get_rate($guestbook_content, $username);        

        /* numbers */
        $repository_number = $this->getDoctrine()->getRepository('AppGuestbookBundle:Number');
        $numbers = $repository_number->show_numbers();        

        $repository_nach = $this->getDoctrine()->getRepository('AppGuestbookBundle:Nach');
        $nach = $repository_nach->get_nach();

        $topnach = $repository_nach->get_topnach();

        $repository_achives = $this->getDoctrine()->getRepository('AppGuestbookBundle:Achive');
        $achives = $repository_achives->achives_for_guestbook();        

        /* attention */
        $repository_attention = $this->getDoctrine()->getRepository('AppTournamentBundle:Content');
        $attention = $repository_attention->get_content('attention');                

        /* for old type quote */
        $old_style_quote = $repository->get_old_quote($guestbook_content);

        return $this->render($this->bundle.':show.html.twig',
            array('form' => $form->createView(), 'tz' => $timezone, 'guestbook' => $guestbook_content,
                  'countpage' => $countpage, 'page' => $page,
                  'rate_message' => $rate[0], 'rate_user' => $rate[1],
                  'count_message' => $count_message, 'attention' => $attention,
                  'numbers' => $numbers, 'nach' => $nach, 'topnach' => $topnach,
                  'achives' => $achives, 'quote' => $old_style_quote));

    }

   public function editAction(Request $request)
    {
        $user = $this->getUser();
        if(!$user)
            return new Response(json_encode(array("error" => 1, "text" => "Вы не авторизированы.")));

        $userId = $user->getId();
        $tz = $user->getTimezone();        

        $referer = $request->headers->get('referer');

        $guestbook = strstr($referer, "guestbook");
        $guestbook = substr($guestbook, 0, 9);
        $bundle = "AppGuestbookBundle:Guestbook";
        $url = "app_guestbook_show";

        if($guestbook != "guestbook") {

            $guestbook = strstr($referer, "adminbook");
            $bundle = "AppAdminBundle:Adminbook";
            $url = "app_admin_guestbook";
            if($guestbook != "adminbook")
                throw $this->createAccessDeniedException();


            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException();
            }            
        }

        $repository = $this->getDoctrine()->getRepository($bundle);

        // return new Response(json_encode(array("error" => 1, "text" => $request->request->get('id'))));

        if(!$request->request->get('id'))
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));

        if(!$request->request->get('message'))
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));

        $id = $request->request->get('id');
        $message = $request->request->get('message');
        $message = trim(strip_tags($message));
        if($message == "")
            return new Response(json_encode(array("error" => 1, "text" => "Отправлено пустое сообщение.")));

        $info = $repository->get_info_for_edit($id);

        if(empty($info))
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));

        if($info[0]['status'] != 1)
            return new Response(json_encode(array("error" => 1, "text" => "Сообщения нет.")));

        if($info[0]['user'] != $userId)
            return new Response(json_encode(array("error" => 1, "text" => "Редактирование невозможно.")));

        // проверка по времени 15 минут
        $date_now = date("d.m.Y H:i:s");
        $date_post = $info[0]['created']->format("d.m.Y H:i:s");


        if(strtotime($date_now) > (strtotime($date_post) + 900))
            return new Response(json_encode(array("error" => 1, "text" => "Время для редактирования истекло.")));

        $post = $this->getDoctrine()->getRepository($bundle)->find($id);

        $post->setMessage($message);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $replacedmessage = $this->get('app.text_mode')->replace_text($message);

        $renderer_message = nl2br($replacedmessage);

        return new Response(json_encode(array("error" => 0, "text" => $renderer_message, "hide" => $message)));

    }

    public function rankingAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_BANNED_0')) {
            return false;
        } 

        $user = $this->getUser();
        if(!$user)
            return false;
        $userId = $user->getId();


        if(!$request->request->get('sign') or !$request->request->get('id'))
            return false;

        $id = $request->request->get('id');
        if($request->request->get('sign') == 'u')
            $sign = 1;
        else
            $sign = -1;

        $repository = $this->getDoctrine()->getRepository('AppGuestbookBundle:Rate');
        $author = $repository->check_rank($id, $userId, $sign);

        if(!$author)
            return false;

        $rate = new Rate();

        $em = $this->getDoctrine()->getManager();

        $rate->setMessage($id);
        $rate->setAuthor($author);
        $rate->setUser($userId);
        $rate->setSign($sign);

        $em->persist($rate);
        $em->flush();

        $rank_message = $repository->get_rank_message($id);

        $new_rank_user = $repository->get_rank_user($author);

        $rank_message['author_id'] = $author;
        $rank_message['author_sum'] = $new_rank_user;

        return new Response(json_encode($rank_message));
    }    

    public function postAction($post, Request $request) {

        $referer = $request->headers->get('referer');

        $guestbook = strstr($referer, "adminbook");
        $guestbook = substr($guestbook, 0, 9);
        $bundle = "AppAdminBundle:Adminbook";
        $url = "app_admin_guestbook";

        if($guestbook != "adminbook") {

            $guestbook = strstr($referer, "guestbook");
            $bundle = "AppGuestbookBundle:Guestbook";
            $url = "app_guestbook_show";
        }

        $repository = $this->getDoctrine()->getRepository($bundle);
        $page = $repository->get_page($post, $this->posts);

        return $this->redirectToRoute($url, ['page' => $page, '_fragment' => 'post'.$post]);
    }
}