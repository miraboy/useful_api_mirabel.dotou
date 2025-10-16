<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', 
        'balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }   
     /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = ['balance' => 'float'];
}