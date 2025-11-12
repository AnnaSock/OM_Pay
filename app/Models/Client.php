<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends User
{
     protected $table = 'users';

     /**
      * Get the comptes for the client.
      */
}
