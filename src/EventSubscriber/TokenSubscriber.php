<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use TypeError;

class TokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $authToken;

    /**
     * TokenSubscriber constructor.
     * @param $authToken
     */
    public function __construct($authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController && $event->getRequest()->getMethod() === 'POST') {
            $requestSignature = $event->getRequest()->headers->get('Signature');
            $postBody = $event->getRequest()->getContent();
            $postBody = json_encode(json_decode($postBody));
            $signature = hash_hmac('sha256', $postBody, $this->authToken);
            if ($requestSignature !== $signature) {
                throw new AccessDeniedHttpException('Invalid request signature');
            }

            $controller->setRequestId($event->getRequest()->get('request_id'));
            $controller->setScope($event->getRequest()->get('scope'));
            //todo:: validate scope
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
