<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_index")
     */
    public function indexAction(Request $request)
    {
        //get all users from db
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("AppBundle:User");
        //TODO: Paginate
        $users = $user_repo->findAll();
        //render template with users
        return $this->render('user/index.html.twig', [
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
        if($request->getMethod() === "GET"){
            //render template with user
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
        if($request->getMethod() === "POST"){
            //get POST data
            $data = $request->request->all();
            //name
            $name = $data["name"];
            $user->setName($name);
            //email
            $email = $data["email"];
            //check if other user exists with new email
            if(false){
                $error = "user with this email already exists";
            }
            $user->setEmail($email);
            $user->setUsername($email);
            //set new password

            $plainPassword = $data["plain_password"];

            //password can be max 4096 characters long
            if (strlen($plainPassword) > 4096 || strlen($plainPassword) < 1) {
                $error = "Invalid password length";
            }
            //create hashed password
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $plainPassword);
            //set password
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            //render template with new user
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
        return $this->createNotFoundException("user not found");
    }

    /**
     * @Route("/users/create", name="users_create")
     */
    public function reportingAction(Request $request)
    {
        //if method is GET -> render template
        if($request->getMethod() === "GET"){
            return $this->render('user/create.html.twig');
        }
        //if method is POST -> create user and redirect
        if($request->getMethod() === "POST"){
            //create User Object
            $new_user = new User();
            //get POST data
            $data = $request->request->all();
            $email = $data["email"];
            if($email === null || $email === ""){
                $error = "please specify email";
            }
            //check if user with this email already exists
            $em = $this->getDoctrine()->getManager();
            $existing_user = $em->getRepository('AppBundle:User')->findOneByEmail($email);
            if($existing_user){
                $error = "email already taken";
            }
            $new_user->setEmail($email);
            $new_user->setUsername($email);
            $new_user->setName($data["name"]);
            $role = $data["role"];
            if(!$this->roleExists($role)){
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
            $this->redirectToRoute("users_show", array("id" => $user_id));
        }
        return $this->createNotFoundException("Not Found");
    }

    /**
     * @Route("/users/{id}/delete", name="users_delete", requirements={"id": "\d+"})
     */
    public function deleteUserAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("AppBundle:User");
        $user = $user_repo->find($id);
        if(!$user){
            $error = "cannot delete user, user does not exist";
        }
        $em->remove($user);
        $em->flush();
        $this->redirectToRoute("users_index");
    }

    private function roleExists($role){
        if($role == "ROLE_ADMIN"){
            return true;
        }

        if($role == "ROLE_AZUBI"){
            return true;
        }

        if($role == "ROLE_MANAGE"){
            return true;
        }

        if($role == "ROLE_TEACHER"){
            return true;
        }
        return false;

    }
}
