<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Controller\Image;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Image controller abstract implementation
 */
abstract class AbstractImageController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em Entity manager
     */
    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }

    /**
     * @param array $ids Image IDs
     *
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage[]
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getImages(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $this->getImageRepository()->createQueryBuilder('o');

        $images = $qb
            ->where($qb->expr()->in('o.id', $ids))
            ->orderBy('o.position')
            ->getQuery()
            ->getResult();

        if (count($images) !== count($ids)) {
            throw new NotFoundHttpException('Unable to find one or more images.');
        }

        return $images;
    }

    /**
     * @param mixed $id Image ID
     *
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getImage($id): AbstractImage
    {
        $image = $this->getImageRepository()->find($id);

        if (empty($image)) {
            throw new NotFoundHttpException(sprintf('Unable to find image by ID "%s".', $id));
        }

        return $image;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getImageRepository(): EntityRepository
    {
        return $this->em->getRepository(AbstractImage::class);
    }
}
