<?php

namespace AppBundle\Controller;

/**
 * Handles security logic.
 */

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{


    /**
     * @Route("/", name="login_base")
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

        if ($auth_checker->isGranted('ROLE_ADMIN') || $auth_checker->isGranted('ROLE_AZUBI')) {
            return $this->redirectToRoute('reporting_index');
        } elseif ($auth_checker->isGranted('ROLE_MANAGE') || $auth_checker->isGranted('ROLE_TEACHER')) {
            return $this->redirectToRoute('reporting_index');
        } else {
            return $this->redirectToRoute('login');
        }
    }

}
