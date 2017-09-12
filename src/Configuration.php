<?php
namespace FiremonPHP\Auth;


use FiremonPHP\Manager\Manager;

class Configuration
{
    /**
     * @var Manager
     */
    private static $_manager;

    /**
     * Security salt for authorized actions
     * @var string
     */
    private static $_securitySalt;

    /**
     * @var string
     */
    private static $_collectionName = 'authentication';
    /**
     * milliseconds
     * @var int
     */
    private static $expireTime = 3600;
    /**
     * milliseconds
     * @var int
     */
    private static $rememberTime = 3600;

    /**
     * For security reasons is strong recommends change it
     * you can change it by config function
     * @var array
     */
    private static $_fields = [
        'name' => 'name',
        'email' => 'email',
        'username' => 'username',
        'password' => 'password',
        'remember_token' => 'remember_token',
        'expire' => 'expire',
        'logged' => 'logged',
        'created' => 'created',
        'modified' => 'modified',
        'active' => 'active',
        'roles' => 'roles',
        'personal' => 'personal'
    ];

    /**
     * This fields is avaliable on token payload
     * for configure it use setTokenFields()
     * @var array
     */
    private static $_tokenFields = [
      'username',
      'roles'
    ];

    /**
     * @return array
     */
    public static function getFields()
    {
        return self::$_fields;
    }

    /**
     * @param Manager $manager
     */
    public static function setManager(Manager $manager)
    {
        self::$_manager = $manager;
    }

    /**
     * @return Manager
     * @throws \ErrorException
     */
    public static function getManger()
    {
        if (self::$_manager instanceof Manager) {
            return self::$_manager;
        }

        throw new \ErrorException('Authentication need configure manager, see \'setManger()\' function.');
    }

    /**
     * @param int $milliseconds
     */
    public static function setRememberTime(int $milliseconds)
    {
        self::$rememberTime = $milliseconds;
    }

    /**
     * @return int
     */
    public static function getRememberTime()
    {
        return self::$rememberTime;
    }

    /**
     * @param string $name
     */
    public static function setCollectionName(string $name)
    {
        self::$_collectionName = $name;
    }

    /**
     * @return string
     */
    public static function getCollectionName()
    {
        return self::$_collectionName;
    }

    /**
     * @param int $milliseconds
     */
    public static function setExpireTime(int $milliseconds)
    {
        self::$expireTime = $milliseconds;
    }

    public static function getExpireTime()
    {
        return self::$expireTime;
    }

    /**
     * @param string $securitySalt
     */
    public static function setSecuritySalt(string $securitySalt)
    {
        if (strlen($securitySalt) >= 64) {
            self::$_securitySalt = $securitySalt;
            return;
        }

        throw new \InvalidArgumentException('The security key is invalid, please enter more than 64 characters!');
    }

    /**
     * @return string
     * @throws \ErrorException
     */
    public static function getSecuritySalt()
    {
        if (self::$_securitySalt !== null) {
            return self::$_securitySalt;
        }

        throw new \ErrorException('Authentication need configuration: security_salt, see \'setSecuritySalt\' function!');
    }

    /**
     * @param array $fields
     */
    public static function setTokenFields(array $fields)
    {
        self::$_tokenFields = array_merge(self::$_tokenFields, $fields);
    }

    /**
     * @return array
     */
    public static function getTokenFields()
    {
        return self::$_tokenFields;
    }
}