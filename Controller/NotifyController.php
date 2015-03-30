<?php
namespace Payum2\Bundle\PayumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Payum2\Request\NotifyTokenizedDetailsRequest;
use Payum2\Registry\RegistryInterface;
use Payum2\Bundle\PayumBundle\Service\TokenManager;

class NotifyController extends Controller 
{
    public function doAction(Request $request)
    {
        $token = $this->getTokenManager()->getTokenFromRequest($request, array(
            'paymentNameParameter' => 'payumPaymentName',
            'tokenParameter' => 'payumToken',
        ));

        $payment = $this->getPayum()->getPayment($token->getPaymentName()); 
        $payment->execute(new NotifyTokenizedDetailsRequest(
            array_replace($request->query->all(), $request->request->all()),
            $token
        ));

        return new Response('', 204);
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum2');
    }

    /**
     * @return TokenManager
     */
    protected function getTokenManager()
    {
        return $this->get('payum2.token_manager');
    }
}