<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Appointment extends Model
{
    protected $fillable = ['tenant_id', 'service_id', 'professional_id', 'customer_id', 'date', 'start_time', 'end_time', 'status', 'notes'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function professional() { return $this->belongsTo(Professional::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }

    public static function isSlotAvailable($professionalId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = self::where('professional_id', $professionalId)
            ->where('date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            });
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return !$query->exists();
    }
}
