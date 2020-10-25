<?php
namespace App\Repository;

use Hash;
use App\Model\User;
class UserRepository{

    public function create($data) {
       $data['password'] =  Hash::make($data['password']);
       return User::create($data);
    }

    public function modify($id, $data) {
        $project = User::where("id", $id)
         ->update($data);
        return $project;
    }

    public function delete($id) {
        return User::destroy($id);
    }

    public function getUser($id){
        return User::find($id);
    }
}