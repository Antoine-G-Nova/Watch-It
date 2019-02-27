<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\DepartementRepository;
use Unirest;
use Faker\Factory;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Casting;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Departement;
use App\Entity\Job;
use App\Entity\Team;

class DepartementFixtures extends Fixture
{
    private $repo;

    public function __construct(DepartementRepository $repository)
    {
        $this->repo = $repository;
    }


    public function load(ObjectManager $manager)
    {
        for( $j = 1 ; $j <= 20 ; $j++ ) {

            // Requête pour obtenir une liste de film.
            $filmResponse = Unirest\Request::get('https://api.themoviedb.org/3/discover/movie?api_key=68d344e1b7babcb0dba7864b416715d5&language=fr&sort_by=popularity.desc&include_adult=false&include_video=false&page='.$j);

            $filmsData = $filmResponse->body->{'results'};            
            
            foreach( $filmsData as $key => $film) { // On boucle sur le tableau de film


                $castingResponse = Unirest\Request::get('https://api.themoviedb.org/3/movie/'. $film -> {'id'} .'/credits?api_key=68d344e1b7babcb0dba7864b416715d5');

                $crewData = $castingResponse->body->{'crew'};



                if( count($crewData) == 0 ) {
                    $nbCrew = -1;
                } elseif( count($crewData) == 1 ) {
                    $nbCrew = 0;
                } elseif( count($crewData) == 2 ) {
                    $nbCrew = 1;
                } elseif( count($crewData) == 3 ) {
                    $nbCrew = 2;
                } elseif( count($crewData) == 4 ) {
                    $nbCrew = 3;
                } elseif( count($crewData) == 5 ) {
                    $nbCrew = 4;
                } elseif( count($crewData) == 6 ) {
                    $nbCrew = 5;
                } elseif( count($crewData) == 7 ) {
                    $nbCrew = 6;
                } elseif( count($crewData) > 7 ) {
                    $nbCrew = 7;
                }

                for( $i = 0; $i <= $nbCrew; $i++) {
                    
                    if(!($this->repo->findOneBy(['name'=>$crewData[$i]->{'department'}])) ) {
                        $departement = new Departement();
                        $departement->setName($crewData[$i]->{'department'});
                        $manager->persist($departement);
                    }

                }
                $manager->flush();
                
            }
        }
    }
}
