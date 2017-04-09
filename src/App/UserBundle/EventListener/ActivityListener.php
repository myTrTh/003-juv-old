<?php

namespace App\UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\getResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManager;
use App\UserBundle\Entity\Activity;

class ActivityListener
{
    private $token_storage;
    protected $em;
    protected $request;

    public function __construct(TokenStorageInterface $token_storage, EntityManager $em, $request)
    {
        $this->token_storage = $token_storage;
        $this->em = $em;
        $this->request = $request;
    }

    public function onKernelRequest(getResponseEvent $event)
    {
        $token = $this->token_storage->getToken();
        $user = $token ? $token->getUser() : null;
        
        if($user && $user != 'anon.')
        {
            $repository = $this->em->getRepository('AppUserBundle:Activity');
            $initUser = $repository->findOneByUser($user->getId());
            if($initUser)
                $this->em->remove($initUser);

            $ip = $this->request->getMasterRequest()->getClientIp();
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $last_page = $_SERVER['REQUEST_URI'];

            $activity = new Activity();

            $activity->setUser($user->getId());
            $activity->setUserAgent($user_agent);
            $activity->setIp($ip);
            $activity->setLastPage($last_page);

            $this->em->persist($activity);
            $this->em->flush();

        }
    }
}
