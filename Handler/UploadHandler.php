<?php


namespace Steven\CustomVichUploadableBundle\Handler;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class UploadHandler
{
    /**
     * @var PropertyAccessor
     */
    private $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function uploadFile($entity, $uploadableField, $annotation)
    {
        $file = $this->accessor->getValue($entity, $uploadableField);
        if ($file instanceof UploadedFile) {
            $this->removeOldFile($entity, $annotation);
            $filename = $file->getClientOriginalName();
            $file->move($annotation->getPath(), $filename);
            $this->accessor->setValue($entity, $annotation->getFilename(), $filename);
        }
    }

    public function setFileFromFilename($entity, $uploadableField, $annotation)
    {
        $file = $this->getFileFromfilename($entity, $annotation);
        $this->accessor->setValue($entity, $uploadableField, $file);
    }

    public function removeOldFile($entity, $annotation)
    {
        $file = $this->getFileFromfilename($entity, $annotation);
        if ($file !== null) {
            unlink($file->getRealPath());
        }
    }

    private function getFileFromfilename($entity, $annotation): ?File
    {
        $filename = $this->accessor->getValue($entity, $annotation->getFilename());
        if (empty($filename)) {
            return null;
        } else {
            return new File($annotation->getPath() . DIRECTORY_SEPARATOR . $filename);
        }
    }

    public function removeFile($entity, $uploadableField)
    {
        $file = $this->accessor->getValue($entity, $uploadableField);
        if ($file instanceof File) {
            // to avoid error in the case it was already removed
            @unlink($file->getRealPath());
        }
    }
}
