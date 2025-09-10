<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.admin')]
#[Title('Manage Customer')]
class ManageCustomer extends Component
{
    use WithFileUploads, WithPagination;

    public $importFile;
    public $name;
    public $contactNumber;
    public $address;
    public $email;
    public $customerType = 'wholesale';
    public $businessName; // Corrected type
    public $search = '';

    public $editCustomerId;
    public $editName;
    public $editContactNumber;
    public $editAddress;
    public $editEmail;
    public $editCustomerType;
    public $editBusinessName; // Corrected typo

    public $deleteId;

    public function createCustomer()
    {
        $this->reset(['name', 'contactNumber', 'address', 'email', 'customerType', 'businessName']);
        $this->js("$('#createCustomerModal').modal('show')");
    }

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'contactNumber' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'customerType' => 'nullable',
            'businessName' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            Customer::create([
                'name' => $this->name,
                'phone' => $this->contactNumber,
                'address' => $this->address,
                'email' => $this->email,
                'type' => $this->customerType,
                'business_name' => $this->businessName,
            ]);

            $this->js("Swal.fire('Success!', 'Customer Created Successfully', 'success')");
            $this->reset(['name', 'contactNumber', 'address', 'email', 'customerType', 'businessName']);
            $this->js('$("#createCustomerModal").modal("hide")');
        } catch (Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
            $this->js("Swal.fire('Error!', 'Failed to create customer: " . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function editCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $this->editCustomerId = $customer->id;
        $this->editName = $customer->name;
        $this->editContactNumber = $customer->phone;
        $this->editBusinessName = $customer->business_name;
        $this->editCustomerType = $customer->type;
        $this->editAddress = $customer->address;
        $this->editEmail = $customer->email;

        $this->dispatch('open-edit-modal');
    }

    public function updateCustomer()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editContactNumber' => 'nullable|string|max:20',
            'editEmail' => 'nullable|email|max:255|unique:customers,email,' . $this->editCustomerId,
            'editCustomerType' => 'nullable',
            'editBusinessName' => 'nullable|string|max:255',
            'editAddress' => 'nullable|string|max:500',
        ]);

        try {
            $customer = Customer::findOrFail($this->editCustomerId);
            $customer->update([
                'name' => $this->editName,
                'phone' => $this->editContactNumber,
                'business_name' => $this->editBusinessName,
                'type' => $this->editCustomerType,
                'address' => $this->editAddress,
                'email' => $this->editEmail,
            ]);

            $this->js("Swal.fire('Success!', 'Customer Updated Successfully', 'success')");
            $this->reset(['editCustomerId', 'editName', 'editContactNumber', 'editAddress', 'editEmail', 'editCustomerType', 'editBusinessName']);
            $this->js('$("#editCustomerModal").modal("hide")');
        } catch (Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            $this->js("Swal.fire('Error!', 'Failed to update customer: " . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-delete');
    }

    #[On('confirmDelete')]
    public function deleteCustomer()
    {
        try {
            $customer = Customer::findOrFail($this->deleteId);
            $customer->delete();
            $this->reset('deleteId');
            $this->js("Swal.fire('Success!', 'Customer deleted successfully.', 'success')");
        } catch (Exception $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());
            $this->js("Swal.fire('Error!', 'Failed to delete customer: " . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function exportCustomers(): StreamedResponse
    {
        $fileName = 'customers_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Name',
                'Phone',
                'Email',
                'Type',
                'Business Name',
                'Address',
                'Created At',
            ]);

            $customers = Customer::all();
            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->id,
                    $customer->name,
                    $customer->phone,
                    $customer->email,
                    $customer->type,
                    $customer->business_name,
                    $customer->address,
                    $customer->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCustomers()
    {
        $this->reset('importFile');
        $this->js("$('#importCustomerModal').modal('show')");
    }

    public function handleImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            $path = $this->importFile->store('imports');
            // TODO: Implement actual import logic using Laravel Excel
            $this->js("Swal.fire('Success!', 'Customers imported successfully.', 'success')");
            $this->reset('importFile');
            $this->js("$('#importCustomerModal').modal('hide')");
        } catch (Exception $e) {
            Log::error('Error importing customers: ' . $e->getMessage());
            $this->js("Swal.fire('Error!', 'Import failed: " . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('business_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-customer', [
            'customers' => $customers,
        ]);
    }
}