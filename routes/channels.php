<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('contact.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('contact', function () {
    return Auth::check(); // Seuls les utilisateurs authentifiés peuvent écouter
});
