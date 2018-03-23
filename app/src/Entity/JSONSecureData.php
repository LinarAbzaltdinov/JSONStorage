<?php
/**
 * Created by PhpStorm.
 * User: linarkou
 * Date: 22.03.2018
 * Time: 13:42
 */

namespace App\Entity;

use App\Service\PasswordGenerator;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class JSONSecureData extends JSONData
{
    private const URL_PREFIX = "private/";
    private const PASS_LENGTH = 8;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = password_hash($password);
    }

    public function getUrl()
    {
        return self::URL_PREFIX . parent::getUrl();
    }

}