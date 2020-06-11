<?php

require_once("./Aes.php");



$iv = "0123456789876543";
$key = "12345678abcdefgh";

$aes = new Aes($key, $iv);

// //对字符串进行加密
// $text = $aes->encrypt("1234567890abcdef");
// echo bin2hex($text)."\n";

// echo bin2hex($aes->encrypt("1234567890abcdef1"))."\n";
// echo bin2hex($aes->encrypt("1234567890abcdef1234567890abcdef"))."\n";
// //对字符串进行解密
// echo $aes->decrypt($text);
//
$text = $aes->encrypt("12345678901234567890");
//echo base64_encode($text)."\n";

//"RGJ04WXGpbwHels9711PLiUgjTXGN8wODGPXlSXddvQ="
//echo $aes->decrypt(base64_decode("IlnmB2UzD84R5Mx4A+BPUMjHFGWxsDlAhrzuzC6nY/M="));

//echo base64_decode("YWJj");

// //对文件进行压缩+加密
// $aes->zipfile("./in.txt", "zip.data");

// //对文件进行解密+解压
// $aes->unzipfile("./zip.data", "out.txt");

// //对文件进行加密
$aes->encryptFile("./in.txt", "encrypt.data");

// //对文件进行解密
$aes->decryptFile("./encrypt.data", "out2.txt");


//测试Java加密的内容
$java_path = dirname(dirname(dirname(__FILE__)));
$text = file_get_contents($java_path."/java/1.dat");
//echo $text;
echo $aes->decrypt($text);

//解压Java加密的文件
$aes->decryptFile($java_path."/java/2.dat", "java-out2.txt");