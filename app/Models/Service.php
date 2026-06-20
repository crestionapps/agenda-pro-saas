<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'price', 'duration', 'category', 'is_active'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function professionals() { return $this->belongsToMany(Professional::class, 'service_professional'); }
    public function appointments() { return $this->hasMany(Appointment::class); }
}
