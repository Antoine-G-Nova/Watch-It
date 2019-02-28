<?php

namespace App\Controller\back;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @Route("/admin", name="admin_") */
class AdminTeamController extends AbstractController
{
    /**
     * @Route("/team/add", name="team_add",methods= {"GET", "POST"})
     * @Route("/team/edit/{id}", name="team_edit",methods= {"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function addAndEdit(Request $request, EntityManagerInterface $manager, Team $team = null)
    {
        $flashMessage = 'Votre team a bien été modifié';

        if(!$team) {
            $team = new Team();
            $flashMessage = 'Votre team a bien été créé';
        }
        
        $edit_mode = $team->getId();

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($team);
            $manager->flush();

                $this->addFlash(
                    'success',
                    $flashMessage
                );

            return $this->redirectToRoute('admin_team_list', ['id' => $request->request->get('team')['movie']]);
        }

        return $this->render('admin_team/add_team.html.twig', [
            'update_mode' => $edit_mode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/team/list/{id}", name="team_list", requirements={"id"="\d+"})
     */
    public function list(TeamRepository $repo, $id )
    {
        $teams = $repo->findBy(['movie' => $id]);

        return $this->render('admin_team/list_team.html.twig', [
            'teams_data' => $teams,
        ]);
    }

    /**
     * @Route("/team/delete/{id}", name="team_delete", requirements={"id"="\d+"})
     */
    public function delete(Request $request, Team $team, EntityManagerInterface $em )
    {
        $em->remove($team);
        $em->flush();

        $this->addFlash(
            'danger',
            'Votre team a bien été supprimé'
        );

        return $this->redirect($request->headers->get('referer'));
    }
}
