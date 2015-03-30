<?php
namespace Payum2\Bundle\PayumBundle\Tests\Functional\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AuthorizeNetAimPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\Be2BillPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PayexPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\OmnipayPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PaypalExpressCheckoutNvpPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PaypalProCheckoutNvpPaymentFactory;
use Payum2\Bundle\PayumBundle\DependencyInjection\PayumExtension;
use Symfony\Component\DependencyInjection\Reference;

class PayumExtensionTest extends  \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLoadExtensionWithPaypalExpressCheckoutConfiguredPayment()
    {
        if (false == class_exists('Payum2\Paypal\ExpressCheckout\Nvp\PaymentFactory')) {
            $this->markTestSkipped('Skipped because payment library is not installed.');
        }
        
        $config = array(
            'contexts' => array(
                'a_context' => array(
                    'paypal_express_checkout_nvp' => array(
                        'api' => array(
                            'options' => array(
                                'username' => 'a_username',
                                'password' => 'a_password',
                                'signature' => 'a_signature',
                                'sandbox' => true
                            )
                        )
                    ),
                )
            )
        );

        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);
        
        $extension = new PayumExtension;
        $extension->addPaymentFactory(new PaypalExpressCheckoutNvpPaymentFactory);
        
        $extension->load($configs, $containerBuilder);
        
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.api'));
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.payment'));

        $this->assertDefinitionContainsMethodCall(
            $containerBuilder->getDefinition('payum2.context.a_context.payment'),
            'addApi',
            new Reference('payum2.context.a_context.api')
        );
    }

    /**
     * @test
     */
    public function shouldLoadExtensionWithPaypalProCheckoutConfiguredPayment()
    {
        if (false == class_exists('Payum2\Paypal\ProCheckout\Nvp\PaymentFactory')) {
            $this->markTestSkipped('Skipped because payment library is not installed.');
        }
        
        $config = array(
            'contexts' => array(
                'a_context' => array(
                    'paypal_pro_checkout_nvp' => array(
                        'api' => array(
                            'options' => array(
                                'username' => 'a_username',
                                'password' => 'a_password',
                                'partner' => 'a_partner',
                                'vendor' => 'a_vendor',
                                'sandbox' => true
                            )
                        )
                    ),
                )
            )
        );

        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new PayumExtension;
        $extension->addPaymentFactory(new PaypalProCheckoutNvpPaymentFactory);

        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.api'));
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.payment'));
    }

    /**
     * @test
     */
    public function shouldLoadExtensionWithBe2billConfiguredPayment()
    {
        if (false == class_exists('Payum2\Be2Bill\PaymentFactory')) {
            $this->markTestSkipped('Skipped because payment library is not installed.');
        }
        
        $config = array(
            'contexts' => array(
                'a_context' => array(
                    'be2bill' => array(
                        'api' => array(
                            'options' => array(
                                'identifier' => 'a_identifier',
                                'password' => 'a_password',
                                'sandbox' => true
                            )
                        )
                    ),
                )
            )
        );

        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new PayumExtension;
        $extension->addPaymentFactory(new Be2BillPaymentFactory);

        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.api'));
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.payment'));

        $this->assertDefinitionContainsMethodCall(
            $containerBuilder->getDefinition('payum2.context.a_context.payment'),
            'addApi',
            new Reference('payum2.context.a_context.api')
        );
    }

    /**
     * @test
     */
    public function shouldLoadExtensionWithAuthorizeNetAimConfiguredPayment()
    {
        if (false == class_exists('Payum2\AuthorizeNet\Aim\PaymentFactory')) {
            $this->markTestSkipped('Skipped because payment library is not installed.');
        }
        
        $config = array(
            'contexts' => array(
                'a_context' => array(
                    'authorize_net_aim' => array(
                        'api' => array(
                            'options' => array(
                                'login_id' => 'a_login',
                                'transaction_key' => 'a_transaction_key',
                                'sandbox' => true
                            )
                        )
                    ),
                )
            )
        );

        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new PayumExtension;
        $extension->addPaymentFactory(new AuthorizeNetAimPaymentFactory);

        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.api'));
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.payment'));

        $this->assertDefinitionContainsMethodCall(
            $containerBuilder->getDefinition('payum2.context.a_context.payment'),
            'addApi',
            new Reference('payum2.context.a_context.api')
        );
    }

    /**
     * @test
     */
    public function shouldLoadExtensionWithOmnipayConfiguredPayment()
    {
        if (false == class_exists('Payum2\Bridge\Omnipay\PaymentFactory')) {
            $this->markTestSkipped('Skipped because payment library is not installed.');
        }
        
        $config = array(
            'contexts' => array(
                'a_context' => array(
                    'omnipay' => array(
                        'type' => 'Stripe',
                        'options' => array(
                            'apiKey' => 'abc123',
                        )
                    ),
                )
            )
        );

        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new PayumExtension;
        $extension->addPaymentFactory(new OmnipayPaymentFactory);

        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.gateway'));
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.payment'));

        $this->assertDefinitionContainsMethodCall(
            $containerBuilder->getDefinition('payum2.context.a_context.payment'),
            'addApi',
            new Reference('payum2.context.a_context.gateway')
        );
    }

    /**
     * @test
     */
    public function shouldLoadExtensionWithPayexConfiguredPayment()
    {
        if (false == class_exists('Payum2\Payex\PaymentFactory')) {
            $this->markTestSkipped('Skipped because payment library is not installed.');
        }

        $config = array(
            'contexts' => array(
                'a_context' => array(
                    'payex' => array(
                        'api' => array(
                            'options' => array(
                                'encryption_key' => 'aKey',
                                'account_number' => 'aNum'
                            )
                        )
                    ),
                )
            )
        );

        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new PayumExtension;
        $extension->addPaymentFactory(new PayexPaymentFactory);

        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.api.order'));
        $this->assertTrue($containerBuilder->hasDefinition('payum2.context.a_context.payment'));

        $this->assertDefinitionContainsMethodCall(
            $containerBuilder->getDefinition('payum2.context.a_context.payment'),
            'addApi',
            new Reference('payum2.context.a_context.api.order')
        );
    }

    protected function assertDefinitionContainsMethodCall(Definition $serviceDefinition, $expectedMethod, $expectedFirstArgument)
    {
        foreach ($serviceDefinition->getMethodCalls() as $methodCall) {
            if ($expectedMethod == $methodCall[0] && $expectedFirstArgument == $methodCall[1][0]) {
                return;
            }
        }
        
        $this->fail(sprintf(
            'Failed assert that service (Class: %s) has method %s been called with first argument %s',
            $serviceDefinition->getClass(),
            $expectedMethod,
            $expectedFirstArgument
        ));
    }
}