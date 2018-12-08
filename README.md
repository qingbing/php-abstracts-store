# php-abstracts-store
abstracts : store 存储组件的抽象

可扩展成 cache、session、cookie等。

## 注意事项
 - 将 Store 的"isWorking"设置成false，则取值始终为"$default"
 - Store 的键名支持 字符串、数字、数组等可序列化的变量
 - Store 的键值支持 字符串、数字、数组等可序列化的变量
 - Store 支持批量的设置、获取、删除操作

## 使用方法
### 定义
```php
class TestStore extends Store
{
    private $_storeData = [];

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
```

### 示例实例化使用
```php
        // 获取实例
        $store = new TestStore([
            'ttl' => "86400",
            'isEncrypt' => "false",
            'isWorking' => "true",
        ]);
        var_dump($store);


        // ====== 普通用法 ======
        $key = "name";
        // 设置id值
        $status = $store->set($key, "ss");
        var_dump($status);
        // 获取id值
        $name = $store->get($key);
        var_dump($name);
        // 删除id值
        $status = $store->delete($key);
        var_dump($status);
        // 判断换成是否存在
        $status = $store->has($key);
        var_dump($status);


        // ====== 批量用法 ======
        // 批量设置
        $status = $store->setMultiple([
            "name" => 'ss',
            "author" => [
                'qingbing',
                '10000',
            ],
        ]);
        var_dump($status);
        // 批量获取
        $values = $store->getMultiple(["name", "author"]);
        var_dump($values);
        // 批量删除
        $status = $store->deleteMultiple(["name", "author"]);
        var_dump($status);


        // ====== 键、值随意化 ======
        $key = ["sex", "name"];
        // 设置
        $status = $store->set($key, ["女", ["xxx"]]);
        var_dump($status);
        // 获取
        $status = $store->get($key);
        var_dump($status);
        // 删除
        $status = $store->delete($key);
        var_dump($status);


        // ====== 清空 ======
        // 清空命名空间换成
        $status = $store->clear();
        var_dump($status);
```

## ====== 异常代码集合 ======

异常代码格式：1004 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 100400101 : 存取批量操作参数必须可遍历
 - 100400102 : 存取批量操作参数必须可遍历
 - 100400103 : 存取批量操作参数必须可遍历

```