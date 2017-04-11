<?php

namespace App\VoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\UserBundle\Entity\User;
use App\VoteBundle\Entity\Vote;
use App\VoteBundle\Entity\Option;
use App\VoteBundle\Entity\Result;
use App\VoteBundle\Form\CompletedType;

class VoteController extends Controller {

    # 1 - type to registration (user id)
    # 2 - type to session and ip (session + ip)
    private $type;

    public function __construct() {
        $this->type = 1; 
    }

    public function listAction($page) {

    	$rv = $this->getDoctrine()->getRepository('AppVoteBundle:Vote');
        $result = $rv->get_vote_list($page);
        $votes = $result[0];
        $countpage = $result[1];

        return $this->render('AppVoteBundle:Vote:list.html.twig', array('votes' => $votes, 'page' => $page,
                             'countpage' => $countpage));
    }

    public function showAction($vote, $access) {

        $user = $this->getUser();
        # get timezone and id user
        if($user) {
        	$userId = $user->getId();
        } else {
        	$userId = 0;
        }

    	# get Vote
    	$rv = $this->getDoctrine()->getRepository('AppVoteBundle:Vote');
    	$vote = $rv->show_vote_info($vote);

    	# option repository
    	$ro = $this->getDoctrine()->getRepository('AppVoteBundle:Option');

        # vote status user
    	$rr = $this->getDoctrine()->getRepository('AppVoteBundle:Result');
        $session_id = $this->get('session')->getId();
        $ip = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
    	$user_status = $rr->user_status($userId, $vote['id'], $this->type, $ip, $session_id);

        # get user vote
        $form = Request::createFromGlobals();

        if($form->request->has('submit') && ($user_status == 0)) {
            $radio = $form->request->get('radio');
            if(count($radio) == 0) {
                $this->get('session')->getFlashBag()->add('error', 'Выберите вариант ответа.');
                return $this->redirect($this->generateUrl('app_vote_show', array('vote' => $vote['id'])));
            }

            # check multiple
            if($vote['checked'] != 1 and $vote['checked'] != 0) {
                if(count($radio) > $vote['checked']) {
                    $this->get('session')->getFlashBag()->add('error', 'Вы превысили допустимое количество ответов.');
                    return $this->redirect($this->generateUrl('app_vote_show', array('vote' => $vote['id'])));
                }
            }

            $anon = (int) $form->request->get('anon');

        	$session_id = $this->get('session')->getId();
            $ip = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();

            for($i=0;$i<count($radio);$i++) {
                $result = new Result();
                $result->setUser($userId);
                $result->setSession($session_id);
                $result->setIp($ip);
                $result->setVote($vote['id']);
                $result->setOptions($radio[$i]);
                $result->setAnon($anon);
                $result->setStatus(1);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($result);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('app_vote_show', array('vote' => $vote['id'])));

        }

        # completed
        $compeleted = new Vote();
        $form_completed = $this->createForm(CompletedType::class, $compeleted);
        $request = Request::createFromGlobals();
        $form_completed->handleRequest($request);

        if($form_completed->isSubmitted()) {
            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or $vote['user'] == $userId) {
                $completed_vote = $this->getDoctrine()->getRepository('AppVoteBundle:Vote')->find($vote['id']);
                $completed_vote->setStatus(2);
                $completed_vote->setCompleted(new \DateTime());
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($completed_vote);
                $em->flush();
                return $this->redirect($this->generateUrl('app_vote_show', array('vote' => $vote['id'])));                
            }
        }

        # open vote
        if($userId == 0 or $user_status > 0 or $access == 'open' or $vote['status' ] == 2) {

            $options = $ro->show_vote_options($vote['id'], 'cnt DESC, id ASC');

            $results = $rr->get_results($vote['id']);
            $result_users = $results[0];
            $result_sum = $results[1];
            $sum = $results[2];            


        	return $this->render('AppVoteBundle:Vote:show_open.html.twig', array(
        		'vote' => $vote, 'options' => $options,
                'results' => $result_users, 'option_sum' => $result_sum, 'sum' => $sum,
                'type' => $this->type,
                'completed' => $form_completed->createView()));

        # close vote
        } else {

            $options = $ro->show_vote_options($vote['id'], 'id ASC');         

        	return $this->render('AppVoteBundle:Vote:show_close.html.twig', array(
        		'vote' => $vote, 'options' => $options, 'completed' => $form_completed->createView()));
        }
    }

	public function createAction(Request $request) {
        $user = $this->getUser();
		if (!is_object($user)) {
			throw new AccessDeniedException('This user does not have access to this section.');
		} else {
			$userId = $user->getId();
		}

        if($request->isMethod("POST")) {
            if($request->request->has('create_vote')) {
            	# check head
                $head = trim(strip_tags($request->request->get('head')));

            	$checked = (int) trim(strip_tags($request->request->get('checked')));

            	$row_options = $request->request->get('option');

            	# check options
            	$options = [];
            	foreach ($row_options as  $value) {
            		$option = trim(strip_tags($value));
            		if($option != '')
            			$options[] = $option;
            	}

            	# delete duplicate
            	$options = array_unique($options);

                # reset keys
                $options = array_values($options);

            	if(count($options) >= 2 and $head != '') {

            		$vote = new Vote();
		            $vote->setTitle($head);
		            $vote->setUser($userId);
                    $vote->setChecked($checked);
		            $vote->setStatus(1);

		            $em = $this->getDoctrine()->getEntityManager();
		            $em->persist($vote);
		            $em->flush();

		            for($i=0;$i<count($options);$i++)
		            {
		                $option = new Option();
		                $option->setDescription($options[$i]);
		                $option->setVotes($vote->getId());
		                $option->setStatus(1);
		                $em->persist($option);
		            }
		            $em->flush();
		            return $this->redirect($this->generateUrl('app_vote_list'));
            	} else {
                    $this->get('session')->getFlashBag()->add('error', 'Должна быть заполнена тема и минимум два варианта ответа.');                
                }
            }
        }
        return $this->render('AppVoteBundle:Vote:create.html.twig');
    }
}
