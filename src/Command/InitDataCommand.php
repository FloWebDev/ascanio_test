<?php

namespace App\Command;

use App\Entity\Priority;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @link https://symfony.com/doc/current/console.html
 */
class InitDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'init:data';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Permet de créer les données de départ (les priorités).')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Cette commande permet de créer les données de départ pour la table des priorités. Sans l\'utlisation préalable de cette commande, le site ne pourra pas fonctionner correctement.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Tableau des couleurs pour les priorités
        $colors = [
            array('faible', 'warning'),
            array('normal', 'info'),
            array('important', 'primary'),
            array('prioritaire', 'success'),
            array('urgent', 'danger')
        ];

        // Création des Priorités
        for ($i = 0; $i < 5; $i++) {
            $priority = new Priority();
            $priority->setName($colors[$i][0]);
            $priority->setZRank($i+1);
            $priority->setColor($colors[$i][1]);
            $this->em->persist($priority);
        }

        $this->em->flush();

        echo "Génération des données initiales terminées\n";

        return 0;
    }
}