<?php

declare(strict_types=1);

namespace HyperfTest\Database\Doris;

use App\Model\GoodsDorisCatalog;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class DorisCatalogTest extends TestCase
{
    public function index()
    {
        $this->transaction();
        $this->mysql();
        $this->es();
        $this->sqlsrv();
        $this->pgsql();
        $this->oracle();
    }

    public function mysql(): void
    {
        // 查询
        /** @var GoodsDorisCatalog $first */
        $first = GoodsDorisCatalog::query()
            ->where('id', 1)
            ->first();
        $this->assertNotNull($first);

        $goods_id = rand(1, 10000);
        $first->goods_id = $goods_id;
        $first->save();
        $this->assertEquals($first->goods_id, $goods_id);

        // 修改
        $data['goods_id'] = -1;
        GoodsDorisCatalog::query()->where('id', 1)->update($data);
        $row = GoodsDorisCatalog::query()->where('id', 1)->first();
        $this->assertEquals($data['goods_id'], $row->goods_id);

        // 插入
        $goodsId = time() + 1;
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

    protected function transaction(): void
    {
        $data['goods_id'] = time();
        // 异常会滚情况
        try {
            Db::connection('doris_catalog_mysql')->beginTransaction();
            GoodsDorisCatalog::query()->where('id', 2)->update($data);
            throw new RuntimeException('error');
            Db::connection('doris_catalog_mysql')->commit();
        } catch (Throwable $throwable) {
            Db::connection('doris_catalog_mysql')->rollBack();
        }
        $goods_id = GoodsDorisCatalog::query()->where('id', 2)->pluck('goods_id')->get(0);
        $this->assertNotEquals($goods_id, $data['goods_id']);

        // 正常提交情况
        Db::connection('doris_catalog_mysql')->transaction(function () use ($data) {
            GoodsDorisCatalog::query()->where('id', 2)->update($data);
            // 不支持事务隔离查询
            // $goods_id = GoodsDorisCatalog::query()->where('id', 2)->pluck('goods_id');
            GoodsDorisCatalog::query()->where('id', 0)->delete();
        });

        $goods_id = GoodsDorisCatalog::query()->where('id', 2)->pluck('goods_id')->get(0);

        $this->assertEquals($goods_id, $data['goods_id']);
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
        Db::connection('doris_sqlsrv')->table('Users')->where('UserName', 'sqlsrv')->delete();

        $insert = [
            ['UserName' => 'sqlsrv', 'Email' => '1@a.com'],
        ];
        Db::connection('doris_sqlsrv')->table('Users')->insert($insert);

        $update['Email'] = '2@a.com';
        Db::connection('doris_sqlsrv')->table('Users')->where('UserName', 'sqlsrv')->update($update);

        $data = Db::connection('doris_sqlsrv')->table('Users')
            ->where('UserName', 'sqlsrv')
            ->first();
        $this->assertEquals($data->Email, '2@a.com');

        Db::connection('doris_sqlsrv')->table('Users')->where('UserName', 'sqlsrv')->delete();

        $count = Db::connection('doris_sqlsrv')->table('Users')->where('UserName', 'sqlsrv')->count();
        $this->assertEquals($count, 0);
    }

    protected function pgsql(): void
    {
        $username = '12345';
        Db::connection('doris_pg')->table('user_info')->where('username', $username)->delete();
        $insert = [
            ['username' => $username, 'email' => '55@aa.com'],
        ];
        Db::connection('doris_pg')->table('user_info')->insert($insert);
        $update = ['email' => '66@aa.com'];
        Db::connection('doris_pg')->table('user_info')->where('username', $username)->update($update);

        $data = Db::connection('doris_pg')->table('user_info')
            ->where('username', $username)
            ->first();
        $this->assertEquals($data->email, '66@aa.com');
        Db::connection('doris_pg')->table('user_info')->where('username', $username)->delete();
        $count = Db::connection('doris_pg')->table('user_info')->count();
        $this->assertEquals($count, 0);
    }

    protected function oracle(): void
    {
        $dwbh = '12345';
        $insert = [
            ['DWBH' => $dwbh, 'OLDCODE' => '55'],
        ];
        Db::connection('doris_catalog_oracle')->table('A_BOLD')->insert($insert);

        $oldcode = '54321';
        $update = ['OLDCODE' => $oldcode];
        Db::connection('doris_catalog_oracle')->table('A_BOLD')->where('DWBH', $dwbh)->update($update);

        $row = Db::connection('doris_catalog_oracle')->table('A_BOLD')
            ->where('DWBH', $dwbh)
            ->orderBy('DWBH', 'desc')
            ->first();
        $this->assertNotEmpty($row);
        $this->assertEquals($oldcode, $row->OLDCODE);

        Db::connection('doris_catalog_oracle')->table('A_BOLD')->where('DWBH', $dwbh)->delete();
        $count = Db::connection('doris_catalog_oracle')->table('A_BOLD')
            ->where('DWBH', $dwbh)
            ->count();
        $this->assertEquals($count, 0);
    }
}
