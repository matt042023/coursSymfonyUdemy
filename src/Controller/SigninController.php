<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception;

class SigninController extends AbstractController
{
    #[Route('/signin', name: 'app_signin')]
    public function index(Request $req, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $PasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(SigninType::class, $user);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Si le formulaire est valide, on persiste l'utilisateur
                $user = $form->getData();

                $passwordNotCripted = $user->getPassword();

                $hashedPassword = $PasswordHasher->hashPassword($user, $passwordNotCripted);

                $user->setPassword($hashedPassword);

                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();

                // Ajouter un message flash de succès
                $this->addFlash('success', 'Formulaire soumis avec succès !');

                // Redirection pour éviter une double soumission
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException) {
                // Cet email est déjà utilisé
                $this->addFlash('error', 'Cet email est déjà utilisé. Veuillez en choisir un autre.');
                return $this->redirectToRoute('app_signin');
            } catch (Exception) {
                // Gestion générique des autres erreurs
                $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer plus tard.');
                return $this->redirectToRoute('app_signin');
            }
        }

        return $this->render('signin/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
