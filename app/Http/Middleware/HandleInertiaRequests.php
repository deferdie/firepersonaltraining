<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $impersonation = null;
        $trainerId = $request->session()->get('impersonating_from_trainer_id');
        if ($trainerId && $user && !$user->is_trainer) {
            $trainer = User::find($trainerId);
            $impersonation = [
                'active' => true,
                'trainer_name' => $trainer?->name ?? 'Trainer',
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_trainer' => $user->is_trainer,
                ] : null,
            ],
            'impersonation' => $impersonation,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
