<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use App\Service\ConvertUrlVideoService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BaseController extends AbstractController
{
    const KEY_DIRECTORY_AVATAR = "picture_avatars";
    const KEY_DIRECTORY_TRICK = "picture_tricks";

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;
    /**
     * @var ConvertUrlVideoService
     */
    protected $convertUrlVideoService;
    /**
     * @var PictureRepository
     */
    protected $pictureRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        ConvertUrlVideoService $convertUrlVideoService,
        PictureRepository $pictureRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->convertUrlVideoService = $convertUrlVideoService;
        $this->pictureRepository = $pictureRepository;
    }

    public function uploadMainPicture($form, $fieldName, $object): void
    {
        //We recover the transmitted avatar
        $avatar = $form->get($fieldName)->getData();

        //Call function for manage avatar
        $this->managePicture($avatar, $object);
    }

    /**
     * Manage document before the flush
     *
     * @param mixed $pictures
     * @param mixed $object
     * @return void
     */
    public function managePicture($pictures, $object): void
    {   
        if ($pictures != null) {
            $this->checkPictureExist($object);
            $file = $this->generateUniqueName($pictures);
            
            switch (get_class($object)) {
                case User::class:
                    $this->movePicture($pictures, $file, self::KEY_DIRECTORY_AVATAR);
                    $object->setAvatar($file);
                    break;
                case Trick::class:
                    $this->movePicture($pictures, $file, self::KEY_DIRECTORY_TRICK);
                    $object->setMainPicture($file);
                    break;
                case Picture::class:
                    $this->movePicture($pictures, $file, self::KEY_DIRECTORY_TRICK);
                    $object->setName($file);
                    break;
                default:
            }
        }
    }

    /**
     * Delete picture in the folter if already a picture
     *
     * @param mixed $object
     * @return void
     */
    public function checkPictureExist($object): void
    {
        switch (get_class($object)) {
            case User::class:
                if ($object->getAvatar() != null) {
                    //We get the fullname of the document
                    $name = $object->getAvatar();
                    //Delete document in the folder
                    unlink($this->getParameter(self::KEY_DIRECTORY_AVATAR) . '/' . $name);
                }
                break;
            case Trick::class:
                if ($object->getMainPicture() != null) {
                    //We get the fullname of the document
                    $name = $object->getMainPicture();
                    //Delete document in the folder
                    unlink($this->getParameter(self::KEY_DIRECTORY_TRICK) . '/' . $name);
                }
                break;
            case Picture::class:
                if ($object->getName() != null) {
                    //We get the fullname of the document
                    $name = $object->getName();
                    //Delete document in the folder
                    unlink($this->getParameter(self::KEY_DIRECTORY_TRICK) . '/' . $name);
                }
                break;
            default:
        }
    }

    /**
     * Copy the file in uploads folder
     *
     * @param mixed $picture
     * @param string $file
     * @param string $keyParameter
     * @return void
     */
    private function movePicture($picture, string $file, string $keyParameter): void
    {
        $picture->move(
            $this->getParameter($keyParameter),
            $file
        );
    }

    /**
     * Copy the file in uploads folder
     *
     * @param mixed $picture
     * @return void
     */
    private function generateUniqueName($picture): string
    {
        return md5(uniqid()) . '.' . $picture->guessExtension();
    }
}