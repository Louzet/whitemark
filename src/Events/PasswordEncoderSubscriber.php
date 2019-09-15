<?php


namespace App\Events;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onEncodePassword', EventPriorities::PRE_WRITE]
        ];
    }

    /**
     * Permet de hash le mot de passe de l'utilisateur juste après la deserialization et avant l'insertion en base de donnée
     * @param ViewEvent $event
     */
    public function onEncodePassword(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod(); // POST, GET, PUT, etc...

        if (!$result instanceof User && Request::METHOD_POST !== $method) {
            return;
        }

        $hash = $this->passwordEncoder->encodePassword($result, $result->getPassword());
        $result->setPassword($hash);
    }
}