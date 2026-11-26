<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class AvatarController extends Controller
{
    /** Validation rules for a profile photo upload. Photos are resized in the browser before upload. */
    public static function rules(bool $required): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:3072',
            'dimensions:min_width=200,min_height=200',
        ];
    }

    /** Serve a user's profile photo to signed-in users. */
    public function show(User $user): Response
    {
        $avatar = $user->avatar()->first();
        abort_unless($avatar, 404);

        return response(base64_decode($avatar->data), 200, [
            'Content-Type' => $avatar->mime,
            'Cache-Control' => 'private, max-age=31536000, immutable', // the URL changes when the photo does
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Upload or replace the signed-in user's photo. */
    public function update(Request $request): RedirectResponse
    {
        $request->validateWithBag('avatar', [
            'avatar' => self::rules(true),
        ], [
            'avatar.dimensions' => 'The photo must be at least 200 by 200 pixels.',
        ]);

        $request->user()->setAvatar($request->file('avatar'));

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /** Remove the photo. Workers must keep one, so only students and admins can remove theirs. */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user->isWorker(), 403, 'Laundry agents must keep a profile photo.');

        $user->removeAvatar();

        return Redirect::route('profile.edit')->with('status', 'avatar-removed');
    }
}
