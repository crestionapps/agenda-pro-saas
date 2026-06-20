<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Professional extends Model
{
    protected $fillable = ['tenant_id', 'name', 'email', 'phone', 'photo', 'bio', 'is_active'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function services() { return $this->belongsToMany(Service::class, 'service_professional'); }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function businessHours() { return $this->hasMany(BusinessHour::class); }
}
