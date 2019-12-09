<?php

namespace Routes\PostAccounts;

use App\Account;

function routePostAccounts()
{
    $data = array_filter($_POST, function ($value) {
        return !empty($value);
    });
    
    $account = new Account();
    foreach($data as $key => $value) {
        $account->$key = $value;
    }
    $account->save();
    return;
}
