<?php
namespace App\Service;

use Illuminate\Support\Facades\Storage;
use App\Repository\UserRepository;
use App\Service\UsertService;
class UserService {
    function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function projects() {
        return $this->userRepository->projects();
    }
    public function create($data) {
        return $this->userRepository
             ->create($data);
    }

    public function getUser($id){
        return $this->userRepository
            ->getUser($id);
    }
    public function delete($id){
       $user = $this->userRepository->getUser($id);
       $user->delete();
    }
    public function edit($id,$data){
        return  $user = $this->userRepository
                ->modify($id,$data);
    }

}