<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('address')->orderByDesc('id')->paginate(12);
        return Inertia::render('Dashboard', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        $user->load('address');
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'email|max:255',
            'address.country' => 'string|max:255',
            'address.city' => 'string|max:255',
            'address.post_code' => 'string|max:255',
            'address.street' => 'string|max:255',
        ]);
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
        return response()->json(['success' => true]);
    }
}
