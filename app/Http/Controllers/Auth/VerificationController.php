<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if (!$request->user()->hasVerifiedEmail()) {
            $request->fulfill();
            event(new Verified($request->user()));
        }

        return $this->redirectVerified($request->user());
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent! Check your inbox.');
    }

    private function redirectVerified($user): RedirectResponse
    {
        if ($user->role === 'archer' && $user->archer) {
            return redirect()->route('archers.show', $user->archer);
        }
        if ($user->role === 'coach' && $user->coach) {
            return redirect()->route('coaches.show', $user->coach);
        }
        return redirect()->route('archers.index');
    }
}
