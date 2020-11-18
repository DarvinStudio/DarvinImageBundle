<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Command\Imagine\Cache;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Warmup Imagine cache command
 */
class WarmupCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmerInterface
     */
    private $imagineCacheWarmer;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @param \Doctrine\ORM\EntityManager                                   $em                 Entity manager
     * @param \Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmerInterface $imagineCacheWarmer Imagine cache warmer
     * @param int                                                           $chunkSize          Chunk size
     */
    public function __construct(EntityManager $em, ImagineCacheWarmerInterface $imagineCacheWarmer, int $chunkSize)
    {
        parent::__construct();

        $this->em = $em;
        $this->imagineCacheWarmer = $imagineCacheWarmer;
        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('darvin:image:cache:warmup')
            ->setDescription('Generates Imagine cache for all images.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $qb = $this->em->getRepository(AbstractImage::class)->createQueryBuilder('o');

        $countQb = clone $qb;
        $count = (int)$countQb->select($countQb->expr()->count('o'))->getQuery()->getSingleScalarResult();

        if (0 === $count) {
            $io->comment('No images found, exiting.');

            return 0;
        }

        $io->progressStart($count);

        $iterator = $qb->getQuery()->iterate();

        while ($iterator->next()) {
            $row = $iterator->current();
            /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $image */
            $image = reset($row);

            try {
                $this->imagineCacheWarmer->warmupImageCache($image);
            } catch (\Exception $ex) {
                $io->warning($ex->getMessage());
            }

            $io->progressAdvance();
            $io->write(' '.$image->getFilename());

            if ($iterator->key() > 0 && 0 === $iterator->key() % $this->chunkSize) {
                $this->em->clear();
            }
        }

        $io->progressFinish();

        return 0;
    }
}
