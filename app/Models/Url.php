<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    protected $fillable = [
        'user_id',
        'original_url',
        'short_url',
        'visit_count'
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visit_count' => 'integer',
    ];

    /**
     * The default attributes for the model.
     *
     * @var array
     */
    protected $attributes = [
        'visit_count' => 0,
    ];

    /**
     * Define an inverse one-to-many relationship with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
