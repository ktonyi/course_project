<?php

namespace App\Controller;

use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
       
        $latestTemplates = $this->entityManager->getRepository(Template::class)
            ->findBy([], ['createdAt' => 'DESC'], 10);

        
        $topTemplates = $this->entityManager->createQueryBuilder()
            ->select('t, COUNT(f.id) as formCount')
            ->from(Template::class, 't')
            ->leftJoin('t.forms', 'f')
            ->groupBy('t.id')
            ->orderBy('formCount', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

       
        $tags = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT t.tags')
            ->from(Template::class, 't')
            ->getQuery()
            ->getResult();
        $uniqueTags = [];
        foreach ($tags as $tagArray) {
            foreach ($tagArray['tags'] as $tag) {
                $uniqueTags[$tag] = true;
            }
        }
        $uniqueTags = array_keys($uniqueTags);

        return $this->render('home.html.twig', [
            'latest_templates' => $latestTemplates,
            'top_templates' => $topTemplates,
            'tags' => $uniqueTags,
        ]);
    }
}