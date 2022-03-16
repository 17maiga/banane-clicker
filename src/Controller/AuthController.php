<?php

namespace App\Controller;


use App\Entity\Upgrade;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Exception;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

// All authentication and registration-related functions
class AuthController extends AbstractController implements AccessDeniedHandlerInterface
{
    // Login the user
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Get any errors and the last entered username from the login form
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login form
        return $this->render('auth/login.html.twig', [
            'page'          => 'login',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    // Logout the user
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

    // Register a new user
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $registry): Response
    {
        // If a user is already logged in, redirect to the user's account ('/u')
        if ($this->getUser()) return $this->redirectToRoute('account');

        // Instantiate a new User, create a form and bind the user and request to the form
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            // Hash the plain password from the form and set the user's password to the hashed one
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Set the user's score to 0 and their data to an empty array
            $user->setScore(0)
                 ->setData([]);

            // Write the new user to the database
            $manager = $registry->getManager();
            $manager->persist($user);
            $manager->flush();

            // Redirect to the login form ('/login')
            return $this->redirectToRoute('app_login');
        }

        // Render the registration form
        return $this->render('auth/register.html.twig', [
            'page'              => 'register',
            'registrationForm'  => $form->createView(),
        ]);
    }

    // Is triggered whenever a user tries to access a page they aren't allowed to open
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // If a user is logged in, redirect to the account ('/u'), else redirect to the login form ('/login')
        if ($this->getUser()) return $this->redirectToRoute('account');
        return $this->redirectToRoute('app_login');
    }
}
