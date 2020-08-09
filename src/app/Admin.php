<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', // table columns with admin details
    ];

    # Inverse of the relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}