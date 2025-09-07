<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;

class Payments extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $paymentId;
    public $amount;

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'amount' => 'required|numeric',
    ];

    public function render()
    {
        $query = Payment::query();
        if ($this->search) {
            $query->where('amount', 'like', "%{$this->search}%");
        }

        $payments = $query->latest()->paginate(12);

        return view('livewire.payments', [
            'payments' => $payments,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $this->paymentId = $payment->id;
        $this->amount = $payment->amount;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->paymentId) {
            $payment = Payment::withTrashed()->findOrFail($this->paymentId);
            $payment->update(['amount' => $this->amount]);
            $this->sendToast('green', 'Pago actualizado');
        } else {
            Payment::create(['amount' => $this->amount]);
            $this->sendToast('green', 'Pago creado');
        }

    $this->resetForm();
    $this->showForm = false;
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->paymentId = null;
        $this->amount = null;
        $this->showForm = false;
    }

    public function performSearch() {}

    public function clearSearch()
    {
        $this->search = '';
    }

    public function confirmAction($action, $id = null)
    {
        if (is_array($action) && isset($action['action'])) {
            $id = $action['id'] ?? $id;
            $action = $action['action'];
        }

        if ($action === 'delete') return $this->delete($id);
        if ($action === 'restore') return $this->restore($id);
        if ($action === 'forceDelete') return $this->forceDelete($id);
    }

    public function delete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $payment = Payment::findOrFail($id);
        if (method_exists($payment, 'delete')) {
            $payment->delete();
            $this->sendToast('orange', 'Pago eliminado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Payment::class, 'withTrashed')) {
            $payment = Payment::withTrashed()->findOrFail($id);
            if (method_exists($payment, 'restore')) {
                $payment->restore();
                $this->sendToast('green', 'Pago restaurado');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Payment::class, 'withTrashed')) {
            $payment = Payment::withTrashed()->findOrFail($id);
            if (method_exists($payment, 'forceDelete')) {
                $payment->forceDelete();
                $this->sendToast('red', 'Pago eliminado permanentemente');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    protected function sendToast($type, $message)
    {
        if (method_exists($this, 'dispatch')) {
            try { $this->dispatch('toast', ['type' => $type, 'message' => $message]); return; } catch (\Throwable $e) { }
        }

        if (method_exists($this, 'dispatchBrowserEvent')) {
            $this->dispatchBrowserEvent('toast', ['type' => $type, 'message' => $message]);
            return;
        }

        session()->flash('toast', ['type' => $type, 'message' => $message]);
    }
}
