<?php

namespace App\Controller\front;

use App\Entity\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\MovieSearchType;
use App\Entity\MovieSearch;
use App\Form\GenreType;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(MovieRepository $repo, PaginatorInterface $paginator, Request $request)
    {

        $search = new MovieSearch();
        $form = $this->createForm(MovieSearchType::class, $search);
        $form->handleRequest($request);

        if($search->getName()) {
            $filmsData = $paginator->paginate(
                $repo->findByCategory($search->getName()->getId()),
                $request->query->getInt('page', 1),
                20
            );
        } else {

            $filmsData = $paginator->paginate(
                $repo->findAllCustom(),
                $request->query->getInt('page', 1),
                20
            );
        }

        return $this->render('front/index.html.twig', [
            'films_data' => $filmsData,
            'form'       => $form->createView()
        ]);
    }


    /**
     * @Route("/film/{id}", name="film_show", requirements={"id"="\d+"})
     */
    public function show(Movie $movie)
    {

        return $this->render('front/show.html.twig', [
            'film_data' => $movie
        ]);
    }

    /**
     * @Route("/category", name="category")
     */
    public function categroy(GenreRepository $repo)
    {
        
        $genres = $repo->findAll();


        return $this->render('front/category.html.twig', [
            'genres' => $genres
        ]);
    }

     /**
     * @Route("/category/{id}", name="film_category", requirements={"id"="\d+"})
     */
    public function filmByCategory(GenreRepository $repo, PaginatorInterface $paginator, Request $request, $id)
    {
        $filmsData = $paginator->paginate(
            $repo->findFilmByCategory($id),
            $request->query->getInt('page', 1),
            15
        );
     
        return $this->render('front/filmBycategory.html.twig', [
            'films_data' => $filmsData
        ]);
    }


}