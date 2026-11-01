<?php

namespace App\Controllers;

use App\Models\BorrowItemModel;
use App\Models\BorrowTransactionModel;
use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\EquipmentModel;
use App\Models\ReservationModel;
use Config\Database;

class AdminController extends BaseController
{
    protected $helpers = ['form', 'url', 'equipment'];

    public function equipment()
    {
        $session = session();
        $departmentList = (new DepartmentModel())->orderBy('department_name', 'ASC')->findAll();
        $departmentIds = array_map('intval', array_column($departmentList, 'department_id'));
        $assignedDepartmentId = (int) $session->get('department_id');
        $requestedDepartment = filter_var($this->request->getGet('department_id'), FILTER_VALIDATE_INT);

        if ($requestedDepartment !== false && in_array($requestedDepartment, $departmentIds, true)) {
            $selectedDepartmentId = $requestedDepartment;
        } elseif (in_array($assignedDepartmentId, $departmentIds, true)) {
            $selectedDepartmentId = $assignedDepartmentId;
        } else {
            $selectedDepartmentId = $departmentIds[0] ?? null;
        }

        $applyDepartment = static function (EquipmentModel $model) use ($selectedDepartmentId): EquipmentModel {
            if ($selectedDepartmentId !== null) {
                $model->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            return $model;
        };

        $totalAssets = $applyDepartment(new EquipmentModel())->countAllResults();

        $search = $this->request->getGet('search');
        $search = is_string($search) ? trim($search) : '';
        $categoryId = filter_var($this->request->getGet('category_id'), FILTER_VALIDATE_INT);
        $statusInput = $this->request->getGet('status');
        $conditionInput = $this->request->getGet('condition');
        if (! is_string($conditionInput) || trim($conditionInput) === '') {
            $conditionInput = $this->request->getGet('condition_status');
        }
        $status = is_string($statusInput) ? strtoupper(trim($statusInput)) : '';
        $condition = is_string($conditionInput) ? strtoupper(trim($conditionInput)) : '';

        $equipmentModel = $applyDepartment((new EquipmentModel())
            ->select('equipment.*, equipment_categories.category_name')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->orderBy('equipment.equipment_id', 'DESC'));

        if ($search !== '') {
            $equipmentModel->groupStart()
                ->like('equipment.equipment_name', $search)
                ->orLike('equipment.equipment_code', $search)
                ->orLike('equipment.brand', $search)
                ->orLike('equipment.model', $search)
                ->orLike('equipment_categories.category_name', $search)
                ->groupEnd();
        }

        if ($categoryId !== false && $categoryId > 0) {
            $equipmentModel->where('equipment.category_id', $categoryId);
        }

        if (in_array($status, ['AVAILABLE', 'IN_USE', 'RESERVED'], true)) {
            $equipmentModel->where('equipment.status', $status);
        } else {
            $status = '';
        }

        if (in_array($condition, ['GOOD', 'EXCELLENT', 'FAIR', 'DAMAGED'], true)) {
            $equipmentModel->where('equipment.condition_status', $condition);
        } else {
            $condition = '';
        }

        $equipmentList = $equipmentModel->paginate(8, 'inventory');
        $pager = $equipmentModel->pager;

        $userName = trim((string) $session->get('full_name')) ?: (string) $session->get('id_number');
        $userRole = 'System Admin';

        return view('admin/equipment/index', [
            'equipmentList'        => $equipmentList,
            'categories'           => (new CategoryModel())->orderBy('category_name', 'ASC')->findAll(),
            'departmentList'       => $departmentList,
            'selectedDepartmentId' => $selectedDepartmentId,
            'totalAssets'          => $totalAssets,
            'userName'             => $userName,
            'userRole'             => $userRole,
            'search'               => $search,
            'categoryId'           => $categoryId !== false && $categoryId > 0 ? $categoryId : null,
            'statusFilter'         => $status,
            'conditionFilter'      => $condition,
            'pager'                => $pager,
            'paginationDetails'    => $pager->getDetails('inventory'),
        ]);
    }

    public function dashboard()
    {
        $session = session();
        $departmentList = (new DepartmentModel())->orderBy('department_name', 'ASC')->findAll();
        $departmentIds = array_map('intval', array_column($departmentList, 'department_id'));
        $requestedDepartment = filter_var($this->request->getGet('department_id'), FILTER_VALIDATE_INT);
        $assignedDepartmentId = (int) $session->get('department_id');
        $selectedDepartmentId = $requestedDepartment !== false && in_array($requestedDepartment, $departmentIds, true)
            ? $requestedDepartment
            : (in_array($assignedDepartmentId, $departmentIds, true) ? $assignedDepartmentId : ($departmentIds[0] ?? null));

        $searchInput = $this->request->getGet('search');
        $search = is_string($searchInput) ? trim($searchInput) : '';
        $filterInput = $this->request->getGet('filter');
        $filter = is_string($filterInput) ? strtolower(trim($filterInput)) : 'all';
        if (! in_array($filter, ['all', 'faculty', 'students'], true)) {
            $filter = 'all';
        }

        $scopeDepartment = static function ($query) use ($selectedDepartmentId) {
            if ($selectedDepartmentId !== null) {
                $query->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            return $query;
        };

        $equipmentQuery = static fn () => $scopeDepartment(new EquipmentModel());
        $totalEquipment = $equipmentQuery()->countAllResults();
        $previousMonthEquipment = $equipmentQuery()->where('created_at <', date('Y-m-01 00:00:00'))->countAllResults();
        $equipmentGrowth = $previousMonthEquipment > 0
            ? (int) round((($totalEquipment - $previousMonthEquipment) / $previousMonthEquipment) * 100)
            : null;

        $reservationQuery = static function () use ($selectedDepartmentId) {
            $query = (new ReservationModel())
                ->join('equipment', 'equipment.equipment_id = reservations.equipment_id');
            if ($selectedDepartmentId !== null) {
                $query->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            return $query;
        };
        $activeReservations = $reservationQuery()->whereIn('reservations.status', ['PENDING', 'APPROVED'])->countAllResults();

        $transactionQuery = static function () use ($selectedDepartmentId) {
            $query = Database::connect()->table('borrow_transactions')
                ->join('borrow_items', 'borrow_items.transaction_id = borrow_transactions.transaction_id')
                ->join('equipment', 'equipment.equipment_id = borrow_items.equipment_id');
            if ($selectedDepartmentId !== null) {
                $query->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            return $query;
        };
        $countTransactions = static function (callable $applyFilter) use ($transactionQuery): int {
            $query = $transactionQuery()->select('COUNT(DISTINCT borrow_transactions.transaction_id) AS total', false);
            $applyFilter($query);
            return (int) ($query->get()->getRowArray()['total'] ?? 0);
        };
        $ongoingBorrowings = $countTransactions(static fn ($query) => $query->where('borrow_transactions.status', 'CHECKED_OUT'));
        $todayStart = date('Y-m-d 00:00:00');
        $tomorrowStart = date('Y-m-d 00:00:00', strtotime('+1 day'));
        $dueToday = $countTransactions(static fn ($query) => $query
            ->where('borrow_transactions.status', 'CHECKED_OUT')
            ->where('borrow_transactions.expected_return_date >=', $todayStart)
            ->where('borrow_transactions.expected_return_date <', $tomorrowStart));
        $pendingReturns = $countTransactions(static fn ($query) => $query
            ->where('borrow_transactions.status', 'CHECKED_OUT')
            ->where('borrow_transactions.expected_return_date <', $tomorrowStart));
        $now = date('Y-m-d H:i:s');
        $overdueReturns = $countTransactions(static function ($query) use ($now) {
            $query->groupStart()
                ->groupStart()
                    ->where('borrow_transactions.status', 'CHECKED_OUT')
                    ->where('borrow_transactions.expected_return_date <', $now)
                ->groupEnd()
                ->orGroupStart()
                    ->where('borrow_transactions.status', 'RETURNED')
                    ->where('borrow_transactions.actual_return_date > borrow_transactions.expected_return_date', null, false)
                ->groupEnd()
                ->groupEnd();
        });

        $pendingQuery = static function (?string $role = null) use ($selectedDepartmentId, $search) {
            $query = (new ReservationModel())
                ->select('reservations.*, users.id_number, users.role AS requester_role, user_profiles.first_name, user_profiles.last_name, equipment.equipment_name, equipment.equipment_code, equipment.department_id, equipment.status AS equipment_status')
                ->join('users', 'users.user_id = reservations.user_id')
                ->join('user_profiles', 'user_profiles.user_id = users.user_id', 'left')
                ->join('equipment', 'equipment.equipment_id = reservations.equipment_id')
                ->where('reservations.status', 'PENDING');
            if ($selectedDepartmentId !== null) {
                $query->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            if ($search !== '') {
                $query->groupStart()
                    ->like('equipment.equipment_name', $search)
                    ->orLike('equipment.equipment_code', $search)
                    ->orLike('users.id_number', $search)
                    ->orLike('user_profiles.first_name', $search)
                    ->orLike('user_profiles.last_name', $search)
                    ->orLike('reservations.purpose', $search)
                    ->groupEnd();
            }
            if ($role !== null) {
                $query->where('users.role', $role);
            }
            return $query;
        };

        $pendingCounts = [
            'all' => $pendingQuery()->countAllResults(),
            'faculty' => $pendingQuery('STAFF')->countAllResults(),
            'students' => $pendingQuery('STUDENT')->countAllResults(),
        ];
        $selectedRole = match ($filter) {
            'faculty' => 'STAFF',
            'students' => 'STUDENT',
            default => null,
        };
        $pendingModel = $pendingQuery($selectedRole)
            ->orderBy('reservations.reservation_id', 'DESC');
        $pendingReservations = $pendingModel->paginate(5, 'pending_approvals');
        $pager = $pendingModel->pager;
        $pager->only(['department_id', 'filter', 'search']);

        $userName = trim((string) $session->get('full_name')) ?: (string) $session->get('id_number');

        return view('admin/dashboard', [
            'departmentList' => $departmentList,
            'selectedDepartmentId' => $selectedDepartmentId,
            'userName' => $userName,
            'userRole' => 'System Admin',
            'search' => $search,
            'filter' => $filter,
            'equipmentStats' => [
                'total' => $totalEquipment,
                'growth' => $equipmentGrowth,
                'activeReservations' => $activeReservations,
                'ongoingBorrowings' => $ongoingBorrowings,
                'dueToday' => $dueToday,
                'pendingReturns' => $pendingReturns,
                'overdueReturns' => $overdueReturns,
            ],
            'pendingReservations' => $pendingReservations,
            'pendingCounts' => $pendingCounts,
            'pager' => $pager,
            'paginationDetails' => $pager->getDetails('pending_approvals'),
        ]);
    }

    public function transactions()
    {
        $session = session();
        $departmentList = (new DepartmentModel())->orderBy('department_name', 'ASC')->findAll();
        $departmentIds = array_map('intval', array_column($departmentList, 'department_id'));
        $assignedDepartmentId = (int) $session->get('department_id');
        $requestedDepartment = filter_var($this->request->getGet('department_id'), FILTER_VALIDATE_INT);

        if ($requestedDepartment !== false && in_array($requestedDepartment, $departmentIds, true)) {
            $selectedDepartmentId = $requestedDepartment;
        } elseif (in_array($assignedDepartmentId, $departmentIds, true)) {
            $selectedDepartmentId = $assignedDepartmentId;
        } else {
            $selectedDepartmentId = $departmentIds[0] ?? null;
        }

        $search = $this->request->getGet('search');
        $search = is_string($search) ? trim($search) : '';

        $scopeDepartment = static function ($query) use ($selectedDepartmentId) {
            if ($selectedDepartmentId !== null) {
                $query->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            return $query;
        };

        $reservationQuery = function () use ($scopeDepartment, $search) {
            $model = (new ReservationModel())
                ->select('reservations.*, users.id_number, users.role AS requester_role, user_profiles.first_name, user_profiles.last_name, equipment.equipment_name, equipment.equipment_code, equipment.status AS equipment_status')
                ->join('users', 'users.user_id = reservations.user_id')
                ->join('user_profiles', 'user_profiles.user_id = users.user_id', 'left')
                ->join('equipment', 'equipment.equipment_id = reservations.equipment_id');

            $scopeDepartment($model);

            if ($search !== '') {
                $model->groupStart()
                    ->like('equipment.equipment_name', $search)
                    ->orLike('equipment.equipment_code', $search)
                    ->orLike('users.id_number', $search)
                    ->orLike('user_profiles.first_name', $search)
                    ->orLike('user_profiles.last_name', $search)
                    ->orLike('reservations.purpose', $search)
                    ->groupEnd();
            }

            return $model;
        };

        $reservations = $reservationQuery()
            ->where('reservations.status', 'PENDING')
            ->orderBy('reservations.reservation_id', 'DESC')
            ->findAll();

        $approvedReservations = $reservationQuery()
            ->where('reservations.status', 'APPROVED')
            ->orderBy('reservations.reservation_id', 'DESC')
            ->findAll();

        $txQuery = function () use ($scopeDepartment, $search) {
            $model = (new BorrowTransactionModel())
                ->select('borrow_transactions.*, borrow_items.equipment_id, borrow_items.condition_out, equipment.equipment_name, equipment.equipment_code, users.id_number, users.role AS requester_role, user_profiles.first_name, user_profiles.last_name')
                ->join('borrow_items', 'borrow_items.transaction_id = borrow_transactions.transaction_id')
                ->join('equipment', 'equipment.equipment_id = borrow_items.equipment_id')
                ->join('users', 'users.user_id = borrow_transactions.user_id')
                ->join('user_profiles', 'user_profiles.user_id = users.user_id', 'left');

            $scopeDepartment($model);

            if ($search !== '') {
                $model->groupStart()
                    ->like('equipment.equipment_name', $search)
                    ->orLike('equipment.equipment_code', $search)
                    ->orLike('users.id_number', $search)
                    ->orLike('user_profiles.first_name', $search)
                    ->orLike('user_profiles.last_name', $search)
                    ->groupEnd();
            }

            return $model;
        };

        $transactions = $txQuery()
            ->where('borrow_transactions.status', 'CHECKED_OUT')
            ->orderBy('borrow_transactions.transaction_id', 'DESC')
            ->findAll();

        $returnedHistory = $txQuery()
            ->where('borrow_transactions.status', 'RETURNED')
            ->orderBy('borrow_transactions.actual_return_date', 'DESC')
            ->findAll(10);

        $counts = [
            'pending'  => count($reservations),
            'checkout' => count($approvedReservations),
            'active'   => count($transactions),
            'returned' => count($returnedHistory),
        ];

        $userName = trim((string) $session->get('full_name')) ?: (string) $session->get('id_number');

        return view('admin/transactions/index', [
            'departmentList'       => $departmentList,
            'selectedDepartmentId' => $selectedDepartmentId,
            'userName'             => $userName,
            'userRole'             => 'System Admin',
            'search'               => $search,
            'reservations'         => $reservations,
            'approvedReservations' => $approvedReservations,
            'transactions'         => $transactions,
            'returnedHistory'      => $returnedHistory,
            'counts'               => $counts,
        ]);
    }

    public function index()
    {
        return $this->transactions();
    }

    public function updateReservationStatus($id, $status)
    {
        $status = strtoupper((string) $status);
        $model = new ReservationModel();
        $reservation = $model->find($id);

        if (! $reservation || strtoupper($reservation['status']) !== 'PENDING' || ! in_array($status, ['APPROVED', 'REJECTED'], true)) {
            return redirect()->back()->with('error', 'That reservation cannot be updated.');
        }

        if (! $model->update($id, ['status' => $status])) {
            return redirect()->back()->with('error', 'Unable to update the reservation.');
        }

        return redirect()->back()->with('success', 'Reservation ' . strtolower($status) . '.');
    }

    public function checkout($reservationId)
    {
        $reservation = (new ReservationModel())->find($reservationId);
        if (! $reservation || strtoupper($reservation['status']) !== 'APPROVED') {
            return redirect()->to('admin/transactions')->with('error', 'Only approved reservations can be checked out.');
        }

        $equipmentModel = new EquipmentModel();
        $equipment = $equipmentModel->find($reservation['equipment_id']);
        if (! $equipment || strtoupper($equipment['status']) !== 'AVAILABLE') {
            return redirect()->to('admin/transactions')->with('error', 'This equipment is not available for checkout.');
        }

        $now = date('Y-m-d H:i:s');
        $transactionModel = new BorrowTransactionModel();
        $itemModel = new BorrowItemModel();
        $db = Database::connect();
        $db->transBegin();

        $transactionId = $transactionModel->insert([
            'user_id'              => (int) $reservation['user_id'],
            'processed_by'         => (int) session()->get('user_id'),
            'reservation_id'       => (int) $reservation['reservation_id'],
            'borrow_date'          => $now,
            'expected_return_date' => date('Y-m-d H:i:s', strtotime($reservation['reservation_date'] . ' ' . $reservation['end_time'])),
            'status'               => 'CHECKED_OUT',
        ]);

        $itemId = $transactionId ? $itemModel->insert([
            'transaction_id' => (int) $transactionId,
            'equipment_id'   => (int) $equipment['equipment_id'],
            'condition_out'  => $equipment['condition_status'] ?? 'GOOD',
        ]) : false;

        $equipmentUpdated = $itemId && $equipmentModel->update($equipment['equipment_id'], ['status' => 'IN_USE']);
        $reservationUpdated = $equipmentUpdated && (new ReservationModel())->update($reservationId, ['status' => 'CHECKED_OUT']);

        if (! $transactionId || ! $itemId || ! $equipmentUpdated || ! $reservationUpdated || ! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('admin/transactions')->with('error', 'Checkout failed. No checkout changes were saved.');
        }

        $db->transCommit();
        return redirect()->to('admin/transactions')->with('success', 'Equipment checked out.');
    }

    public function returnItem($transactionId)
    {
        if (! $this->validate(['condition_in' => 'required|in_list[GOOD,FAIR,DAMAGED]'])) {
            return redirect()->to('admin/transactions')->with('error', 'Select the equipment condition on return.');
        }

        $transactionModel = new BorrowTransactionModel();
        $transaction = $transactionModel->find($transactionId);
        $itemModel = new BorrowItemModel();
        $item = $itemModel->where('transaction_id', $transactionId)->first();

        if (! $transaction || $transaction['status'] !== 'CHECKED_OUT' || ! $item || $item['returned_at'] !== null) {
            return redirect()->to('admin/transactions')->with('error', 'That transaction is not awaiting return.');
        }

        $reservationModel = new ReservationModel();
        $reservation = ! empty($transaction['reservation_id'])
            ? $reservationModel->find($transaction['reservation_id'])
            : null;

        // Older checkouts predate the reservation link; match only an unambiguous original slot.
        if (! $reservation) {
            $expectedReturn = strtotime($transaction['expected_return_date']);
            if ($expectedReturn !== false) {
                $legacyMatches = $reservationModel
                    ->where('user_id', $transaction['user_id'])
                    ->where('equipment_id', $item['equipment_id'])
                    ->where('reservation_date', date('Y-m-d', $expectedReturn))
                    ->where('end_time', date('H:i:s', $expectedReturn))
                    ->whereIn('status', ['APPROVED', 'CHECKED_OUT'])
                    ->findAll();
                $reservation = count($legacyMatches) === 1 ? $legacyMatches[0] : null;
            }
        }

        if (! $reservation || ! in_array(strtoupper((string) $reservation['status']), ['APPROVED', 'CHECKED_OUT'], true)) {
            return redirect()->to('admin/transactions')->with('error', 'The reservation linked to this checkout could not be verified.');
        }

        $now = date('Y-m-d H:i:s');
        $db = Database::connect();
        $db->transBegin();

        $itemUpdated = $itemModel->update($item['borrow_item_id'], [
            'condition_in' => $this->request->getPost('condition_in'),
            'returned_at'  => $now,
        ]);
        $transactionUpdated = $transactionModel->update($transactionId, [
            'reservation_id'     => (int) $reservation['reservation_id'],
            'actual_return_date' => $now,
            'status'             => 'RETURNED',
        ]);
        $equipmentUpdated = (new EquipmentModel())->update($item['equipment_id'], [
            'status'           => 'AVAILABLE',
            'condition_status' => $this->request->getPost('condition_in'),
        ]);
        $reservationUpdated = $reservationModel->update($reservation['reservation_id'], ['status' => 'RETURNED']);

        if (! $itemUpdated || ! $transactionUpdated || ! $equipmentUpdated || ! $reservationUpdated || ! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('admin/transactions')->with('error', 'Return failed. No return changes were saved.');
        }

        $db->transCommit();
        return redirect()->to('admin/transactions')->with('success', 'Equipment return recorded.');
    }
}
