<?php


namespace Jazor\Crypt;


class RC4
{
    private $_s_encrypt = null;
    private $_s_decrypt = null;
    private $_password = null;
    private $_encrypt_offset_i = 0;
    private $_decrypt_offset_i = 0;
    private $_encrypt_offset_j = 0;
    private $_decrypt_offset_j = 0;

	public function getPassword(){
		return $this->_password;
	}
    public function __construct($password)
    {
        $this->_password = array_values( unpack('C*', $password));
        $this->_s_encrypt = $this->init();
        $this->_s_decrypt = array_slice($this->_s_encrypt, 0, 256);
    }

    private function init()
    {
	    $p = $this->_password;
        $j = 0;
        $len = count($p);
        $k = [];
        $s = [];
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
            $k[$i] = $p[$i % $len];
        }
        for ($i = 0; $i < 256; $i++) {
            $j = (($j + $s[$i] + $k[$i]) & 0xff);
            $tmp = $s[$i];
            $s[$i] = $s[$j]; //交换s[i]和s[j]
            $s[$j] = $tmp;
        }
        return $s;
    }

    public function encrypt($buffer)
    {
	    $buffer = array_values(unpack('C*', $buffer));
        $i = 0; $j = 0; $t = 0;
        $end_offset = count($buffer);
        for ($k = 0; $k < $end_offset; $k++)
        {
            $this->_encrypt_offset_i = $i = ($this->_encrypt_offset_i + 1) & 0xff;

            $this->_encrypt_offset_j = $j = ($this->_encrypt_offset_j + $this->_s_encrypt[$i]) & 0xff;
            $tmp = $this->_s_encrypt[$i];
            $this->_s_encrypt[$i] = $this->_s_encrypt[$j];
            $this->_s_encrypt[$j] = $tmp;
            $t = ($this->_s_encrypt[$i] + $this->_s_encrypt[$j]) & 0xff;
            $buffer[$k] ^= $this->_s_encrypt[$t];
        }
        array_unshift($buffer, 'C*');

        return call_user_func_array('pack', $buffer);
    }

    public function decrypt($buffer)
    {
	    $buffer = array_values(unpack('C*', $buffer));
        $i = 0; $j = 0; $t = 0;
        $end_offset = count($buffer);
        for ($k = 0; $k < $end_offset; $k++)
        {
            $this->_decrypt_offset_i = $i = ($this->_decrypt_offset_i + 1) & 0xff;

            $this->_decrypt_offset_j = $j = ($this->_decrypt_offset_j + $this->_s_decrypt[$i]) & 0xff;
            $tmp = $this->_s_decrypt[$i];
            $this->_s_decrypt[$i] = $this->_s_decrypt[$j];
            $this->_s_decrypt[$j] = $tmp;
            $t = ($this->_s_decrypt[$i] + $this->_s_decrypt[$j]) & 0xff;
            $buffer[$k] ^= $this->_s_decrypt[$t];
        }
        array_unshift($buffer, 'C*');

        return call_user_func_array('pack', $buffer);
    }
}
