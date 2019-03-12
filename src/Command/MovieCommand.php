<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;

class MovieCommand extends Command
{
    protected static $defaultName = 'app:nova:updateposter';
    private $movieRepo;
    private $manager;

    //recuperer le service repository me permettant de recuperer la liste des mes movies
    // recuperer aussi le service manager me permettant de mettre a jour mes films
    public function __construct(MovieRepository $repo, EntityManagerInterface $manager){

        $this->movieRepo = $repo;
        $this->manager = $manager;

        // necessaire a la commande si constructeur custom
        parent::__construct();
    }

    protected function configure()
    {
        $this
        ->setDescription('Update les posters de ma table Movie')
        ->setHelp('Cette commande est géniale');

        // permet de rajouter un ou plusieurs argument en entrée de ma console
        //->addArgument('nova', InputArgument::REQUIRED, 'The username of the user.');
    }

    /*
     cette fonction contient le code executé lors de l'appel de ma commande

     InputInterface permet de recuperer les parametre passé a ma commande via la console

     OutputInterface permet d'afficher du texte / donnée pour communiquer avec mon utilisateur
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Update Poster begin !',
            '============',
            '',
        ]);

        //recuperer toute la liste des films puis inserer l'url retournée en BDD
        //note : pour ne pas se telescoper avec la propriété poster actuelle , creer une nouvelle propriété 

         // Attention à remplacer les espaces presents dans nos titre grace à l'expression suivante        
        // $sanitizedTitle = str_replace(" ", "+", $movie->getTitle());

        $movies = $this->movieRepo->findAll();

        foreach( $movies as $movie) {
            
            $result = $this->getCurl($movie->getTitle());


            if($result){ //Si j'ai un resultat

                $output->writeln([
                    $movie->getTitle(),
                ]);

                //Je recupere la note et je la set sur la nouvelle propriété de mon movie

                    $movie->setRating($result->Metascore);

                $this->manager->flush();
            }
        }



        $output->writeln([
            'Update Poster End !',
            '============',
            '',
        ]);
    }

    //modifier la fonction getcurl pour prendre dynamiquement le titre du film en parametre
    private function getCurl($title){

        $dbmovieApiUrl = 'http://www.omdbapi.com/?apikey=55429286&t='. urlencode($title);

        //lorsque j'utilise cUrl , je dois d'abord initialiser la connexion
        $curl = curl_init();

        //je passe a minimam ces 2 option de connexion, a savoir l'url que je souhaite appeler (1) et que je souhaite que curl m'affiche le retour (2)
        curl_setopt($curl, CURLOPT_URL, $dbmovieApiUrl); //1
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //2
        
        //j'execute la configuration precedente initlisée sur curl pour obtenir une reponse
        $jsonResponse = curl_exec($curl);
        $response = json_decode($jsonResponse);

        //je ferme la connexion à l'url (important)
        curl_close($curl);

        if(isset($response->Response) && $response->Response == "False"){
            $response = null;
        }

        return $response;
    }
}