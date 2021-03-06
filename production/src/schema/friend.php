<?php
// src/friends.php


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="friends")
 * @ORM\Entity 
 **/
class Friend
{
    /** 
     * @var string
     *
     * @ORM\Column(name="username", type="string", precision=0, scale=0, nullable=false, unique=false)
      * @ORM\Id
      */
    protected $username;
    /**
     * @var integer
     *
     * @ORM\Column(name="permission", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $permission;

    function __construct($username, $permission){
        $this->username= $username;
        $this->permission = $permission;
    }

    public function getName()
    {
        return $this->username;
    }

    public function setName($username)
    {
        $this->username = $username;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function setPermission($permission){
        $this->permission = $permission;

    }


}
