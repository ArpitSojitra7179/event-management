<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\softDeletes;
use Laravel\Passport\HasApiTokens;
use Laravel\Cashier\Billable;
use App\Enums\UserStatus;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
        'phone',
        'country_name',
        'country_code',
        'region_name',
        'region_code',
        'status',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatus::class,
    ];


    public function metas() {
        return $this->hasMany(UserMeta::class);
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function transactions() {
        return $this->hasManyThrough(Transaction::class, Ticket::class);
    }
    
    public function supports() {
        return $this->hasMany(Support::class);
    }

    protected function name(): Attribute {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
            get: fn ($value) => ucfirst($value),
        );
    }

    protected function email() : Attribute {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
        );
    }

    public function routeNotificationForSlack($notification)
    {
        $meta = $this->metas()->where('key', 'slack_notification_key')->first();
        $value = json_encode($meta->value);
        $url = json_decode($value);

        return $url->webhook_url;
    }
}
