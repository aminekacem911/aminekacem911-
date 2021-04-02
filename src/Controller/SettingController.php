<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/setting")
 */
class SettingController extends AbstractController
{
   

    /**
     * @Route("/{id}/edit", name="setting_edit", methods={"GET","POST"})
     */
    public function edit(SettingRepository $rep,Request $request, Setting $setting): Response
    {
    
        $set =$rep->find($setting);
        $form = $this->createForm(SettingType::class,$setting);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $imageFile = $form->get('logo')->getData();
            $fileName=md5(uniqid()).'.'.$imageFile->getExtension();
            $imageFile->move($this->getParameter('image_directory'),$fileName);
            $set->setLogo($fileName);
            $article = $form->getData();
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($set);
            $entityManager->flush();
            $this->addFlash('success', 'Settings  updated successfully!');
        }

        return $this->render('setting/edit.html.twig', [
            'setting' => $set,
            'form' => $form->createView(),
        ]);
    }

    
}
