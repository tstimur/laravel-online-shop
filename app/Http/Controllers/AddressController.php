<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddressStoreRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(AddressStoreRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $hasDefault = Address::query()
            ->where('user_id', $user->id)
            ->where('is_default', true)
            ->exists();

        $address = new Address($request->validated());
        $address->user_id = $user->id;
        $address->is_default = !$hasDefault;
        $address->save();

        return redirect()
            ->route('profile.form')
            ->with('success', 'Address added.');
    }

    public function update(Address $address, AddressUpdateRequest $request): RedirectResponse
    {
        $this->ensureOwner($address);

        $address->fill($request->validated());
        $address->save();

        return redirect()
            ->route('profile.form')
            ->with('success', 'Address updated.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->ensureOwner($address);

        DB::transaction(function () use ($address): void {
            $userId = $address->user_id;
            $wasDefault = $address->is_default;

            $address->delete();

            if ($wasDefault) {
                $nextDefault = Address::query()
                    ->where('user_id', $userId)
                    ->orderByDesc('created_at')
                    ->first();

                if ($nextDefault) {
                    $nextDefault->is_default = true;
                    $nextDefault->save();
                }
            }
        });

        return redirect()
            ->route('profile.form')
            ->with('success', 'Address deleted.');
    }

    public function setDefault(Address $address): RedirectResponse
    {
        $this->ensureOwner($address);

        DB::transaction(function () use ($address): void {
            Address::query()
                ->where('user_id', $address->user_id)
                ->update(['is_default' => false]);

            $address->is_default = true;
            $address->save();
        });

        return redirect()
            ->route('profile.form')
            ->with('success', 'Default address updated.');
    }

    private function ensureOwner(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(404);
        }
    }
}
