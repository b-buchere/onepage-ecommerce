<?php
namespace App\Controller;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/product', name: 'product_', methods: ['GET'])]
class ProductController extends AbstractController
{
    
    #[Route('/{id}', name: 'get')]
    public function index(): Response
    {      
        $faker = Factory::create();

        $data = [];
        for($i = 0; $i < mt_rand(1,10); $i++){
            
            for($modeleValeur = 0; $modeleValeur  < mt_rand(1,10); $modeleValeur++){
                $data[$i][]=$faker->randomFloat( mt_rand(1,2), 1, 100);
            }
            sort($data[$i], SORT_ASC);
            if(count($data[$i]) > 1 ){
                $nomModele = $faker->realText(20,1);
                $data[$nomModele] = $data[$i];
                unset($data[$i]);
            }            
            
        }        
        
        return new JsonResponse($data);
    }

    #[Route('/modele/{id}', name: 'getModele')]
    public function modele(): Response
    {
        /*$faker = Factory::create();
        
        $data = [];
        for($i = 0; $i < mt_rand(1,10); $i++){            
            $data[]=$faker->randomFloat( mt_rand(1,2), 1, 100);
        }
            sort($data[$i], SORT_ASC);
            
            
        }        
        
        return new JsonResponse($data);*/
    }
}