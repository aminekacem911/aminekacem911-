<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\UtilisateurRepository;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
          * @Route("/login", name="app_login")
          */
    public function login(Request $request, UtilisateurRepository $rep, AuthenticationUtils $authenticationUtils, Session $session): Response
    {
        $errors = $authenticationUtils->getLastAuthenticationError();
        if ($errors) {
            $this->addFlash('error', 'invalid credentials!');
        }
        $utilisateur = new Utilisateur();

        $form = $request->request->all();
                
        $user = $rep->findAll();
        foreach ($user as $u) {
            if ($u->getEmail() == $request->request->get('email')) {
                if (in_array('ROLE_ADMIN', $u->getRoles())) {
                    $url = 'admin';
                } else {
                    $url = 'app_navigation_search' ;
                }
                        
                return $this->redirectToRoute($url);
            }
        }
              
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /**
     * @Route("/register", name="register", methods={"GET","POST"})
     */
    public function new(UtilisateurRepository $rep, Request $request, UserPasswordEncoderInterface $passwordEncoder, Session $session): Response
    {
        $utilisateur = new Utilisateur();

        $form = $request->request->all();
        //$form->handleRequest($request);
        $user = $rep->findAll();
        foreach ($user as $u) {
            // dd($request->request->get('email'));
            //dd($u->getEmail());
            if ($u->getEmail() == $request->request->get('email')) {
                $this->addFlash('error', 'email already in use!');
                return $this->redirectToRoute('register');
            }
            if ($request->request->get('password') != $request->request->get('repassword')) {
                $this->addFlash('error', 'password mismatched');
                return $this->redirectToRoute('register');
            }
            if (isset($form['email'])) {
                $utilisateur->setEmail($form['email']);
            }
            if (isset($form['password'])) {
                $utilisateur->setPassword($form['password']);
            }
        }
        if (isset($form['email'])&& isset($form['password'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
    
            $entityManager->persist($utilisateur);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('security/register.html.twig');
    }
}
