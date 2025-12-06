<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestAttendance;
use App\Http\Requests\Attendance\CheckInAttendanceRequest;
use App\Http\Requests\Attendance\CheckOutAttendanceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Check-in attendance untuk quest
     */
    public function checkIn(CheckInAttendanceRequest $request, $questId)
    {
        $user = Auth::user();
        $quest = Quest::findOrFail($questId);

        // Cek quest dalam periode quest
        $now = now();
        if (!($now->between($quest->quest_start_at, $quest->quest_end_at))) {
            return back()->with('error', 'Periode quest sudah berakhir atau belum dimulai.');
        }

        // Cek user sudah terdaftar di quest
        $participation = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->first();

        if (!$participation) {
            return back()->with('error', 'Anda belum terdaftar di quest ini.');
        }

        // Get last attendance record
        $lastAttendance = QuestAttendance::where('quest_participant_id', $participation->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Validasi: hanya bisa check-in jika belum ada record atau last record adalah check-out
        if ($lastAttendance && $lastAttendance->type === 'CHECK_IN') {
            return back()->with('error', 'Anda sudah check-in. Silakan check-out terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            // Upload foto bukti
            $photoUrl = null;
            if ($request->hasFile('proof_photo')) {
                $proofPhoto = $request->file('proof_photo');
                $photoName = time() . '_' . $proofPhoto->getClientOriginalName();
                $proofPhoto->storeAs('public/AttendanceStorage/' . $photoName);
                $photoUrl = '/storage/AttendanceStorage/' . $photoName;
            }

            // Calculate distance using Haversine formula
            $distance = null;
            $isValidLocation = false;
            
            if ($quest->latitude && $quest->longitude) {
                $earthRadius = 6371000; // meters
                $lat1Rad = deg2rad($quest->latitude);
                $lat2Rad = deg2rad($request->latitude);
                $deltaLat = deg2rad($request->latitude - $quest->latitude);
                $deltaLon = deg2rad($request->longitude - $quest->longitude);

                $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                     cos($lat1Rad) * cos($lat2Rad) *
                     sin($deltaLon / 2) * sin($deltaLon / 2);

                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
                
                // Validasi radius (default 100m jika tidak ada)
                $allowedRadius = $quest->radius_meter ?? 100;
                $isValidLocation = $distance <= $allowedRadius;
            }

            // Create attendance record
            QuestAttendance::create([
                'quest_participant_id' => $participation->id,
                'type' => 'CHECK_IN',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'proof_photo_url' => $photoUrl,
                'notes' => $request->notes,
                'distance_from_quest_location' => $distance,
                'is_valid_location' => $isValidLocation,
            ]);

            DB::commit();

            $message = 'Berhasil check-in!';
            
            if (!$isValidLocation && $distance !== null) {
                $allowedRadius = $quest->radius_meter ?? 100;
                $message .= " PERINGATAN: Lokasi Anda berjarak " . round($distance) . " meter dari lokasi quest (batas: " . $allowedRadius . " meter). Attendance tetap tercatat.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error check-in attendance: ' . $e->getMessage());
            return back()->with('error', 'Gagal check-in: ' . $e->getMessage());
        }
    }

    /**
     * Check-out attendance untuk quest
     */
    public function checkOut(CheckOutAttendanceRequest $request, $questId)
    {
        $user = Auth::user();
        $quest = Quest::findOrFail($questId);

        // Cek quest dalam periode quest
        $now = now();
        if (!($now->between($quest->quest_start_at, $quest->quest_end_at))) {
            return back()->with('error', 'Periode quest sudah berakhir atau belum dimulai.');
        }

        // Cek user sudah terdaftar di quest
        $participation = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->first();

        if (!$participation) {
            return back()->with('error', 'Anda belum terdaftar di quest ini.');
        }

        // Get last attendance record
        $lastAttendance = QuestAttendance::where('quest_participant_id', $participation->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Validasi: hanya bisa check-out jika last record adalah check-in
        if (!$lastAttendance || $lastAttendance->type === 'CHECK_OUT') {
            return back()->with('error', 'Anda belum check-in. Silakan check-in terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            // Upload foto bukti
            $photoUrl = null;
            if ($request->hasFile('proof_photo')) {
                $proofPhoto = $request->file('proof_photo');
                $photoName = time() . '_' . $proofPhoto->getClientOriginalName();
                $proofPhoto->storeAs('public/AttendanceStorage/' . $photoName);
                $photoUrl = '/storage/AttendanceStorage/' . $photoName;
            }

            // Calculate distance using Haversine formula
            $distance = null;
            $isValidLocation = false;
            
            if ($quest->latitude && $quest->longitude) {
                $earthRadius = 6371000; // meters
                $lat1Rad = deg2rad($quest->latitude);
                $lat2Rad = deg2rad($request->latitude);
                $deltaLat = deg2rad($request->latitude - $quest->latitude);
                $deltaLon = deg2rad($request->longitude - $quest->longitude);

                $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                     cos($lat1Rad) * cos($lat2Rad) *
                     sin($deltaLon / 2) * sin($deltaLon / 2);

                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
                
                // Validasi radius (default 100m jika tidak ada)
                $allowedRadius = $quest->radius_meter ?? 100;
                $isValidLocation = $distance <= $allowedRadius;
            }

            // Create attendance record
            QuestAttendance::create([
                'quest_participant_id' => $participation->id,
                'type' => 'CHECK_OUT',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'proof_photo_url' => $photoUrl,
                'notes' => $request->notes,
                'distance_from_quest_location' => $distance,
                'is_valid_location' => $isValidLocation,
            ]);

            DB::commit();

            $message = 'Berhasil check-out!';
            
            if (!$isValidLocation && $distance !== null) {
                $allowedRadius = $quest->radius_meter ?? 100;
                $message .= " PERINGATAN: Lokasi Anda berjarak " . round($distance) . " meter dari lokasi quest (batas: " . $allowedRadius . " meter). Attendance tetap tercatat.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error check-out attendance: ' . $e->getMessage());
            return back()->with('error', 'Gagal check-out: ' . $e->getMessage());
        }
    }

    /**
     * Get my attendance records untuk quest
     */
    public function myRecords($questId)
    {
        $user = Auth::user();
        $quest = Quest::findOrFail($questId);

        // Cek user sudah terdaftar di quest
        $participation = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->first();

        if (!$participation) {
            return back()->with('error', 'Anda belum terdaftar di quest ini.');
        }

        // Get all attendance records
        $attendances = QuestAttendance::where('quest_participant_id', $participation->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.attendance.my-records', [
            'quest' => $quest,
            'participation' => $participation,
            'attendances' => $attendances,
        ]);
    }
}
