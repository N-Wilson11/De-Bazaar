<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'advertisement_id',
    ];

    /**
     * Get the user that owns the favorite.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the advertisement that was favorited.
     */
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}
