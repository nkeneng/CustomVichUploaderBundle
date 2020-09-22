<?php


namespace Steven\CustomVichUploadableBundle\Annotations;


use App\Entity\Post;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;

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

    public function isUploadable($entity): bool
    {
        $reflection = new ReflectionClass(get_class($entity));
        return $this->reader->getClassAnnotation($reflection, Uploadable::class) !== null;

    }

    public function getUploadableFields($entity): array
    {
        $reflection = new \ReflectionClass(get_class($entity));

        if (!$this->isUploadable($entity)) {
            return [];
        }

        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, UploadableField::class);
            if ($annotation !== null){
                $properties[$property->getName()] = $annotation;
            }
        }

        return  $properties;
    }
}
