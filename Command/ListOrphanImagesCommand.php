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
use Symfony\Component\Console\Style\StyleInterface;
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
        $databaseOrphans = $filesystemOrphans = [];

        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata->getReflectionClass()->isAbstract() || !$this->uploaderMetaReader->isUploadable($metadata->getName())) {
                continue;
            }

            $inDatabase   = $this->findInDatabase($metadata->getName());
            $inFilesystem = $this->findInFilesystem($metadata->getName());

            foreach ($inDatabase as $pathname) {
                if (!isset($inFilesystem[$pathname])) {
                    $databaseOrphans[$pathname] = $pathname;

                    continue;
                }

                unset($inFilesystem[$pathname]);
            }
            foreach ($inFilesystem as $pathname) {
                $filesystemOrphans[$pathname] = $pathname;
            }
        }

        $this
            ->print($databaseOrphans, $io, 'In database but not in filesystem')
            ->print($filesystemOrphans, $io, 'In filesystem but not in database');

        return 0;
    }

    /**
     * @param string[]                                        $pathnames Orphan image pathnames
     * @param \Symfony\Component\Console\Style\StyleInterface $io        Input/output
     * @param string                                          $title     Title
     *
     * @return ListOrphanImagesCommand
     */
    private function print(array $pathnames, StyleInterface $io, string $title): ListOrphanImagesCommand
    {
        $rows = array_map(function (string $pathname): array {
            $modifiedAt = null;

            $mtime = @filemtime($pathname);

            if (false !== $mtime) {
                $modifiedAt = \DateTime::createFromFormat('U', (string)$mtime)->format('Y-m-d H:i');
            }

            return [$modifiedAt, $pathname];
        }, $pathnames);

        usort($rows, function (array $a, array $b): int {
            if ($a[0] !== $b[0]) {
                return $a[0] <=> $b[0];
            }

            return $a[1] <=> $b[1];
        });

        $io->title($title);
        $io->table(['Updated', 'Pathname'], $rows);

        return $this;
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
