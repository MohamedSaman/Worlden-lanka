<?php

namespace App\Livewire\Admin;

use App\Models\QuantityType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title("Settings")]
#[Layout('components.layouts.admin')]
class Settings extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $quantityTypes = [];
    public $newQuantityTypeName = '';
    public $newQuantityTypeCode = '';
    public $newQuantityTypeDescription = '';
    public $editingQuantityTypeId = null;
    public $editQuantityTypeName = '';
    public $editQuantityTypeCode = '';
    public $editQuantityTypeDescription = '';

    public function mount()
    {
        $this->loadQuantityTypes();
    }

    public function loadQuantityTypes()
    {
        $this->quantityTypes = QuantityType::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function addQuantityType()
    {
        $this->validate([
            'newQuantityTypeName' => 'required|string|max:100',
            'newQuantityTypeCode' => 'required|string|max:50|unique:quantity_types,code',
            'newQuantityTypeDescription' => 'nullable|string|max:500',
        ], [
            'newQuantityTypeName.required' => 'Quantity type name is required.',
            'newQuantityTypeName.unique' => 'This quantity type name already exists.',
            'newQuantityTypeCode.required' => 'Quantity type code is required.',
            'newQuantityTypeCode.unique' => 'This quantity type code already exists.',
        ]);

        try {
            DB::beginTransaction();

            QuantityType::create([
                'name' => $this->newQuantityTypeName,
                'code' => strtolower($this->newQuantityTypeCode),
                'description' => $this->newQuantityTypeDescription,
                'is_active' => true,
            ]);

            DB::commit();

            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Quantity type added successfully!'
            ]);

            $this->resetAddForm();
            $this->loadQuantityTypes();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Add quantity type error: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to add quantity type: ' . $e->getMessage()
            ]);
        }
    }

    public function startEditQuantityType($quantityTypeId)
    {
        $quantityType = QuantityType::find($quantityTypeId);
        if (!$quantityType) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Quantity type not found.'
            ]);
            return;
        }

        $this->editingQuantityTypeId = $quantityType->id;
        $this->editQuantityTypeName = $quantityType->name;
        $this->editQuantityTypeCode = $quantityType->code;
        $this->editQuantityTypeDescription = $quantityType->description;
    }

    public function updateQuantityType()
    {
        $this->validate([
            'editQuantityTypeName' => 'required|string|max:100',
            'editQuantityTypeCode' => 'required|string|max:50|unique:quantity_types,code,' . $this->editingQuantityTypeId,
            'editQuantityTypeDescription' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $quantityType = QuantityType::find($this->editingQuantityTypeId);
            if (!$quantityType) {
                throw new Exception('Quantity type not found.');
            }

            $quantityType->update([
                'name' => $this->editQuantityTypeName,
                'code' => strtolower($this->editQuantityTypeCode),
                'description' => $this->editQuantityTypeDescription,
            ]);

            DB::commit();

            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Quantity type updated successfully!'
            ]);

            $this->resetEditForm();
            $this->loadQuantityTypes();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update quantity type error: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to update quantity type: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteQuantityType($quantityTypeId)
    {
        try {
            DB::beginTransaction();

            $quantityType = QuantityType::find($quantityTypeId);
            if (!$quantityType) {
                throw new Exception('Quantity type not found.');
            }

            // Soft delete by marking as inactive
            $quantityType->update(['is_active' => false]);

            DB::commit();

            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Quantity type deleted successfully!'
            ]);

            $this->loadQuantityTypes();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Delete quantity type error: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to delete quantity type: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelEdit()
    {
        $this->resetEditForm();
    }

    private function resetAddForm()
    {
        $this->newQuantityTypeName = '';
        $this->newQuantityTypeCode = '';
        $this->newQuantityTypeDescription = '';
    }

    private function resetEditForm()
    {
        $this->editingQuantityTypeId = null;
        $this->editQuantityTypeName = '';
        $this->editQuantityTypeCode = '';
        $this->editQuantityTypeDescription = '';
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'quantityTypes' => $this->quantityTypes,
        ]);
    }
}
