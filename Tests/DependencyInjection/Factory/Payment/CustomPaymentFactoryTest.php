<?php
namespace Payum2\Bundle\PayumBundle\Tests\DependencyInjection\Factory\Payment;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\CustomPaymentFactory;

class CustomPaymentFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractPaymentFactory()
    {
        $rc = new \ReflectionClass('Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\CustomPaymentFactory');

        $this->assertTrue($rc->isSubclassOf('Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new CustomPaymentFactory;
    }

    /**
     * @test
     */
    public function shouldAllowGetName()
    {
        $factory = new CustomPaymentFactory;

        $this->assertEquals('custom', $factory->getName());
    }

    /**
     * @test
     */
    public function shouldAllowAddConfiguration()
    {
        $factory = new CustomPaymentFactory;

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');
        
        $factory->addConfiguration($rootNode);

        $processor = new Processor();
        $config = $processor->process($tb->buildTree(), array(array()));

        //come from abstract payment factory
        $this->assertArrayHasKey('actions', $config);
        $this->assertArrayHasKey('apis', $config);
        $this->assertArrayHasKey('extensions', $config);
    }

    /**
     * @test
     */
    public function shouldAllowAddConfigurationWithCustomPaymentService()
    {
        $factory = new CustomPaymentFactory;

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');

        $factory->addConfiguration($rootNode);

        $processor = new Processor();
        $config = $processor->process($tb->buildTree(), array(array(
            'service' => 'foo.payment.service'
        )));

        $this->assertArrayHasKey('service', $config);
        $this->assertEquals('foo.payment.service', $config['service']);

        //come from abstract payment factory
        $this->assertArrayHasKey('actions', $config);
        $this->assertArrayHasKey('apis', $config);
        $this->assertArrayHasKey('extensions', $config);
    }

    /**
     * @test
     */
    public function shouldAllowCreatePaymentAndReturnItsId()
    {
        $factory = new CustomPaymentFactory;

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
            'actions' => array(),
            'apis' => array(),
            'extensions' => array(),
        ));
        
        $this->assertEquals('payum2.context.aContextName.payment', $paymentId);
        $this->assertTrue($container->hasDefinition($paymentId));
        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\Definition', 
            $container->getDefinition($paymentId)
        );
    }

    /**
     * @test
     */
    public function shouldAllowCreatePaymentAndReturnItsIdWhenCustomPaymentServiceSet()
    {
        $factory = new CustomPaymentFactory;

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
                'service' => 'foo.payment.service',
                'actions' => array(),
                'apis' => array(),
                'extensions' => array(),
            ));

        $this->assertEquals('payum2.context.aContextName.payment', $paymentId);
        $this->assertTrue($container->hasDefinition($paymentId));
        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\DefinitionDecorator',
            $container->getDefinition($paymentId)
        );
    }

    /**
     * @test
     */
    public function shouldCallParentsCreateMethod()
    {
        $factory = new CustomPaymentFactory;

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
            'actions' => array('payum2.action.foo'),
            'apis' => array('payum2.api.bar'),
            'extensions' => array('payum2.extension.ololo'),
        ));

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId), 
            'addAction', 
            new Reference('payum2.action.foo')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addApi',
            new Reference('payum2.api.bar')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addExtension',
            new Reference('payum2.extension.ololo')
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