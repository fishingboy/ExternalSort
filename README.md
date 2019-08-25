# ExternalSort - PHP 外部排序法

  很久以前寫的 PHP 外部排序法
  主要是因為如果要排序的資料非常大，大到在記憶體內無法排序時
  可以使用這個外部排序來排，並可以指定一個數量的上限，超過上限才會用外部排序的機制
  在上限內的話則會在記憶體內做完排序就把結果輸出出去
  
  拉出來放一個專案是為了可以貢獻到 Packagist 
  讓有需要的人可以使用 Composer 進行安裝

  安裝請下
    
  `  
    {
        "require": {
            "fishingboy/external_sort": "dev-master"
        }
    }
  `  


  載入請用

  `
  use fishingboy\external_sort\External_sort;
  `
