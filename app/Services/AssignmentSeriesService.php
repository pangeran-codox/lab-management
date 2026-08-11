<?php
namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentOpenPeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignmentSeriesService
{
    /**
     * Buat tugas baru sebagai lanjutan dari tugas yang sudah ada.
     * Tugas lanjutan otomatis dibuat TERTUTUP (is_active = false) —
     * guru harus buka aksesnya manual lewat toggleAccess() saat siap.
     *
     * @param  array  $newData  title, description, deadline, attachment_* (opsional)
     */
    public function continueFrom(Assignment $previous, array $newData): Assignment
    {
        return DB::transaction(function () use ($previous, $newData) {
            // Kalau tugas sebelumnya belum jadi bagian dari series manapun,
            // mulai series baru dari tugas ini (jadi session_number 1).
            if (!$previous->series_id) {
                $previous->update([
                    'series_id'      => (string) Str::uuid(),
                    'session_number' => 1,
                ]);
                $previous->refresh();
            }

            return Assignment::create([
                'title'           => $newData['title'],
                'description'     => $newData['description'] ?? null,
                'deadline'        => $newData['deadline'],
                'attachment_path' => $newData['attachment_path'] ?? null,
                'attachment_name' => $newData['attachment_name'] ?? null,
                'attachment_size' => $newData['attachment_size'] ?? null,

                // Inherit dari tugas sebelumnya — satu series = satu guru, satu kelas, satu mapel
                'teacher_id'      => $previous->teacher_id,
                'organization_id' => $previous->organization_id,
                'class_name'      => $previous->class_name,
                'subject_name'    => $newData['subject_name'] ?? $previous->subject_name,

                'series_id'       => $previous->series_id,
                'session_number'  => $previous->session_number + 1,
                'is_active'       => false,
            ]);
        });
    }

    /**
     * Buka kembali tugas (yang sama) untuk pertemuan berikutnya.
     * Menutup period aktif sebelumnya (kalau ada) dan mencatat round baru.
     */
    public function reopen(Assignment $assignment, Carbon $newDeadline, bool $allowResubmit = false): AssignmentOpenPeriod
    {
        return DB::transaction(function () use ($assignment, $newDeadline, $allowResubmit) {
            $assignment->openPeriods()->whereNull('closed_at')->update(['closed_at' => now()]);

            $roundNumber = ($assignment->openPeriods()->max('round_number') ?? 0) + 1;

            $period = $assignment->openPeriods()->create([
                'round_number'   => $roundNumber,
                'opened_at'      => now(),
                'deadline'       => $newDeadline,
                'allow_resubmit' => $allowResubmit,
            ]);

            // Sinkronkan ke kolom utama supaya isExpired() & tampilan countdown
            // yang sudah ada di blade tidak perlu diubah sama sekali.
            $assignment->update([
                'is_active' => true,
                'deadline'  => $newDeadline,
            ]);

            return $period;
        });
    }

    /**
     * Toggle buka/tutup akses tugas ke siswa (tanpa mengubah deadline/round).
     */
    public function toggleAccess(Assignment $assignment): Assignment
    {
        $assignment->update(['is_active' => !$assignment->is_active]);

        return $assignment;
    }

    public function normalizeName(string $name): string
    {
        return Str::of($name)->lower()->squish()->toString();
    }

    /**
     * Cek apakah siswa dengan nama tertentu sudah pernah submit ke tugas ini
     * (dicocokkan dengan normalisasi nama, bukan exact match).
     */
    public function hasAlreadySubmitted(Assignment $assignment, string $studentName): bool
    {
        $target = $this->normalizeName($studentName);

        return $assignment->submissions()
            ->get(['student_name'])
            ->contains(fn ($sub) => $this->normalizeName($sub->student_name) === $target);
    }

    /**
     * Tandai tiap submission dengan flag `continued_from_previous`
     * kalau nama siswanya (dinormalisasi) juga muncul di submission
     * pertemuan sebelumnya dalam series yang sama.
     */
    public function decorateWithPreviousSession(Assignment $assignment, Collection $submissions): Collection
    {
        $previous = $assignment->previousSession();

        if (!$previous) {
            return $submissions->map(function ($sub) {
                $sub->continued_from_previous = false;
                return $sub;
            });
        }

        $previousNames = $previous->submissions()
            ->pluck('student_name')
            ->map(fn ($n) => $this->normalizeName($n))
            ->flip();

        return $submissions->map(function ($sub) use ($previousNames) {
            $sub->continued_from_previous = isset($previousNames[$this->normalizeName($sub->student_name)]);
            return $sub;
        });
    }
}