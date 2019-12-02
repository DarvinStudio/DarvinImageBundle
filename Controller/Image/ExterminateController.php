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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Image exterminate controller
 */
class ExterminateController extends AbstractImageController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        $exterminated = [];
        $ids          = $request->request->get('ids', []);

        if (!is_array($ids)) {
            $ids = [$ids];
        }
        foreach ($this->getImages($ids) as $image) {
            $this->em->remove($image);

            $exterminated[] = $image->getId();
        }

        $this->em->flush();

        return new JsonResponse([
            'exterminated' => $exterminated,
        ]);
    }
}
