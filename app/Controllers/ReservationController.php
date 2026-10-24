<?php

namespace App\Controllers;

use App\Models\EquipmentModel;
use App\Models\ReservationModel;

class ReservationController extends BaseController
{
    protected $helpers = ['form', 'url'];

    // List all reservations for the authenticated user
    public function index()
    {
        $userId = session()->get('user_id');

        $reservationModel = new ReservationModel();

        $reservations = $reservationModel
            ->select('reservations.*, equipment.equipment_name, equipment.equipment_code')
            ->join('equipment', 'equipment.equipment_id = reservations.equipment_id', 'left')
            ->where('reservations.user_id', $userId)
            ->orderBy('reservations.reservation_id', 'DESC')
            ->findAll();

        return view('reservations/index', [
            'reservations' => $reservations,
        ]);
    }

    // Display reservation booking form for a specific equipment item
    public function create($equipmentId = null)
    {
        if (! $equipmentId) {
            return redirect()->to('equipment')->with('error', 'Please select an equipment item to reserve.');
        }

        $equipmentModel = new EquipmentModel();
        $equipment      = $equipmentModel->find($equipmentId);

        if (! $equipment) {
            return redirect()->to('equipment')->with('error', 'Equipment item not found.');
        }

        if (strtoupper($equipment['status'] ?? '') !== 'AVAILABLE') {
            return redirect()->to('equipment')->with('error', 'This equipment is currently not available for reservation.');
        }

        return view('reservations/create', [
            'equipment' => $equipment,
        ]);
    }

    // Process and validate reservation submission
    public function store()
    {
        $rules = [
            'equipment_id'     => 'required|is_not_unique[equipment.equipment_id]',
            'reservation_date' => 'required|valid_date[Y-m-d]',
            'start_time'       => 'required',
            'end_time'         => 'required',
            'purpose'          => 'required|max_length[255]',
        ];

        $messages = [
            'equipment_id' => [
                'is_not_unique' => 'The selected equipment item does not exist.',
            ],
            'reservation_date' => [
                'valid_date' => 'Please provide a valid reservation date.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $equipmentId = (int) $this->request->getPost('equipment_id');
        $date        = (string) $this->request->getPost('reservation_date');
        $startTime   = (string) $this->request->getPost('start_time');
        $endTime     = (string) $this->request->getPost('end_time');
        $purpose     = (string) $this->request->getPost('purpose');

        // Ensure date is not in the past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Reservation date cannot be in the past.');
        }

        // Ensure start time is chronologically before end time
        if (strtotime($startTime) >= strtotime($endTime)) {
            return redirect()->back()->withInput()->with('error', 'Start time must be earlier than end time.');
        }

        // Check availability to prevent double-booking
        $reservationModel = new ReservationModel();
        $isAvailable      = $reservationModel->checkAvailability($equipmentId, $date, $startTime, $endTime);

        if (! $isAvailable) {
            return redirect()->back()->withInput()->with('error', 'The selected time slot conflicts with an existing reservation. Please choose another date or time.');
        }

        $saved = $reservationModel->insert([
            'user_id'          => (int) session()->get('user_id'),
            'equipment_id'     => $equipmentId,
            'reservation_date' => $date,
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'purpose'          => trim($purpose),
            'status'           => 'PENDING',
        ]);

        if (! $saved) {
            return redirect()->back()->withInput()->with('error', 'Failed to create reservation. Please try again.');
        }

        return redirect()->to('reservations')->with('success', 'Your reservation request has been submitted successfully.');
    }
}
