<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-12-05
 * Version      :   1.0
 */

namespace Abstracts;

use Helper\Coding;
use Helper\Exception;

/**
 * @link https://www.php-fig.org/psr/psr-16/
 * Class Store
 *
 * @property int $ttl
 * @property bool $isEncrypt
 * @property bool $isWorking
 */
abstract class Store extends Component
{
    private $_ttl = 86400;
    private $_isEncrypt = false;
    private $_isWorking = true;

    /**
     * 获取存储变量生效时间
     * @return int
     */
    public function getTtl()
    {
        return $this->_ttl;
    }

    /**
     * 设置存储变量生效时间
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->_ttl = intval($ttl);
    }

    /**
     * 获取存储结果是否加密保存
     * @return bool
     */
    public function getIsEncrypt()
    {
        return $this->_isEncrypt;
    }

    /**
     * 设置存储结果是否加密保存
     * @param bool $isEncrypt
     */
    public function setIsEncrypt($isEncrypt)
    {
        if (is_bool($isEncrypt)) {
            $this->_isEncrypt = $isEncrypt;
        } else {
            $this->_isEncrypt = "true" === $isEncrypt;
        }
    }

    /**
     * 取值是否生效
     * @return bool
     */
    public function getIsWorking()
    {
        return $this->_isWorking;
    }

    /**
     * 取值是否生效
     * @param bool $isWorking
     */
    public function setIsWorking($isWorking)
    {
        if (is_bool($isWorking)) {
            $this->_isWorking = $isWorking;
        } else {
            $this->_isWorking = "false" !== $isWorking;
        }
    }

    /**
     * 获取最终的id
     * @param mixed $key
     * @return string
     */
    abstract protected function buildKey($key);

    /**
     * 编码需要存储的数据信息
     * @param mixed $value
     * @return string
     */
    protected function encodeSaveValue($value)
    {
        return $this->getIsEncrypt() ? Coding::secure_encode($value) : serialize($value);
    }

    /**
     * 解码读取的数据信息
     * @param string $saveValue
     * @return mixed
     */
    protected function decodeSaveValue($saveValue)
    {
        return $this->getIsEncrypt() ? Coding::secure_decode($saveValue) : unserialize($saveValue);
    }

    /**
     * Fetches a value from the cache.
     *
     * @param mixed $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    public function get($key, $default = null)
    {
        if (false === $this->getIsWorking()) {
            return $default;
        }
        $id = $this->buildKey($key);
        $value = $this->getValue($id);
        if (null === $value) {
            return $default;
        }
        return $this->decodeSaveValue($value);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param mixed $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     */
    public function set($key, $value, $ttl = null)
    {
        $id = $this->buildKey($key);
        $ttl = (null === $ttl) ? $this->getTtl() : $ttl;
        return $this->setValue($id, $this->encodeSaveValue($value), $ttl);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param mixed $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete($key)
    {
        $id = $this->buildKey($key);
        return $this->deleteValue($id);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        return $this->clearValues();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     * @throws Exception
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys) && !$keys instanceof \Traversable) {
            throw new Exception("存取批量操作参数必须可遍历", 100400201);
        }
        $R = [];
        foreach ($keys as $key) {
            $R[$key] = $this->get($key, $default);
        }
        return $R;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     * @throws Exception
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values) && !$values instanceof \Traversable) {
            throw new Exception("存取批量操作参数必须可遍历", 100400202);
        }
        $RStatus = true;
        foreach ($values as $key => $value) {
            $status = $this->set($key, $value, $ttl);
            if (false === $status) {
                $RStatus = false;
            }
        }
        return $RStatus;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws Exception
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys) && !$keys instanceof \Traversable) {
            throw new Exception("存取批量操作参数必须可遍历", 100400203);
        }
        $RStatus = true;
        foreach ($keys as $key) {
            $status = $this->delete($key);
            if (false === $status) {
                $RStatus = false;
            }
        }
        return $RStatus;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param mixed $key The cache item key.
     *
     * @return bool
     */
    public function has($key)
    {
        $id = $this->buildKey($key);
        $value = $this->getValue($id);
        return $value !== null;
    }

    /**
     * 获取 id 的信息
     * @param mixed $id
     * @return mixed
     */
    abstract protected function getValue($id);

    /**
     * 保存 id 的信息
     * @param string $id
     * @param string $value
     * @param int $ttl
     * @return bool
     */
    abstract protected function setValue($id, $value, $ttl);

    /**
     * 删除 id 的信息
     * @param string $id
     * @return bool
     */
    abstract protected function deleteValue($id);

    /**
     * 清理当前命名空间下的存取信息
     * @return bool
     */
    abstract protected function clearValues();
}