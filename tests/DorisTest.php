<?php

declare(strict_types=1);

namespace HyperfTest\Database\Doris;

use App\Model\GoodsDoris;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DorisTest extends TestCase
{
    public function index()
    {
        $this->doris();
    }

    public function doris()
    {
        $goods = GoodsDoris::query()->where('id', 19386)->first();
        $this->assertNotNull($goods);
    }
}
