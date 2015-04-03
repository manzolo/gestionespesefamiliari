<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

    public function registerBundles() {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Fi\CoreBundle\FiCoreBundle(),
            new Wb\SchemaExporterBundle\WbSchemaExporterBundle(),
            new Fi\AutenticazioneBundle\AutenticazioneBundle(),
            new Fi\PannelloAmministrazioneBundle\FiPannelloAmministrazioneBundle(),
            new Fi\OracleBundle\FiOracleBundle(),
            new Fi\PersonaleBundle\FiPersonaleBundle(),
            new Fi\NetworkBundle\FiNetworkBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Fi\LdapBundle\FiLdapBundle(),
            new Fi\FOSUserBundle\FiFOSUserBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test', 'localhost'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
