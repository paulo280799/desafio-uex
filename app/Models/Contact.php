<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([UserScope::class])]
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'phone',
        'number',
        'address',
        'cep',
        'district',
        'city',
        'state',
        'country',
        'complement',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {

            if (auth()->user()) {
                $model->user_id = auth()->user()->id;
            }
        });
    }
}
