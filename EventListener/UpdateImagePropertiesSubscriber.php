<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_UPLOAD => 'updateProperties',
        ];
    }

    /**
     * @param \Vich\UploaderBundle\Event\Event $event Event
     */
    public function updateProperties(Event $event)
    {
        $image = $event->getObject();

        if (!$image instanceof AbstractImage) {
            return;
        }

        $image->setExtension(pathinfo($image->getFilename(), PATHINFO_EXTENSION));

        if (null === $image->getName()) {
            $image->setName(preg_replace(sprintf('/\.%s$/', $image->getExtension()), '', $image->getFilename()));
        }
    }
}
