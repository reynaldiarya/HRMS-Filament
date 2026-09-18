<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class Login extends BaseLogin
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $this->redirectUserToPanel();

            return;
        }

        $this->form->fill();
    }

    protected function isUserAllowedToAccessPanel(Authenticatable $user): bool
    {
        if (! ($user instanceof FilamentUser)) {
            return true;
        }

        foreach (Filament::getPanels() as $panel) {
            if ($user->canAccessPanel($panel)) {
                return true;
            }
        }

        return false;
    }

    public function authenticate(): ?LoginResponseContract
    {
        $response = parent::authenticate();

        if (! $response) {
            return null;
        }

        /** @var User $user */
        $user = Filament::auth()->user();
        $targetUrl = $this->getSafeIntendedUrl($user);

        return new class($targetUrl) implements LoginResponseContract
        {
            public function __construct(
                protected string $targetUrl,
            ) {}

            public function toResponse($request): RedirectResponse|Redirector
            {
                return redirect()->to($this->targetUrl);
            }
        };
    }

    protected function redirectUserToPanel(): void
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        redirect()->to($this->getSafeIntendedUrl($user));
    }

    protected function getTargetUrlForUser(User $user): string
    {
        return match (true) {
            $user->hasAnyRole(['super_admin', 'admin']) => Filament::getPanel('admin')->getUrl(),
            $user->hasRole('hr') => Filament::getPanel('hr')->getUrl(),
            $user->hasRole('employee') => Filament::getPanel('employee')->getUrl(),
            default => '/',
        };
    }

    protected function getSafeIntendedUrl(User $user): string
    {
        $targetUrl = $this->getTargetUrlForUser($user);
        $intended = session()->get('url.intended');

        if ($intended) {
            if (str_contains($intended, '/admin') && ! $user->hasAnyRole(['super_admin', 'admin'])) {
                session()->forget('url.intended');

                return $targetUrl;
            }

            if (str_contains($intended, '/hr') && ! $user->hasRole('hr')) {
                session()->forget('url.intended');

                return $targetUrl;
            }

            if (str_contains($intended, '/employee') && ! $user->hasRole('employee')) {
                session()->forget('url.intended');

                return $targetUrl;
            }
        }

        return $intended ?? $targetUrl;
    }
}
