<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

class YoutubeService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function search(string $searchTerm)
    {
        $apiKey = $this->container->getParameter('api_key');
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://youtube.googleapis.com/youtube/v3/search?q='.$searchTerm.'&part=snippet&type=video&key='.$apiKey);
       return $response->toArray();
    }
}