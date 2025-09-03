<?php

namespace App\Livewire\Admin;

use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Product Categories')]
class Category extends Component
{
    use WithPagination;

    public $name, $description, $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $editingCategoryId = null;
    public $deletingCategoryId = null;

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'description' => 'nullable|string|max:500',
    ];

    // Reset pagination when search updates
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleAddModal()
    {
        $this->reset(['name', 'description']);
        $this->resetValidation();
        $this->showAddModal = true;
    }

    public function toggleEditModal($categoryId)
    {
        $category = ProductCategory::findOrFail($categoryId);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->showEditModal = true;
    }

    public function toggleDeleteModal($categoryId)
    {
        $this->deletingCategoryId = $categoryId;
        $this->showDeleteModal = true;
    }

    public function save()
    {
        $this->validate();

        ProductCategory::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->showAddModal = false;
        $this->reset(['name', 'description']);
        session()->flash('message', 'Category added successfully!');
    }

    public function update()
    {
        $this->validate();

        $category = ProductCategory::findOrFail($this->editingCategoryId);
        $category->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->showEditModal = false;
        $this->reset(['name', 'description', 'editingCategoryId']);
        session()->flash('message', 'Category updated successfully!');
    }

    public function delete()
    {
        $category = ProductCategory::findOrFail($this->deletingCategoryId);
        $category->delete();

        $this->showDeleteModal = false;
        $this->deletingCategoryId = null;
        session()->flash('message', 'Category deleted successfully!');
    }

    public function render()
    {
        $categories = ProductCategory::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('livewire.admin.category', compact('categories'));
    }
}
