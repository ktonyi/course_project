<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->getUser() && $this->getUser()->isBlocked()) {
            $this->tokenStorage->setToken(null);
            throw new AccessDeniedException('Your account is blocked.');
        }
        
        $userRepository = $this->entityManager->getRepository(User::class);
        $templateRepository = $this->entityManager->getRepository(Template::class);

        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
            'templates' => $templateRepository->findAll(),
            'forms' => [], 
        ]);
    }

    #[Route('/admin/user/{id}/block', name: 'admin_user_block', methods: ['POST'])]
    public function blockUser(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('block'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsBlocked(true);
            $this->entityManager->flush();

            $this->logger->debug('User blocked', ['email' => $user->getEmail(), 'isBlocked' => $user->isBlocked()]);

          
            if ($this->getUser() === $user) {
                $this->tokenStorage->setToken(null);
                $request->getSession()->invalidate();
                $this->addFlash('success', 'Your account has been blocked. You have been logged out.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('success', 'User blocked successfully!');
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/user/{id}/unblock', name: 'admin_user_unblock', methods: ['POST'])]
    public function unblockUser(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('unblock'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsBlocked(false);
            $this->entityManager->flush();
            $this->logger->debug('User unblocked', ['email' => $user->getEmail(), 'isBlocked' => $user->isBlocked()]);
            $this->addFlash('success', 'User unblocked successfully!');
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->getUser() === $user) {
            $this->addFlash('error', 'You cannot delete yourself!');
            return $this->redirectToRoute('admin');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->logger->debug('User deleted', ['email' => $user->getEmail()]);
            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/user/{id}/toggle-admin', name: 'admin_user_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('toggle-admin'.$user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                $user->setRoles(array_diff($roles, ['ROLE_ADMIN']));
                $this->addFlash('success', 'Admin role removed!');
                $this->logger->debug('Admin role removed', ['email' => $user->getEmail(), 'roles' => $user->getRoles()]);
            } else {
                $roles[] = 'ROLE_ADMIN';
                $user->setRoles($roles);
                $this->addFlash('success', 'Admin role added!');
                $this->logger->debug('Admin role added', ['email' => $user->getEmail(), 'roles' => $user->getRoles()]);
            }
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/template/{id}/delete', name: 'admin_template_delete', methods: ['POST'])]
    public function deleteTemplate(Request $request, Template $template): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$template->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($template);
            $this->entityManager->flush();
            $this->logger->debug('Template deleted', ['title' => $template->getTitle()]);
            $this->addFlash('success', 'Template deleted successfully!');
        }

        return $this->redirectToRoute('admin');
    }
}