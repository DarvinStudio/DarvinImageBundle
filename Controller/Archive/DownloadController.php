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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Archive download controller
 */
class DownloadController
{
    /**
     * @var \Darvin\ImageBundle\Archive\ArchiverInterface
     */
    private $archiver;

    /**
     * @param \Darvin\ImageBundle\Archive\ArchiverInterface $archiver Archiver
     */
    public function __construct(ArchiverInterface $archiver)
    {
        $this->archiver = $archiver;
    }

    /**
     * @param string $filename Archive filename
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $filename): Response
    {
        $pathname = $this->archiver->buildPathname($filename);

        if (!is_readable($pathname)) {
            throw new NotFoundHttpException(sprintf('Image archive "%s" is not readable.', $pathname));
        }

        return new BinaryFileResponse($pathname);
    }
}
