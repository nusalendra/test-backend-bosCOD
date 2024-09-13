<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\LogTransfer;
use App\Models\RekeningAdmin;
use App\Models\RekeningPengguna;
use App\Models\TransaksiTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nilai_transfer' => 'required|numeric|min:0',
            'bank_tujuan' => 'required|string|exists:banks,nama',
            'rekening_tujuan' => 'required|string|min:10|max:15|exists:rekening_penggunas,nomor_rekening',
            'atasnama_tujuan' => 'required|string',
            'bank_pengirim' => 'required|string',
        ], [
            'nilai_transfer.min' => 'Nominal transfer tidak boleh kurang dari Rp. 0.',
            'bank_tujuan.exists' => 'Bank tujuan yang dipilih tidak ditemukan.',
            'rekening_tujuan.exists' => 'Rekening tujuan yang dipilih tidak ada di dalam database.',
            'rekening_tujuan.min' => 'Rekening tujuan tidak boleh kurang dari 10 digit.',
            'rekening_tujuan.max' => 'Rekening tujuan tidak boleh lebih dari 15 digit.'
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$this->cekRekeningPengirim($request, $validator)) {
                return;
            }
            $this->cekRekeningTujuan($request, $validator);
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        try {
            $idTransaksi = $this->generateTransactionId();
            $user = Auth::user();

            $bankPengirim = Bank::where('nama', $request->input('bank_pengirim'))->first();
            $bankTujuan = Bank::where('nama', $request->input('bank_tujuan'))->first();

            if (!$bankPengirim || !$bankTujuan) {
                throw new \Exception('Bank pengirim atau bank tujuan tidak valid.');
            }

            $pengirim = RekeningPengguna::where('user_id', $user->id)
                ->where('bank_id', $bankPengirim->id)
                ->first();

            $penerima = RekeningPengguna::where('nomor_rekening', $request->input('rekening_tujuan'))->first();

            if (!$pengirim || !$penerima) {
                throw new \Exception('Rekening pengirim atau penerima tidak ditemukan.');
            }

            $bankPerantara = RekeningAdmin::where('bank_id', $bankTujuan->id)->first();
            if (!$bankPerantara) {
                throw new \Exception('Tidak ditemukan rekening admin yang sesuai untuk bank tujuan.');
            }

            $transaksi = new TransaksiTransfer();
            $transaksi->id_transaksi = $idTransaksi;
            $transaksi->nilai_transfer = $request->input('nilai_transfer');
            $transaksi->pengirim_id = $pengirim->id;
            $transaksi->penerima_id = $penerima->id;
            $transaksi->bank_perantara_id = $bankPerantara->id;

            $transaksi->kode_unik = $this->generateUniqueCode();
            $transaksi->total_transfer = $transaksi->nilai_transfer + $transaksi->kode_unik;

            $transaksi->save();

            $logTransfer = new LogTransfer();
            $logTransfer->transaksi_id = $transaksi->id;
            $logTransfer->status = 'Success';
            $logTransfer->save();

            $response = [
                'id_transaski' => $transaksi->id_transaksi,
                'nilai_transfer' => $transaksi->nilai_transfer,
                'kode_unik' => $transaksi->kode_unik,
                'biaya_admin' => $transaksi->biaya_admin !== null ? $transaksi->biaya_admin : 0,
                'total_transfer' => $transaksi->total_transfer,
                'bank_perantara' => $bankPerantara->bank->nama,
                'rekening_perantara' => $bankPerantara->nomor_rekening,
                'berlaku_hingga' => $transaksi->berlaku_hingga
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi transfer berhasil',
                'response' => $response
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kesalahan pada database: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi transfer gagal: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Fungsi untuk mengecek rekening pengirim
     */
    private function cekRekeningPengirim($request, $validator)
    {
        $user = Auth::user();
        $bankPengirim = $request->input('bank_pengirim');

        $rekeningPengirim = RekeningPengguna::where('user_id', $user->id)
            ->whereHas('bank', function ($query) use ($bankPengirim) {
                $query->where('nama', $bankPengirim);
            })
            ->first();

        if (!$rekeningPengirim) {
            $validator->errors()->add('bank_pengirim', 'Bank pengirim tidak sesuai dengan data rekening anda.');
            return false;
        }

        if ($rekeningPengirim->nomor_rekening === $request->input('rekening_tujuan')) {
            $validator->errors()->add('rekening_tujuan', 'Rekening tujuan tidak boleh sama dengan rekening pengirim.');
            return false;
        }

        return true;
    }

    /**
     * Fungsi untuk mengecek rekening tujuan
     */
    private function cekRekeningTujuan($request, $validator)
    {
        $rekeningTujuan = $request->input('rekening_tujuan');
        $atasNamaTujuan = $request->input('atasnama_tujuan');
        $bankTujuan = $request->input('bank_tujuan');

        $rekeningPenerima = RekeningPengguna::where('nomor_rekening', $rekeningTujuan)->first();

        if (!$rekeningPenerima) {
            $validator->errors()->add('rekening_tujuan', 'Rekening tujuan tidak ditemukan.');
            return;
        }

        if ($rekeningPenerima->user_id === Auth::user()->id) {
            $validator->errors()->add('rekening_tujuan', 'Tidak bisa melakukan transfer ke rekening sendiri.');
        }

        $bank = Bank::where('id', $rekeningPenerima->bank_id)->where('nama', $bankTujuan)->first();
        if (!$bank) {
            $validator->errors()->add('bank_tujuan', 'Bank tujuan tidak sesuai dengan data rekening.');
        }

        if ($rekeningPenerima->atas_nama !== $atasNamaTujuan) {
            $validator->errors()->add('atasnama_tujuan', 'Nama pemilik rekening tidak cocok dengan data di database.');
        }
    }

    /**
     * Fungsi untuk generate ID Transaksi
     */
    private function generateTransactionId()
    {
        $today = Carbon::now()->format('ymd');
        $counter = TransaksiTransfer::where('id_transaksi', 'like', 'TF' . $today . '%')->count() + 1;
        return 'TF' . $today . str_pad($counter, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Fungsi untuk generate kode unik
     */
    private function generateUniqueCode()
    {
        do {
            $kodeUnik = random_int(100, 999);
        } while (TransaksiTransfer::where('kode_unik', $kodeUnik)->exists());

        return $kodeUnik;
    }
}
