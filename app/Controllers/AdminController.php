<?php

namespace App\Controllers;

use App\Models\BorrowItemModel;
use App\Models\BorrowTransactionModel;
use App\Models\EquipmentModel;
use App\Models\ReservationModel;
use Config\Database;

class AdminController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index()
    {
        $reservations = (new ReservationModel())
            ->select('reservations.*, users.id_number, user_profiles.first_name, user_profiles.last_name, equipment.equipment_name, equipment.equipment_code, equipment.status AS equipment_status')
            ->join('users', 'users.user_id = reservations.user_id')
            ->join('user_profiles', 'user_profiles.user_id = users.user_id', 'left')
            ->join('equipment', 'equipment.equipment_id = reservations.equipment_id')
            ->orderBy('reservations.reservation_id', 'DESC')
            ->findAll();

        $transactions = (new BorrowTransactionModel())
            ->select('borrow_transactions.*, borrow_items.equipment_id, equipment.equipment_name, equipment.equipment_code, users.id_number, user_profiles.first_name, user_profiles.last_name')
            ->join('borrow_items', 'borrow_items.transaction_id = borrow_transactions.transaction_id')
            ->join('equipment', 'equipment.equipment_id = borrow_items.equipment_id')
            ->join('users', 'users.user_id = borrow_transactions.user_id')
            ->join('user_profiles', 'user_profiles.user_id = users.user_id', 'left')
            ->where('borrow_transactions.status', 'CHECKED_OUT')
            ->orderBy('borrow_transactions.transaction_id', 'DESC')
            ->findAll();

        return view('admin/reservations/index', [
            'reservations' => $reservations,
            'transactions' => $transactions,
        ]);
    }

    public function updateReservationStatus($id, $status)
    {
        $status = strtoupper((string) $status);
        $model = new ReservationModel();
        $reservation = $model->find($id);

        if (! $reservation || strtoupper($reservation['status']) !== 'PENDING' || ! in_array($status, ['APPROVED', 'REJECTED'], true)) {
            return redirect()->to('admin/reservations')->with('error', 'That reservation cannot be updated.');
        }

        if (! $model->update($id, ['status' => $status])) {
            return redirect()->to('admin/reservations')->with('error', 'Unable to update the reservation.');
        }

        return redirect()->to('admin/reservations')->with('success', 'Reservation ' . strtolower($status) . '.');
    }

    public function checkout($reservationId)
    {
        $reservation = (new ReservationModel())->find($reservationId);
        if (! $reservation || strtoupper($reservation['status']) !== 'APPROVED') {
            return redirect()->to('admin/reservations')->with('error', 'Only approved reservations can be checked out.');
        }

        $equipmentModel = new EquipmentModel();
        $equipment = $equipmentModel->find($reservation['equipment_id']);
        if (! $equipment || strtoupper($equipment['status']) !== 'AVAILABLE') {
            return redirect()->to('admin/reservations')->with('error', 'This equipment is not available for checkout.');
        }

        $now = date('Y-m-d H:i:s');
        $transactionModel = new BorrowTransactionModel();
        $itemModel = new BorrowItemModel();
        $db = Database::connect();
        $db->transBegin();

        $transactionId = $transactionModel->insert([
            'user_id'              => (int) $reservation['user_id'],
            'processed_by'         => (int) session()->get('user_id'),
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

        if (! $transactionId || ! $itemId || ! $equipmentUpdated || ! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('admin/reservations')->with('error', 'Checkout failed. No checkout changes were saved.');
        }

        $db->transCommit();
        return redirect()->to('admin/reservations')->with('success', 'Equipment checked out.');
    }

    public function returnItem($transactionId)
    {
        if (! $this->validate(['condition_in' => 'required|in_list[GOOD,FAIR,DAMAGED]'])) {
            return redirect()->to('admin/reservations')->with('error', 'Select the equipment condition on return.');
        }

        $transactionModel = new BorrowTransactionModel();
        $transaction = $transactionModel->find($transactionId);
        $itemModel = new BorrowItemModel();
        $item = $itemModel->where('transaction_id', $transactionId)->first();

        if (! $transaction || $transaction['status'] !== 'CHECKED_OUT' || ! $item || $item['returned_at'] !== null) {
            return redirect()->to('admin/reservations')->with('error', 'That transaction is not awaiting return.');
        }

        $now = date('Y-m-d H:i:s');
        $db = Database::connect();
        $db->transBegin();

        $itemUpdated = $itemModel->update($item['borrow_item_id'], [
            'condition_in' => $this->request->getPost('condition_in'),
            'returned_at'  => $now,
        ]);
        $transactionUpdated = $transactionModel->update($transactionId, [
            'actual_return_date' => $now,
            'status'             => 'RETURNED',
        ]);
        $equipmentUpdated = (new EquipmentModel())->update($item['equipment_id'], [
            'status'           => 'AVAILABLE',
            'condition_status' => $this->request->getPost('condition_in'),
        ]);

        if (! $itemUpdated || ! $transactionUpdated || ! $equipmentUpdated || ! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('admin/reservations')->with('error', 'Return failed. No return changes were saved.');
        }

        $db->transCommit();
        return redirect()->to('admin/reservations')->with('success', 'Equipment return recorded.');
    }
}
