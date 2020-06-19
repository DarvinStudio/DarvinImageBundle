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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

/**
 * Update image properties event subscriber
 */
class UpdateImagePropertiesSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::POST_UPLOAD => 'updateProperties',
        ];
    }

    /**
     * @param \Vich\UploaderBundle\Event\Event $event Event
     */
    public function updateProperties(Event $event): void
    {
        $image = $event->getObject();

        if (!$image instanceof AbstractImage) {
            return;
        }

        $image->setExtension(strtolower(pathinfo($image->getFilename(), PATHINFO_EXTENSION)));

        if (null === $image->getName()) {
            $image->setName(preg_replace(sprintf('/\.%s$/', $image->getExtension()), '', $image->getFilename()));
        }
    }
}
