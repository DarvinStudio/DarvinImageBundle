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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Image sort controller
 */
class SortController extends AbstractImageController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(Request $request): Response
    {
        $ids = $request->request->get('ids');

        if (!is_array($ids)) {
            throw new NotFoundHttpException(sprintf('Query parameter "ids" must be an array, got "%s".', gettype($ids)));
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

        $this->em->flush();

        return new Response();
    }
}
