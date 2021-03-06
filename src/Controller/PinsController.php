<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Flex\Response as FlexResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfToken;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home",methods="GET")
     */
    public function index(PinRepository $pinRepository):Response
    { 
       $pins = $pinRepository->findBy([],['createdAt'=>'DESC']);
        return $this->render('pins/index.html.twig',compact('pins'));
    }
    /**
     * @Route("/pins/create", name="app_pins_create",methods={"GET","POST"})
     */
    public function create(Request $request,EntityManagerInterface $em):Response
    {
        $pin = new Pin;
       $form = $this->createForm(PinType::class,$pin);
            
       $form->handleRequest($request);
        
        if($form->isSubmitted()&& $form->isValid()){
        $pin->setUser($this->getUser());
        $em->persist($pin);
        $em->flush();
        
        $this->addFlash('success','Le Pin a été créer avec succes');

         return $this->redirectToRoute("app_home");
          }

             
      return $this->render("/pins/create.html.twig",[
          'form'=>$form->createView()
      ]);
    }
    
    /**
     * @Route("/pins/{id}", name="app_pins_show",methods="GET")
     */
    public function show(Pin $pin):Response
    {
        
        return $this->render("pins/show.html.twig",compact('pin'));
    }
     /**
     * @Route("/pins/{id}/edit", name="app_pins_edit",methods={"GET","PUT"})
     */
    public function edit(Pin $pin,Request $request,EntityManagerInterface $em):Response
    {
        $form = $this->createForm(PinType::class,$pin,[
            'method'=>'PUT'
        ]);
             
             ;
             $form->handleRequest($request);
             if($form->isSubmitted()&& $form->isValid()){
             $em->flush();
             $this->addFlash('success','Le monument a été modifié avec succes');
        
             return $this->redirectToRoute("app_home");
            }
            return $this->render("/pins/edit.html.twig",[
                'pin'=>$pin,
                'form'=>$form->createView()
            ]);
    }
     /**
      * @Route("/pins/{id}",name="app_pins_delete",methods={"DELETE"})
      */
    public function delete(Request $request,Pin $pin,EntityManagerInterface $em):Response
    {
        if($this->isCsrfTokenValid('pin.delete'.$pin->getId(),$request->request->get('csrf_token'))){

        }
        $em->remove($pin);
        $em->flush();
        $this->addFlash('info','Le monument a été supprimé avec succes!');

        return $this->redirectToRoute("app_home");
       return $this->render("/pins/delete.html.twig");
    }
}
