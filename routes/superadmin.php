use App\Http\Controllers\SuperAdmin\SchoolController;

// School Management
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::resource('schools', SchoolController::class);
});