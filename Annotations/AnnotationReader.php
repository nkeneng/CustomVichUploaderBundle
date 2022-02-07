<?php


namespace Steven\CustomVichUploadableBundle\Annotations;


use App\Entity\Post;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;

class AnnotationReader
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * tell if an entity has the annotation
     * IsUploadable
     * @param $entity
     * @return bool
     * @throws ReflectionException
     */
    public function isUploadable($entity): bool
    {
        $reflection = new ReflectionClass(get_class($entity));
        return $this->reader->getClassAnnotation($reflection, Uploadable::class) !== null;

    }

    /**
     * return the list of all uploadable fields
     * of an uploadable entity
     * @param $entity
     * @return array
     * @throws ReflectionException
     */
    public function getUploadableFields($entity): array
    {
        // help getting all infos on a class
        // all properties , methods , filename , and phpDoc
        $reflection = new \ReflectionClass(get_class($entity));

        if (!$this->isUploadable($entity)) {
            return [];
        }

        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            // return an object containing the annotation data of a property
            $annotation = $this->reader->getPropertyAnnotation($property, UploadableField::class);
            if ($annotation !== null) {
                $properties[$property->getName()] = $annotation;
            }
        }

        return $properties;
    }
}
