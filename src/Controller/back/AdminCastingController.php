<?php

namespace App\Controller\back;

use App\Entity\Casting;
use App\Form\CastingType;
use App\Repository\CastingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin", name="admin_")
*/
class AdminCastingController extends AbstractController
{
    /**
     * @Route("/casting/add", name="casting_add",methods= {"GET", "POST"}))
     * @Route("/casting/edit/{id}", name="casting_edit",methods= {"GET", "POST"}, requirements={"id"="\d+"}))
     */
    public function addAndEdit(Request $request, EntityManagerInterface $em,
    Casting $casting = null)
    {
        if(!$casting){
            $casting = new Casting();
        }

        $edit_mode = $casting->getId();

        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($casting);
            $em->flush();

            if($edit_mode){

                $this->addFlash(
                    'success',
                    'Votre casting a bien été modifié'
                );
            } else {
                $this->addFlash(
                    'success',
                    'Votre casting a bien été créé'
                );
            }

            return $this->redirectToRoute('admin_casting_list');
        }

        return $this->render('admin_casting/add_casting.html.twig', [
            'form' => $form->createView(),
            'edit_mode' => $edit_mode,
        ]);
    }

    /**
     * @Route("/casting/list", name="casting_list")
     */
    public function list(CastingRepository $repo)
    {
        $castings = $repo->findAll();

        return $this-> render('admin_casting/list_casting.html.twig', [
            'castings' => $castings,
        ]);
    }

    /**
     * @Route("/casting/delete/{id}", name="casting_delete", requirements={"id"="\d+"})
     */
    public function delete(Casting $casting, EntityManagerInterface $em)
    {
        $em->remove($casting);
        $em->flush();

        $this->addFlash(
            'danger',
            'Votre casting a bien été supprimé'
        );

        return $this->redirectToRoute('admin_casting_list');
    }
}
