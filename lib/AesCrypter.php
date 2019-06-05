<?php

class AesCrypter {

    private $key = '';
    private $algorithm = MCRYPT_RIJNDAEL_128;
    private $mode = MCRYPT_MODE_CBC;

    public function __construct($config = array()) {
        if(isset($config['key']))
		{
			$this->key = $config['key'];
			$this->key = hash('sha256', $this->key, true);
		}
		if (isset($config['algorithm'])) {
            $this->algorithm = $config['algorithm'];
        }
		if (isset($config['mode'])) {
            $this->mode = $config['mode'];
        } 
    }

    public function encrypt($orig_data) {
        $encrypter = mcrypt_module_open($this->algorithm, '',
            $this->mode, '');
        $orig_data = $this->pkcs7padding(
            $orig_data, mcrypt_enc_get_block_size($encrypter)
        );
        mcrypt_generic_init($encrypter, $this->key, substr($this->key, 0, 16));
        $ciphertext = mcrypt_generic($encrypter, $orig_data);
        mcrypt_generic_deinit($encrypter);
        mcrypt_module_close($encrypter);
        return base64_encode($ciphertext);
    }

    public function decrypt($ciphertext) {
        $encrypter = mcrypt_module_open($this->algorithm, '',
            $this->mode, '');
        $ciphertext = base64_decode($ciphertext);
        mcrypt_generic_init($encrypter, $this->key, substr($this->key, 0, 16));
        $orig_data = mdecrypt_generic($encrypter, $ciphertext);
        mcrypt_generic_deinit($encrypter);
        mcrypt_module_close($encrypter);
        return $this->pkcs7unPadding($orig_data);
    }

    public function pkcs7padding($data, $blocksize) {
        $padding = $blocksize - strlen($data) % $blocksize;
        $padding_text = str_repeat(chr($padding), $padding);
        return $data . $padding_text;
    }

    public function pkcs7unPadding($data) {
        $length = strlen($data);
        $unpadding = ord($data[$length - 1]);
        return substr($data, 0, $length - $unpadding);
    }

}