<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AuthController extends AbstractController implements AccessDeniedHandlerInterface
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('auth/login.html.twig', [
            'page'          => 'login',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {
        throw new Exception("hi. 
            we encountered an error when logging you out. 
            we apologize for any inconvenience this may have caused you. 
            why are you logging out of banane-clicker anyways? 
            this is the best game ever created. 
            you definitely shouldn't do anything except play it forever and ever.
        ");
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) return $this->redirectToRoute('account');

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig', [
            'page'              => 'register',
            'registrationForm'  => $form->createView(),
        ]);
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $user = $this->getUser();
        if ($user) return $this->redirectToRoute('account');
        return $this->redirectToRoute('app_login');
    }
}
