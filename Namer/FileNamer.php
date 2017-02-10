<?php
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Namer;

use Darvin\Utils\Transliteratable\TransliteratorInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

/**
 * File namer
 */
class FileNamer implements NamerInterface
{
    /**
     * @var \Darvin\Utils\Transliteratable\TransliteratorInterface
     */
    protected $transliterator;

    /**
     * @param \Darvin\Utils\Transliteratable\TransliteratorInterface $transliterator Transliterator
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

        $name = preg_replace(sprintf('/\.%s$/', $file->getClientOriginalExtension()), '', $file->getClientOriginalName());

        $name = str_replace('.', '_', $name);

        $name = $this->transliterator->transliterate($name, true, ['_'], '_').'.'.$file->guessExtension();

        $name = $this->makeNameUnique($name, $uploadDir);

        return $name;
    }

    /**
     * @param string $name      File name
     * @param string $uploadDir Upload directory
     *
     * @return string
     */
    protected function makeNameUnique($name, $uploadDir)
    {
        $suffix = $name;

        $prefix = 0;

        while (is_file($uploadDir.DIRECTORY_SEPARATOR.$name)) {
            $prefix++;
            $name = $prefix.'_'.$suffix;
        }

        return $name;
    }
}
