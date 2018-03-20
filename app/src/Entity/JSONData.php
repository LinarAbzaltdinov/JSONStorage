<?php
/**
 * Created by PhpStorm.
 * User: elama
 * Date: 20.03.18
 * Time: 11:11
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JSONDataRepository")
 */
class JSONData extends AbstractData
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function convertToXML() {
        return null;
    }

    public function getDataType()
    {
        return parent::JSON_TYPE;
    }
}