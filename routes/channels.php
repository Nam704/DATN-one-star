<?php

use Illuminate\Support\Facades\Broadcast;



Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private-notifications', function ($user) {
    if ($user->isAdmin() || $user->isEmployee()) {
        return true;
    }
});
Broadcast::channel('public', function ($user) {
    return true;
});
Broadcast::channel('admin', function ($user) {
    if ($user->isAdmin()) {
        return true;
    }
});
Broadcast::channel('employee', function ($user) {
    if ($user->isEmployee()) {
        return true;
    }
});
