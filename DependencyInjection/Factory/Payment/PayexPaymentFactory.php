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

class PayexPaymentFactory extends AbstractPaymentFactory
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $contextName, array $config)
    {
        if (false == class_exists('Payum2\Payex\PaymentFactory')) {
            throw new RuntimeException('Cannot find payex payment factory class. Have you installed payum/payex package?');
        }

        $paymentId = parent::create($container, $contextName, $config);
        $paymentDefinition = $container->getDefinition($paymentId);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../Resources/config/payment'));
        $loader->load('payex.xml');

        $orderApiDefinition = new DefinitionDecorator('payum2.payex.api.order.prototype');
        $orderApiDefinition->replaceArgument(1, array(
            'encryptionKey' => $config['api']['options']['encryption_key'],
            'accountNumber' => $config['api']['options']['account_number'],
            'sandbox' => $config['api']['options']['sandbox']
        ));
        $orderApiDefinition->setPublic(true);
        $orderApiId = 'payum2.context.'.$contextName.'.api.order';
        $container->setDefinition($orderApiId, $orderApiDefinition);
        $paymentDefinition->addMethodCall('addApi', array(new Reference($orderApiId)));

        $agreementApiDefinition = new DefinitionDecorator('payum2.payex.api.agreement.prototype');
        $agreementApiDefinition->replaceArgument(1, array(
            'encryptionKey' => $config['api']['options']['encryption_key'],
            'accountNumber' => $config['api']['options']['account_number'],
            'sandbox' => $config['api']['options']['sandbox']
        ));
        $agreementApiDefinition->setPublic(true);
        $agreementApiId = 'payum2.context.'.$contextName.'.api.agreement';
        $container->setDefinition($agreementApiId, $agreementApiDefinition);
        $paymentDefinition->addMethodCall('addApi', array(new Reference($agreementApiId)));

        $recurringApiDefinition = new DefinitionDecorator('payum2.payex.api.recurring.prototype');
        $recurringApiDefinition->replaceArgument(1, array(
            'encryptionKey' => $config['api']['options']['encryption_key'],
            'accountNumber' => $config['api']['options']['account_number'],
            'sandbox' => $config['api']['options']['sandbox']
        ));
        $recurringApiDefinition->setPublic(true);
        $recurringApiId = 'payum2.context.'.$contextName.'.api.recurring';
        $container->setDefinition($recurringApiId, $recurringApiDefinition);
        $paymentDefinition->addMethodCall('addApi', array(new Reference($recurringApiId)));

        $initializeOrderActionDefinition = new DefinitionDecorator('payum2.payex.action.api.initialize_order');
        $initializeOrderActionId = 'payum2.context.'.$contextName.'.action.api.initialize_order';
        $container->setDefinition($initializeOrderActionId, $initializeOrderActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($initializeOrderActionId)));

        $completeOrderActionDefinition = new DefinitionDecorator('payum2.payex.action.api.complete_order');
        $completeOrderActionId = 'payum2.context.'.$contextName.'.action.api.complete_order';
        $container->setDefinition($completeOrderActionId, $completeOrderActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($completeOrderActionId)));

        $checkOrderActionDefinition = new DefinitionDecorator('payum2.payex.action.api.check_order');
        $checkOrderActionId = 'payum2.context.'.$contextName.'.action.api.check_order';
        $container->setDefinition($checkOrderActionId, $checkOrderActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($checkOrderActionId)));

        $createAgreementActionDefinition = new DefinitionDecorator('payum2.payex.action.api.create_agreement');
        $createAgreementActionId = 'payum2.context.'.$contextName.'.action.api.create_agreement';
        $container->setDefinition($createAgreementActionId, $createAgreementActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($createAgreementActionId)));

        $deleteAgreementActionDefinition = new DefinitionDecorator('payum2.payex.action.api.delete_agreement');
        $deleteAgreementActionId = 'payum2.context.'.$contextName.'.action.api.delete_agreement';
        $container->setDefinition($deleteAgreementActionId, $deleteAgreementActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($deleteAgreementActionId)));

        $checkAgreementActionDefinition = new DefinitionDecorator('payum2.payex.action.api.check_agreement');
        $checkAgreementActionId = 'payum2.context.'.$contextName.'.action.api.check_agreement';
        $container->setDefinition($checkAgreementActionId, $checkAgreementActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($checkAgreementActionId)));

        $autoPayAgreementActionDefinition = new DefinitionDecorator('payum2.payex.action.api.autopay_agreement');
        $autoPayAgreementActionId = 'payum2.context.'.$contextName.'.action.api.autopay_agreement';
        $container->setDefinition($autoPayAgreementActionId, $autoPayAgreementActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($autoPayAgreementActionId)));

        $startRecurringPaymentsActionDefinition = new DefinitionDecorator('payum2.payex.action.api.start_recurring_payment');
        $startRecurringPaymentsActionId = 'payum2.context.'.$contextName.'.action.api.start_recurring_payment';
        $container->setDefinition($startRecurringPaymentsActionId, $startRecurringPaymentsActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($startRecurringPaymentsActionId)));

        $stopRecurringPaymentsActionDefinition = new DefinitionDecorator('payum2.payex.action.api.stop_recurring_payment');
        $stopRecurringPaymentsActionId = 'payum2.context.'.$contextName.'.action.api.stop_recurring_payment';
        $container->setDefinition($stopRecurringPaymentsActionId, $stopRecurringPaymentsActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($stopRecurringPaymentsActionId)));

        $checkRecurringPaymentsActionDefinition = new DefinitionDecorator('payum2.payex.action.api.check_recurring_payment');
        $checkRecurringPaymentsActionId = 'payum2.context.'.$contextName.'.action.api.check_recurring_payment';
        $container->setDefinition($checkRecurringPaymentsActionId, $checkRecurringPaymentsActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($checkRecurringPaymentsActionId)));
        
        $paymentDetailsCaptureActionDefinition = new DefinitionDecorator('payum2.payex.action.payment_details_capture');
        $paymentDetailsCaptureActionId = 'payum2.context.'.$contextName.'.action.payment_details_capture';
        $container->setDefinition($paymentDetailsCaptureActionId, $paymentDetailsCaptureActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($paymentDetailsCaptureActionId)));

        $autoPayPaymentDetailsCaptureActionDefinition = new DefinitionDecorator('payum2.payex.action.autopay_payment_details_capture');
        $autoPayPaymentDetailsCaptureActionId = 'payum2.context.'.$contextName.'.action.autopay_payment_details_capture';
        $container->setDefinition($autoPayPaymentDetailsCaptureActionId, $autoPayPaymentDetailsCaptureActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($autoPayPaymentDetailsCaptureActionId)));

        $autoPayPaymentDetailsStatusActionDefinition = new DefinitionDecorator('payum2.payex.action.autopay_payment_details_status');
        $autoPayPaymentDetailsStatusActionId = 'payum2.context.'.$contextName.'.action.autopay_payment_details_status';
        $container->setDefinition($autoPayPaymentDetailsStatusActionId, $autoPayPaymentDetailsStatusActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($autoPayPaymentDetailsStatusActionId)));

        $paymentDetailsStatusActionDefinition = new DefinitionDecorator('payum2.payex.action.payment_details_status');
        $paymentDetailsStatusActionActionId = 'payum2.context.'.$contextName.'.action.payment_details_status';
        $container->setDefinition($paymentDetailsStatusActionActionId, $paymentDetailsStatusActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($paymentDetailsStatusActionActionId)));

        $paymentDetailsSyncActionDefinition = new DefinitionDecorator('payum2.payex.action.payment_details_sync');
        $paymentDetailsSyncActionActionId = 'payum2.context.'.$contextName.'.action.payment_details_sync';
        $container->setDefinition($paymentDetailsSyncActionActionId, $paymentDetailsSyncActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($paymentDetailsSyncActionActionId)));

        $agreementDetailsStatusActionDefinition = new DefinitionDecorator('payum2.payex.action.agreement_details_status');
        $agreementDetailsStatusActionActionId = 'payum2.context.'.$contextName.'.action.agreement_details_status';
        $container->setDefinition($agreementDetailsStatusActionActionId, $agreementDetailsStatusActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($agreementDetailsStatusActionActionId)));

        $agreementDetailsSyncActionDefinition = new DefinitionDecorator('payum2.payex.action.agreement_details_sync');
        $agreementDetailsSyncActionActionId = 'payum2.context.'.$contextName.'.action.agreement_details_sync';
        $container->setDefinition($agreementDetailsSyncActionActionId, $agreementDetailsSyncActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($agreementDetailsSyncActionActionId)));

        return $paymentId;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'payex';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);
        
        $builder->children()
            ->arrayNode('api')->isRequired()->children()
                ->arrayNode('options')->isRequired()->children()
                    ->scalarNode('encryption_key')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('account_number')->isRequired()->cannotBeEmpty()->end()
                    ->booleanNode('sandbox')->defaultTrue()->end()
                ->end()
            ->end()
        ->end();
    }
}