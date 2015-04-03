<?php

namespace Fi\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class fifree2dropdatabaseCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('fifree2:dropdatabase')
                ->setDescription('Cancellazione database fifree')
                ->setHelp('Cancella il database e tutti i dati di fifree')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Se non impostato, il comando non avrà effetto')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $force = $input->getOption('force');

        if (!$force) {
            echo "Specificare l'opzione --force per eseguire il comando";
            exit;
        }

        $command = $this->getApplication()->find('doctrine:database:drop');
        $arguments = array('command' => 'doctrine:database:drop','--force'=>true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
    }

}

?>