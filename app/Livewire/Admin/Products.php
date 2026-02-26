<?php

namespace App\Livewire\Admin;

use App\Models\ProductCategory;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use TheSeer\Tokenizer\Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Product Management')]
class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $product_code, $category_id, $product_name, $supplier_price, $selling_price, $sold, $status;
    public $search = '';
    public $categories;
    public $stock_quantity = 0;
    public $damage_quantity = 0;

    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showImportModal = false;
    public $excel_file;

    public $editingProductId = null;
    public $deletingProductId = null;
    public $deletingProductName = '';

    public $customer_fields = []; // Dynamically generated key-value pairs
    public $fieldKeys = [];       // All field keys globally
    public $newFieldKey = '';
    public $showAddFieldModal = false;
    public $showDeleteFieldModal = false;
    public $deleteFieldKey;

    public $showViewModal = false;
    public $viewProductCode;
    public $viewCategoryName;
    public $viewProductName;
    public $viewSupplierPrice;
    public $viewSellingPrice;
    public $viewStockQuantity;
    public $viewDamageQuantity;
    public $viewSoldQuantity;
    public $viewStatus;
    public $viewCustomerFields = [];

    protected $rules = [
        'category_id' => 'required|exists:product_categories,id',
        'product_name' => 'required|string|min:3|max:255',
        'supplier_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'damage_quantity' => 'required|integer|min:0',
        'customer_fields.*.key' => 'required|string|max:255',
        'customer_fields.*.value' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->categories = ProductCategory::all();

        // Get all field keys dynamically from existing products
        $this->fieldKeys = ProductDetail::whereNotNull('customer_field')
            ->pluck('customer_field')
            ->filter()
            ->map(fn($field) => array_keys($field ?? []))
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset pagination when search changes
    }

    public function manageField($action, $field = null)
    {
        try {
            DB::beginTransaction();

            if ($action === 'add') {
                // Validate newFieldKey input
                $this->validate([
                    'newFieldKey' => 'required|string',
                ], [
                    'newFieldKey.required' => 'Please enter at least one field name.',
                ]);

                $newFields = array_map('trim', explode(',', $this->newFieldKey));

                foreach ($newFields as $f) {
                    if (!$f) continue;

                    $f = ucwords(strtolower($f));

                    // Add to all products
                    foreach (ProductDetail::all() as $product) {
                        $fields = $product->customer_field ?? [];
                        $fields[$f] = null;
                        $product->update(['customer_field' => $fields]);
                    }

                    // Add to Livewire array if not exists
                    if (!in_array($f, $this->fieldKeys)) {
                        $this->fieldKeys[] = $f;
                    }
                }

                $this->newFieldKey = '';
                $message = 'Field(s) added successfully';
            } elseif ($action === 'delete' && $field) {
                if (!in_array($field, $this->fieldKeys)) return;

                $products = ProductDetail::whereNotNull('customer_field')->get();
                foreach ($products as $product) {
                    $fields = is_array($product->customer_field) ? $product->customer_field : [];
                    if (array_key_exists($field, $fields)) {
                        unset($fields[$field]);
                        $product->customer_field = empty($fields) ? null : $fields;
                        $product->save();
                    }
                }

                // Remove from Livewire array
                $this->fieldKeys = array_values(array_filter($this->fieldKeys, fn($f) => $f !== $field));
                $message = 'Field deleted successfully';
            }

            DB::commit();

            $this->js("Swal.fire('Success!', '" . addslashes($message) . "', 'success')");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->js("Swal.fire('Error!', '" . addslashes('Error: ' . $e->getMessage()) . "', 'error')");
        }
    }

    public function toggleAddModal()
    {
        $this->showAddModal = !$this->showAddModal;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->showViewModal = false;

        if ($this->showAddModal) {
            $this->customer_fields = [];
            foreach ($this->fieldKeys as $key) {
                $this->customer_fields[] = ['key' => $key, 'value' => ''];
            }
        } else {
            $this->resetFields();
        }
    }

    public function toggleEditModal()
    {
        $this->showEditModal = !$this->showEditModal;
        $this->showAddModal = false;
        $this->showDeleteModal = false;
        $this->showViewModal = false;
        if (!$this->showEditModal) {
            $this->resetFields();
        }
    }

    public function save()
    {
        $this->rules['product_code'] = 'nullable';
        $this->validate();

        // Get product name and category name first 2 letters (uppercase)
        $productPrefix = strtoupper(substr($this->product_name, 0, 2));
        $categoryName = ProductCategory::find($this->category_id)->name ?? 'XX';
        $categoryPrefix = strtoupper(substr($categoryName, 0, 2));



        $customerField = [];
        foreach ($this->customer_fields as $field) {
            if (!empty($field['key'])) {
                $customerField[$field['key']] = $field['value'] ?? null;
            }
        }

        try {
            $totalQuantity = $this->stock_quantity;
            // Create product without product_code first
            $product = ProductDetail::create([
                'category_id' => $this->category_id,
                'product_name' => $this->product_name,
                'supplier_price' => $this->supplier_price,
                'selling_price' => $this->selling_price,
                'stock_quantity' => $totalQuantity - $this->damage_quantity,
                'damage_quantity' => $this->damage_quantity,
                'status' => 'Active',
                'customer_field' => $customerField,
            ]);

            // Generate product_code using the newly created product ID
            $generatedCode = $productPrefix . $categoryPrefix . '0' . $product->id;

            // Update product with generated code
            $product->update(['product_code' => $generatedCode]);

            $this->resetFields();
            $this->showAddModal = false;
            $this->js("Swal.fire('Success!', 'Product Created Successfully', 'success')");
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . $e->getMessage() . "', 'error')");
        }
    }


    public function editProduct($productId)
    {
        $product = ProductDetail::findOrFail($productId);

        $this->editingProductId = $product->id;
        $this->category_id = $product->category_id;
        $this->product_name = $product->product_name;
        $this->supplier_price = $product->supplier_price;
        $this->selling_price = $product->selling_price;
        $this->stock_quantity = $product->stock_quantity;
        $this->damage_quantity = $product->damage_quantity;
        $this->sold = $product->sold;
        $this->status = $product->status;

        // Convert customer_field array into editable rows
        $this->customer_fields = [];
        if (!empty($product->customer_field)) {
            foreach ($product->customer_field as $key => $value) {
                $this->customer_fields[] = [
                    'key' => $key,
                    'value' => $value ?? ''
                ];
            }
        }

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->rules['product_code'] = 'nullable|string|max:255|unique:product_details,product_code,' . $this->editingProductId;
        $this->validate();

        $customerField = [];
        foreach ($this->customer_fields as $field) {
            if (!empty($field['key'])) {
                $customerField[$field['key']] = $field['value'] ?? null;
            }
        }

        $product = ProductDetail::findOrFail($this->editingProductId);

        // Generate product code if name/category changed or code is null
        $generatedCode = $product->product_code; // default = existing

        if (
            empty($product->product_code) ||
            $this->product_name !== $product->product_name ||
            $this->category_id !== $product->category_id
        ) {
            $productPrefix = strtoupper(substr($this->product_name, 0, 2));
            $categoryName = ProductCategory::find($this->category_id)->name ?? 'XX';
            $categoryPrefix = strtoupper(substr($categoryName, 0, 2));
            $generatedCode = $productPrefix . $categoryPrefix . '0' . $product->id;
        }

        $product->update([
            'category_id'      => $this->category_id,
            'product_name'     => $this->product_name,
            'supplier_price'   => $this->supplier_price,
            'selling_price'    => $this->selling_price,
            'stock_quantity'   => $this->stock_quantity,
            'damage_quantity'  => $this->damage_quantity,
            'sold'             => $this->sold,
            'status'           => $this->status,
            'customer_field'   => $customerField,
            'product_code'     => $generatedCode,
        ]);

        $this->resetFields();
        $this->showEditModal = false;
        $this->js("Swal.fire('Success!', 'Product updated successfully!', 'success')");
    }


    public function confirmDelete($productId)
    {
        $product = ProductDetail::findOrFail($productId);
        $this->deletingProductId = $product->id;
        $this->deletingProductName = $product->product_name;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $product = ProductDetail::findOrFail($this->deletingProductId);
        $product->delete();

        $this->showDeleteModal = false;
        $this->deletingProductId = null;
        $this->deletingProductName = '';

        $this->js("Swal.fire('Success!', 'Product deleted successfully!', 'success')");
    }

    public function viewProduct($productId)
    {
        $product = ProductDetail::with('category')->findOrFail($productId);

        $this->viewProductCode = $product->product_code;
        $this->viewCategoryName = $product->category->name ?? 'N/A';
        $this->viewProductName = $product->product_name;
        $this->viewSupplierPrice = $product->supplier_price;
        $this->viewSellingPrice = $product->selling_price;
        $this->viewStockQuantity = $product->stock_quantity;
        $this->viewDamageQuantity = $product->damage_quantity;
        $this->viewSoldQuantity = $product->sold;
        $this->viewStatus = $product->status;
        $this->viewCustomerFields = $product->customer_field ?? [];

        $this->showViewModal = true;
    }

    public function toggleDeleteModal()
    {
        $this->showDeleteModal = !$this->showDeleteModal;
    }

    public function resetFields()
    {
        $this->reset([
            'product_code',
            'category_id',
            'product_name',
            'supplier_price',
            'selling_price',
            'stock_quantity',
            'damage_quantity',
            'sold',
            'status',
            'customer_fields',
            'editingProductId'
        ]);

        $this->resetValidation();
    }

    public function toggleImportModal()
    {
        $this->showImportModal = !$this->showImportModal;
        if (!$this->showImportModal) {
            $this->excel_file = null;
        }
    }

    public function downloadTemplate(): StreamedResponse
    {
        $fileName = 'product_import_template.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Define your CSV headers for import
            fputcsv($handle, [
                'Product Name',   // [0]
                'Supplier Price', // [1]
                'Selling Price',  // [2]
                'Total Stock',    // [3]
                'Damage Stock',   // [4]
            ]);

            // Add a sample row
            fputcsv($handle, [
                'Sample Product',
                '1000',
                '1500',
                '50',
                '5',
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importFromExcel()
    {
        $this->validate([
            'excel_file' => 'required|mimes:csv,txt|max:10240',
        ]);

        try {
            $filePath = $this->excel_file->getRealPath();
            $handle = fopen($filePath, 'r');

            // Skip header row
            fgetcsv($handle);

            // Get or create a default category
            $defaultCategory = ProductCategory::firstOrCreate(
                ['name' => 'General'],
                ['description' => 'Default category for imported products']
            );

            DB::beginTransaction();
            
            $importedCount = 0;
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (count($row) < 5) continue;

                $productName = $row[0];
                $supplierPrice = floatval($row[1]);
                $sellingPrice = floatval($row[2]);
                $totalStock = intval($row[3]);
                $damageStock = intval($row[4]);

                if (empty($productName)) continue;

                // Create product
                $product = ProductDetail::create([
                    'category_id' => $defaultCategory->id,
                    'product_name' => $productName,
                    'supplier_price' => $supplierPrice,
                    'selling_price' => $sellingPrice,
                    'stock_quantity' => $totalStock - $damageStock,
                    'damage_quantity' => $damageStock,
                    'status' => 'Active',
                    'customer_field' => null,
                ]);

                // Generate product_code
                $productPrefix = strtoupper(substr($productName, 0, 2));
                $categoryPrefix = strtoupper(substr($defaultCategory->name, 0, 2));
                $generatedCode = $productPrefix . $categoryPrefix . '0' . $product->id;

                $product->update(['product_code' => $generatedCode]);
                $importedCount++;
            }

            DB::commit();
            fclose($handle);

            $this->showImportModal = false;
            $this->excel_file = null;
            $this->js("Swal.fire('Success!', '$importedCount products imported successfully.', 'success')");
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($handle)) fclose($handle);
            $this->js("Swal.fire('Error!', '" . addslashes($e->getMessage()) . "', 'error')");
        }
    }


    public function exportToCSV(): StreamedResponse
    {
        $fileName = 'products_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Define your CSV headers
            fputcsv($handle, [
                'Product Code',
                'Category',
                'Product Name',
                'Supplier Price',
                'Selling Price',
                'Stock Quantity',
                'Damage Quantity',
                'Sold',
                'Status',
            ]);

            // Fetch all products with category
            $products = ProductDetail::with('category')->get();

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->product_code,
                    $product->category->name ?? 'N/A',
                    $product->product_name,
                    $product->supplier_price,
                    $product->selling_price,
                    $product->stock_quantity,
                    $product->damage_quantity,
                    $product->sold,
                    $product->status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = ProductDetail::with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('product_code', 'like', '%' . $this->search . '%')
                        ->orWhere('product_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.products', [
            'products' => $products
        ]);
    }
}
