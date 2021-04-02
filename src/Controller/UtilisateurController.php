<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/users", name="utilisateur_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UtilisateurRepository $utilisateurRepository, Session $session): Response
    {
            //besoin de droits admin
            $utilisateur = $this->getUser();
            if(!$utilisateur)
            {
                    $session->set("message", "Merci de vous connecter");
                    return $this->redirectToRoute('app_login');
            }

            else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
                    return $this->render('utilisateur/index.html.twig', [
                            'utilisateurs' => $utilisateurRepository->findAll(),
                    ]);
            }

            return $this->redirectToRoute('home');
    }



    /**
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Utilisateur $utilisateur): Response
    {
            //accès géré dans le security.yaml
            return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
            ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur, UserPasswordEncoderInterface $passwordEncoder, Session $session, $id): Response
    {
            $utilisateur = $this->getUser();
            if($utilisateur->getId() != $id )
            {
                    // un utilisateur ne peut pas en modifier un autre
                    $session->set("message", "Vous ne pouvez pas modifier cet utilisateur");
                    return $this->redirectToRoute('membre');
            }
            
            $form = $this->createForm(UtilisateurType::class, $utilisateur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                    $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('utilisateur_index');
            }

            return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
            ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur, Session $session, $id): Response
    {
            $utilisateur = $this->getUser();
            if($utilisateur->getId() != $id )
            {
                    // un utilisateur ne peut pas en supprimer un autre
                    $session->set("message", "Vous ne pouvez pas supprimer cet utilisateur");
                    return $this->redirectToRoute('membre');
            }

            if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token')))
            {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($utilisateur);
                    $entityManager->flush();
                    // permet de fermer la session utilisateur et d'éviter que l'EntityProvider ne trouve pas la session
                    $session = new Session();
                    $session->invalidate();
            }

            return $this->redirectToRoute('home');
    }
}
