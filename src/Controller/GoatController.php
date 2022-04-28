<?php

namespace App\Controller;

use App\Entity\Goat;
use App\Entity\GoatCommand;
use App\Form\GoatCommandType;
use App\Form\GoatType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security ;

#[Route('/goat', name:"goat")]

class GoatController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function listAction(ManagerRegistry $doctrine): Response
    {   $em = $doctrine->getManager();
        $goatRepo = $em->getRepository('App:Goat');
        $goats = $goatRepo->findAll();

        $args = array('goats' => $goats);
        return $this->render('Goat/goat_list.html.twig', $args);
    }

    #[Route('/achat', name: '_achat')]
    public function achatAction(ManagerRegistry $doctrine, Request $req, FormFactoryInterface $formFactory): Response{

        $em = $doctrine->getManager();
        $goatRepo = $em->getRepository('App:Goat');
        $goats = $goatRepo->findAll();
        $qteTab = [];
        for($i=0;$i<= 100;$i++){
            $qteTab[$i] = $i;
        }
        $raceTab = [];
        for($i=0;$i<count($goats);$i++){
            $raceTab[$i]=$goats[$i]->getId();
        }

        $cmd= new GoatCommand();
        $form = $formFactory->createNamed("Formulaire_de_commande",GoatCommandType::class,$cmd,['quantite'=>$qteTab, 'race'=>$raceTab,'action'=>$this->generateUrl('goat_achat')]);

        $form->handleRequest($req);

        if ($form->isSubmitted())//Pas besoin de vérifier la validité puisque l'utilisateur ne peut sélectionner ou entrer des valeurs incorrectes
        {
            $this->addFlash('Info', 'Commande de chèvre réussie');
            $data=$form->getData();
            return $this->redirect($this->generateUrl('panier_achat',['idGoat'=>$data->getRace(),'quantity'=>$data->getQuantite()]));
        }

        $args = array('form' => $form);
        return $this->renderForm('Goat/goat_commande.html.twig',$args);
    }

    #[Route('/view/{id}', name: '_view')]
    public function viewAction($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $goatRepo = $em->getRepository('App:Goat');
        $goat = $goatRepo->find($id);
        return $this->render('Goat/goat_view.html.twig', array('goat' => $goat, 'id' => $id));
    }

    #[Route('/add', name: '_add')]
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function chevreAjoutAction(EntityManagerInterface $em, Request $req): Response
    {
        $goat = new Goat();

        $form = $this->createForm(GoatType::class, $goat);
        $form->add('send', SubmitType::class, ['label' => 'Ajout chèvre']);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->addFlash('Info', 'Ajout de chèvre réussi');
            $em->persist($goat);
            $em->flush();
            return $this->redirect($this->generateUrl('goat_list'));
        }

        if ($form->isSubmitted())
            $this->addFlash('Info', 'Ajout incorrect');

        $args = array('myform' => $form->createView());
        return $this->render('Goat/goat_add.html.twig',$args);
    }

    #[Route('/delete/{id}', name: '_delete')]
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction($id, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $PGs = $em->getRepository("App:PanierGoat")->findBy(array('goat' => $id));
        foreach ($PGs as $pg){
            $em->remove($pg);
            $em->flush();
            $pg->getPanier()->updateTotalPrice($doctrine);
        }
        $em->remove($em->getRepository('App:Goat')->find($id));
        $em->flush();
        $session->getFlashBag()->add('info', 'L\'enregistremement a été supprimé');
        return $this->redirect($this->generateUrl('goat_list'));
    }
}
