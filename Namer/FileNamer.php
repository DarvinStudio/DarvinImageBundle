<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
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
     * {@inheritDoc}
     */
    public function name($object, PropertyMapping $mapping): string
    {
        $uploadDir = $mapping->getUploadDestination().DIRECTORY_SEPARATOR.$mapping->getUploadDir($object);

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $mapping->getFile($object);

        $extension = $file->guessExtension();

        if (null === $extension) {
            $extension = '';
        }

        $name = preg_replace(sprintf('/\.%s$/', $file->getClientOriginalExtension()), '', $file->getClientOriginalName() ?: '');
        $name = str_replace('.', '-', $name);
        $name = $this->transliterator->transliterate($name);
        $name = $this->makeNameUnique($name, $uploadDir, $extension);

        return $name.'.'.$extension;
    }

    /**
     * @param string $name      File name
     * @param string $uploadDir Upload directory
     * @param string $extension File extension
     *
     * @return string
     */
    protected function makeNameUnique(string $name, string $uploadDir, string $extension): string
    {
        $prefix = $name;

        $suffix = 0;

        while (is_file(sprintf('%s%s%s.%s', $uploadDir, DIRECTORY_SEPARATOR, $name, $extension))) {
            $suffix++;
            $name = $prefix.'-'.$suffix;
        }

        return $name;
    }
}
