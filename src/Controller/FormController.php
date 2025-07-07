<?php

namespace App\Controller;

use App\Entity\Template;
use App\Entity\Form;
use App\Form\FormAnswerType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FormController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/template/{id}/fill', name: 'form_fill', methods: ['GET', 'POST'])]
    public function fill(Request $request, Template $template): Response
    {
        
        $this->denyAccessUnlessGranted('ROLE_USER');

       
        if (!$template->isPublic() && !$template->getAllowedUsers()->contains($this->getUser()) && $this->getUser() !== $template->getCreatedBy() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Вы не можете заполнить этот шаблон.');
        }

       
        if ($this->getUser()->isBlocked()) {
            throw new AccessDeniedException('Ваш аккаунт заблокирован.');
        }

        $formEntity = new Form();
        $formEntity->setTemplate($template);
        $formEntity->setUser($this->getUser());

        $form = $this->createForm(FormAnswerType::class, $formEntity, [
            'questions' => $template->getQuestions(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($formEntity);
            $this->entityManager->flush();

            $this->addFlash('success', 'Форма успешно заполнена!');
            return $this->redirectToRoute('profile');
        }

        return $this->render('form/fill.html.twig', [
            'template' => $template,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/form/{id}', name: 'form_view', methods: ['GET'])]
    public function view(Form $form): Response
    {
       
        if ($this->getUser() !== $form->getUser() && $this->getUser() !== $form->getTemplate()->getCreatedBy() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Вы не можете просматривать эту форму.');
        }

        return $this->render('form/view.html.twig', [
            'form' => $form,
            'template' => $form->getTemplate(),
        ]);
    }
}