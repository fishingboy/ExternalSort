# ExternalSort - PHP 外部排序法

![Tests](https://github.com/fishingboy/ExternalSort/actions/workflows/ci.yml/badge.svg)

**繁體中文** | [English](README.md)

很久以前寫的 PHP 外部排序法。
主要是因為如果要排序的資料非常大，大到在記憶體內無法排序時，
可以使用這個外部排序來排，並可以指定一個數量的上限，超過上限才會用外部排序的機制；
在上限內的話則會在記憶體內做完排序就把結果輸出出去。

拉出來放一個專案是為了可以貢獻到 Packagist，讓有需要的人可以使用 Composer 進行安裝。

## 安裝

```json
{
    "require": {
        "fishingboy/external_sort": "dev-master"
    }
}
```

## 使用方式

```php
use fishingboy\external_sort\External_sort;

$sorter = new External_sort([
    'block_size'  => 1000,       // 記憶體內最多暫存幾筆，超過即寫入暫存檔
    'result_file' => 'out.txt',  // 最終排序結果輸出路徑
    'data_type'   => 'number',   // 'number'（整數）或 'text'（字串）
]);

$sorter->add_data($value);  // 可傳入單筆值或陣列，重複呼叫
$sorter->create_result();   // 執行 k-way merge，結果寫入 result_file
```
