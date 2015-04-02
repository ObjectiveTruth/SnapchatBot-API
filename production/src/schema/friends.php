<?php
// src/friends.php
/**
 * @Entity @Table(name="friends")
 **/
class Friend
{
    /** @username @Id @Column(type="string") **/
    protected $username;
    /** @permission @Column(type="integer") **/
    protected $permission;

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
