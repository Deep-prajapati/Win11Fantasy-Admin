<?php

namespace App\Livewire\Users;

use App\Models\Transection;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TnxShow extends Component
{
    use WithPagination;

    public $type, $search, $date;

    public function render()
    {
        $query = Transection::with('user')->orderBy('created_at', 'desc');

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->date) {
            $query->whereDate('created_at', $this->date);
        }

        $tnx = $query->paginate(env('PER_PAGE_RECORDS', 10));

        return view('livewire.users.tnx-show', [
            'tnxlist' => $tnx,
        ]);
    }
    public function exportPdf()
    {
        $query = Transection::with('user')->orderBy('created_at', 'desc');

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->date) {
            $query->whereDate('created_at', $this->date);
        }

        $transactions = $query->get();

        $pdf = Pdf::loadView('exports.transactions', [
            'transactions' => $transactions
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'transactions.pdf');
    }
}
