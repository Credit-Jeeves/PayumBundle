<?php
namespace Payum2\Bundle\PayumBundle\EventListener;

use Payum2\Bundle\PayumBundle\Request\ResponseInteractiveRequest;
use Payum2\Exception\LogicException;
use Payum2\Request\InteractiveRequestInterface;
use Payum2\Request\RedirectUrlInteractiveRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class InteractiveRequestListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (false == $event->getException() instanceof InteractiveRequestInterface) {
            return;
        }

        $interactiveRequest = $event->getException();
            
        if ($interactiveRequest instanceof ResponseInteractiveRequest) {
            $event->setResponse($interactiveRequest->getResponse());
            $event->stopPropagation();
            
            return;
        }
        
        if ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
            $event->setResponse(new RedirectResponse($interactiveRequest->getUrl()));
            $event->stopPropagation();
            
            return;
        }
        
        $ro = new \ReflectionObject($interactiveRequest);
        
        $event->setException(new LogicException(
            sprintf('Cannot convert interactive request %s to symfony response.', $ro->getShortName()), 
            null, 
            $interactiveRequest
        ));
    }
}