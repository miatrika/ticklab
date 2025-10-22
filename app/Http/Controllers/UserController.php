<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtrer par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }
        
        // Recherche par nom ou email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->latest()->paginate(15)->withQueryString();
        
        // Statistiques pour les badges
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'technicien' => User::where('role', 'technicien')->count(),
            'personnel' => User::where('role', 'personnel')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];
        
        return view('users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,technicien,personnel',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'is_active' => true,
            'must_change_password' => true,
        ]);
        
        $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        Notification::create([
            'user_id' => $admin->id,
            'type' => 'user_created',
            'title' => 'Nouvel utilisateur créé',
            'message' => "Un nouvel utilisateur {$user->name} ({$user->role}) a été créé dans le système",
        ]);
    }
    
        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès. Il devra changer son mot de passe à la première connexion.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,technicien,personnel',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Utilisateur {$status} avec succès");
    }
}