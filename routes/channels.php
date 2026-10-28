<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Authorized for admin, staff, or customers tracking the artist's queue
Broadcast::channel('artist-queue.{artistId}', function ($user, $artistId) {
    return true;
});

// Broadcast artist live status updates
Broadcast::channel('artist-status', function ($user) {
    return true;
});

// Authorized only for Admin and Staff members
Broadcast::channel('admin-queue', function ($user) {
    return $user && ($user->isAdmin() || $user->isStaff());
});

// User specific queue notifications
Broadcast::channel('user-queue.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

