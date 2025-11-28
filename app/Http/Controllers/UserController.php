<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('address');

        // Filter by user fields
        foreach (['id', 'first_name', 'last_name', 'email'] as $field) {
            if ($value = $request->input($field)) {
                $query->where($field, 'like', "%$value%");
            }
        }

        // Join addresses table for address filters
        $addressFields = ['country', 'city', 'post_code', 'street'];
        $addressFilter = false;
        foreach ($addressFields as $field) {
            if ($value = $request->input($field)) {
                if (!$addressFilter) {
                    $query->join('addresses', 'users.id', '=', 'addresses.user_id');
                    $addressFilter = true;
                }
                $query->where("addresses.$field", 'like', "%$value%");
            }
        }

        // Avoid duplicate users when joining
        if ($addressFilter) {
            $query->select('users.*');
        }

        $users = $query->orderByDesc('users.id')->paginate(12)->appends($request->except('page'));
        return Inertia::render('Dashboard', [
            'users' => $users,
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt('password'), // default password
        ]);
        if (isset($data['address'])) {
            $user->address()->create($data['address']);
        }
        $user->load('address');
        return redirect()->back();
    }

    public function show(User $user)
    {
        $user->load('address');
        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        if ($user->address) {
            $user->address->update($data['address']);
        } else {
            $user->address()->create($data['address']);
        }
        $user->load('address');
        return redirect()->back();
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back();
    }
}
