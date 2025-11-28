<?php
namespace App\Http\Controllers;

use App\Jobs\CacheUsersJob;
use Illuminate\Support\Facades\Cache;
use App\Jobs\RemoveUserCacheJob;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Inertia\Inertia;

/**
 * Controller for managing users and their cache.
 *
 * Handles user CRUD, caching, and cache invalidation jobs.
 */
class UserController extends Controller
{
    /**
     * Display a paginated list of users, with filtering and caching.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
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
                    $query->leftJoin('addresses', 'users.id', '=', 'addresses.user_id');
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
        CacheUsersJob::dispatch($users->items());
        return Inertia::render('Dashboard', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created user and address, and invalidate cache.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Display a user, loading from cache if available, otherwise from DB and cache it.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $key = "user:{$id}";
        $cached = Cache::get($key);
        if ($cached) {
            return response()->json($cached);
        }
        $user = User::with('address')->findOrFail($id);
        Cache::put($key, $user, now()->addMinutes(5));
        return response()->json($user);
    }

    /**
     * Update the specified user and address, and invalidate cache.
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
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
        Cache::forget("user:{$user->id}");
        return redirect()->back();
    }

    /**
     * Remove the specified user from storage and cache.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();
        RemoveUserCacheJob::dispatch($user->id);
        return redirect()->back();
    }
}
