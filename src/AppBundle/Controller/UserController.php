<?php

namespace AppBundle\Controller;

/**
 * Handles logic for users and their roles.
 */

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_index")
     */
    public function indexAction(Request $request, $page = 0)
    {
        //get all users from db
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("AppBundle:User");
        //TODO: Paginate
        $users = $user_repo->findAll();
        //render template with users

        return $this->render('default/index.html.twig', [
            'users' => $users,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    /**
     * @Route("/users/{id}", name="users_show", requirements={"id": "\d+"})
     */
    public function showUserAction(Request $request, $id)
    {
        //get user from db
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')->find($id);
        //check if user exists
        if (!$user) {
            throw $this->createNotFoundException('User does not exist');
        }
        //render template with user

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    /**
     * @Route("/users/create", name="users_create")
     */
    public function reportingAction(Request $request)
    {
        //if method is GET -> render template
        if ($request->getMethod() === "GET") {
            return $this->render('user/create.html.twig');
        }
        //if method is POST -> create user and redirect
        if ($request->getMethod() === "POST") {
            //create User Object
            $new_user = new User();
            //get POST data
            $data = $request->request->all();
            $email = $data["email"];
            if ($email === null || $email === "") {
                $error = "please specify email";
            }
            //check if user with this email already exists
            $em = $this->getDoctrine()->getManager();
            $existing_user = $em->getRepository('AppBundle:User')->findOneByEmail($email);
            if ($existing_user) {
                $error = "email already taken";
            }
            $new_user->setEmail($email);
            $new_user->setUsername($email);
            $new_user->setName($data["name"]);
            $role = $data["role"];
            if (!$this->roleExists($role)) {
                $error = "invalid role";
            }
            $new_user->setRoles(array($role));

            $plainPassword = $data["plain_password"];

            //password can be max 4096 characters long
            if (strlen($plainPassword) > 4096 || strlen($plainPassword) < 1) {
                $error = "Invalid password length";
            }
            //create hashed password
            $password = $this->get('security.password_encoder')
                ->encodePassword($new_user, $plainPassword);
            //set password
            $new_user->setPassword($password);
            $new_user->setIsActive(true);
            //save user to db
            $em->persist($new_user);
            $em->flush();
            //redirect to show user with new user
            $user_id = $new_user->getId();
            return $this->redirectToRoute("users_show", array("id" => $user_id));
        }
    }

    private function roleExists($role)
    {
        $blRoleExists = false;
        switch (true) {
            case ($role == "ROLE_ADMIN"):
                $blRoleExists = true;
                break;
            case ($role == "ROLE_AZUBI"):
                $blRoleExists = true;
                break;
            case ($role == "ROLE_MANAGE"):
                $blRoleExists = true;
                break;
            case ($role == "ROLE_TEACHER"):
                $blRoleExists = true;
                break;
        }
        return $blRoleExists;
    }
}
