<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/account', name: 'app_account')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $user = $this->userRepository->findOneBy(['email' => 'demo@netflix.com']);

            if (!$user) {
                $user = new User();
                $user->setEmail('test.hdfi');
                $user->setFirstName('Eliott');
                $user->setLastName('Alderson');
                $user->setUsername('mrrobot');
                $user->setPassword('demo1234');
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
