<?php

namespace App\Service;

use App\Entity\Trick;
use App\Repository\VideoRepository;

class ConvertUrlVideoService
{
    /**
     * @var VideoRepository
     */
    private $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Renvoi un lecteur d'après l'url
     *
     * @param Trick $trick
     * @return array
     */
    public function VidProviderUrl2Player(Trick $trick): array
    {
        $arrayVideo = [];
        /* Parameter video player */
        $aWidth = 240;
        $aHeight = 160;
        $aAutoplay = false;

        foreach ($this->videoRepository->findBy(["trick" => $trick]) as $video) {
            $v = $this->VidProviderUrl2VideoID($video->getUrl());

            switch ($v['type']) {
                case 'youtube':
                    $arrayVideo[] = [
                        "iframe" => '<iframe width="' . $aWidth . '" height="' . $aHeight . '" src="https://www.youtube.com/embed/' . $v['videoId'] . ($aAutoplay ? '?autoplay=1' : '') . '" frameborder="0" allowfullscreen autoplay="1"></iframe>',
                        "id" => $video->getId()
                    ];
                    break;
                case 'vimeo':
                    $arrayVideo[] = [
                        "iframe" => '<iframe src="https://player.vimeo.com/video/' . $v['videoId'] . ($aAutoplay ? '?autoplay=1' : '') . '" width="' . $aWidth . '" height="' . $aHeight . '" frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
                        "id" => $video->getId()
                    ];
                    break;
                case 'dailymotion':
                    $arrayVideo[] = [
                        "iframe" => '<iframe frameborder="0" width="' . $aWidth . '" height="' . $aHeight . '" src="//www.dailymotion.com/embed/video/' . $v['videoId'] . ($aAutoplay ? '?autoplay=1' : '') . '" allowfullscreen></iframe>',
                        "id" => $video->getId()
                    ];
                    break;
                default:
                    $arrayVideo[] = "";
            }
        }

        return $arrayVideo;
    }

    //Renvoi l'id et le type de vidéo d'après une URL
    private function VidProviderUrl2VideoID($aUrl)
    {
        $vid = '';
        $type = "";

        if (strpos($aUrl, 'youtube') !== false) {
            // youtube
            if (preg_match('/(.+)youtube\.com\/watch\?v=([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[2];
                $type = 'youtube';
            }
        } elseif (strpos($aUrl, 'youtu.be') !== false) {
            // youtu.be
            if (preg_match('/(.+)youtu.be\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[2];
                $type = 'youtube';
            }
        } elseif (strpos($aUrl, 'vimeo') !== false) {
            // vimeo
            if (preg_match('/https:\/\/vimeo.com\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[1];
                $type = 'vimeo';
            }
        } elseif (strpos($aUrl, 'dailymotion') !== false) {
            // dailymotion
            if (preg_match('/(.+)dailymotion.com\/video\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[2];
                $type = 'dailymotion';
            }
        } elseif (strpos($aUrl, 'dai.ly') !== false) {
            // dailymotion
            if (preg_match('/(.+)dai.ly\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[2];
                $type = 'dailymotion';
            }
        }
        return empty($type) ? "" : ['type' => $type, 'videoId' => $vid];
    }
}
