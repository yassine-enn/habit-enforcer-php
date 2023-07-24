<?php


class User
{
    public $id;
    public $username;
    public $status;
    public $membership;


    public static function getByUserName($username)
    {
        $sql = "SELECT * FROM users where username = '$username'";

        $result = ConfigClass::getConnection()->query($sql);

        $user = new User();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $user->id = $row['id'];
            $user->username = $row['username'];
        }

        return $user;
    }

    public static function getById($id)
    {
        $sql = "SELECT * FROM users where id = '$id'";

        $result = ConfigClass::getConnection()->query($sql);

        $user = new User();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $user->id = $row['id'];
            $user->username = $row['username'];
        }

        return $user;
    }


    public static function hasGroup($id)
    {
        $sql = "SELECT * FROM group_members where member_id = '$id'";

        $result = ConfigClass::getConnection()->query($sql);


        if ($result->num_rows > 0) {
            return true;
        }
        
        return false;
    }


    public function userGroups(){
        $sql = "SELECT * FROM group_members where member_id = '$this->id'";
        $result = ConfigClass::getConnection()->query($sql);

        $groups = [];

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $groups[] = Group::getById($row['group_id']);
            }
        }

        return $groups;
    }
}
