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

Broadcast::channel('App.Models.User', function ($user) {
  return true;
});
Broadcast::channel('App.Models.Company', function ($company) {
    return true;
});

Broadcast::channel('App.Models.Manager', function ($user) {
   return true;
});
