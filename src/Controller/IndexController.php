<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\YoutubeService;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(YoutubeService $youtubeService): Response
    {


        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController'
        ]);
    }

    /**
     * @Route("/search/{term}", name="search")
     */
    public function searchOnYoutube(YoutubeService $youtubeService, $term = null)
    {
        $response = $youtubeService->search($term);


        return $this->render('index/videos-list.html.twig', [
            'response' => $response
        ]);

    }




}
