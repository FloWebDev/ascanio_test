<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\TaskList;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/new", name="task_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        // is it an Ajax request ?
        if ($request->isXmlHttpRequest()) {
            $task = new Task();
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                // Par défaut, on place la nouvelle tâche créée en début de liste
                // L'utilisateur pourra modifier l'ordre par la suite côté Front
                $task->setZOrder(1);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                // Cleanage des z-order
                $this->cleanOrderTask();
    
                $this->addFlash(
                    'success',
                    'Tâche ajoutée.'
                );

                return $this->json([
                    'success' => true,
                    'message' => array('Ajout effectué')
                ]);
            } else {
                // Récupération des erreurs du formulaire afin de pouvoir les afficher en JS
                $errorList = array();
                $errors = $form->getErrors(true);
                foreach($errors as $error) {
                    $errorList[] = $error->getMessage();
                }
        
                return $this->json([
                    'success' => false,
                    'message' => $errorList
                ]);
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"POST"})
     */
    public function edit($id, Task $task, Request $request): Response
    {
        // is it an Ajax request ?
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                    'success',
                    'Tâche modifiée.'
                );
    
                return $this->json([
                    'formId' => $id,
                    'success' => true,
                    'message' => array('Modifications effectuées')
                ]);
            } else {
                // Récupération des erreurs du formulaire afin de pouvoir les afficher en JS
                $errorList = array();
                $errors = $form->getErrors(true);
                foreach($errors as $error) {
                    $errorList[] = $error->getMessage();
                }
        
                return $this->json([
                    'formId' => $id,
                    'success' => false,
                    'message' => $errorList
                ]);
            }
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

        // Cleanage des z-order
        $this->cleanOrderTask();

        $this->addFlash(
            'success',
            'Tâche supprimée.'
        );

        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/{id}/up", name="task_up", methods={"GET"})
     */
    public function toUp(Task $task): Response
    {
        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/{id}/down", name="task_down", methods={"GET"})
     */
    public function toDown(Task $task): Response
    {
        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/{id}/right", name="task_right", methods={"GET"})
     */
    public function toRight(Task $task): Response
    {
        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/{id}/left", name="task_left", methods={"GET"})
     */
    public function toLeft(Task $task): Response
    {
        return $this->redirectToRoute('list');
    }

    /**
     * Permet d'ordonner et réactualiser le z_order des tâches pour éviter
     * que plusieurs tâches aient le même z_order 
     * sans pour autant ajouter de constraint d'unicité sur ce champ dans l'entity
     * (car pourrait avoir des effets de bord bloquant pour la suite des évolutions)
     */
    private function cleanOrderTask() {
        // Récupération de toutes les listes
        $lists = $this->getDoctrine()->getRepository(TaskList::class)->findAll();
        if (is_array($lists) && !empty($lists)) {
            foreach($lists as $list) {
                // Pour chaque liste, récupération de toutes les tâches
                // dans un ordre défini dans la propriété Tasks de l'entité TaskList
                $tasks = $list->getTasks();
                foreach($tasks as $rang => $task) {
                    //
                    $task->setZOrder($rang + 1);
                    $this->getDoctrine()->getManager()->flush();

                }
            }
        }
    }
}
