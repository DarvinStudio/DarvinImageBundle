<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Controller\Archive;

use Darvin\ImageBundle\Archive\ArchiverInterface;
use Darvin\ImageBundle\Form\Factory\ArchiveFormFactoryInterface;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Archive build controller
 */
class BuildController
{
    /**
     * @var \Darvin\ImageBundle\Archive\ArchiverInterface
     */
    private $archiver;

    /**
     * @var \Darvin\Utils\Flash\FlashNotifierInterface
     */
    private $flashNotifier;

    /**
     * @var \Darvin\ImageBundle\Form\Factory\ArchiveFormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Darvin\ImageBundle\Archive\ArchiverInterface                $archiver      Archiver
     * @param \Darvin\Utils\Flash\FlashNotifierInterface                   $flashNotifier Flash notifier
     * @param \Darvin\ImageBundle\Form\Factory\ArchiveFormFactoryInterface $formFactory   Archive form factory
     * @param \Symfony\Component\Routing\RouterInterface                   $router        Router
     * @param \Symfony\Contracts\Translation\TranslatorInterface           $translator    Translator
     */
    public function __construct(
        ArchiverInterface $archiver,
        FlashNotifierInterface $flashNotifier,
        ArchiveFormFactoryInterface $formFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->archiver = $archiver;
        $this->flashNotifier = $flashNotifier;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->createBuildForm()->handleRequest($request);

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
                $this->flashNotifier->error($message);
            }

            return new RedirectResponse($url);
        }

        set_time_limit(0);

        $filename = $this->archiver->archive();

        $message = $this->translator->trans('archive.action.build.success', [], 'darvin_image');
        $url = $this->router->generate('darvin_image_archive_download', [
            'filename' => $filename,
        ]);

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse(null, true, $message, [], $url);
        }

        $this->flashNotifier->success($message);

        return new RedirectResponse($url);
    }
}
