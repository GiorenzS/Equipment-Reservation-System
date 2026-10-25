<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table            = 'user_profiles';
    protected $primaryKey       = 'profile_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'contact_number',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'user_id'        => 'required|integer|is_unique[user_profiles.user_id,profile_id,{profile_id}]',
        'first_name'     => 'required|max_length[50]',
        'last_name'      => 'required|max_length[50]',
        'email'          => 'required|valid_email|max_length[255]',
        'contact_number' => 'permit_empty|max_length[20]',
    ];

    // Create a profile linked to a user
    public function createProfile(int $userId, string $firstName, string $lastName, string $email, ?string $contactNumber = null): int|bool
    {
        return $this->insert([
            'user_id'        => $userId,
            'first_name'     => $firstName,
            'last_name'      => $lastName,
            'email'          => $email,
            'contact_number' => $contactNumber,
        ]);
    }

    // Retrieve profile by user_id
    public function getProfileByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    // Update profile by user_id
    public function updateProfile(int $userId, array $data): bool
    {
        return (bool) $this->where('user_id', $userId)->set($data)->update();
    }
}
