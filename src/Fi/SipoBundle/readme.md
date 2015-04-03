  #app/AppKernel.php
  public function registerBundles() {
    $bundles = array(
        new Fi\SipoBundle\FiSipoBundle()
    );
  }

  #app/config/config.yml
  parameters:
    schemasipo: SIPO 

  #app/config/routing.yml

fi_sipo:
    resource: "@FiSipoBundle/Resources/config/routing.yml"
    prefix:   /

