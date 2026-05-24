<?php

namespace fishingboy\external_sort\Tests;

use fishingboy\external_sort\External_sort;
use PHPUnit\Framework\TestCase;

class ExternalSortTest extends TestCase
{
    private string $resultFile;

    protected function setUp(): void
    {
        $this->resultFile = sys_get_temp_dir() . '/external_sort_test_' . uniqid() . '.txt';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->resultFile)) {
            unlink($this->resultFile);
        }
    }

    private function readNumbers(): array
    {
        $lines = file($this->resultFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_map('intval', $lines ?: []);
    }

    private function readLines(): array
    {
        return file($this->resultFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    }

    // 資料量在 block_size 內，走記憶體排序路徑
    public function testSortNumbersInMemory(): void
    {
        $sorter = new External_sort([
            'block_size'  => 100,
            'result_file' => $this->resultFile,
            'data_type'   => 'number',
        ]);

        $sorter->add_data([5, 3, 8, 1, 9, 2]);
        $sorter->create_result();

        $this->assertSame([1, 2, 3, 5, 8, 9], $this->readNumbers());
    }

    // 資料量超過 block_size，觸發外部排序合併路徑
    public function testSortNumbersExternalMerge(): void
    {
        $sorter = new External_sort([
            'block_size'  => 3,
            'result_file' => $this->resultFile,
            'data_type'   => 'number',
        ]);

        $sorter->add_data([5, 3, 8]); // 達到 block_size，寫入第一個暫存檔
        $sorter->add_data([1, 9, 2]); // 達到 block_size，寫入第二個暫存檔
        $sorter->create_result();

        $this->assertSame([1, 2, 3, 5, 8, 9], $this->readNumbers());
    }

    // 文字型態，記憶體排序路徑
    public function testSortTextInMemory(): void
    {
        $sorter = new External_sort([
            'block_size'  => 100,
            'result_file' => $this->resultFile,
            'data_type'   => 'text',
        ]);

        $sorter->add_data(['banana', 'apple', 'cherry']);
        $sorter->create_result();

        $this->assertSame(['apple', 'banana', 'cherry'], $this->readLines());
    }

    // 文字型態，外部排序合併路徑
    public function testSortTextExternalMerge(): void
    {
        $sorter = new External_sort([
            'block_size'  => 2,
            'result_file' => $this->resultFile,
            'data_type'   => 'text',
        ]);

        $sorter->add_data(['banana', 'apple']);
        $sorter->add_data(['cherry', 'avocado']);
        $sorter->create_result();

        $this->assertSame(['apple', 'avocado', 'banana', 'cherry'], $this->readLines());
    }

    // 逐筆呼叫 add_data（非陣列）
    public function testAddSingleValues(): void
    {
        $sorter = new External_sort([
            'block_size'  => 100,
            'result_file' => $this->resultFile,
            'data_type'   => 'number',
        ]);

        $sorter->add_data(3);
        $sorter->add_data(1);
        $sorter->add_data(2);
        $sorter->create_result();

        $this->assertSame([1, 2, 3], $this->readNumbers());
    }

    // 跨多個暫存檔的大量資料合併
    public function testLargeDatasetMultipleBlocks(): void
    {
        $sorter = new External_sort([
            'block_size'  => 10,
            'result_file' => $this->resultFile,
            'data_type'   => 'number',
        ]);

        $data = range(50, 1);
        $sorter->add_data($data);
        $sorter->create_result();

        $this->assertSame(range(1, 50), $this->readNumbers());
    }
}
