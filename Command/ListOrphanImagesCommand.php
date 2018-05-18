<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Command;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * List orphan images command
 */
class ListOrphanImagesCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @var string
     */
    private $uploadPath;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @param string                                        $name            Command name
     * @param \Doctrine\ORM\EntityManager                   $em              Entity manager
     * @param \Vich\UploaderBundle\Storage\StorageInterface $uploaderStorage Uploader storage
     * @param int                                           $chunkSize       Chunk size
     * @param string                                        $uploadPath      Upload path
     * @param string                                        $webDir          Web directory
     */
    public function __construct($name, EntityManager $em, StorageInterface $uploaderStorage, $chunkSize, $uploadPath, $webDir)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->uploaderStorage = $uploaderStorage;
        $this->chunkSize = $chunkSize;
        $this->uploadPath = trim($uploadPath, DIRECTORY_SEPARATOR);
        $this->webDir = rtrim($webDir, DIRECTORY_SEPARATOR);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Shows list of orphan images.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $inDatabase   = $this->findInDatabase();
        $inFilesystem = $this->findInFilesystem();

        foreach ($inDatabase as $pathname) {
            if (!isset($inFilesystem[$pathname])) {
                $io->error(sprintf('Image "%s" exists in database but not in filesystem.', $pathname));

                continue;
            }

            unset($inFilesystem[$pathname]);
        }
        foreach ($inFilesystem as $pathname) {
            $io->error(sprintf('Image "%s" exists in filesystem but not in database.', $pathname));
        }
    }

    /**
     * @return array
     */
    private function findInDatabase()
    {
        $pathnames = [];

        $iterator = $this->em->getRepository(AbstractImage::class)->createQueryBuilder('o')->getQuery()->iterate();

        while ($row = $iterator->next()) {
            /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $image */
            $image = reset($row);

            $pathname = $this->uploaderStorage->resolveUri($image, AbstractImage::PROPERTY_FILE);

            $pathnames[$pathname] = $pathname;

            if ($iterator->key() > 0 && 0 === $iterator->key() % $this->chunkSize) {
                $this->em->clear();
            }
        }

        return $pathnames;
    }

    /**
     * @return array
     */
    private function findInFilesystem()
    {
        $pathnames = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ((new Finder())->in($this->webDir.DIRECTORY_SEPARATOR.$this->uploadPath)->files() as $file) {
            $pathname = DIRECTORY_SEPARATOR.$this->uploadPath.DIRECTORY_SEPARATOR.$file->getRelativePathname();

            $pathnames[$pathname] = $pathname;
        }

        return $pathnames;
    }
}
