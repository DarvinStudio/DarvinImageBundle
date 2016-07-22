<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Controller;

use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Archive controller
 */
class ArchiveController extends Controller
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buildAction(Request $request)
    {
        $form = $this->getArchiveFormFactory()->createBuildForm()->handleRequest($request);

        if (!$form->isValid()) {
            $messages = [];

            /** @var \Symfony\Component\Form\FormError $error */
            foreach ($form->getErrors(true) as $error) {
                $messages[] = $error->getMessage();
            }

            $url = $request->headers->get('referer');

            if ($request->isXmlHttpRequest()) {
                return new AjaxResponse('', false, implode(' ', $messages), [], $url);
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
            return new AjaxResponse('', true, $message, [], $url);
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
    public function downloadAction($filename)
    {
        $pathname = $this->getArchiver()->buildPathname($filename);

        if (!is_readable($pathname)) {
            throw $this->createNotFoundException(sprintf('Image archive "%s" is not readable.', $pathname));
        }

        return new BinaryFileResponse($pathname);
    }

    /**
     * @return \Darvin\ImageBundle\Form\Factory\ArchiveFormFactory
     */
    private function getArchiveFormFactory()
    {
        return $this->get('darvin_image.archive.form_factory');
    }

    /**
     * @return \Darvin\ImageBundle\Archive\ArchiverInterface
     */
    private function getArchiver()
    {
        return $this->get('darvin_image.archiver');
    }

    /**
     * @return \Darvin\Utils\Flash\FlashNotifierInterface
     */
    private function getFlashNotifier()
    {
        return $this->get('darvin_utils.flash.notifier');
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    private function getTranslator()
    {
        return $this->get('translator');
    }
}
