<?php declare(strict_types=1);
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder\Filter;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * Direct Imagine URL builder filter
 */
class DirectImagineFilter implements FilterInterface
{
    public const FILTER_NAME_PARAM = 'filter_name';
    public const NAME              = 'direct_imagine';

    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager
     */
    private $imagineCacheManager;

    /**
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager $imagineCacheManager Imagine cache manager
     */
    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildUrl(string $imagePathname, array $parameters = []): string
    {
        if (!isset($parameters[self::FILTER_NAME_PARAM])) {
            throw new \InvalidArgumentException(sprintf("%s must be provided to %s options", self::FILTER_NAME_PARAM, self::class));
        }

        return $this->imagineCacheManager->getBrowserPath($imagePathname, $parameters[self::FILTER_NAME_PARAM]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
