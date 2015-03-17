<?php
namespace Payum2\Bundle\PayumBundle\Registry;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Payum2\Registry\AbstractRegistry;

class ContainerAwareRegistry extends AbstractRegistry implements ContainerAwareInterface 
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function getService($id)
    {
        return $this->container->get($id);
    }
}