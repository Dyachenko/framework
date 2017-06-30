<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Cache\Hierarchy\HierarchicalPoolInterface;
use Cache\Taggable\TaggablePoolInterface as Instance;

/**
 * Proxy to Cache
 *
 * Example of usage
 *     use Bluz\Proxy\Cache;
 *
 *     if (!$result = Cache::get('some unique id')) {
 *          $result = 2*2;
 *          Cache::set('some unique id', $result);
 *     }
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance|false getInstance()
 *
 * @method   static bool delete($key)
 * @see      CacheItemPoolInterface::deleteItem()
 *
 * @method   static bool clear()
 * @see      CacheItemPoolInterface::clear()
 */
class Cache
{
    use ProxyTrait;

    /**
     * No expiry TTL value
     */
    const TTL_NO_EXPIRY = 0;

    /**
     * @var array
     */
    protected static $pools = [];

    /**
     * Init cache instance
     *
     * @return Instance|false
     * @throws ComponentException
     */
    protected static function initInstance()
    {
        $adapter = Config::getData('cache', 'adapter');
        return self::getAdapter($adapter);
    }

    /**
     * Get Cache Adapter
     *
     * @param  string $adapter
     *
     * @return Instance|false
     * @throws ComponentException
     */
    public static function getAdapter($adapter)
    {
        $config = Config::getData('cache');

        if ($config && $adapter && isset($config['enabled']) && $config['enabled']) {
            if (!isset($config['pools'][$adapter])) {
                throw new ComponentException("Class `Proxy\\Cache` required configuration for `$adapter` adapter");
            } else {
                if (!isset(static::$pools[$adapter])) {
                    static::$pools[$adapter] = $config['pools'][$adapter]();
                }
                return static::$pools[$adapter];
            }
        }
        return false;
    }

    /**
     * Get value of cache item
     *
     * @param  string $key
     *
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function get($key)
    {
        if (!$cache = self::getInstance()) {
            return false;
        }

        $key = self::prepare($key);

        if ($cache->hasItem($key)) {
            $item = $cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }
        return false;
    }

    /**
     * Set value of cache item
     *
     * @param  string $key
     * @param  mixed  $data
     * @param  int    $ttl
     * @param  array  $tags
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function set($key, $data, $ttl = self::TTL_NO_EXPIRY, $tags = [])
    {
        if (!$cache = self::getInstance()) {
            return false;
        }

        $key = self::prepare($key);

        $item = $cache->getItem($key);
        $item->set($data);

        if (self::TTL_NO_EXPIRY !== $ttl) {
            $item->expiresAfter($ttl);
        }

        if (!empty($tags)) {
            $item->setTags($tags);
        }

        return $cache->save($item);
    }

    /**
     * Prepare key
     *
     * @return string
     */
    public static function prepare($key)
    {
        return str_replace(['-', '/', '\\', '@', ':'], '_', $key);
    }

    /**
     * Clear cache items by tags
     *
     * @see    TaggablePoolInterface::clearTags()
     *
     * @return bool
     */
    public static function clearTags(array $tags)
    {
        if (self::getInstance() instanceof HierarchicalPoolInterface) {
            return self::getInstance()->clearTags($tags);
        }
        return false;
    }
}
