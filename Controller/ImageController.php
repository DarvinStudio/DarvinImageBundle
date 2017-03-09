<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Controller;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Image controller
 */
class ImageController extends Controller
{
    /**
     * @param int $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($this->getImage($id));
        $em->flush();

        return new Response();
    }

    /**
     * @param int $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function disableAction($id)
    {
        $image = $this->getImage($id);

        if (!$image->isEnabled()) {
            throw $this->createNotFoundException(sprintf('Image with ID "%d" already disabled.', $id));
        }

        $image->setEnabled(false);

        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }

    /**
     * @param int $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function enableAction($id)
    {
        $image = $this->getImage($id);

        if ($image->isEnabled()) {
            throw $this->createNotFoundException(sprintf('Image with ID "%d" already enabled.', $id));
        }

        $image->setEnabled(true);

        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sortAction(Request $request)
    {
        $ids = $request->request->get('ids');

        if (!is_array($ids)) {
            throw $this->createNotFoundException(sprintf('Query parameter "ids" must be an array, got "%s".', gettype($ids)));
        }
        if (empty($ids)) {
            return new Response();
        }

        /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage[] $images */
        $images = $positions = [];

        foreach ($this->getImages($ids) as $image) {
            $images[$image->getId()] = $image;
            $positions[] = $image->getPosition();
        }
        foreach ($ids as $index => $id) {
            $images[$id]->setPosition($positions[$index]);
        }

        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }

    /**
     * @param int[] $ids Image IDs
     *
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage[]
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getImages(array $ids)
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
            throw $this->createNotFoundException('Unable to find one or more images.');
        }

        return $images;
    }

    /**
     * @param int $id Image ID
     *
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getImage($id)
    {
        $image = $this->getImageRepository()->find($id);

        if (empty($image)) {
            throw $this->createNotFoundException(sprintf('Unable to find image by ID "%d".', $id));
        }

        return $image;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getImageRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository(AbstractImage::class);
    }
}
