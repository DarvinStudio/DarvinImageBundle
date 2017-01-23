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

        $image = $em->getRepository(AbstractImage::class)->find($id);

        if (empty($image)) {
            throw $this->createNotFoundException(sprintf('Unable to find image by ID "%d".', $id));
        }

        $em->remove($image);
        $em->flush();

        return new Response();
    }
}
