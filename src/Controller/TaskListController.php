<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\TaskList;
use App\Form\TaskListType;
use App\Repository\TaskRepository;
use App\Repository\TaskListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskListController extends AbstractController
{
    /**
     * Page centrale de l'application
     * 
     * @Route("/", name="home_page")
     */
    public function index(TaskListRepository $taskListRepository, TaskRepository $taskRepository, Request $request)
    {
        $lists = $taskListRepository->findBy([], [
            'z_order' => 'ASC'
        ]);

        return $this->render('list/index.html.twig', [
            'lists' => $lists
        ]);
    }

    /**
     * @Route("/lists", name="task_list_index", methods={"GET"})
     */
    public function getAll(TaskListRepository $taskListRepository): Response
    {
        return $this->render('task_list/index.html.twig', [
            'task_lists' => $taskListRepository->findBy([], [
                'z_order' => 'ASC'
            ]),
        ]);
    }

    /**
     * @Route("/list/add", name="task_list_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $taskList = new TaskList();
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($taskList);
            $entityManager->flush();

            // Message Flash
            $this->addFlash(
                'success',
                'Liste créée.'
            );

            return $this->redirectToRoute('task_list_index');
        }

        return $this->render('task_list/new.html.twig', [
            'task_list' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list/{id}/edit", name="task_list_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TaskList $taskList): Response
    {
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Message Flash
            $this->addFlash(
                'success',
                'Liste modifiée.'
            );

            return $this->redirectToRoute('task_list_index');
        }

        return $this->render('task_list/edit.html.twig', [
            'task_list' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list/{id}/delete", name="task_list_delete", methods={"GET"})
     */
    public function delete(TaskList $taskList, EntityManagerInterface $em): Response
    {
        // On récupère et supprimer toutes les tâches associées à la liste
        $tasks = $taskList->getTasks();
        foreach($tasks as $task) {
            $em->remove($task);
            $em->flush();
        }

        // Puis on supprime la liste
        $em->remove($taskList);
        $em->flush();

        // Message Flash
        $this->addFlash(
            'success',
            'Liste supprimée (avec toutes ses tâches associées).'
        );

        return $this->redirectToRoute('task_list_index');
    }
}
