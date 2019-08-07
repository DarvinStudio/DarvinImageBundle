<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Controller;

use Darvin\ImageBundle\Archive\ArchiverInterface;
use Darvin\ImageBundle\Form\Factory\ArchiveFormFactoryInterface;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Archive controller
 */
class ArchiveController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buildAction(Request $request): Response
    {
        $form = $this->getArchiveFormFactory()->createBuildForm()->handleRequest($request);

        if (!$form->isValid()) {
            $messages = [];

            /** @var \Symfony\Component\Form\FormError $error */
            foreach ($form->getErrors(true) as $error) {
                $messages[] = $error->getMessage();
            }

            $url = $request->headers->get('referer', '/');

            if ($request->isXmlHttpRequest()) {
                return new AjaxResponse(null, false, implode(' ', $messages), [], $url);
            }
            foreach ($messages as $message) {
                $this->getFlashNotifier()->error($message);
            }

            return $this->redirect($url);
        }

        set_time_limit(0);

        $filename = $this->getArchiver()->archive();

        $message = $this->getTranslator()->trans('archive.action.build.success', [], 'darvin_image');
        $url = $this->generateUrl('darvin_image_archive_download', [
            'filename' => $filename,
        ]);

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse(null, true, $message, [], $url);
        }

        $this->getFlashNotifier()->success($message);

        return $this->redirect($url);
    }

    /**
     * @param string $filename Archive filename
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function downloadAction(string $filename): Response
    {
        $pathname = $this->getArchiver()->buildPathname($filename);

        if (!is_readable($pathname)) {
            throw $this->createNotFoundException(sprintf('Image archive "%s" is not readable.', $pathname));
        }

        return new BinaryFileResponse($pathname);
    }

    /**
     * @return \Darvin\ImageBundle\Form\Factory\ArchiveFormFactoryInterface
     */
    private function getArchiveFormFactory(): ArchiveFormFactoryInterface
    {
        return $this->get('darvin_image.archive.form_factory');
    }

    /**
     * @return \Darvin\ImageBundle\Archive\ArchiverInterface
     */
    private function getArchiver(): ArchiverInterface
    {
        return $this->get('darvin_image.archiver');
    }

    /**
     * @return \Darvin\Utils\Flash\FlashNotifierInterface
     */
    private function getFlashNotifier(): FlashNotifierInterface
    {
        return $this->get('darvin_utils.flash.notifier');
    }

    /**
     * @return \Symfony\Contracts\Translation\TranslatorInterface
     */
    private function getTranslator(): TranslatorInterface
    {
        return $this->get('translator');
    }
}
