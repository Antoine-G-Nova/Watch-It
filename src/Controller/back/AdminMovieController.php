<?php

namespace App\Controller\back;

use App\Entity\Movie;
use App\Form\MovieEditType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @Route("/admin", name="admin_") */
class AdminMovieController extends AbstractController
{
    /**
     * @Route("/film/add", name="film_add",methods= {"GET", "POST"})
     * @Route("/film/edit/{id}", name="film_edit", methods= {"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function add(Request $request, EntityManagerInterface $manager, Movie $movie = null)
    {
        $flashMessage = 'Votre film a bien été modifié';

        if(!$movie) {
            $movie = new Movie();
            $flashMessage = 'Votre film a bien été créé';
        }
        
        
        $form = $this->createForm(MovieEditType::class, $movie);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($movie);
            $manager->flush();
            
            $this->addFlash(
                'success',
                $flashMessage
            );

            return $this->redirectToRoute('admin_film_list');
        }
        
        $edit_mode = $movie->getId();

        return $this->render('admin_movie/add_film.html.twig', [
            'form' => $form->createView(),
            'update_mode' => $edit_mode // Permet de savoir on est en création ou en update
        ]);
    }

    /**
     * @Route("/film/list", name="film_list")
     */
    public function list(MovieRepository $repo )
    {
        $movies = $repo->findAll();

        return $this->render('admin_movie/list_film.html.twig', [
            'films_data' => $movies,
        ]);
    }

    /**
     * @Route("/film/delete/{id}", name="film_delete", requirements={"id"="\d+"})
     */
    public function delete(Movie $movie, EntityManagerInterface $em )
    {
        $em->remove($movie);
        $em->flush();

        $this->addFlash(
            'danger',
            'Votre film a bien été supprimé'
        );

        return $this->redirectToRoute('admin_film_list');
    }
}
