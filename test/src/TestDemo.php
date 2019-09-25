<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-10-27
 * Version      :   1.0
 */

namespace Test;

use DBootstrap\Abstracts\Tester;
use TestClass\TestStore;

/**
 * Class TestDemo
 * @package Test
 */
class TestDemo extends Tester
{
    /**
     * 执行函数
     * @throws \Exception
     */
    public function run()
    {
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
    }
}