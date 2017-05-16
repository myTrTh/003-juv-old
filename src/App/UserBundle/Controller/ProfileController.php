<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\UserBundle\Entity\User;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    /**
     * Show the user.
     */
    public function showAction() {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $id = $user->getId();

        $repository_champion = $this->getDoctrine()->getRepository('AppGuestbookBundle:Champion');
        $champions = $repository_champion->champions_for_user($id);

        $repository_number = $this->getDoctrine()->getRepository('AppGuestbookBundle:Number');
        $number = $repository_number->number_for_user($id);                

        $tournament_player = $this->getDoctrine()->getRepository('AppTournamentBundle:Tournamentusers')->get_tournaments($id);        
        $games = $this->getDoctrine()->getRepository('AppTournamentBundle:Calendar')->get_games($id);

        $repository_rate = $this->getDoctrine()->getRepository('AppGuestbookBundle:Rate');
        $rate = $repository_rate->get_my_rate($id);

        $repository_guestbook = $this->getDoctrine()->getRepository('AppGuestbookBundle:Guestbook');
        $messages = $repository_guestbook->how_messages($id);        

        $activity = $this->getDoctrine()->getRepository('AppUserBundle:Activity')->show_user_activity($id);

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user, 'number' => $number, 'champions' => $champions,
            "tournaments" => $tournament_player, "rate" => $rate,
            "games" => $games, "messages" => $messages, 'activity' => $activity
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        $repository_champion = $this->getDoctrine()->getRepository('AppGuestbookBundle:Champion');
        $champions = $repository_champion->champions_for_user($id);

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
            'champions' => $champions
        ));
    }
}
