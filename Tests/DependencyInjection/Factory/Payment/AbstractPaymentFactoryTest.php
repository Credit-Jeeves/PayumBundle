<?php
namespace Payum2\Bundle\PayumBundle\Tests\DependencyInjection\Factory\Payment;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;

class AbstractPaymentFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementPaymentFactoryInterface()
    {
        $rc = new \ReflectionClass('Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory');
        
        $this->assertTrue($rc->implementsInterface('Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PaymentFactoryInterface'));
    }

    /**
     * @test
     */
    public function shouldBeAbstract()
    {
        $rc = new \ReflectionClass('Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory');

        $this->assertTrue($rc->isAbstract());
    }

    /**
     * @test
     */
    public function shouldAllowAddConfiguration()
    {
        $factory = $this->createAbstractPaymentFactory();

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');
        
        $factory->addConfiguration($rootNode);

        $processor = new Processor();
        $processor->process($tb->buildTree(), array());
    }

    /**
     * @test
     */
    public function shouldAllowConfigureCustomActions()
    {
        $factory = $this->createAbstractPaymentFactory();

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');

        $factory->addConfiguration($rootNode);

        $processor = new Processor();
        
        $config = $processor->process($tb->buildTree(), array());        
        $this->assertArrayHasKey('actions', $config);
        $this->assertEmpty($config['actions']);

        $config = $processor->process($tb->buildTree(), array(array(
            'actions' => array(
                'payum2.action.foo',
                'payum2.action.bar'
            ))
        ));
        $this->assertArrayHasKey('actions', $config);
        $this->assertContains('payum2.action.foo', $config['actions']);
        $this->assertContains('payum2.action.bar', $config['actions']);
    }

    /**
     * @test
     */
    public function shouldAllowConfigureCustomApis()
    {
        $factory = $this->createAbstractPaymentFactory();

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');

        $factory->addConfiguration($rootNode);

        $processor = new Processor();

        $config = $processor->process($tb->buildTree(), array());
        $this->assertArrayHasKey('apis', $config);
        $this->assertEmpty($config['apis']);

        $config = $processor->process($tb->buildTree(), array(array(
            'apis' => array(
                'payum2.api.foo',
                'payum2.api.bar'
            ))
        ));
        $this->assertArrayHasKey('apis', $config);
        $this->assertContains('payum2.api.foo', $config['apis']);
        $this->assertContains('payum2.api.bar', $config['apis']);
    }

    /**
     * @test
     */
    public function shouldAllowConfigureCustomExtensions()
    {
        $factory = $this->createAbstractPaymentFactory();

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');

        $factory->addConfiguration($rootNode);

        $processor = new Processor();

        $config = $processor->process($tb->buildTree(), array());
        $this->assertArrayHasKey('extensions', $config);
        $this->assertEmpty($config['extensions']);

        $config = $processor->process($tb->buildTree(), array(array(
            'extensions' => array(
                'payum2.extension.foo',
                'payum2.extension.bar'
            ))
        ));
        $this->assertArrayHasKey('extensions', $config);
        $this->assertContains('payum2.extension.foo', $config['extensions']);
        $this->assertContains('payum2.extension.bar', $config['extensions']);
    }

    /**
     * @test
     */
    public function shouldAllowCreatePaymentAndReturnItsId()
    {
        $factory = $this->createAbstractPaymentFactory();

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
            'actions' => array(),
            'apis' => array(),
            'extensions' => array(),
        ));
        
        $this->assertEquals('payum2.context.aContextName.payment', $paymentId);
        $this->assertTrue($container->hasDefinition($paymentId));
    }

    /**
     * @test
     */
    public function shouldAddCustomActions()
    {
        $factory = $this->createAbstractPaymentFactory();

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
            'actions' => array(
                'payum2.action.foo',
                'payum2.action.bar',
            ),
            'apis' => array(),
            'extensions' => array(),
        ));

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId), 
            'addAction', 
            new Reference('payum2.action.foo')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addAction',
            new Reference('payum2.action.bar')
        );
    }

    /**
     * @test
     */
    public function shouldAddCommonActions()
    {
        $factory = $this->createAbstractPaymentFactory();

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
            'actions' => array(),
            'apis' => array(),
            'extensions' => array(),
        ));

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addAction',
            new Reference('payum2.action.capture_details_aggregated_model')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addAction',
            new Reference('payum2.action.sync_details_aggregated_model')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addAction',
            new Reference('payum2.action.status_details_aggregated_model')
        );
    }

    /**
     * @test
     */
    public function shouldAddCommonExtensions()
    {
        $factory = $this->createAbstractPaymentFactory();

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
            'actions' => array(),
            'apis' => array(),
            'extensions' => array(),
        ));

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addExtension',
            new Reference('payum2.extension.endless_cycle_detector')
        );
    }

    /**
     * @test
     */
    public function shouldAddCustomApis()
    {
        $factory = $this->createAbstractPaymentFactory();

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
                'actions' => array(),
                'apis' => array(
                    'payum2.api.foo',
                    'payum2.api.bar',
                ),
                'extensions' => array(),
            ));

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addApi',
            new Reference('payum2.api.foo')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addApi',
            new Reference('payum2.api.bar')
        );
    }

    /**
     * @test
     */
    public function shouldAddCustomExtensions()
    {
        $factory = $this->createAbstractPaymentFactory();

        $container = new ContainerBuilder;

        $paymentId = $factory->create($container, 'aContextName', array(
                'actions' => array(),
                'apis' => array(),
                'extensions' => array(
                    'payum2.extension.foo',
                    'payum2.extension.bar',
                ),
            ));

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addExtension',
            new Reference('payum2.extension.foo')
        );
        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addExtension',
            new Reference('payum2.extension.bar')
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractPaymentFactory
     */
    protected function createAbstractPaymentFactory()
    {
        return $this->getMockForAbstractClass('Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory');
    }
}