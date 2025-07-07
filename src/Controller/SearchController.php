<?php

namespace App\Controller;

use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $tag = $request->query->get('tag');
        $templates = [];

        if ($tag) {
            $templates = $this->entityManager->createQueryBuilder()
                ->select('t')
                ->from(Template::class, 't')
                ->where('t.tags LIKE :tag')
                ->setParameter('tag', '%' . $tag . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('search/results.html.twig', [
            'templates' => $templates,
            'tag' => $tag,
        ]);
    }
}