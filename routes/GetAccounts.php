<?php

namespace Routes\GetAccounts;

use App\Account;
use function App\Renderer\render;

function routeGetAccounts()
{
    $path1 = __DIR__ . "/../files/array-1.json";
    $path2 = __DIR__ . "/../files/array-2.json";

    if (!file_exists($path1) && !file_exists($path2)) {
        $fillRand = function ($count) {
            $result = [];
            for ($i = 0; $i < $count; $i++) {
                $result[] = rand(0, 5685);
            }
            return $result;
        };
        $data = $fillRand(100);
        file_put_contents($path1, json_encode($data));

        $modifiedData = array_map(function ($key) use ($data) {
            return ($key % 2 != 0 || $key % 4 == 0) ? ($data[$key] - 23) * 2 : $data[$key];
        }, array_keys($data));
        $filtredData = array_filter($modifiedData, function ($value) {
            return $value >= 2450 && $value < 4031;
        });
        file_put_contents($path2, json_encode(array_values($filtredData)));
    }

    $newData1 = json_decode(file_get_contents($path1), true);
    $newData2 = json_decode(file_get_contents($path2), true);

    if (Account::count() === 0) {
        foreach ($newData1 as $key => $value) {
            $account = new Account();
            $account->name = 'Василий' . $key;
            $account->surname = 'Пупкин' . ($key + 6);
            $account->patronymic = 'Александрович' . $value;
            $account->birthday = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - $key, date('Y') - 21));
            $account->save();
            $account->account = $account->id;
            $account->amount = $value + $newData2[4];
            $account->save();
        }
    }

    $limit = 8;
    $maxPage = ceil(Account::count() / $limit);
    $page = array_key_exists('page', $_GET) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $accounts = Account::getAccounts($limit, $offset);
    $params = [
        'accounts' => $accounts,
        'page' => $page,
        'maxPage' => $maxPage
    ];

    $template = __DIR__ . "/../templates/accounts.phtml";
    echo render($template, $params);
    return;
}