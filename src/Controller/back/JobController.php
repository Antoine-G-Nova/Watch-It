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
class JobController extends AbstractController
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

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('admin_job_list');
        }

        return $this->render('admin_job/add_job.html.twig', [
            'form' => $form->createView(),
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

        return $this->redirectToRoute('admin_job_list');
    }

}
