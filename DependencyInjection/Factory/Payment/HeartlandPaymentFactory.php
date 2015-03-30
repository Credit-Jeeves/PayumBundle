<?php
namespace Payum2\Bundle\PayumBundle\DependencyInjection\Factory\Payment;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;

use Payum2\Exception\RuntimeException;

class HeartlandPaymentFactory extends AbstractPaymentFactory
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $contextName, array $config)
    {
        if (false == class_exists('Payum2\Heartland\PaymentFactory')) {
            throw new RuntimeException(
                'Cannot find Heartland payment factory class. Have you installed payum2/heartland?'
            );
        }
        
        $paymentId = parent::create($container, $contextName, $config);
        $paymentDefinition = $container->getDefinition($paymentId);
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../Resources/config/payment'));
        $loader->load('heartland.xml');
        $apiDefinition = new DefinitionDecorator('payum2.heartland.api');
        $apiDefinition->replaceArgument(0, $config['api']['options']);
//        $apiDefinition->replaceArgument(1, $loader->);
        $apiId = 'payum2.context.'.$contextName.'.api';
        $container->setDefinition($apiId, $apiDefinition);
        $paymentDefinition->addMethodCall('addApi', array(new Reference($apiId)));
        
        $captureActionDefinition = new DefinitionDecorator('payum2.heartland.action.make_blind_payment');
        $captureActionId = 'payum2.context.'.$contextName.'.action.make_blind_payment';
        $container->setDefinition($captureActionId, $captureActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($captureActionId)));

        $captureActionDefinition = new DefinitionDecorator('payum2.heartland.action.register_token_to_additional_merchant');
        $captureActionId = 'payum2.context.'.$contextName.'.action.register_token_to_additional_merchant';
        $container->setDefinition($captureActionId, $captureActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($captureActionId)));

        $captureActionDefinition = new DefinitionDecorator('payum2.heartland.action.get_token');
        $captureActionId = 'payum2.context.'.$contextName.'.action.get_token';
        $container->setDefinition($captureActionId, $captureActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($captureActionId)));

        $statusActionDefinition = new DefinitionDecorator('payum2.heartland.action.status');
        $statusActionId = 'payum2.context.'.$contextName.'.action.status';
        $container->setDefinition($statusActionId, $statusActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($statusActionId)));
        
        return $paymentId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'heartland';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);
        
        $builder->children()
            ->arrayNode('api')->isRequired()->children()
                ->arrayNode('options')->isRequired()->children()
                    ->scalarNode('env')->defaultValue('')->end()
                    ->scalarNode('application_id')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('username')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ->end();
    }
}
