<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\TaskList;
use App\Repository\TaskRepository;
use App\Repository\TaskListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskListController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(TaskListRepository $taskListRepository, TaskRepository $taskRepository, Request $request)
    {
        $lists = $taskListRepository->findBy([], [
            'z_order' => 'ASC',
            'name' => 'ASC'
        ]);

        // Création des formulaires d'édition pour les différentes tâches
        $tasks = $taskRepository->findAll();
        $forms = array();
        if (is_array($tasks) && !empty($tasks)) {
            foreach($tasks as $task) {
                $form = $this->createForm(TaskType::class, $task);
                $forms[$task->getId()] = $form->createView();
            }
        }

        // Création du formulaire d'ajout
        $newTaskForm = $this->createForm(TaskType::class, new Task());

        return $this->render('list/index.html.twig', [
            'lists' => $lists,
            'editForms' => $forms,
            'newForm' => $newTaskForm->createView()
        ]);
    }
}
