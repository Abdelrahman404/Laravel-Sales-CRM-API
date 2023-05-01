<?php

namespace App\Helpers;

use App\Models\Client;
use App\Models\User;

class AuthorizationHelper{   


    public function clientAssignedToUser($clientId, $userId){

        $client = Client::find($clientId);

        return $client->responsible_seller_id == $userId;

    }

    public function userIsAdmin($userId){

        $user = User::find($userId);
        
        return $user->type == 'admin';
        
    }
}
