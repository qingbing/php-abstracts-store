<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-12-05
 * Version      :   1.0
 */

namespace TestClass;

use Abstracts\Store;

class TestStore extends Store
{
    private $_storeData = [];

    /**
     * 属性赋值后执行函数
     */
    public function init()
    {
        // TODO: Implement init() method.
    }

    /**
     * 获取最终的 id
     * @param mixed $key
     * @return string
     */
    protected function buildKey($key)
    {
        return md5(is_string($key) ? $key : json_encode($key));
    }

    /**
     * 获取 id 的信息
     * @param mixed $id
     * @return mixed
     */
    protected function getValue($id)
    {
        return isset($this->_storeData[$id]) ? $this->_storeData[$id] : null;
    }

    /**
     * 保存 id 的信息
     * @param string $id
     * @param string $value
     * @param int $ttl
     * @return bool
     */
    protected function setValue($id, $value, $ttl)
    {
        // 测试示例，不考虑 ttl 的情况
        $this->_storeData[$id] = $value;
        return true;
    }

    /**
     * 删除 id 的信息
     * @param string $id
     * @return bool
     */
    protected function deleteValue($id)
    {
        unset($this->_storeData[$id]);
        return true;
    }

    /**
     * 清理所有值
     * @return bool
     */
    protected function clearValues()
    {
        $this->_storeData = [];
        return true;
    }
}