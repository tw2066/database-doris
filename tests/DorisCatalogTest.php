<?php

declare(strict_types=1);

namespace HyperfTest\Database\Doris;

use App\Model\GoodsDorisCatalog;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DorisCatalogTest extends TestCase
{
    public function index()
    {
        $this->mysql();
        $this->es();
        $this->sqlsrv();
        $this->pgsql();
    }

    public function mysql()
    {
        // 查询
        $first = GoodsDorisCatalog::query()
            ->where('id', 1)
            ->first();
        $this->assertNotNull($first);

        // 修改
        $data['goods_id'] = rand(1, 10000);
        GoodsDorisCatalog::query()->where('id', 1)->update($data);
        $data = GoodsDorisCatalog::query()->where('id', 1)->first();
        $this->assertEquals($data['goods_id'], $data['goods_id']);

        // 插入
        $goodsId = time();
        $result = GoodsDorisCatalog::query()
            ->insert([
                'goods_id' => $goodsId,
                'goods_code' => "12\\'3",
                'goods_name' => '123',
            ]);
        $this->assertTrue($result);
        $count = GoodsDorisCatalog::query()->where('goods_id', $goodsId)->count();
        $this->assertEquals($count, 1);

        // 删除
        GoodsDorisCatalog::query()->where('goods_id', $goodsId)->delete();
        $count = GoodsDorisCatalog::query()->where('goods_id', $goodsId)->count();
        $this->assertEquals($count, 0);
    }

    protected function es(): void
    {
        $count = Db::connection('doris_es')->table('jc_order_103')
            ->where(function ($query) {
                $query
                    ->where('isHemp', 1)
                    ->orWhere('createdTime', '>', 1750032311);
            })
            ->limit(10)
            ->count();
        $this->assertTrue($count > 0);
    }

    protected function sqlsrv(): void
    {
        $data = Db::connection('doris_sqlsrv')->table('spzl')
            ->limit(5)
            ->get();
        $this->assertNotEmpty($data);
        $count = Db::connection('doris_sqlsrv')->table('spzl')->count();
        $this->assertTrue($count > 0);
    }

    protected function pgsql(): void
    {
        $data = Db::connection('doris_pg')->table('view_user')
            ->where('fnumber', 'WNO_10')
            ->orderBy('fid', 'desc')
            ->first();
        $this->assertNotEmpty($data);
        $count = Db::connection('doris_pg')->table('view_user')->count();
        $this->assertTrue($count > 0);
    }
}
