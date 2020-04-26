<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Priority;
use App\Entity\TaskList;
use App\Repository\PriorityRepository;
use App\Repository\TaskListRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $prioRepo;
    private $taskListRepo;

    public function __construct(PriorityRepository $prioRepo, TaskListRepository $taskListRepo) {
        $this->prioRepo = $prioRepo;
        $this->taskListRepo = $taskListRepo;
    }

    public function load(ObjectManager $manager)
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
            $manager->persist($priority);
        }

        $manager->flush();

        // Récupération de toutes les Priorités créées
        $priorities = $this->prioRepo->findAll();

        // Création des Listes
        $taskList = new TaskList();
        $taskList->setName('à faire');
        $taskList->setZOrder(1);
        $manager->persist($taskList);

        $taskList = new TaskList();
        $taskList->setName('en cours');
        $taskList->setZOrder(2);
        $manager->persist($taskList);

        $taskList = new TaskList();
        $taskList->setName('fini');
        $taskList->setZOrder(3);
        $manager->persist($taskList);

        $manager->flush();

        // Récupération de toutes les Listes crées
        $taskLists = $this->taskListRepo->findAll();

        // Instance Faker
        $faker = \Faker\Factory::create('fr_FR');

        // Création des 50 tâches
        foreach ($taskLists as $index => $list) {
            for($i = 0; $i < 17; $i++) {
                shuffle($priorities);
                $task = new Task();
                $task->setName($faker->words(5, true));
                $task->setZOrder($i + 1);
                $task->setContent($faker->text(random_int(40, 300), true));
                $task->setTaskList($list);
                $task->setPriority($priorities[0]);
                $manager->persist($task);
            }
        }

        $manager->flush();

        echo "Génération des fixtures terminées\n";
    }
}
