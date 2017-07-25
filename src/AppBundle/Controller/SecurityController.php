<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{


    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/login/redirect", name="login_redirect")
     */
    public function loginRedirectAction(Request $request)
    {
        $auth_checker = $this->get('security.authorization_checker');

        if($auth_checker->isGranted('ROLE_ADMIN') || $auth_checker->isGranted('ROLE_AZUBI'))
        {
            return $this->redirectToRoute('components');
        }
        else if($auth_checker->isGranted('ROLE_MANAGE') || $auth_checker->isGranted('ROLE_TEACHER')){
            return $this->redirectToRoute('reporting');
        }
        else
        {
            return $this->redirectToRoute('login');
        }
    }

    //TODO: remove this
    /**
     * @Route("/createadmin", name="create_admin")
     */
    public function createAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("AppBundle:User");
        $user = new User();
        $user->setEmail("t.s@headtrip.eu");
        $user->setName("Thomas Schäfer");
        $user->setRoles(array("ROLE_TEACHER"));
        $user->setIsActive(true);
        $user->setUsername("t.s@headtrip.eu");
        $user->setAddress("asdf");
        $user->setDateOfBirth(date_create());
        $user->setGender("male");

        $plainPassword = "123";

        //password can be max 4096 characters long
        if (strlen($plainPassword) > 4096) {
            return new JsonResponse("success: false");
        }
        //create hashed password
        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $plainPassword);
        //set password
        $user->setPassword($password);

        //save user to db
        $em->persist($user);
        $em->flush();

        return new JsonResponse("success: true");
    }
}
