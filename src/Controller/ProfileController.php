<?php

namespace App\Controller;

use App\Entity\Template;
use App\Entity\Question;
use App\Entity\Form;
use App\Entity\QuestionTypeEnum;
use App\Form\TemplateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'profile')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER','ROLE_ADMIN');

        if ($this->getUser()->isBlocked()) {
            throw new AccessDeniedException('Your account is blocked.');
        }

        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $template->setCreatedBy($this->getUser());
            $template->setCreatedAt(new \DateTimeImmutable());

            $tags = $form->get('tags')->getData();
            $template->setTags(is_array($tags) ? $tags : []);

            $questions = $form->get('questions')->getData();
            $typeCounts = [
                QuestionTypeEnum::STRING->value => 0,
                QuestionTypeEnum::TEXT->value => 0,
                QuestionTypeEnum::INTEGER->value => 0,
                QuestionTypeEnum::CHECKBOX->value => 0,
            ];

            foreach ($questions as $index => $question) {
                $type = $question->getType()->value;
                $typeCounts[$type]++;
                if ($typeCounts[$type] > 4) {
                    $form->get('questions')->addError(new \Symfony\Component\Form\FormError("Максимум 4 вопроса типа '$type'."));
                    return $this->render('profile/index.html.twig', [
                        'form' => $form->createView(),
                        'templates' => $this->getUser()->getTemplates(),
                        'forms' => $this->entityManager->getRepository(Form::class)->findBy(['user' => $this->getUser()]),
                    ]);
                }
                $question->setPosition($index + 1);
                $template->addQuestion($question);
            }

            $this->entityManager->persist($template);
            $this->entityManager->flush();

            $this->addFlash('success', 'Шаблон успешно создан!');
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'templates' => $this->getUser()->getTemplates(),
            'forms' => $this->entityManager->getRepository(Form::class)->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/profile/template/{id}/delete', name: 'template_delete', methods: ['POST'])]
    public function deleteTemplate(Request $request, Template $template): Response
    {
        if ($this->getUser() && $this->getUser()->isBlocked()) {
            throw new AccessDeniedException('Your account is blocked.');
        }

        if ($this->getUser() !== $template->getCreatedBy() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You can only delete your own templates.');
        }

        if ($this->isCsrfTokenValid('delete' . $template->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($template);
            $this->entityManager->flush();
            $this->addFlash('success', 'Template deleted successfully!');
        }

        return $this->redirectToRoute('profile');
    }
}