<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018-2019, Darvin Studio
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Vich\UploaderBundle\Metadata\MetadataReader;
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
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Vich\UploaderBundle\Metadata\MetadataReader
     */
    private $uploaderMetaReader;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @var array
     */
    private $uploaderMappings;

    /**
     * @param string                                        $name               Command name
     * @param \Doctrine\ORM\EntityManager                   $em                 Entity manager
     * @param \Symfony\Component\Filesystem\Filesystem      $filesystem         Filesystem
     * @param \Vich\UploaderBundle\Metadata\MetadataReader  $uploaderMetaReader Uploader metadata reader
     * @param \Vich\UploaderBundle\Storage\StorageInterface $uploaderStorage    Uploader storage
     * @param mixed                                         $chunkSize          Chunk size
     * @param array                                         $uploaderMappings   Uploader mappings
     */
    public function __construct(
        string $name,
        EntityManager $em,
        Filesystem $filesystem,
        MetadataReader $uploaderMetaReader,
        StorageInterface $uploaderStorage,
        $chunkSize,
        array $uploaderMappings
    ) {
        parent::__construct($name);

        $this->em = $em;
        $this->filesystem = $filesystem;
        $this->uploaderMetaReader = $uploaderMetaReader;
        $this->uploaderStorage = $uploaderStorage;
        $this->chunkSize = (int)$chunkSize;
        $this->uploaderMappings = $uploaderMappings;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Shows list of orphan images.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata->getReflectionClass()->isAbstract() || !$this->uploaderMetaReader->isUploadable($metadata->getName())) {
                continue;
            }

            $inDatabase   = $this->findInDatabase($metadata->getName());
            $inFilesystem = $this->findInFilesystem($metadata->getName());

            foreach ($inDatabase as $pathname) {
                if (!isset($inFilesystem[$pathname])) {
                    $io->writeln(sprintf('Image "%s" exists in database but not in filesystem.', $pathname));

                    continue;
                }

                unset($inFilesystem[$pathname]);
            }
            foreach ($inFilesystem as $pathname) {
                $io->writeln(sprintf('Image "%s" exists in filesystem but not in database.', $pathname));
            }
        }

        return 0;
    }

    /**
     * @param string $class Entity class
     *
     * @return array
     */
    private function findInDatabase(string $class): array
    {
        $pathnames = [];
        $iterator  = $this->em->getRepository($class)->createQueryBuilder('o')->getQuery()->iterate();

        while ($row = $iterator->next()) {
            foreach (array_keys($this->uploaderMetaReader->getUploadableFields($class)) as $field) {
                $pathname = $this->uploaderStorage->resolvePath(reset($row), $field);

                if (null !== $pathname) {
                    $pathnames[$pathname] = $pathname;
                }
            }
            if ($iterator->key() > 0 && 0 === $iterator->key() % $this->chunkSize) {
                $this->em->clear();
            }
        }

        $this->em->clear();

        return $pathnames;
    }

    /**
     * @param string $class Entity class
     *
     * @return array
     */
    private function findInFilesystem(string $class): array
    {
        $pathnames = [];

        foreach ($this->uploaderMetaReader->getUploadableFields($class) as $field => $params) {
            $dir = $this->uploaderMappings[$params['mapping']]['upload_destination'];

            if (in_array(AbstractImage::class, class_parents($class))) {
                $dir .= DIRECTORY_SEPARATOR.$class::{'getUploadDir'}();
            }
            if (!$this->filesystem->exists($dir)) {
                continue;
            }
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            foreach ((new Finder())->in($dir)->depth(0)->files() as $file) {
                $pathnames[$file->getPathname()] = $file->getPathname();
            }
        }

        return $pathnames;
    }
}
