<?php

namespace App\Controller\back;

use App\Entity\Departement;
use App\Form\DepartementType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartementRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

/**
* @Route("/admin", name="admin_")
*/
class AdminDepartementController extends AbstractController
{
    /**
     * @Route("/departement/add", name="departement_add",methods= {"GET", "POST"})
     * @Route("/departement/edit/{id}", name="departement_edit",methods= {"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function editAndAdd(Request $request, EntityManagerInterface $em, Departement $departement = null)
    {
        if(!$departement){
            $departement = new Departement();
        }

        $edit_mode = $departement->getId();

        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($departement);
            $em->flush();

            if($edit_mode){

                $this->addFlash(
                    'success',
                    'Votre département a bien été modifié'
                );
            } else {
                $this->addFlash(
                    'success',
                    'Votre département a bien été créé'
                );
            }

            return $this->redirectToRoute('admin_departement_list');
        }

        return $this->render('admin_departement/add_departement.html.twig', [
            'form' => $form->createView(),
            'edit_mode' => $edit_mode,
        ]);
    }

    /**
     * @Route("/departement/list", name="departement_list")
     */
    public function list(DepartementRepository $repo)
    {
        $departement = $repo->findAll();

        return $this-> render('admin_departement/list_departement.html.twig', [
            'departements' => $departement,
        ]);
    }

    /**
     * @Route("/departement/delete/{id}", name="departement_delete", requirements={"id"="\d+"})
     */
    public function delete(Departement $departement, EntityManagerInterface $em)
    {
        $em->remove($departement);
        $em->flush();

        $this->addFlash(
            'danger',
            'Votre département a bien été supprimé'
        );

        return $this->redirectToRoute('admin_departement_list');
    }
}
