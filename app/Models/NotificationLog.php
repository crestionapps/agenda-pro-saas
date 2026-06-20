<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationLog extends Model
{
    protected $fillable = ['tenant_id', 'type', 'recipient', 'status'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}
