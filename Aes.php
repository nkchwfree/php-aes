<?php

class Aes {
    private $key;
    private $iv;

    /**
     * 构造方法
     * @param string $key AES秘钥
     * @param string $iv  初始化向量
     */
    function __construct($key, $iv) {
        $this->key = $key;
        $this->iv = $iv;
    }

    /**
     * AES/CBC/PKCS5Padding 加密
     *
     * @param string $str 明文
     */
    public function encrypt($str) {
        return openssl_encrypt($str, 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * AES/CBC/PKCS5Padding 解密
     *
     * @param string $encryptedStr 密文
     */
    public function decrypt($encryptedStr) {
        return openssl_decrypt($encryptedStr, 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * 对文件内容进行加密，并将加密后数据写入新文件
     * @param  string $inFile  待加密的文件
     * @param  string $outFile 加密后的文件名
     * @return boolean         是否执行成功
     */
    public function encryptFile($inFile, $outFile) {
        $fin = fopen($inFile, "rb");
        $fout = fopen($outFile, "wb");
        $size = 1024*1024-1;

        if ($fin !== FALSE && $fout!==FALSE) {
            while(($con !== FALSE) && !feof($fin)) {
                $con = fread($fin, $size);
                fwrite($fout, $this->encrypt($con));
            }

            fclose($fin);
            fclose($fout);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 对文件内容进行加密，将解密后内容写入新文件
     * @param  string $inFile  输入文件的文件名
     * @param  string $outFile 输出文件的文件名
     * @return boolean         是否执行成功
     */
    public function decryptFile($inFile, $outFile) {
        $fin = fopen($inFile, "rb");
        $fout = fopen($outFile, "wb");

        //每次处理1M
        $size = 1024*1024;

        if ($fin !== FALSE && $fout!==FALSE) {
            while(($con !== FALSE) && !feof($fin)) {
                $con = fread($fin, $size);
                fwrite($fout, $this->decrypt($con));
            }

            fclose($fin);
            fclose($fout);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 对文件内容先进行压缩，再对压缩后的内容进行加密，将加密内容写入新文件
     * @param  string $inFile  输入文件的文件名
     * @param  string $outFile 输出文件的文件名
     * @return boolean         是否执行成功
     */
    function zipFile($inFile, $outFile) {
        $tempfile = tempnam(sys_get_temp_dir(), "PHP_TMP_");

        //首先压缩文件
        $gz = gzopen($tempfile,'w9');
        $fin = fopen($inFile, "rb");
        $size = 1024*1024/16;
        if ($fin !== FALSE && $ftemp!==FALSE) {
            while(($con !== FALSE) && !feof($fin)) {
                $con = fread($fin, $size);
                gzwrite($gz, $con);
                //echo $con;
            }

            fclose($fin);
            gzclose($gz);
        }

        register_shutdown_function("unlink", $tempfile);

        //加密文件
        return $this->encryptFile($tempfile, $outFile);
    }

    /**
     * 对文件内容先进行解密，再对解密后的内容进行解压缩，将解压后内容写入输出文件
     * @param  string $inFile  输入文件的文件名
     * @param  string $outFile 输出文件的文件名
     * @return boolean         是否执行成功
     */
    function unzipFile($inFile, $outFile) {
        $tempfile = tempnam(sys_get_temp_dir(), "PHP_TMP_");

        $this->decryptFile($inFile, $tempfile);

        $fin = gzopen($tempfile, "r");
        $fout = fopen($outFile, "wb");
        $size = 1024*1024/16;

        if ($fin !== FALSE && $fout!==FALSE) {
            //解压缩文件
            while(($con !== FALSE) && !gzeof($fin)) {
                $con = gzread($fin, $size);
                //echo $con;
                fwrite($fout, $con);
            }
            gzclose($fin);

            fclose($fout);
            return TRUE;
        }
        return FALSE;
    }
}



