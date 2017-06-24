<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder\Filter;


use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class DirectImagineFilter implements FilterInterface
{
    const FILTER_NAME_PARAM = 'filter_name';
    const NAME = 'direct_imagine';

    /**
     * @var CacheManager
     */
    private $liipCacheManager;

    /**
     * DirectImagineFilter constructor.
     * @param CacheManager $liipCacheManager
     */
    public function __construct(CacheManager $liipCacheManager)
    {
        $this->liipCacheManager = $liipCacheManager;
    }

    /**
     * @param string $imagePathname Image pathname
     * @param array $parameters Parameters
     * @return string
     * @throws FilterException
     */
    public function buildUrl($imagePathname, array $parameters)
    {
        if (!isset($parameters[self::FILTER_NAME_PARAM])) {
            throw new FilterException(
                sprintf("%s must be provided to %s options", self::FILTER_NAME_PARAM, self::class)
            );
        }
        return $this->liipCacheManager->getBrowserPath($imagePathname, $parameters[self::FILTER_NAME_PARAM]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}