<?php
$arr = array("OSAKA", "Xianeibu");
echo array_search('Xianeibu', $arr);
echo $arr[array_search('Xianeibu', $arr)+1];
?>