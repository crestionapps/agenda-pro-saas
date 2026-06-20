<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    protected $fillable = ['tenant_id', 'name', 'email', 'password', 'phone', 'avatar', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function appointments() { return $this->hasMany(Appointment::class, 'customer_id'); }
    public function reviews() { return $this->hasMany(Review::class, 'customer_id'); }
    public function isSuperAdmin() { return $this->role === 'super_admin'; }
    public function isAdmin() { return $this->role === 'admin'; }
    public function isManager() { return $this->role === 'manager'; }
    public function isCustomer() { return $this->role === 'customer'; }
}
