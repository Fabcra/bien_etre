<?php
/**
 * Created by PhpStorm.
 * User: Fab
 * Date: 5/02/18
 * Time: 09:28
 */

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

//service upload
class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir=$targetDir;
    }

    public function upload(UploadedFile $file){

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    public function getTargetDir(){

        return $this->targetDir;
    }

}