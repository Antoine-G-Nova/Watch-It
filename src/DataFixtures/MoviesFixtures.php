<?php

namespace App\DataFixtures;

use Unirest;
use Faker\Factory;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Casting;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class MoviesFixtures extends Fixture
{

    private $repo;

    public function __construct(GenreRepository $genRepo) {

        $this->repo = $genRepo;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Requête pour obtenir tous les genres.
        $genresResponse = Unirest\Request::get('https://api.themoviedb.org/3/genre/movie/list?api_key=68d344e1b7babcb0dba7864b416715d5&language=fr');

        $genresData = $genresResponse->body->{'genres'};

        // J'enregistre tous les genre en BDD en forcant l'indexation
        foreach( $genresData as $genre) {
            $newGenre = new Genre();
            $newGenre->setId($genre->{'id'})
                     ->setName($genre->{'name'})
                     ->setColor($faker->hexcolor());
            $manager->persist($newGenre);
        }

        $manager->flush(); // J'enregistre

        // Boucle pour créer les films et les acteurs associés 
        for( $j = 1 ; $j <= 30 ; $j++ ) {

            // Requête pour obtenir une liste de film.
            $filmResponse = Unirest\Request::get('https://api.themoviedb.org/3/discover/movie?api_key=68d344e1b7babcb0dba7864b416715d5&language=fr&sort_by=popularity.desc&include_adult=false&include_video=false&page='.$j);

            $filmsData = $filmResponse->body->{'results'};            
            
            foreach( $filmsData as $key => $film) { // On boucle sur le tableau de film
            
                $newFilm = new Movie;
                $newFilm->setTitle($film->{'title'})
                        ->setImage('https://image.tmdb.org/t/p/w500/'. $film->{'poster_path'});
                
                if($film->{'overview'}) {

                    $newFilm->setOverview($film->{'overview'});
                }                        
                $manager->persist($newFilm);

                
                foreach( $film->{'genre_ids'} as $genre ){                 
                   
                    $genreClass = $this->repo->find($genre); // On récupére l'entity du genre en cours

                    $newFilm->addGenre($genreClass); // on l'ajoute au film en cours

                    $manager->persist($newFilm);
                }

                $castingResponse = Unirest\Request::get('https://api.themoviedb.org/3/movie/'. $film -> {'id'} .'/credits?api_key=68d344e1b7babcb0dba7864b416715d5');
                $castingData = $castingResponse->body->{'cast'};

                
                
                if( count($castingData)  == 0 ) {

                    return $manager->flush();

                } elseif( count($castingData) == 1 ) {

                    $nbActorMax = 1;

                } elseif( count($castingData)  == 2 ) {

                    $nbActorMax = 2;

                } elseif( count($castingData)  == 3 ) {

                    $nbActorMax = 3;

                } elseif( count($castingData)  == 5 ) {

                    $nbActorMax = 5;

                } elseif( count($castingData)  == 6 ) {

                    $nbActorMax = 6;

                } elseif( count($castingData)  == 7 ) {

                    $nbActorMax = 7;

                } elseif( count($castingData)  > 7 ) {

                    $nbActorMax = 8;
                }
                

                for( $i = 0; $i <= $nbActorMax; $i++) {

                    $person = new Person();
                    $person->setName($castingData[$i]->{'name'});

                    if($castingData[$i]->{'profile_path'}) {

                        $person->setImage($castingData[$i]->{'profile_path'});
                    }
                    $manager->persist($person);

                    $casting = new Casting();
                    $casting->setCharacterName($castingData[$i]->{'character'})
                            ->setOrderCredit($i)
                            ->setMovie($newFilm)
                            ->setPerson($person);
                    $manager->persist($casting);
                }

                $manager->flush();
                
            }
        }

    }
}
