<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\EventListener\Imagine\Cache;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Imagine\Cache\Warmer\ImagineCacheWarmerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

/**
 * Warm Imagine cache event subscriber
 */
class WarmSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Darvin\ImageBundle\Imagine\Cache\Warmer\ImagineCacheWarmerInterface
     */
    private $imagineCacheWarmer;

    /**
     * @param \Darvin\ImageBundle\Imagine\Cache\Warmer\ImagineCacheWarmerInterface $imagineCacheWarmer Imagine cache warmer
     */
    public function __construct(ImagineCacheWarmerInterface $imagineCacheWarmer)
    {
        $this->imagineCacheWarmer = $imagineCacheWarmer;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::POST_UPLOAD => 'warmCache',
        ];
    }

    /**
     * @param \Vich\UploaderBundle\Event\Event $event Event
     */
    public function warmCache(Event $event): void
    {
        $image = $event->getObject();

        if ($image instanceof AbstractImage && null !== $image->getFile()) {
            $this->imagineCacheWarmer->warmImageCache($image);
        }
    }
}
