<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Tenant extends Model
{
    protected $fillable = ['name', 'slug', 'type', 'description', 'address', 'phone', 'email', 'logo', 'banner', 'city', 'is_active'];

    public function users() { return $this->hasMany(User::class); }
    public function professionals() { return $this->hasMany(Professional::class); }
    public function services() { return $this->hasMany(Service::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function businessHours() { return $this->hasMany(BusinessHour::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function subscription() { return $this->hasOne(TenantSubscription::class)->where('is_active', true); }
}
