<?php

use Illuminate\Support\Facades\Broadcast;



Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('imports', function ($user) {
    return $user->isAdmin();
});
Broadcast::channel('return-imports', function ($user) {
    if ($user->isAdmin() || $user->isEmployee()) {
        return true;
    }
});
// Broadcast::channel('user-login', function ($user) {
//     return true;
// });
