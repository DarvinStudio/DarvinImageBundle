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

use Symfony\Component\HttpFoundation\Response;

/**
 * Image delete controller
 */
class DeleteController extends AbstractImageController
{
    /**
     * @param mixed $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke($id): Response
    {
        $this->em->remove($this->getImage($id));
        $this->em->flush();

        return new Response();
    }
}
