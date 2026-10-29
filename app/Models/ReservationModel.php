<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table            = 'reservations';
    protected $primaryKey       = 'reservation_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'equipment_id',
        'reservation_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'user_id'          => 'required|integer',
        'equipment_id'     => 'required|integer',
        'reservation_date' => 'required|valid_date[Y-m-d]',
        'start_time'       => 'required',
        'end_time'         => 'required',
        'purpose'          => 'required|max_length[255]',
        'status'           => 'permit_empty|in_list[PENDING,APPROVED,CHECKED_OUT,RETURNED,REJECTED,CANCELLED]',
    ];

    /**
     * Check whether an equipment item is available for a given date/time range.
     *
     * Returns true if no overlapping PENDING or APPROVED reservations exist.
     * Overlap logic: two time ranges overlap when start_a < end_b AND start_b < end_a.
     *
     * @param int    $equipmentId
     * @param string $date      Format Y-m-d
     * @param string $startTime Format H:i or H:i:s
     * @param string $endTime   Format H:i or H:i:s
     * @param int|null $excludeReservationId  Ignore this reservation (for edits)
     * @return bool
     */
    public function checkAvailability(int $equipmentId, string $date, string $startTime, string $endTime, ?int $excludeReservationId = null): bool
    {
        $builder = $this->where('equipment_id', $equipmentId)
                        ->where('reservation_date', $date)
                        ->whereIn('status', ['PENDING', 'APPROVED'])
                        ->where('start_time <', $endTime)
                        ->where('end_time >', $startTime);

        if ($excludeReservationId !== null) {
            $builder->where('reservation_id !=', $excludeReservationId);
        }

        return $builder->countAllResults() === 0;
    }
}
