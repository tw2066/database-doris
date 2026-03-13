# Hyperf Database Doris

[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](http://www.php.net)
[![Hyperf](https://img.shields.io/badge/hyperf-~3.1.0-red.svg)](https://hyperf.io)

## 📖 简介

`database-doris` 是专为 Hyperf 框架设计的数据库连接组件，提供对 Apache Doris 数据库的完整支持。通过 Doris 的 Multi-Catalog 功能，可以使用一套 MySQL 协议连接和操作多种异构数据源，实现统一的数据访问层。

## ✨ 特性

- 🚀 **完整的 Doris 支持** - 基于 `hyperf/database` ~3.1.0，完美兼容 Hyperf ORM
- 🔌 **Multi-Catalog 集成** - 通过 Doris 访问 MySQL、PostgreSQL、SQL Server、Elasticsearch 等数据源
- ⚡ **SQL 透传模式** - 支持 SQL 自动转换，优化查询性能
- 🎯 **标准 ORM 操作** - 完全支持 Hyperf 的 Model、查询构造器等功能
- 💾 **多连接池配置** - 支持多个 Doris 连接和 Catalog 配置
- 🛠️ **模型生成** - 支持通过命令行自动生成数据模型

## 📦 安装

```bash
composer require tangwei/database-doris
```

## 🔧 配置

在 `config/autoload/databases.php` 中添加 Doris 连接配置：

### 1. 基础 Doris 连接

```php
'doris' => [
    'driver'    => 'doris',
    'host'      => '127.0.0.1',
    'database'  => 'your_database',
    'port'      => 9030,
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'prefix'    => '',
    'pool'      => [
        'min_connections' => 1,
        'max_connections' => 20,
        'connect_timeout' => 10.0,
        'wait_timeout'    => 8.0,
        'heartbeat'       => -1,
        'max_idle_time'   => 60,
    ],
],
```

### 2. Doris Catalog 连接（推荐）

Catalog 模式允许通过 Doris 访问外部数据源，无需在本地安装其他数据库驱动。

#### 2.1 MySQL Catalog

```php
'doris_catalog_mysql' => [
    'driver'    => 'doris_catalog_mysql',
    'host'      => '127.0.0.1',
    'catalog'   => 'mysql_catalog_name',  // Doris 中创建的 MySQL Catalog 名称
    'database'  => 'target_database',
    'port'      => 9030,
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'prefix'    => '',
    'pool'      => [
        'min_connections' => 1,
        'max_connections' => 20,
        'connect_timeout' => 10.0,
        'wait_timeout'    => 8.0,
        'heartbeat'       => -1,
        'max_idle_time'   => 60,
    ],
],
```

#### 2.2 PostgreSQL Catalog

```php
'doris_catalog_pgsql' => [
    'driver'    => 'doris_catalog_pgsql',
    'host'      => '127.0.0.1',
    'catalog'   => 'postgresql_catalog',  // Doris 中创建的 PG Catalog 名称
    'database'  => 'public',
    'port'      => 9030,
    'username'  => 'postgres',
    'password'  => 'password',
    'charset'   => 'utf8',
    'prefix'    => '',
    'pool'      => [
        'min_connections' => 1,
        'max_connections' => 20,
        'connect_timeout' => 10.0,
        'wait_timeout'    => 8.0,
        'heartbeat'       => -1,
        'max_idle_time'   => 60,
    ],
],
```

#### 2.3 SQL Server Catalog

```php
'doris_catalog_sqlsrv' => [
    'driver'    => 'doris_catalog_sqlsrv',
    'host'      => '127.0.0.1',
    'catalog'   => 'sqlserver_catalog',  // Doris 中创建的 SQL Server Catalog 名称
    'database'  => 'dbo',
    'port'      => 9030,
    'username'  => 'sa',
    'password'  => 'password',
    'charset'   => 'utf8',
    'prefix'    => '',
    'pool'      => [
        'min_connections' => 1,
        'max_connections' => 20,
        'connect_timeout' => 10.0,
        'wait_timeout'    => 8.0,
        'heartbeat'       => -1,
        'max_idle_time'   => 60,
    ],
],
```

#### 2.4 Elasticsearch Catalog

```php
'doris_es' => [
    'driver'    => 'doris_catalog',
    'host'      => '127.0.0.1',
    'catalog'   => 'es',              // Doris 中创建的 ES Catalog 名称
    'database'  => 'default_db',
    'port'      => 9030,
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'prefix'    => '',
    'pool'      => [
        'min_connections' => 1,
        'max_connections' => 20,
        'connect_timeout' => 10.0,
        'wait_timeout'    => 8.0,
        'heartbeat'       => -1,
        'max_idle_time'   => 60,
    ],
],
```

### 配置说明

- `driver`: 驱动类型
  - `doris` - 直接连接 Doris
  - `doris_catalog` - 通用 Catalog 连接
  - `doris_catalog_mysql` - MySQL Catalog 连接
  - `doris_catalog_pgsql` - PostgreSQL Catalog 连接
  - `doris_catalog_sqlsrv` - SQL Server Catalog 连接
  
- `catalog`: Doris 中创建的 Catalog 名称（仅 Catalog 模式需要）
- `passthrough_sql_select`: 是否启用 SELECT 语句透传（默认：`false`），启用后会自动处理参数绑定，提升查询性能

## 💡 使用指南

### 1. 创建 Model

#### 1.1 直接连接 Doris

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class GoodsDoris extends Model
{
    protected ?string $table = 'goods';
    
    protected ?string $connection = 'doris';
}
```

#### 1.2 通过 Catalog 连接

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class GoodsDorisCatalog extends Model
{
    protected ?string $table = 'goods';
    
    protected ?string $connection = 'doris_catalog_mysql';
}
```

### 2. 基本 CRUD 操作

```php
use App\Model\GoodsDorisCatalog;
use Hyperf\DbConnection\Db;

// 查询
$goods = GoodsDorisCatalog::query()->where('id', 1)->first();
$list = GoodsDorisCatalog::query()->where('status', 1)->get();
$count = GoodsDorisCatalog::query()->count();

// 插入
GoodsDorisCatalog::query()->insert([
    'goods_id' => 123,
    'goods_code' => 'GC001',
    'goods_name' => '测试商品',
]);

// 更新
GoodsDorisCatalog::query()
    ->where('id', 1)
    ->update(['goods_name' => '新商品名称']);

// 删除
GoodsDorisCatalog::query()->where('id', 1)->delete();
```

### 3. 使用查询构造器

```php
// MySQL Catalog
$data = Db::connection('doris_catalog_mysql')
    ->table('goods')
    ->where('id', '>', 100)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

// PostgreSQL Catalog
$user = Db::connection('doris_catalog_pgsql')
    ->table('user')
    ->where('number', '10')
    ->first();

// SQL Server Catalog
$records = Db::connection('doris_catalog_sqlsrv')
    ->table('spzl')
    ->limit(5)
    ->get();

// Elasticsearch Catalog
$count = Db::connection('doris_es')
    ->table('order')
    ->where(function ($query) {
        $query->where('status', 1)
              ->orWhere('createdTime', '>', 1750032311);
    })
    ->count();
```

### 4. 复杂查询示例

```php
// 子查询
$goods = GoodsDorisCatalog::query()
    ->whereHas('orders', function ($query) {
        $query->where('status', 'paid');
    })
    ->get();

// 关联查询
$goods = GoodsDorisCatalog::query()
    ->with(['category', 'supplier'])
    ->where('price', '>', 100)
    ->get();

// 聚合查询
$result = GoodsDorisCatalog::query()
    ->selectRaw('category_id, COUNT(*) as count, SUM(price) as total')
    ->groupBy('category_id')
    ->havingRaw('SUM(price) > 1000')
    ->get();
```

## 🛠️ 代码生成

使用 Hyperf 的模型生成命令：

```bash
# 生成 Doris 模型
php bin/hyperf.php gen:model doris --table=goods

# 生成指定连接的模型
php bin/hyperf.php gen:model --pool doris_catalog_mysql goods
```

在 `databases.php` 中已配置了 `commands.gen:model` 选项，可以自定义生成参数：

```php
'commands' => [
    'gen:model' => [
        'path'          => 'app/Model',
        'force_casts'   => true,
        'inheritance'   => 'Model',
        'with_comments' => true,      // 生成字段注释
        'property_case' => 1,         // 属性命名风格
        'uses'          => '',
    ],
],
```

## 🚀 高级特性

### SQL 透传模式

Catalog 连接支持 SQL 透传功能，可以自动将绑定参数嵌入 SQL 语句，避免预处理，提升查询性能。

```php
'doris_catalog_mysql' => [
    // ...
    'passthrough_sql_select' => true,  // 默认启用 SELECT 透传模式
],
```

**注意事项：**
- 透传模式会自动处理参数转义，防止 SQL 注入
- 适用于复杂查询场景，性能更优
- 默认情况下 Catalog 模式不启用 SELECT 透传
- catalog事务支持(不推荐使用)

## 📊 Doris Catalog 配置参考

在 Doris 中创建 Catalog 的 SQL 示例：

### MySQL Catalog

```sql
CREATE CATALOG mysql_catalog PROPERTIES (
    "type" = "mysql",
    "mysql.host" = "192.168.1.100",
    "mysql.port" = "3306",
    "mysql.user" = "root",
    "mysql.password" = "password",
    "mysql.database" = "target_db"
);
```

### PostgreSQL Catalog

```sql
CREATE CATALOG postgresql_catalog PROPERTIES (
    "type" = "postgresql",
    "postgresql.host" = "192.168.1.100",
    "postgresql.port" = "5432",
    "postgresql.user" = "postgres",
    "postgresql.password" = "password",
    "postgresql.database" = "target_db"
);
```

### SQL Server Catalog

```sql
CREATE CATALOG sqlserver_catalog PROPERTIES (
    "type" = "sqlserver",
    "sqlserver.host" = "192.168.1.100",
    "sqlserver.port" = "1433",
    "sqlserver.user" = "sa",
    "sqlserver.password" = "password",
    "sqlserver.database" = "target_db"
);
```

### Elasticsearch Catalog

```sql
CREATE CATALOG es PROPERTIES (
    "type" = "elasticsearch",
    "es.host" = "192.168.1.100",
    "es.port" = "9200",
    "es.user" = "elastic",
    "es.password" = "password"
);
```

## ✅ 测试

测试覆盖：
- ✅ 基础 CRUD 操作
- ✅ 多 Catalog 连接（MySQL、PG、SQL Server、ES）
- ✅ 复杂查询条件
- ✅ 模型关联

## 🔍 常见问题

### Q1: Catalog 模式和直接连接有什么区别？

**A:** 
- **直接连接** (`doris`): 直接连接到 Doris 数据库，操作 Doris 本地表
- **Catalog 模式**: 通过 Doris 的多表源功能访问外部数据库，无需本地安装对应驱动

### Q2: 为什么使用 Catalog 模式？

**A:**
1. **统一接口** - 使用 MySQL 协议访问多种数据源
2. **简化部署** - 无需安装 PostgreSQL、SQL Server 等驱动
3. **性能优化** - Doris 的查询优化器可以优化跨源查询
4. **开发效率** - 统一的 API，降低学习成本

### Q3: SQL 透传模式的优缺点？

**A:**
- **优点**: 
  - 性能更好，避免预处理开销
  - 适合复杂查询和大数据量场景
- **缺点**: 
  - 需要确保参数正确转义（组件已自动处理）
  - 调试时 SQL 日志中显示的是完整 SQL

### Q4: 如何处理特殊字符和 SQL 注入？

**A:** 组件会自动处理参数转义，包括单引号、反斜杠等特殊字符，无需手动处理。

```php
// 安全，组件会自动转义
GoodsDorisCatalog::query()->insert([
    'goods_code' => "12\\'3",  // 自动处理引号和反斜杠
]);
```

## 📝 最佳实践

1. **推荐使用 Catalog 模式** - 特别是需要访问多种数据源的场景
2. **合理配置连接池** - 根据业务负载调整 `min_connections` 和 `max_connections`
3. **启用 SQL 透传** - 对于查询密集型应用，建议开启 `passthrough_sql_select`
4. **使用 Model 而非原生 SQL** - 充分利用 ORM 的安全性和便利性
5. **监控慢查询** - Doris 和 Catalog 都应配置慢查询日志

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📄 License

MIT License

## 🔗 相关链接

- [Hyperf 官方文档](https://hyperf.wiki)
- [Apache Doris 官方文档](https://doris.apache.org)
- [Doris Multi-Catalog](https://doris.apache.org/docs/data-catalog/catalog)
- [Hyperf Database](https://github.com/hyperf/hyperf)

