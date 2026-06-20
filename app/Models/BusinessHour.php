<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BusinessHour extends Model
{
    protected $fillable = ['tenant_id', 'professional_id', 'day_of_week', 'open_time', 'close_time', 'is_open'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function professional() { return $this->belongsTo(Professional::class); }
}
