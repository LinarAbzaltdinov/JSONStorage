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
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", fieldName="dataType")
 */
class JSONData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="json")
     */
    protected $data;

    /**
     *
     * @ORM\Column(type="datetime", options={"default"="NOW"})
     */
    protected $createdDate;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    protected $downloadAmount = 0;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $deleteAfterFirstAccess = false;

    /**
     * @return mixed
     */
    public function getDeleteAfterFirstAccess()
    {
        return $this->deleteAfterFirstAccess;
    }

    /**
     * @param mixed $deleteAfterFirstAccess
     */
    public function setDeleteAfterFirstAccess( $deleteAfterFirstAccess ): void
    {
        $this->deleteAfterFirstAccess = $deleteAfterFirstAccess;
    }

    /**
     * @return mixed
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId( $id )
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData( $data )
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate( $createdDate )
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return mixed
     */
    public function getDownloadAmount()
    {
        return $this->downloadAmount;
    }

    /**
     * @param mixed $downloadAmount
     */
    public function setDownloadAmount( $downloadAmount )
    {
        $this->downloadAmount = $downloadAmount;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl( $url )
    {
        $this->url = $url;
    }

    public function __construct( $data, $deleteAfterAccess )
    {
        $this->data        = $data;
        $this->url         = preg_replace( '/[^A-Za-z0-9\-]/', '',
            password_hash( $data, PASSWORD_DEFAULT ) ); // generates unique URL
        $this->createdDate = new \DateTime( "now" );
        $this->deleteAfterFirstAccess = $deleteAfterAccess;
    }

    public function incrementDownloadAmount()
    {
        $this->downloadAmount++;
    }
}