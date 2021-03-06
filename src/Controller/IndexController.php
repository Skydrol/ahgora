<?php

namespace App\Controller;

use App\Entity\Schedule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $entityManager = $this->getDoctrine()->getManager();
        $lastSchedule = $entityManager->getRepository(Schedule::class)->findBy([],['id' => 'DESC'],1)[0]->getDailyTimes();

        $lastSchedule = json_decode($lastSchedule,true);
        foreach ($lastSchedule as $key => $value){
            if($value == null){
                $lastSchedule[$key] = 0;
            }
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'lastSchedule' => $lastSchedule
        ]);
    }

    /**
     * @Route("/search/{term}", name="search")
     */
    public function searchOnYoutube(YoutubeService $youtubeService, $term = null)
    {
        $response = $youtubeService->search($term);

        $videos = $response['items'];
        $titlesAndDescriptions = ' ';

        $videosIds = [];

        foreach ($videos as $video){
            $titlesAndDescriptions .= ' '.$video['snippet']['title'];
            $titlesAndDescriptions .= ' '.$video['snippet']['description'];
            $videosIds[] = $video['id']['videoId'];
        }

        $titlesAndDescriptions = str_replace('-','',$titlesAndDescriptions);

        $mostCommonWords = $this->getMostCommonWords($titlesAndDescriptions,[$term,'-'],5);
        $contentDetails = $youtubeService->contentDetails($videosIds);

        return $this->render('index/videos-list.html.twig', [
            'response' => $response,
            'mostCommonWords' => $mostCommonWords,
            'contentDetails' => $contentDetails['items']
        ]);

    }

    public function getMostCommonWords($string, $stop_words, $max_count = 5) {
        $string = preg_replace('/ss+/i', '', $string);
        $string = trim($string); // trim the string
        $string = preg_replace('/[^a-zA-Z -]/', '', $string); // only take alphabet characters, but keep the spaces and dashes too…
        $string = strtolower($string); // make it lowercase

        preg_match_all('/\b.*?\b/i', $string, $match_words);
        $match_words = $match_words[0];

        foreach ( $match_words as $key => $item ) {
            if ( $item == '' || in_array(strtolower($item), $stop_words) || strlen($item) <= 3 ) {
                unset($match_words[$key]);
            }
        }

        $word_count = str_word_count( implode(" ", $match_words) , 1);
        $frequency = array_count_values($word_count);
        arsort($frequency);

        //arsort($word_count_arr);
        $keywords = array_slice($frequency, 0, $max_count);
        return $keywords;
    }

    /**
     * @Route("/schedule", name="schedule")
     */
    public function schedule(Request $request)
    {
        if($request->getMethod() == Request::METHOD_POST){
            $allTimes = json_encode($request->request->all());
            $entityManager = $this->getDoctrine()->getManager();
            $schedule = new Schedule();
            $schedule->setDailyTimes($allTimes);
            $entityManager->persist($schedule);
            $entityManager->flush();
            return $this->redirectToRoute('index');

        }
        return $this->render('Schedule/schedule.html.twig');
    }




}
