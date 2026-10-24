<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserProfileModel;
use Config\Database;

class AuthController extends BaseController
{
    protected $helpers = ['form', 'url'];

    // Display the registration page.
    public function register()
    {
        if (session()->get('isLoggedIn') || session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/register');
    }

    // Process registration request.
    public function registerPost()
    {
        $rules = [
            'id_number'        => 'required|min_length[3]|max_length[50]|is_unique[users.id_number]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'first_name'       => 'required|max_length[50]',
            'last_name'        => 'required|max_length[50]',
            'email'            => 'required|valid_email|max_length[255]|is_unique[user_profiles.email]',
            'contact_number'   => 'permit_empty|max_length[20]',
            'department'       => 'permit_empty|max_length[100]',
            'role'             => 'permit_empty|in_list[ADMIN,STAFF,STUDENT]',
        ];

        $messages = [
            'id_number' => [
                'is_unique' => 'This ID number is already registered.',
            ],
            'email' => [
                'is_unique' => 'This email address is already in use.',
            ],
            'password_confirm' => [
                'matches' => 'The password confirmation does not match.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $userModel        = new UserModel();
            $userProfileModel = new UserProfileModel();

            $role = $this->request->getPost('role') ?: 'STUDENT';

            $userId = $userModel->insert([
                'id_number' => $this->request->getPost('id_number'),
                'password'  => $this->request->getPost('password'),
                'role'      => $role,
            ]);

            if (! $userId) {
                throw new \RuntimeException('Unable to create user credentials.');
            }

            $profileCreated = $userProfileModel->createProfile(
                (int) $userId,
                (string) $this->request->getPost('first_name'),
                (string) $this->request->getPost('last_name'),
                (string) $this->request->getPost('email'),
                $this->request->getPost('contact_number') ?: null,
                $this->request->getPost('department') ?: null
            );

            if (! $profileCreated) {
                throw new \RuntimeException('Unable to save user profile details.');
            }

            $db->transCommit();

            return redirect()->to('login')->with('success', 'Registration successful! You may now log in.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }

    // Display the login page.
    public function login()
    {
        if (session()->get('isLoggedIn') || session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/login');
    }

    // Process login request.
    public function loginPost()
    {
        $rules = [
            'id_number' => 'required',
            'password'  => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idNumber = (string) $this->request->getPost('id_number');
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->where('id_number', $idNumber)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid ID number or password.');
        }

        $userProfileModel = new UserProfileModel();
        $profile          = $userProfileModel->getProfileByUserId((int) $user['user_id']);

        session()->regenerate();
        session()->set([
            'user_id'      => (int) $user['user_id'],
            'id_number'    => $user['id_number'],
            'role'         => $user['role'],
            'first_name'   => $profile['first_name'] ?? '',
            'last_name'    => $profile['last_name'] ?? '',
            'full_name'    => trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')),
            'email'        => $profile['email'] ?? '',
            'department'   => $profile['department'] ?? '',
            'is_logged_in' => true,
            'isLoggedIn'   => true,
        ]);

        $displayName = ! empty($profile['first_name']) ? $profile['first_name'] : $user['id_number'];

        return redirect()->to('/')->with('success', "Welcome back, {$displayName}!");
    }

    // Log out user and invalidate session.
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login')->with('success', 'You have been successfully logged out.');
    }
}
