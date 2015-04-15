<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity 
 **/
class User
{
    /** 
     * @var string
     *
     * @ORM\Column(name="username", type="string", precision=0, scale=0, nullable=false, unique=false)
      * @ORM\Id
      */
    protected $username;

    /** 
     * @var string
     *
     * @ORM\Column(name="password", type="string", precision=0, scale=0, nullable=false, unique=false)
      */
    protected $password;

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

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function setPermission($permission){
        $this->permission = $permission;

    }


}
