<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 17:00
 */

namespace Darvin\ImageBundle\Namer;

use Darvin\Utils\Strings\Transliterator\TransliteratorInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

/**
 * File namer
 */
class FileNamer implements NamerInterface
{
    /**
     * @var \Darvin\Utils\Strings\Transliterator\TransliteratorInterface
     */
    private $transliterator;

    /**
     * @param \Darvin\Utils\Strings\Transliterator\TransliteratorInterface $transliterator Transliterator
     */
    public function __construct(TransliteratorInterface $transliterator)
    {
        $this->transliterator = $transliterator;
    }

    /**
     * {@inheritdoc}
     */
    public function name($object, PropertyMapping $mapping)
    {
        $uploadDir = $mapping->getUploadDestination().DIRECTORY_SEPARATOR.$mapping->getUploadDir($object);

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $mapping->getFile($object);

        $name = $suffix = $this->transliterator->transliterate($file->getClientOriginalName(), true, array('_', '.'));

        $prefix = 0;

        while (is_file($uploadDir.DIRECTORY_SEPARATOR.$name)) {
            $prefix++;
            $name = $prefix.'_'.$suffix;
        }

        return $name;
    }
}
