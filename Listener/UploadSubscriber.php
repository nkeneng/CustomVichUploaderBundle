<?php


namespace Steven\CustomVichUploadableBundle\Listener;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Steven\CustomVichUploadableBundle\Annotations\AnnotationReader;
use Steven\CustomVichUploadableBundle\Handler\UploadHandler;

class UploadSubscriber implements EventSubscriber
{
    /**
     * @var AnnotationReader
     */
    private $reader;
    /**
     * @var UploadHandler
     */
    private $handler;

    public function __construct(AnnotationReader $reader, UploadHandler $handler)
    {

        $this->reader = $reader;
        $this->handler = $handler;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
            Events::postRemove
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->preEvent($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->preEvent($args);
    }

    public function preEvent(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        foreach ($this->reader->getUploadableFields($entity) as $uploadableField => $annotation) {
            $this->handler->uploadFile($entity, $uploadableField, $annotation);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        foreach ($this->reader->getUploadableFields($entity) as $uploadableField => $annotation) {
            $this->handler->removeFile($entity, $uploadableField);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        foreach ($this->reader->getUploadableFields($entity) as $uploadableField => $annotation) {
            $this->handler->setFileFromFilename($entity, $uploadableField, $annotation);
        }
    }
}
