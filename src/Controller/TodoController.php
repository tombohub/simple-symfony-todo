<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Enum\TodoStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends AbstractController
{
    #[Route('/', name: 'app_todo')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $todo = new Todo();

        $form = $this->createFormBuilder($todo)
            ->add('title', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todo = $form->getData();
            $entityManager->persist($todo);
            $entityManager->flush();
            $this->addFlash('info', 'Todo created');
            return $this->redirectToRoute('app_todo');
        }

        $todos = $entityManager->getRepository(Todo::class)->findAll();
        return $this->render('index.html.twig', [
            'form' => $form,
            'todos' => $todos
        ]);
    }

    #[Route('/toggle/{id}', name: 'app_toggle')]
    public function toggleStatus(EntityManagerInterface $entityManager, int $id)
    {
        /** @var Todo */
        $todo = $entityManager->getRepository(Todo::class)->find($id);

        $newStatus = $todo->getStatus() === TodoStatus::DONE ? TodoStatus::NOT_DONE : TodoStatus::DONE;
        $todo->setStatus($newStatus);
        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->redirectToRoute('app_todo');
    }
}
