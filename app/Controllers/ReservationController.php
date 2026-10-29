<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\EquipmentModel;
use App\Models\ReservationModel;

class ReservationController extends BaseController
{
    protected $helpers = ['form', 'url', 'equipment'];

    public function index()
    {
        $session = session();
        $userId = (int) $session->get('user_id');
        $departmentList = (new DepartmentModel())->orderBy('department_name', 'ASC')->findAll();
        $departmentIds = array_map('intval', array_column($departmentList, 'department_id'));
        $requestedDepartment = filter_var($this->request->getGet('department_id'), FILTER_VALIDATE_INT);
        $assignedDepartmentId = (int) $session->get('department_id');

        if ($requestedDepartment !== false && in_array($requestedDepartment, $departmentIds, true)) {
            $selectedDepartmentId = $requestedDepartment;
        } elseif (in_array($assignedDepartmentId, $departmentIds, true)) {
            $selectedDepartmentId = $assignedDepartmentId;
        } else {
            $selectedDepartmentId = $departmentIds[0] ?? null;
        }

        $searchInput = $this->request->getGet('search');
        $search = is_string($searchInput) ? trim($searchInput) : '';
        $statusInput = $this->request->getGet('status');
        $status = is_string($statusInput) ? strtoupper(trim($statusInput)) : '';

        if (! in_array($status, ['PENDING', 'APPROVED', 'REJECTED', 'CANCELLED'], true)) {
            $status = '';
        }

        $reservationModel = (new ReservationModel())
            ->select('reservations.*, equipment.equipment_name, equipment.equipment_code, equipment.brand, equipment.model, equipment.department_id, equipment_categories.category_name')
            ->join('equipment', 'equipment.equipment_id = reservations.equipment_id', 'left')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->where('reservations.user_id', $userId)
            ->orderBy('reservations.reservation_date', 'DESC')
            ->orderBy('reservations.reservation_id', 'DESC');

        if ($selectedDepartmentId !== null) {
            $reservationModel->groupStart()
                ->where('equipment.department_id', $selectedDepartmentId)
                ->orWhere('equipment.department_id IS NULL', null, false)
                ->groupEnd();
        }

        if ($search !== '') {
            $reservationModel->groupStart()
                ->like('equipment.equipment_name', $search)
                ->orLike('equipment.equipment_code', $search)
                ->orLike('equipment.brand', $search)
                ->orLike('equipment.model', $search)
                ->orLike('equipment_categories.category_name', $search)
                ->orLike('reservations.purpose', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $reservationModel->where('reservations.status', $status);
        }

        $reservations = $reservationModel->paginate(8, 'reservations');

        return view('reservations/index', [
            'reservations' => $reservations,
            'departmentList' => $departmentList,
            'selectedDepartmentId' => $selectedDepartmentId,
            'userName' => trim((string) $session->get('full_name')) ?: (string) $session->get('id_number'),
            'userRole' => (string) $session->get('role'),
            'search' => $search,
            'statusFilter' => $status,
            'pager' => $reservationModel->pager,
            'paginationDetails' => $reservationModel->pager->getDetails('reservations'),
        ]);
    }

    public function show($reservationId = null)
    {
        $reservation = (new ReservationModel())
            ->select('reservations.*, equipment.equipment_name, equipment.equipment_code, equipment.brand, equipment.model, equipment_categories.category_name')
            ->join('equipment', 'equipment.equipment_id = reservations.equipment_id', 'left')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->where('reservations.reservation_id', (int) $reservationId)
            ->where('reservations.user_id', (int) session()->get('user_id'))
            ->first();

        if ($reservation === null) {
            return redirect()->to('reservations')->with('error', 'Reservation not found.');
        }

        return view('reservations/show', ['reservation' => $reservation]);
    }

    public function create($equipmentId = null)
    {
        if (! $equipmentId) {
            return redirect()->to('equipment')->with('error', 'Please select an equipment item to reserve.');
        }

        $equipment = (new EquipmentModel())->find($equipmentId);

        if (! $equipment) {
            return redirect()->to('equipment')->with('error', 'Equipment item not found.');
        }

        if (strtoupper($equipment['status'] ?? '') !== 'AVAILABLE') {
            return redirect()->to('equipment')->with('error', 'This equipment is currently not available for reservation.');
        }

        return view('reservations/create', ['equipment' => $equipment]);
    }

    public function store()
    {
        $rules = [
            'equipment_id' => 'required|is_not_unique[equipment.equipment_id]',
            'reservation_date' => 'required|valid_date[Y-m-d]',
            'start_time' => 'required',
            'end_time' => 'required',
            'purpose' => 'required|max_length[255]',
        ];

        $messages = [
            'equipment_id' => ['is_not_unique' => 'The selected equipment item does not exist.'],
            'reservation_date' => ['valid_date' => 'Please provide a valid reservation date.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $equipmentId = (int) $this->request->getPost('equipment_id');
        $date = (string) $this->request->getPost('reservation_date');
        $startTime = (string) $this->request->getPost('start_time');
        $endTime = (string) $this->request->getPost('end_time');
        $purpose = (string) $this->request->getPost('purpose');

        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Reservation date cannot be in the past.');
        }

        if (strtotime($startTime) >= strtotime($endTime)) {
            return redirect()->back()->withInput()->with('error', 'Start time must be earlier than end time.');
        }

        $reservationModel = new ReservationModel();
        if (! $reservationModel->checkAvailability($equipmentId, $date, $startTime, $endTime)) {
            return redirect()->back()->withInput()->with('error', 'The selected time slot conflicts with an existing reservation. Please choose another date or time.');
        }

        $saved = $reservationModel->insert([
            'user_id' => (int) session()->get('user_id'),
            'equipment_id' => $equipmentId,
            'reservation_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'purpose' => trim($purpose),
            'status' => 'PENDING',
        ]);

        if (! $saved) {
            return redirect()->back()->withInput()->with('error', 'Failed to create reservation. Please try again.');
        }

        return redirect()->to('reservations')->with('success', 'Your reservation request has been submitted successfully.');
    }
}