<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Command;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Update image dimensions command
 */
class UpdateImageDimensionsCommand extends Command
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
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $io;

    /**
     * @param string                                        $name            Command name
     * @param \Doctrine\ORM\EntityManager                   $em              Entity manager
     * @param \Vich\UploaderBundle\Storage\StorageInterface $uploaderStorage Uploader storage
     * @param int                                           $chunkSize       Chunk size
     */
    public function __construct($name, EntityManager $em, StorageInterface $uploaderStorage, $chunkSize)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->uploaderStorage = $uploaderStorage;
        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Updates width and height of all images.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = $io = new SymfonyStyle($input, $output);

        $qb = $this->em->getRepository(AbstractImage::class)->createQueryBuilder('o');

        $countQb = clone $qb;
        $count = (int)$countQb->select('COUNT(o)')->getQuery()->getSingleScalarResult();

        if (0 === $count) {
            $io->note('Nothing to update, exiting.');

            return;
        }

        $io->progressStart($count);

        $iterator = $qb->getQuery()->iterate();

        while ($iterator->next()) {
            $row = $iterator->current();
            /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $image */
            $image = reset($row);

            $pathname = $this->uploaderStorage->resolvePath($image, 'file');

            $messageParts = ['', $image->getId(), $pathname];

            $dimensions = @getimagesize($pathname);

            if (is_array($dimensions)) {
                list($width, $height) = $dimensions;

                $image
                    ->setWidth($width)
                    ->setHeight($height);

                $messageParts[] = $image->getDimensions();
                $io->writeln(implode(' ', $messageParts));
            } else {
                $io->error(implode(' ', $messageParts));
            }

            $this->flushIfNeeded($iterator);

            $io->progressAdvance();
        }

        $this->flush();
    }

    /**
     * @param \Doctrine\ORM\Internal\Hydration\IterableResult $iterator Iterator
     */
    private function flushIfNeeded(IterableResult $iterator)
    {
        if ($iterator->key() > 0 && 0 === $iterator->key() % $this->chunkSize) {
            $this->flush();
        }
    }

    private function flush()
    {
        $this->io->comment('Flushing...');

        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();
    }
}
