<?php
require_once __DIR__ . '/../model/UserDetails.php';
require_once __DIR__ . '/../model/User.php';

class UserDetailsController
{
    private $userDetails;
    private $user;

    public function __construct()
    {
        $this->userDetails = new UserDetails();
        $this->user = new User();
    }

    public function index()
    {
        $result = $this->userDetails->read();
        $userDetails = array();

        while ($row = $result->fetch_assoc()) {
            $userDetails[] = $this->enrichUserDetails($row);
        }

        return $userDetails;
    }

    public function show($id)
    {
        $this->userDetails->user_id = $id;
        $this->userDetails->readOne();

        if (!empty($this->userDetails->first_name)) {
            return $this->enrichUserDetails(array(
                'user_id' => $this->userDetails->user_id,
                'first_name' => $this->userDetails->first_name,
                'middle_name' => $this->userDetails->middle_name,
                'last_name' => $this->userDetails->last_name,
                'suffix' => $this->userDetails->suffix,
                'phone_number' => $this->userDetails->phone_number,
                'email' => $this->userDetails->email,
                'updated_at' => $this->userDetails->updated_at
            ));
        }

        return null;
    }

    public function store($data)
    {
        if (empty($data['user_id'])) {
            return array('success' => false, 'message' => 'User ID is required');
        }

        // Check if user exists
        $this->user->user_id = $data['user_id'];
        $this->user->readOne();
        if (empty($this->user->username)) {
            return array('success' => false, 'message' => 'User not found');
        }

        // Check if user details already exist
        $this->userDetails->user_id = $data['user_id'];
        $this->userDetails->readOne();
        if (!empty($this->userDetails->first_name)) {
            return array('success' => false, 'message' => 'User details already exist. Use update instead.');
        }

        $this->userDetails->user_id = $data['user_id'];
        $this->userDetails->first_name = $data['first_name'] ?? '';
        $this->userDetails->middle_name = $data['middle_name'] ?? '';
        $this->userDetails->last_name = $data['last_name'] ?? '';
        $this->userDetails->suffix = $data['suffix'] ?? '';
        $this->userDetails->phone_number = $data['phone_number'] ?? '';
        $this->userDetails->email = $data['email'] ?? '';

        if ($this->userDetails->create()) {
            return array(
                'success' => true,
                'message' => 'User details created successfully',
                'user_id' => $this->userDetails->user_id
            );
        }

        return array('success' => false, 'message' => 'Failed to create user details');
    }

    public function update($id, $data)
    {
        $this->userDetails->user_id = $id;
        $this->userDetails->readOne();

        if (empty($this->userDetails->first_name)) {
            return array('success' => false, 'message' => 'User details not found');
        }

        $this->userDetails->first_name = $data['first_name'] ?? $this->userDetails->first_name;
        $this->userDetails->middle_name = $data['middle_name'] ?? $this->userDetails->middle_name;
        $this->userDetails->last_name = $data['last_name'] ?? $this->userDetails->last_name;
        $this->userDetails->suffix = $data['suffix'] ?? $this->userDetails->suffix;
        $this->userDetails->phone_number = $data['phone_number'] ?? $this->userDetails->phone_number;
        $this->userDetails->email = $data['email'] ?? $this->userDetails->email;

        if ($this->userDetails->update()) {
            return array('success' => true, 'message' => 'User details updated successfully');
        }

        return array('success' => false, 'message' => 'Failed to update user details');
    }

    public function destroy($id)
    {
        $this->userDetails->user_id = $id;

        if ($this->userDetails->delete()) {
            return array('success' => true, 'message' => 'User details deleted successfully');
        }

        return array('success' => false, 'message' => 'Failed to delete user details');
    }

    public function getProfile($userId)
    {
        $userDetails = $this->show($userId);

        if ($userDetails) {
            return array(
                'user_id' => $userDetails['user_id'],
                'first_name' => $userDetails['first_name'],
                'middle_name' => $userDetails['middle_name'],
                'last_name' => $userDetails['last_name'],
                'suffix' => $userDetails['suffix'],
                'phone_number' => $userDetails['phone_number'],
                'email' => $userDetails['email'],
                'full_name' => $userDetails['full_name'],
                'updated_at' => $userDetails['updated_at']
            );
        }

        return null;
    }

    /** Validates and saves a customer's own profile, creating the row if needed. */
    public function updateProfile($userId, $data)
    {
        $fields = array();
        foreach (['first_name', 'middle_name', 'last_name', 'suffix', 'phone_number', 'email'] as $f) {
            $fields[$f] = trim($data[$f] ?? '');
        }

        if ($fields['first_name'] === '' || $fields['last_name'] === '') {
            return array('success' => false, 'message' => 'First and last name are required.');
        }
        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return array('success' => false, 'message' => 'Please enter a valid email address.');
        }
        if ($fields['phone_number'] !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $fields['phone_number'])) {
            return array('success' => false, 'message' => 'Please enter a valid phone number.');
        }

        $owner = $this->userDetails->findUserIdByEmail($fields['email']);
        if ($owner && $owner !== (int)$userId) {
            return array('success' => false, 'message' => 'That email is already used by another account.');
        }

        if ($this->show($userId)) {
            $result = $this->update($userId, $fields);
        } else {
            $result = $this->store($fields + array('user_id' => $userId));
        }

        if ($result['success']) {
            $result['message'] = 'Profile updated.';
        }
        return $result;
    }

    public function searchByEmail($email)
    {
        // This would require a custom query in the model
        // For now, we'll implement a basic search through all records
        $allUsers = $this->index();
        $matches = array();

        foreach ($allUsers as $user) {
            if (stripos($user['email'], $email) !== false) {
                $matches[] = $user;
            }
        }

        return $matches;
    }

    public function searchByName($name)
    {
        $allUsers = $this->index();
        $matches = array();

        foreach ($allUsers as $user) {
            $fullName = strtolower($user['full_name']);
            $searchName = strtolower($name);

            if (stripos($fullName, $searchName) !== false) {
                $matches[] = $user;
            }
        }

        return $matches;
    }

    private function enrichUserDetails($row)
    {
        $fullName = $row['first_name'];
        if (!empty($row['middle_name'])) {
            $fullName .= ' ' . $row['middle_name'];
        }
        $fullName .= ' ' . $row['last_name'];
        if (!empty($row['suffix'])) {
            $fullName .= ' ' . $row['suffix'];
        }

        return array(
            'user_id' => $row['user_id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'suffix' => $row['suffix'],
            'phone_number' => $row['phone_number'],
            'email' => $row['email'],
            'updated_at' => $row['updated_at'],
            'full_name' => $fullName
        );
    }
}
?>