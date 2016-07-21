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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Archive controller
 */
class ArchiveController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buildAction()
    {
        set_time_limit(0);

        $filename = $this->getArchiver()->archive();

        return $this->redirectToRoute('darvin_image_archive_download', [
            'filename' => $filename,
        ]);
    }

    /**
     * @param string $filename Filename
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
     * @return \Darvin\ImageBundle\Archive\Archiver
     */
    private function getArchiver()
    {
        return $this->get('darvin_image.archiver');
    }
}
