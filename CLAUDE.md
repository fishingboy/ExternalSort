# CLAUDE.md

本文件提供 Claude Code (claude.ai/code) 在此專案中運作的指引。

## 專案概述

單一類別的 PHP 外部排序函式庫，用於處理記憶體無法容納的大型資料集。已發布至 Packagist，套件名稱為 `fishingboy/external_sort`。

## 安裝與使用

透過 Composer 安裝：
```json
{ "require": { "fishingboy/external_sort": "dev-master" } }
```

```php
use fishingboy\external_sort\External_sort;

$sorter = new External_sort([
    'block_size'  => 1000,       // 寫入暫存檔前，記憶體內最多暫存幾筆資料
    'result_file' => 'out.txt',  // 最終排序結果的輸出路徑
    'data_type'   => 'number',   // 'number'（轉 int）或 'text'（trim 字串）
    'unique'      => false,      // 已宣告但尚未實作去重功能
]);

$sorter->add_data($value);   // 重複呼叫新增資料；達到 block_size 時自動寫入暫存檔
$sorter->create_result();    // 將所有暫存檔進行 k-way merge，輸出至 result_file
```

## 演算法

1. **緩衝階段** — `add_data()` 將資料累積至 `$tmp_array`，當筆數達到 `block_size` 時，`write_file()` 對緩衝區排序並寫入 PHP `tmpfile()` 暫存檔，檔案 handle 儲存於 `$file_array`。
2. **合併階段** — `create_result()` 將所有暫存檔 seek 回開頭，反覆從所有開啟的檔案 handle 中找出最小值（k-way merge），依序寫入 `$result_file`。

## 已知問題

- **`create_result()` 有硬編碼的 `$k > 10000` 中斷保護** — 當合併迭代次數超過 10,000 次時，輸出會被靜默截斷。資料量大或區塊多時必然觸發此限制。
- **`$unique` 設定從未被套用** — 旗標有儲存，但去重邏輯根本不存在。
- **`write_file($data)` 忽略傳入的參數** — 實際上永遠對 `$this->tmp_array` 排序，`$data` 參數完全無效。
- **`get_row()`** 參照了從未定義的 `$this->customized`、`$this->row_curr`、`$this->row_end`、`$this->records`，是舊版本留下的廢棄壞程式碼。
- **`get_result()`** 是除錯用的輔助方法，會直接 echo 含 HTML `<br>` 標籤的原始內容，不適合在正式環境使用。

## 測試

使用 PHPUnit 10（需要 PHP 8.1+）：

```bash
composer install
./vendor/bin/phpunit tests/
```

執行單一測試案例：

```bash
./vendor/bin/phpunit tests/ExternalSortTest.php --filter testSortNumbersExternalMerge
```

## Git 提交規範

commit 訊息一律使用**中文**撰寫。
