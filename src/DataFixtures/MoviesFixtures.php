<?php

namespace App\DataFixtures;

use Unirest;
use Faker\Factory;
use App\Entity\Job;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Utils\Slugger;
use App\Entity\Casting;
use App\Entity\Departement;
use App\Repository\JobRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartementRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class MoviesFixtures extends Fixture
{

    private $repo;
    private $depRepo;
    private $jobRepo;
    private $slugger;
    private $encoder;


    public function __construct(GenreRepository $genRepo, DepartementRepository $repository, JobRepository $jobRepo, Slugger $slugger, UserPasswordEncoderInterface $encoder) {

        $this->repo = $genRepo;
        $this->depRepo = $repository;
        $this->jobRepo = $jobRepo;
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {

        $faker = Factory::create();

        // Je créé un admin
        $user = new User();
            $password = $this->encoder->encodePassword($user, 'toto');
            $user->setUsername("Admin")
                 ->setEmail($faker->email)
                 ->setPassword($password)
                 ->setRole('ROLE_ADMIN');
            $manager->persist($user);

        

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
        for( $j = 1 ; $j <= 10 ; $j++ ) {

            // Requête pour obtenir une liste de film.
            $filmResponse = Unirest\Request::get('https://api.themoviedb.org/3/discover/movie?api_key=68d344e1b7babcb0dba7864b416715d5&language=fr&sort_by=popularity.desc&include_adult=false&include_video=false&page='.$j);

            $filmsData = $filmResponse->body->{'results'};
            
            foreach( $filmsData as $key => $film) { // On boucle sur le tableau de film

                $newFilm = new Movie;
                $newFilm->setTitle($film->{'title'})
                        ->setImage('https://image.tmdb.org/t/p/w500/'. $film->{'poster_path'})
                        ->setReleaseDate(new \DateTime($film->{'release_date'}))
                        ->setSlug($this->slugger->slugify($film->{'title'}));
                
                if($film->{'overview'}) {

                    $newFilm->setOverview($film->{'overview'});
                }
                $manager->persist($newFilm);

                
                foreach( $film->{'genre_ids'} as $genre ){ // On ajoute des genres au film en cours 
                   
                    $genreClass = $this->repo->find($genre); // On récupére l'entity du genre en cours

                    $newFilm->addGenre($genreClass); // on l'ajoute au film en cours

                    $manager->persist($newFilm);
                }

                // On ajoute les castings/ person / job / departements

                $castingResponse = Unirest\Request::get('https://api.themoviedb.org/3/movie/'. $film -> {'id'} .'/credits?api_key=68d344e1b7babcb0dba7864b416715d5');

                $castingData = $castingResponse->body->{'cast'};

                $crewData = $castingResponse->body->{'crew'};


                if( count($castingData) <= 10){
                    $nbActorMax = count($castingData) - 1;
                } else {
                    $nbActorMax = 10;
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

                if( count($crewData) <= 10){
                    $nbCrew = count($crewData) - 1;
                } else {
                    $nbCrew = 10;
                }

                for( $i = 0; $i <= $nbCrew; $i++) {

                    $person = new Person();
                    $person->setName($crewData[$i]->{'name'});

                    if($crewData[$i]->{'profile_path'}) {
                        $person->setImage($crewData[$i]->{'profile_path'});
                    }
                    $manager->persist($person);

                    if( !($this->depRepo->findOneBy(['name'=>$crewData[$i]->{'department'}]) )) {
                        $departement = new Departement();
                        $departement->setName($crewData[$i]->{'department'});
                        $manager->persist($departement);
                    }
                    $manager->flush();

                    if(!($this->jobRepo->findOneBy(['name'=>$crewData[$i]->{'job'}])) ) {
                        $job = new Job();
                        $job->setName($crewData[$i]->{'job'})
                            ->setDepartement($this->depRepo->findOneBy(['name'=> $crewData[$i]->{'department'}]));
                        $manager->persist($job);
                    }
                    $manager->flush();

                    $team = new Team();
                    $team->setMovie($newFilm)
                        ->setPerson($person)
                        ->setJob($this->jobRepo->findOneBy(['name'=>$crewData[$i]->{'job'}]));
                    $manager->persist($team);
                }
                $manager->flush();
                
            }
        }

    }
}
