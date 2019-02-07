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
use Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

/**
 * Warmup Imagine cache event subscriber
 */
class WarmupImagineCacheSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmerInterface
     */
    private $imagineCacheWarmer;

    /**
     * @param \Darvin\ImageBundle\Imagine\Cache\ImagineCacheWarmerInterface $imagineCacheWarmer Imagine cache warmer
     */
    public function __construct(ImagineCacheWarmerInterface $imagineCacheWarmer)
    {
        $this->imagineCacheWarmer = $imagineCacheWarmer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_UPLOAD => 'warmupCache',
        ];
    }

    /**
     * @param \Vich\UploaderBundle\Event\Event $event Event
     */
    public function warmupCache(Event $event)
    {
        $image = $event->getObject();

        if ($image instanceof AbstractImage && null !== $image->getFile()) {
            $this->imagineCacheWarmer->warmupImageCache($image);
        }
    }
}
