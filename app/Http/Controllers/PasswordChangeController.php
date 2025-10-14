<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'new_password.required' => 'Le nouveau mot de passe est requis.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = Auth::user();

        // Vérifier que le mot de passe actuel est correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Vérifier que le nouveau mot de passe est différent de l'ancien
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Le nouveau mot de passe doit être différent de l\'ancien.']);
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        $user->must_change_password = false;
        $user->save();

        // Rediriger selon le rôle
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Mot de passe changé avec succès !');
        } elseif ($user->isTechnicien()) {
            return redirect()->route('technicien.dashboard')
                ->with('success', 'Mot de passe changé avec succès !');
        } else {
            return redirect()->route('tickets.index')
                ->with('success', 'Mot de passe changé avec succès !');
        }
    }
}
