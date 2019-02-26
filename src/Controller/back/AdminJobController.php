<?php

namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;

/**
* @Route("/admin", name="admin_")
*/
class AdminJobController extends AbstractController
{
    /**
     * @Route("/job/add", name="job_add",methods= {"GET", "POST"})
     * @Route("/job/edit/{id}", name="job_edit",methods= {"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function editAndAdd(Request $request, EntityManagerInterface $em,
    Job $job = null)
    {
        if( !$job ) {
            $job = new Job();
        }

        $edit_mode = $job->getId();

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($job);
            $em->flush();

            if($edit_mode){

                $this->addFlash(
                    'success',
                    'Votre job a bien été modifié'
                );
            } else {
                $this->addFlash(
                    'success',
                    'Votre job a bien été créé'
                );
            }

            return $this->redirectToRoute('admin_job_list');
        }

        return $this->render('admin_job/add_job.html.twig', [
            'form' => $form->createView(),
            'edit_mode' => $edit_mode
        ]);
    }

    /**
     * @Route("/job/list", name="job_list")
     */
    public function list(JobRepository $repo)
    {
        $jobs = $repo->findAll();

        return $this-> render('admin_job/list_job.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * @Route("/job/delete/{id}", name="job_delete", requirements={"id"="\d+"})
     */
    public function delete(Job $job, EntityManagerInterface $em)
    {
        $em->remove($job);
        $em->flush();

        $this->addFlash(
            'danger',
            'Votre job a bien été supprimé'
        );

        return $this->redirectToRoute('admin_job_list');
    }

}
