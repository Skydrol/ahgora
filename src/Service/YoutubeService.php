<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

class YoutubeService
{
    private $container;
    private $apiKey;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->apiKey = $this->container->getParameter('api_key');
    }

    public function search(string $searchTerm)
    {
        $url = 'https://youtube.googleapis.com/youtube/v3/search?q='.$searchTerm.'&part=snippet&type=video&regionCode=BR&maxResults=200&key='.$this->apiKey;
        $client = HttpClient::create();
        $response = $client->request('GET', $url);
        return $response->toArray();
    }

    public function contentDetails($videosIds){

        $spreadedArray = str_replace(['"','[',']'],'',json_encode($videosIds));
        $url = 'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id='.$spreadedArray.'&key='.$this->apiKey;
        //var_dump($url);
        //die;

        $client = HttpClient::create();
        $response = $client->request('GET', $url);
        return $response->toArray();
    }
}