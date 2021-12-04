<?php

namespace App\Service;

use App\Entity\Trick;
use App\Repository\VideoRepository;

class ConvertUrlVideoService
{
    const VIDEO_WIDTH = 240;
    const VIDEO_HEIGHT = 160;
    const VIDEO_AUTOPLAY = false;

    /**
     * @var VideoRepository
     */
    private $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Return a reader from the url
     *
     * @param Trick $trick
     * @return array
     */
    public function VidProviderUrl2Player(Trick $trick): array
    {
        $arrayVideo = [];

        foreach ($this->videoRepository->findBy(["trick" => $trick]) as $video) {
            $v = $this->VidProviderUrl2VideoID($video->getUrl());

            switch ($v['type']) {
                case 'youtube':
                    $arrayVideo[] = [
                        "iframe" => '<iframe width="' . self::VIDEO_WIDTH . '" height="' . self::VIDEO_HEIGHT . '" src="https://www.youtube.com/embed/' . $v['videoId'] . (self::VIDEO_AUTOPLAY ? '?autoplay=1' : '') . '" frameborder="0" allowfullscreen autoplay="1"></iframe>',
                        "id" => $video->getId()
                    ];
                    break;
                case 'vimeo':
                    $arrayVideo[] = [
                        "iframe" => '<iframe src="https://player.vimeo.com/video/' . $v['videoId'] . (self::VIDEO_AUTOPLAY ? '?autoplay=1' : '') . '" width="' . self::VIDEO_WIDTH . '" height="' . self::VIDEO_HEIGHT . '" frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
                        "id" => $video->getId()
                    ];
                    break;
                case 'dailymotion':
                    $arrayVideo[] = [
                        "iframe" => '<iframe frameborder="0" width="' . self::VIDEO_WIDTH . '" height="' . self::VIDEO_HEIGHT . '" src="//www.dailymotion.com/embed/video/' . $v['videoId'] . (self::VIDEO_AUTOPLAY ? '?autoplay=1' : '') . '" allowfullscreen></iframe>',
                        "id" => $video->getId()
                    ];
                    break;
                default:
                    $arrayVideo[] = "";
            }
        }

        return $arrayVideo;
    }

    /**
     * Return video id and type from url
     *
     * @param string $aUrl
     * @return array
     */
    private function VidProviderUrl2VideoID(string $aUrl): array
    {
        $vid = "";
        $type = "";

        if (strpos($aUrl, 'youtube') !== false || strpos($aUrl, 'youtu.be') !== false) {
            // youtube
            if (preg_match('/(.+)youtube\.com\/watch\?v=([\w-]+)/', $aUrl, $vid) || preg_match('/(.+)youtu.be\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[2];
                $type = 'youtube';
            }
        } elseif (strpos($aUrl, 'vimeo') !== false) {
            // vimeo
            if (preg_match('/https:\/\/vimeo.com\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[1];
                $type = 'vimeo';
            }
        } elseif (strpos($aUrl, 'dailymotion') !== false || strpos($aUrl, 'dai.ly') !== false) {
            // dailymotion
            if (preg_match('/(.+)dailymotion.com\/video\/([\w-]+)/', $aUrl, $vid) || preg_match('/(.+)dai.ly\/([\w-]+)/', $aUrl, $vid)) {
                $vid = $vid[2];
                $type = 'dailymotion';
            }
        }
        return empty($type) ? [] : ['type' => $type, 'videoId' => $vid];
    }
}
