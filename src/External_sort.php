<?php

namespace fishingboy\external_sort;

/**
 * 外部排序
 *
 * @author Leo Kuo <et282523@hotmail.com>
 */
class External_sort
{
    private int    $block_size  = 1000;
    private string $result_file = 'result.txt';
    private string $data_type   = 'number';

    private array $tmp_array  = [];
    private array $file_array = [];

    public function __construct($config = NULL)
    {
        if (isset($config['block_size']))  $this->block_size  = $config['block_size'];
        if (isset($config['result_file'])) $this->result_file = $config['result_file'];
        if (isset($config['data_type']))   $this->data_type   = $config['data_type'];
    }

    public function add_data($data): void
    {
        if (is_array($data)) {
            $this->tmp_array = array_merge($this->tmp_array, $data);
        } else {
            $this->tmp_array[] = $data;
        }

        if (count($this->tmp_array) >= $this->block_size) {
            $this->write_file($this->tmp_array);
        }
    }

    private function write_file(array $data): void
    {
        $this->file_array[] = tmpfile();
        $fp = $this->file_array[count($this->file_array) - 1];

        sort($data);

        foreach ($data as $value) {
            fwrite($fp, $value . "\n");
        }

        $this->tmp_array = [];
    }

    public function create_result(): void
    {
        if (count($this->tmp_array) > 0) {
            $this->write_file($this->tmp_array);
        }

        $fp_result = fopen($this->result_file, 'w');

        foreach ($this->file_array as $fp) {
            fseek($fp, 0);
        }

        $tmp = [];
        while (count($this->file_array)) {
            unset($min, $min_i);

            foreach ($this->file_array as $i => $fp) {
                if (!isset($tmp[$i])) {
                    $t = fgets($fp);
                    if ($t === false) {
                        unset($this->file_array[$i]);
                        continue;
                    }
                    $tmp[$i] = ($this->data_type === 'number') ? intval($t) : trim($t);
                }

                if (!isset($min) || $min > $tmp[$i]) {
                    $min   = $tmp[$i];
                    $min_i = $i;
                }
            }

            if (count($this->file_array)) {
                fwrite($fp_result, $min . "\n");
                unset($tmp[$min_i]);
            }
        }

        fclose($fp_result);
    }

    public function get_result(): void
    {
        if (count($this->tmp_array) > 0) {
            $this->write_file($this->tmp_array);
        }

        foreach ($this->file_array as $fp) {
            fseek($fp, 0);
            while (($line = fgets($fp)) !== false) {
                echo "$line <br>";
            }
            echo "=========================<br>";
        }
    }
}
