<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\FollowupsDashboard;
use App\Livewire\LeadsList;
use App\Livewire\LeadCreate;
use App\Livewire\LeadEdit;
use App\Livewire\LeadShow;
use App\Livewire\OrdersList;
use App\Livewire\OrderCreate;
use App\Livewire\OrganizationEdit;
use App\Livewire\OrganizationsList;
use App\Livewire\OrganizationUsers;
use App\Livewire\RepurchaseList;
use App\Livewire\RepurchaseManagement;
use App\Livewire\UserEdit;
use App\Livewire\UserProfile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Leads Routes
    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/', LeadsList::class)->name('index');
        Route::get('/create', LeadCreate::class)->name('create');
        Route::get('/{lead}', LeadShow::class)->name('show');
        Route::get('/{lead}/edit', LeadEdit::class)->name('edit');


    });

    // Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', OrdersList::class)->name('index');
        Route::get('/create/{lead?}', OrderCreate::class)->name('create');
    });

    // Repurchase Reminders
    Route::get('/repurchase-reminders', RepurchaseList::class)->name('repurchase.index');

    // Organization Management (optional - for creating new orgs)
    Route::prefix('organizations')->name('organizations.')->group(function () {

        Route::get('/', OrganizationsList::class)->name('index');

        Route::get('/create', function () {
            return view('organizations.create');
        })->name('create');

        Route::get('/{organization}/edit', OrganizationEdit::class)
            ->name('edit');

        Route::prefix('{organization}/users')->name('users.')->group(function () {

            Route::get('/', OrganizationUsers::class)->name('index');

            Route::get('/create', UserEdit::class)->name('create');

            Route::get('/{user}/edit', UserEdit::class)->name('edit');

        });

    });

    Route::get('/organizations/create', App\Livewire\OrganizationCreate::class)
        ->name('organizations.create');

    Route::get('/organization/settings', \App\Livewire\OrganizationSettings::class)
        ->name('organization.settings');
    Route::get('/organization/edit', OrganizationEdit::class)->name('organization.edit');



    Route::get('/profile', UserProfile::class)
        ->name('profile');



    Route::get('/repurchase', RepurchaseManagement::class)->name('repurchase.index');
    Route::get('/followups', FollowupsDashboard::class)->name('followups.dashboard');



});

require __DIR__ . '/auth.php';