<?php
namespace FiremonPHP\Auth\PasswordHash;


class PasswordHash
{
    /**
     * @var string
     */
    private $_password;

    /**
     * Default config for this object
     * @var array
     */
    protected $_defaultConfig = [
        'hashType' => PASSWORD_DEFAULT,
        'hashOptions' => []
    ];

    public function __construct(string $password = null)
    {
        if ($password !== null) {
            $this->setPassword($password);
        }
    }

    /**
     * This function can receive this object for second parameter!
     * @param string $password
     * @param string $hashPassword
     * @return bool
     */
    public function check(string $password, string $hashPassword)
    {
        return password_verify($password, $hashPassword);
    }

    /**
     * Return password hashed
     * @return bool|string
     */
    public function __toString()
    {
        return $this->_password;
    }

    /**
     * @param string $password
     */
    private function setPassword(string $password)
    {
        if (preg_match('/^\$2y\$.{56}$/', $password)) {
            $this->_password = $password;
            return;
        }

        $this->_password = password_hash($password, $this->_defaultConfig['hashType'], $this->_defaultConfig['hashOptions']);
    }
}