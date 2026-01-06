<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Request; 
use Doctrine\ORM\EntityManagerInterface;

final class TaskController extends AbstractController
{
    /*//#[Route('/task', name: 'app_task')]
    #[Route('/tasks', name: 'app_task')]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }*/

    //#[Route('/task', name: 'app_task')]
    #[Route('/tasks', name: 'app_task')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /*#[Route('/tasks/{id}', name: 'app_task_show')]
    public function show(int $id, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }*/

    #[Route('/task/{id<\d+>}', name: 'app_task_show')]
    public function show(Task $task): Response
    {       
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/task/new', name: 'task_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response 
    {
        $task = new Task;

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($task);
            $manager->flush();

            //dd($request->request->all());

            //dd($product);

            $this->addFlash(
                'notice',
                'Task added successfully!'
            );

            return $this->redirectToRoute('app_task_show', [
                'id' => $task->getId()
            ]);
        }

        // W Symfony do Twig powinno się przekazywać widok formularza, nie obiekt formularza

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);

        /*return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);*/

    }

    #[Route('/task/{id<\d+>}/edit', 'task_edit')]
    public function edit(Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $manager->flush();

            //dd($request->request->all());

            //dd($product);

            $this->addFlash(
                'notice',
                'Task updated successfully!'
            );

            return $this->redirectToRoute('app_task_show', [
                'id' => $task->getId()
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/task/{id<\d+>}/delete', 'task_delete')]
    public function delete(Request $request, Task $task, EntityManagerInterface $manager): Response
    {
        if ($request->isMethod('POST')) {

            $manager->remove($task);

            $manager->flush();

            $this->addFlash(
                'notice',
                'Task deleted successfully!'
            );

            return $this->redirectToRoute('app_task');
        }

        return $this->render('task/delete.html.twig', [
            'id' => $task->getId(),
        ]);
    }

    #[Route('/task/{id<\d+>}/toggle', name: 'task_toggle')]
    public function toggle(Request $request, Task $task, EntityManagerInterface $manager): Response
    {
        // ręczna kontrola metody (bez methods: ['POST'] w routingu)
        if (!$request->isMethod('POST')) {
            // możesz też zrobić redirect zamiast 405, ale 405 jest czytelne
            return new Response('Method Not Allowed', 405);
        }

        $task->setIsDone(!$task->isDone()); // toggle
        $manager->flush();

        $this->addFlash(
            'notice', 
            'Task status updated!'
        );

        return $this->redirectToRoute('app_task_show', [
            'id' => $task->getId(),
        ]);
    }
}
