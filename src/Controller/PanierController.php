<?php

namespace App\Controller;

use App\Entity\PanierGoat;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security ;

#[Route('/panier', name: 'panier')]

class PanierController extends AbstractController
{

    #[Route('/view', name: '_view_self')]
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function viewSelfAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $client = $em->getRepository('App:UserV2')->find($this->getUser()->getId());
        $idPanier = $client->getPanier();
        $panier = $em->getRepository('App:Panier')->find($idPanier);
        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $idPanier));
        $panier->updateTotalPrice($doctrine);
        return $this->render('Panier/panier_view.html.twig', array('panier' => $panier, 'client' => $client, 'PGs'=>$collecPaniers));
    }


    #[Route('/view/{id}', name: '_view', requirements: ["id" => "\d+"])]
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function viewAction($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $client = $em->getRepository('App:UserV2')->find($id);
        $idPanier = $client->getPanierId();
        $panier = $em->getRepository('App:Panier')->find($idPanier);
        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $idPanier));
        $panier->updateTotalPrice($doctrine);
        return $this->render('Panier/panier_view.html.twig', array('panier' => $panier, 'client' => $client, 'PGs'=>$collecPaniers));
    }

    #[Route(name: '_view_menu')]
    public function viewMenuAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $client = $em->getRepository('App:UserV2')->find($this->getUser()->getId());
        $idPanier = $client->getPanier();
        $panier = $em->getRepository('App:Panier')->find($idPanier);
        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $idPanier));
        $panier->updateTotalPrice($doctrine);
        return $this->render('Panier/panier_view_menu.html.twig', array('panier' => $panier, 'client' => $client, 'PGs'=>$collecPaniers));
    }

    #[Route('/achat/{idGoat}/{quantity}', name: '_achat', requirements: ["idGoat" => "\d+", "quantity" => "\d+"])]
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function achatAction($idGoat, $quantity, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $panier= $this->getUser()->getPanier();
        $target=$em->getRepository("App:Goat")->find($idGoat);
        $client= $em->getRepository('App:UserV2')->find($this->getUser()->getId());
        if($target->getStock()<$quantity){
            $this->addFlash('info',"Vous ne pouvez pas acheter plus de chèvres qu'il n'y en a en stock");
        }
        else{
            $PGs = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $panier->getId(),'goat'=>$target));
            if($PGs==[]){
                $PG = new PanierGoat($panier,$target,$quantity);
            }
            else{
                $PG = $PGs[0];
                $PG->addQuantite($quantity);
            }
            $em->persist($PG);
            $target->setStock($target->getStock()-$quantity);
            $em->flush();
        }
        $panier->updateTotalPrice($doctrine);

        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $panier->getId()));
        return $this->render('Panier/panier_view.html.twig', array('panier' => $panier, 'client' => $client, 'PGs'=>$collecPaniers));
    }

    #[Route('/remove/{idGoat}', name: '_remove', requirements: ["idGoat" => "\d+"])]
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function remAction($idGoat, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $panier= $this->getUser()->getPanier();
        $target=$em->getRepository("App:Goat")->find($idGoat);
        $client= $em->getRepository('App:UserV2')->find($this->getUser()->getId());
        $PGs = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $panier->getId()));
        foreach ($PGs as $pg){
            if($pg->getGoat()->getId()==$idGoat){
                $em->remove($pg);
                $target->setStock($target->getStock()+$pg->getQuantite());
            }
        }
        $em->flush();
        $panier->updateTotalPrice($doctrine);

        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $panier->getId()));
        return $this->render('Panier/panier_view.html.twig', array('panier' => $panier, 'client' => $client, 'PGs'=>$collecPaniers));
    }

    #[Route('/vider/{id}', name: '_vider', requirements: ["id" => "\d+"], defaults: ["id" => -1])]
    public function viderAction($id,ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        if($id == -1){ // Afin de s'assurer qu'un user n'as accès qu'a son panier
            $client = $em->getRepository('App:UserV2')->find($this->getUser()->getId());
        }
        else{
            if ($this->isGranted('ROLE_SUPERADMIN') || $this->isGranted('ROLE_ADMIN')) { // Sauf si c'est un admin ou un super-admin
                $client = $em->getRepository('App:UserV2')->find($id);
                }
            else{
                $this->addFlash('info',"Vous n'avez pas les droit pour faire ça");
                return $this->redirectToRoute("panier_view_menu");
            }
            }

        $idPanier = $client->getPanier();
        $panier = $em->getRepository('App:Panier')->find($idPanier);
        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $idPanier));
        foreach($collecPaniers as $pg){
            $pg->getGoat()->addStock($pg->getQuantite());
            $em->remove($pg);
            $em->flush();
        }
        $panier->updateTotalPrice($doctrine);
        return $this->render('Panier/panier_view.html.twig', array('panier' => $panier, 'client' => $client, 'PGs'=>[]));
    }

    #[Route('/commander/{id}', name: '_commander', requirements: ["id" => "\d+"])]
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function commanderAction($id,ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        if($id == -1){
            $client = $em->getRepository('App:UserV2')->find($this->getUser()->getId());
        }
        else{
            if ($this->isGranted('ROLE_SUPERADMIN') || $this->isGranted('ROLE_ADMIN')) {
                $client = $em->getRepository('App:UserV2')->find($id);

            }
        }

        $idPanier = $client->getPanier();
        $panier = $em->getRepository('App:Panier')->find($idPanier);
        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $idPanier));
        foreach($collecPaniers as $pg){
            $em->remove($pg);
            $em->flush();
        }
        $panier->updateTotalPrice();
        return $this->render('Panier/panier_view.html.twig', array('panier' => $panier, 'client' => $client));
    }
}
