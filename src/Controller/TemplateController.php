<?php

namespace App\Controller;

use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/template/{id}', name: 'template_view')]
    public function view(Template $template): Response
    {
        return $this->render('template/view.html.twig', [
            'template' => $template,
        ]);
    }
}