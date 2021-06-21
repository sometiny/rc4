<?php


namespace Jazor\Crypt;

/***
 * Class RC4
 * @package Jazor\Crypt
 */
class RC4
{
    /***
     * @var array
     */
    private $_s_encrypt = null;

    /***
     * @var array
     */
    private $_s_decrypt = null;

    /***
     * @var string
     */
    private $_password = null;

    /***
     * @var int
     */
    private $_encrypt_offset_i = 0;

    /***
     * @var int
     */
    private $_decrypt_offset_i = 0;

    /***
     * @var int
     */
    private $_encrypt_offset_j = 0;

    /***
     * @var int
     */
    private $_decrypt_offset_j = 0;

    /***
     * @return null
     */
	public function getPassword() {
		return $this->_password;
	}

    /***
     * RC4 constructor. generate two password for encrypt and decrypt
     * @param string $password
     */
    public function __construct($password) {
        $this->_password = $password;
        $this->_s_encrypt = $this->init();
        $this->_s_decrypt = array_slice($this->_s_encrypt, 0, 256);
    }

    /***
     * generate password for encrypt and decrypt
     * @return array
     */
    private function init() {
	    $p = $this->_password;
        $j = 0;
        $len = strlen($p);
        $k = array();
        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
            $k[$i] = ord($p[$i % $len]);
        }
        for ($i = 0; $i < 256; $i++) {
            $j = (($j + $s[$i] + $k[$i]) & 0xff);
            $tmp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $tmp;
        }
        return $s;
    }

    /***
     * encrypt process is based stream, if you execute 'encrypt' twice with the same data in a same instance, the result may be different
     * @param string $buffer data need encrypt
     * @return string encrypted data
     */
    public function encrypt($buffer) {
        $end_offset = strlen($buffer);
        for ($k = 0; $k < $end_offset; $k++) {
            $this->_encrypt_offset_i = $i = ($this->_encrypt_offset_i + 1) & 0xff;

            $this->_encrypt_offset_j = $j = ($this->_encrypt_offset_j + $this->_s_encrypt[$i]) & 0xff;
            $tmp = $this->_s_encrypt[$i];
            $this->_s_encrypt[$i] = $this->_s_encrypt[$j];
            $this->_s_encrypt[$j] = $tmp;
            $t = ($this->_s_encrypt[$i] + $this->_s_encrypt[$j]) & 0xff;
            $buffer[$k] = chr(ord($buffer[$k]) ^ $this->_s_encrypt[$t]);
        }

        return $buffer;
    }

    /***
     * decrypt process is based stream, if you execute 'decrypt' twice with the same data in a same instance, the result may be different also
     * @param string $buffer data need decrypt
     * @return string decrypted data
     */
    public function decrypt($buffer) {
        $end_offset = strlen($buffer);
        for ($k = 0; $k < $end_offset; $k++) {
            $this->_decrypt_offset_i = $i = ($this->_decrypt_offset_i + 1) & 0xff;

            $this->_decrypt_offset_j = $j = ($this->_decrypt_offset_j + $this->_s_decrypt[$i]) & 0xff;
            $tmp = $this->_s_decrypt[$i];
            $this->_s_decrypt[$i] = $this->_s_decrypt[$j];
            $this->_s_decrypt[$j] = $tmp;
            $t = ($this->_s_decrypt[$i] + $this->_s_decrypt[$j]) & 0xff;
            $buffer[$k] = chr(ord($buffer[$k]) ^ $this->_s_decrypt[$t]);
        }

        return $buffer;
    }
}
