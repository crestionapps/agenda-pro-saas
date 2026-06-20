<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Review extends Model
{
    protected $fillable = ['tenant_id', 'customer_id', 'rating', 'comment', 'manager_reply'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
}
