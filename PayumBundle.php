<?php
namespace Payum2\Bundle\PayumBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\CustomPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\OmnipayPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PaypalExpressCheckoutNvpPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PaypalProCheckoutNvpPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\Be2BillPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PayexPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AuthorizeNetAimPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Storage\DoctrineStorageFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Storage\FilesystemStorageFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\HeartlandPaymentFactory;

class PayumBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var $extension \Payum2\Bundle\PayumBundle\DependencyInjection\PayumExtension */
        $extension = $container->getExtension('payum');

        $extension->addPaymentFactory(new PaypalExpressCheckoutNvpPaymentFactory);
        $extension->addPaymentFactory(new PaypalProCheckoutNvpPaymentFactory);
        $extension->addPaymentFactory(new Be2BillPaymentFactory);
        $extension->addPaymentFactory(new AuthorizeNetAimPaymentFactory);
        $extension->addPaymentFactory(new PayexPaymentFactory);
        $extension->addPaymentFactory(new OmnipayPaymentFactory);
        $extension->addPaymentFactory(new CustomPaymentFactory);
        $extension->addPaymentFactory(new HeartlandPaymentFactory());

        $extension->addStorageFactory(new FilesystemStorageFactory);
        $extension->addStorageFactory(new DoctrineStorageFactory);
    }
}
