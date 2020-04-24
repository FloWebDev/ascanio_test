<?php

namespace App\Controller;

use App\Form\TaskType;
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
        $listes = $taskListRepository->findBy([], [
            'z_order' => 'ASC',
            'name' => 'ASC'
        ]);

        $tasks = $taskRepository->findBy([], [
            'z_order' => 'DESC',
            'name' => 'ASC'
        ]);

        $forms = array();
        foreach($tasks as $task) {
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);

            $forms[$task->getId()] = $form->createView();
        }

        return $this->render('list/index.html.twig', [
            'listes' => $listes,
            'forms' => $forms
        ]);
    }
}
