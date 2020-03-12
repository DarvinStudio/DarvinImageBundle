<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\EventListener;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\Utils\Event\ClonableEvents;
use Darvin\Utils\Event\CloneEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Copy cloned image file event subscriber
 */
class CopyClonedImageFileSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ClonableEvents::CLONED => ['copyFile', -10],
        ];
    }

    /**
     * @param \Darvin\Utils\Event\CloneEvent $event Event
     */
    public function copyFile(CloneEvent $event): void
    {
        $original = $event->getOriginal();

        if (!$original instanceof AbstractImage) {
            return;
        }

        /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $clone */
        $clone = $event->getClone();

        if (null === $clone->getFile()) {
            $event->setClone(null);

            return;
        }

        $clone->setName(null);
    }
}
