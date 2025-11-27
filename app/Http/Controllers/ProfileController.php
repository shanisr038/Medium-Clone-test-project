<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // =============================
        // 📸 Handle Avatar Upload (Media Library)
        // =============================
        if ($request->hasFile('image')) {
            $user
                ->addMedia($request->file('image'))
                ->toMediaCollection('avatar'); // singleFile auto-updates
        }

        // Remove "image" from $data so it doesn't conflict with DB
        unset($data['image']);

        // =============================
        // ✏️ Update user fields
        // =============================
        $user->fill($data);

        // Reset email verification if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // =============================
        // 🗑️ Delete avatar (Media Library)
        // =============================
        $user->clearMediaCollection('avatar');

        // Delete user completely
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
