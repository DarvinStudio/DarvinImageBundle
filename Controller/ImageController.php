<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Controller;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Form\Type\ImageEditType;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Image controller
 */
class ImageController extends AbstractController
{
    /**
     * @param mixed $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($this->getImage($id));
        $em->flush();

        return new Response();
    }

    /**
     * @param mixed $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function disableAction($id): Response
    {
        $image = $this->getImage($id);

        if (!$image->isEnabled()) {
            throw $this->createNotFoundException(sprintf('Image with ID "%s" already disabled.', $id));
        }

        $image->setEnabled(false);

        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param mixed                                     $id      Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request, $id): Response
    {
        $image = $this->getImage($id);

        $form = $this->createForm(ImageEditType::class, $image, [
            'action' => $this->generateUrl('darvin_image_image_edit', [
                'id' => $id,
            ]),
        ])->handleRequest($request);

        $template = $this->container->getParameter(
            sprintf('darvin_image.action.edit.template.%s', $request->isXmlHttpRequest() ? 'partial' : 'full')
        );

        $render = function (AbstractController $controller) use ($form, $image, $template) {
            return $controller->renderView($template, [
                'form'  => $form->createView(),
                'image' => $image,
            ]);
        };

        if (!$form->isSubmitted()) {
            return new Response($render($this));
        }
        if (!$form->isValid()) {
            if ($request->isXmlHttpRequest()) {
                return new AjaxResponse($render($this), false, FlashNotifierInterface::MESSAGE_FORM_ERROR);
            }

            $this->getFlashNotifier()->formError();

            return new Response($render($this));
        }

        $this->getDoctrine()->getManager()->flush();

        $message = $this->getTranslator()->trans('image.edit.success', [], 'darvin_image');

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse($render($this), true, $message);
        }

        $this->getFlashNotifier()->success($message);

        return $this->redirectToRoute('darvin_image_image_edit', [
            'id' => $id,
        ]);
    }

    /**
     * @param mixed $id Image ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function enableAction($id): Response
    {
        $image = $this->getImage($id);

        if ($image->isEnabled()) {
            throw $this->createNotFoundException(sprintf('Image with ID "%s" already enabled.', $id));
        }

        $image->setEnabled(true);

        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exterminateAction(Request $request): Response
    {
        $exterminated = [];
        $ids          = $request->request->get('ids', []);

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($this->getImages($ids) as $image) {
            $em->remove($image);

            $exterminated[] = $image->getId();
        }

        $em->flush();

        return new JsonResponse([
            'exterminated' => $exterminated,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sortAction(Request $request): Response
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
     * @param array $ids Image IDs
     *
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage[]
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getImages(array $ids): array
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
     * @param mixed $id Image ID
     *
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getImage($id): AbstractImage
    {
        $image = $this->getImageRepository()->find($id);

        if (empty($image)) {
            throw $this->createNotFoundException(sprintf('Unable to find image by ID "%s".', $id));
        }

        return $image;
    }

    /**
     * @return \Darvin\Utils\Flash\FlashNotifierInterface
     */
    private function getFlashNotifier(): FlashNotifierInterface
    {
        return $this->get('darvin_utils.flash.notifier');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getImageRepository(): EntityRepository
    {
        return $this->getDoctrine()->getManager()->getRepository(AbstractImage::class);
    }

    /**
     * @return \Symfony\Contracts\Translation\TranslatorInterface
     */
    private function getTranslator(): TranslatorInterface
    {
        return $this->get('translator');
    }
}
