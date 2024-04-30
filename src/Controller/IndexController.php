<?php
namespace App\Controller;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {      
        $faker = Factory::create();
        $toplevel = [];
        $secndlevel = [];
        for( $toplevelCount = 0; $toplevelCount < 4; $toplevelCount++){
            $toplevel[$toplevelCount] = $faker->realText(20, 1);
            for($secndlevelCount = 0; $secndlevelCount < mt_rand(2,10); $secndlevelCount++){
                $secndlevel[$toplevelCount][]=$faker->realText(20, 1);
            }
        }
        
        
        return $this->render('base.html.twig',[
            'toplevel'=>$toplevel,
            'scndlevel'=>$secndlevel
        ]);
    }

}