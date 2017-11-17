<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Command;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmer;
use Doctrine\ORM\EntityManager;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Warmup Imagine cache command
 */
class WarmupImagineCacheCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmer
     */
    private $imagineCacheWarmer;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @param string                                               $name               Command name
     * @param \Doctrine\ORM\EntityManager                          $em                 Entity manager
     * @param \Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmer $imagineCacheWarmer Imagine cache warmer
     * @param int                                                  $chunkSize          Chunk size
     */
    public function __construct($name, EntityManager $em, ImagineCacheWarmer $imagineCacheWarmer, $chunkSize)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->imagineCacheWarmer = $imagineCacheWarmer;
        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Generates Imagine cache for all images.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $qb = $this->em->getRepository(AbstractImage::class)->createQueryBuilder('o');

        $countQb = clone $qb;
        $io->progressStart($countQb->select($countQb->expr()->count('o'))->getQuery()->getSingleScalarResult());

        $iterator = $qb->getQuery()->iterate();

        while ($iterator->next()) {
            $row = $iterator->current();
            /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $image */
            $image = reset($row);

            try {
                $this->imagineCacheWarmer->warmupImageCache($image);
            } catch (NotLoadableException $ex) {
                $io->warning($ex->getMessage());
            }

            $io->progressAdvance();
            $io->write(' '.$image->getFilename());

            if ($iterator->key() > 0 && 0 === $iterator->key() % $this->chunkSize) {
                $this->em->clear();
            }
        }

        $io->progressFinish();
    }
}
