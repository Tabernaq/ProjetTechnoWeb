<?php

namespace App\Controller;

use App\Entity\Goat;
use App\Form\GoatType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/goat', name:"goat")]

class GoatFormController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function listAction(ManagerRegistry $doctrine): Response
    {   $em = $doctrine->getManager();
        $goatRepo = $em->getRepository('App:Goat');
        $goats = $goatRepo->findAll();
        return $this->render('GoatForm/list.html.twig', array('goats' => $goats));
    }

    #[Route('/view/{id}', name: '_view')]
    public function viewAction($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $goatRepo = $em->getRepository('App:Goat');
        $goat = $goatRepo->find($id);
        return $this->render('GoatForm/view.html.twig', array('goat' => $goat, 'id' => $id));
    }

    #[Route('/add', name: '_add')]
    public function chevreAjoutAction(EntityManagerInterface $em, Request $req): Response
    {
        $gaotRepo = $em->getRepository('App:Goat');
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
        return $this->render('GoatForm/goat_add.html.twig',$args);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function deleteAction($id, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($em->getRepository('App:Goat')->find($id));
        $em->flush();
        $session->getFlashBag()->add('info', 'L\'enregistremement a été supprimé');
        return $this->redirect($this->generateUrl('goat_list'));
    }
}
