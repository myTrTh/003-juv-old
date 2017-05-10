<?php

namespace App\UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\getResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManager;
use App\UserBundle\Entity\Notification;

class NotificationListener
{
    private $token_storage;
    protected $em;
    protected $request;
    protected $twig;

    public function __construct(TokenStorageInterface $token_storage, EntityManager $em, $request, \Twig_Environment $twig)
    {
        $this->token_storage = $token_storage;
        $this->em = $em;
        $this->request = $request;
        $this->twig = $twig;
    }

    public function onKernelRequest(getResponseEvent $event)
    {

        $token = $this->token_storage->getToken();
        $user = $token ? $token->getUser() : null;
          
        // ONLY USER
        if($user && $user != 'anon.') {

            $route = $this->request->getMasterRequest()->getRequestUri();
            $userId = $user->getId();

            $options = $user->getOptions();

            $repository = $this->em->getRepository('AppUserBundle:Notification');

            // GUESTBOOK NOTIFICATION
            $guestbook_last_date = $repository->last_visit($userId, 'guestbook');

            $new_guestbook = $this->em->getRepository('AppUserBundle:Notification')->get_single_new($userId, 'AppGuestbookBundle:Guestbook', $guestbook_last_date);

            if($options['notification']['notification_guestbook'] == 'true')
                $this->twig->addGlobal('notification_guestbook', $new_guestbook);

            // send info in guestbook notification
            $guestbook_pattern = "/guestbook/";
            preg_match($guestbook_pattern, $route, $guestbook_result);
            if(count($guestbook_result)) {
                $repository->delete_visit($userId, $guestbook_result);

                $notification = new Notification();

                $notification->setUser($userId);
                $notification->setRoute($guestbook_result[0]);

                $this->em->persist($notification);
                $this->em->flush();
            }

            $roles = $user->getRoles();
            if(in_array('ROLE_ADMIN', $roles)) {
                // ADMBOOK NOTIFICATION
                $adminbook_last_date = $repository->last_visit($userId, 'adminbook');

                $new_adminbook = $this->em->getRepository('AppUserBundle:Notification')->get_single_new($userId, 'AppAdminBundle:Adminbook', $adminbook_last_date);

                $this->twig->addGlobal('notification_adminbook', $new_adminbook);

                // send info in guestbook notification
                $adminbook_pattern = "/adminbook/";
                preg_match($adminbook_pattern, $route, $adminbook_result);
                if(count($adminbook_result)) {
                    $repository->delete_visit($userId, $adminbook_result);

                    $notification = new Notification();

                    $notification->setUser($userId);
                    $notification->setRoute($adminbook_result[0]);

                    $this->em->persist($notification);
                    $this->em->flush();
                }
            }


            // VOTES NOTIFICATION
            $new_votes = $repository->get_more_new("AppVoteBundle:Vote", $userId);

            if($options['notification']['notification_vote'] == 'true')
                $this->twig->addGlobal('notification_vote', $new_votes);

            // send info in vote notification
            $vote_pattern = "/vote\/[0-9]+/";
            preg_match($vote_pattern, $route, $vote_result);
            if(count($vote_result)) {
                $repository->delete_visit($userId, $vote_result);

                $notification = new Notification();

                $notification->setUser($userId);
                $notification->setRoute($vote_result[0]);

                $this->em->persist($notification);
                $this->em->flush();
            }
        }
    }
}
