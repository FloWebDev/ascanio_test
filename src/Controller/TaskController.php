<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index", methods={"GET"})
     */
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"POST"})
     */
    public function edit($id, Task $task, Request $request): Response
    {
        // is it an Ajax request?
        $isAjax = $request->isXmlHttpRequest();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($isAjax) {
                return $this->json([
                    'formId' => $id,
                    'success' => true,
                    'message' => array('Modifications effectuées')
                ]);
            } else {
                return $this->redirectToRoute('list');
            }
        }

        // Récupération des erreurs du formulaire afin de pouvoir les afficher en JS
        $errorList = array();
        $errors = $form->getErrors(true);
        foreach($errors as $error) {
            $errorList[] = $error->getMessage();
        }

        if ($isAjax) {
            return $this->json([
                'formId' => $id,
                'success' => false,
                'message' => $errorList
            ]);
        } else {
            return $this->redirectToRoute('list');
        }
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"GET"})
     */
    public function delete(Task $task): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Tâche supprimée.'
        );
        return $this->redirectToRoute('list');
    }
}
