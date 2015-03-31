<?php
namespace Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractPaymentFactory implements PaymentFactoryInterface  
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $contextName, array $config)
    {
        $paymentDefinition = $this->createPaymentDefinition($container, $contextName, $config);
        $paymentDefinition->setPublic(true);
        $paymentId = 'payum2.context.'.$contextName.'.payment';
        $container->setDefinition($paymentId, $paymentDefinition);

        foreach (array_reverse($config['actions']) as $actionId) {
            $paymentDefinition->addMethodCall(
                'addAction',
                array(new Reference($actionId), $forcePrepend = true)
            );
        }

        foreach (array_reverse($config['apis']) as $apiId) {
            $paymentDefinition->addMethodCall(
                'addApi',
                array(new Reference($apiId), $forcePrepend = true)
            );
        }

        foreach (array_reverse($config['extensions']) as $extensionId) {
            $paymentDefinition->addMethodCall(
                'addExtension',
                array(new Reference($extensionId), $forcePrepend = true)
            );
        }
        
        $this->addCommonActions($paymentDefinition);
        $this->addCommonExtensions($paymentDefinition);

        return $paymentId;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('actions')
                    ->useAttributeAsKey('key')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('apis')
                    ->useAttributeAsKey('key')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('extensions')
                    ->useAttributeAsKey('key')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ContainerBuilder $container
     * @param $contextName
     * @param array $config
     * 
     * @return Definition
     */
    protected function createPaymentDefinition(ContainerBuilder $container, $contextName, array $config)
    {
        $paymentDefinition = new Definition();
        $paymentDefinition->setClass(
            $this->createContextParameter($container, $contextName, '%payum2.payment.class%', 'payment.class')
        );
        
        return $paymentDefinition;
    }
    

    /**
     * @param ContainerBuilder $container
     * @param string $contextName
     * @param string $parameter
     * @param string $contextParameter
     * 
     * @return string
     */
    protected function createContextParameter(ContainerBuilder $container, $contextName, $parameter, $contextParameter)
    {
        $contextParameter = sprintf('payum2.context.%s.%s', $contextName, $contextParameter);
        
        $container->setParameter($contextParameter, $parameter);
        
        return "%{$contextParameter}%";
    }

    /**
     * @param Definition $paymentDefinition
     */
    protected function addCommonActions(Definition $paymentDefinition)
    {
        $paymentDefinition->addMethodCall(
            'addAction',
            array(new Reference('payum2.action.capture_details_aggregated_model'))
        );

        $paymentDefinition->addMethodCall(
            'addAction',
            array(new Reference('payum2.action.sync_details_aggregated_model'))
        );

        $paymentDefinition->addMethodCall(
            'addAction',
            array(new Reference('payum2.action.status_details_aggregated_model'))
        );
    }

    /**
     * @param Definition $paymentDefinition
     */
    protected function addCommonExtensions(Definition $paymentDefinition)
    {
        $paymentDefinition->addMethodCall(
            'addExtension', 
            array(new Reference('payum2.extension.endless_cycle_detector'))
        );
    }
}