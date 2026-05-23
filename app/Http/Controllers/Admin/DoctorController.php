<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
{
    public function index(Request $request): Response
    {
        $doctors = Doctor::query()
            ->with(['schedules' => fn ($q) => $q->orderBy('day_of_week')->orderBy('start_time')])
            ->when($request->string('q')->toString(), function ($q, string $term): void {
                $q->where(function ($inner) use ($term): void {
                    $inner->where('name', 'ILIKE', "%{$term}%")
                        ->orWhere('specialization', 'ILIKE', "%{$term}%")
                        ->orWhere('polyclinic', 'ILIKE', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/doctors/Index', [
            'doctors' => $doctors,
            'filters' => ['q' => $request->string('q')->toString()],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/doctors/Form', [
            'doctor' => null,
        ]);
    }

    public function store(DoctorRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $doctor = Doctor::create($request->safe()->except('schedules'));

            foreach ((array) $request->validated('schedules', []) as $schedule) {
                $doctor->schedules()->create($schedule);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Dokter berhasil dibuat.']);

        return to_route('admin.doctors.index');
    }

    public function edit(Doctor $doctor): Response
    {
        $doctor->load(['schedules' => fn ($q) => $q->orderBy('day_of_week')->orderBy('start_time')]);

        return Inertia::render('admin/doctors/Form', [
            'doctor' => $doctor,
        ]);
    }

    public function update(DoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        DB::transaction(function () use ($request, $doctor): void {
            $doctor->update($request->safe()->except('schedules'));

            $doctor->schedules()->delete();

            foreach ((array) $request->validated('schedules', []) as $schedule) {
                $doctor->schedules()->create($schedule);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Dokter berhasil diperbarui.']);

        return to_route('admin.doctors.index');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Dokter dihapus.']);

        return back();
    }
}
