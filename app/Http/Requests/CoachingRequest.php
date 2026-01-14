<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CoachingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'jenis_diklat' => 'required|numeric',
            'nama_diklat' => 'required|string|max:255',
            'institusi_penyelenggara' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'dokumen_pelaksanaan' => 'nullable|file|mimes:pdf|max:900',
            'dokumen_evaluasi' => 'nullable|file|mimes:pdf|max:900',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_diklat.required' => 'Jenis diklat wajib dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'dokumen_pelaksanaan.mimes' => 'Dokumen pelaksanaan harus berupa PDF.',
            'dokumen_evaluasi.mimes' => 'Dokumen evaluasi harus berupa PDF.',
        ];
    }
}
