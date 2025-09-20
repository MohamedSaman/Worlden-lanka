<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Customer;

#[Layout('components.layouts.admin')]
#[Title('Customer Sales Details')]
class CustomerSaleDetails extends Component
{
    use WithPagination;

    public $modalData = null;
    public $search = ''; // Added search property
    // Note: printing now targets only the currently visible table page

    public function viewSaleDetails($customerId)
    {
        // Get customer details
        $customer = Customer::findOrFail($customerId);

        // Get customer sales summary
        $salesSummary = DB::table('sales')
            ->where('customer_id', $customerId)
            ->select(
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(due_amount) as total_due'),
                DB::raw('SUM(total_amount) - SUM(due_amount) as total_paid')
            )
            ->first();

        // Get individual invoices
        $invoices = Sale::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get back-forward due summary from customer_accounts
        $backForwardSummary = DB::table('customer_accounts')
            ->where('customer_id', $customerId)
            ->select(DB::raw('SUM(back_forward_amount) as back_forward_due'))
            ->first();

        // Get product-wise sales with product details
        $productSales = DB::table('sales_items')
            ->join('sales', 'sales_items.sale_id', '=', 'sales.id')
            ->join('product_details', 'sales_items.product_id', '=', 'product_details.id')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('sales.customer_id', $customerId)
            ->select(
                'sales_items.product_id',
                'sales_items.price',
                'sales_items.discount',
                DB::raw('SUM(sales_items.quantity) as total_quantity'),
                DB::raw('SUM(sales_items.price * sales_items.quantity) as total_item_sales'),
                DB::raw('SUM(sales.total_amount) as total_sales'),
                'sales.invoice_number',
                'sales.created_at as sale_date',
                'product_details.product_name',
                'product_details.category_id as product_category',
                'product_details.product_code',
                'customers.name as customer_name'
            )
            ->groupBy(
                'sales_items.product_id',
                'sales.invoice_number',
                'sales_items.price',
                'sales_items.discount',
                'sales.created_at',
                'product_details.product_name',
                'product_details.category_id',
                'product_details.product_code',
                'customers.name'
            )
            ->orderBy('sales.created_at', 'desc')
            ->get();

        $invoiceSales = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('sales.customer_id', $customerId)
            ->select(
                'sales.id',
                'sales.invoice_number',
                'sales.created_at as sale_date',
                DB::raw('SUM(sales_items.price * sales_items.quantity - sales_items.discount) as total_invoice_amount'),
                'customers.name as customer_name'
            )
            ->join('sales_items', 'sales.id', '=', 'sales_items.sale_id')
            ->groupBy('sales.id', 'sales.invoice_number', 'sales.created_at', 'customers.name')
            ->orderBy('sales.created_at', 'desc')
            ->get();

        // Get paid records related to this customer's sales
        $payments = DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->where('sales.customer_id', $customerId)
            ->select(
                'payments.amount',
                'payments.due_payment_method',
                'payments.payment_reference',
                'payments.payment_date',
                'payments.created_at',
                'payments.is_completed',
                'payments.status'
            )
            ->orderBy('payments.created_at', 'desc')
            ->get();

        // Build unified invoice summary rows timeline: Back-Forward first, then Invoices and Paid ordered by date
        $invoiceSummaryRows = [];

        $bfAmount = $backForwardSummary->back_forward_due ?? 0;

        // Collect invoice and payment events with comparable dates
        $events = [];
        foreach ($invoiceSales as $inv) {
            $events[] = [
                'type' => 'invoice',
                'description' => 'Invoice ' . $inv->invoice_number,
                'date' => $inv->sale_date,
                'amount' => floatval($inv->total_invoice_amount ?? 0),
            ];
        }
        foreach ($payments as $p) {
            $isPaid = ($p->is_completed === 1) || (strtolower((string)$p->status) === 'paid');
            if (!$isPaid) continue;
            $label = 'Paid' . (!empty($p->payment_method) ? (' - ' . ucfirst($p->payment_method)) : '');
            if (!empty($p->payment_reference)) {
                $label .= ' (' . $p->payment_reference . ')';
            }
            $events[] = [
                'type' => 'paid',
                'description' => $label,
                'date' => $p->created_at,
                'amount' => floatval($p->amount ?? 0),
            ];
        }

        // Sort events by date ascending so earlier invoices come before later payments and so on
        usort($events, function ($a, $b) {
            $da = $a['date'] ? strtotime($a['date']) : 0;
            $db = $b['date'] ? strtotime($b['date']) : 0;
            if ($da === $db) {
                // Ensure invoices appear before payments if same timestamp
                if ($a['type'] === $b['type']) return 0;
                return $a['type'] === 'invoice' ? -1 : 1;
            }
            return $da <=> $db;
        });

        // Start with Back-Forward due (if any), then the ordered timeline
        if ($bfAmount && floatval($bfAmount) != 0.0) {
            $invoiceSummaryRows[] = [
                'type' => 'backforward',
                'description' => 'Back-Forward Due',
                'date' => null,
                'amount' => floatval($bfAmount),
            ];
        }
        $invoiceSummaryRows = array_merge($invoiceSummaryRows, $events);

        $this->modalData = [
            'customer' => $customer,
            'salesSummary' => $salesSummary,
            'invoices' => $invoices,
            'productSales' => $productSales,
            'invoiceSales' => $invoiceSales,
            'backForwardDue' => $backForwardSummary->back_forward_due ?? 0,
            'invoiceSummaryRows' => $invoiceSummaryRows,
        ];

        $this->dispatch('open-customer-sale-details-modal');
    }

    // For print functionality (main table)
    public function printData()
    {
        // Trigger JavaScript print function from the frontend for the current table view
        $this->dispatch('print-customer-table');
    }

    // For CSV export
    public function exportToCSV()
    {
        // Build a summary aligned with the main table: total_sales from sales,
        // and total_due/total_paid from customer_accounts (back_forward + current_due)
        $customerSales = DB::table('customers')
            ->leftJoin(DB::raw('(
                SELECT customer_id,
                       COUNT(DISTINCT invoice_number) AS invoice_count,
                       SUM(total_amount) AS total_sales
                FROM sales
                GROUP BY customer_id
            ) AS sales_summary'), 'customers.id', '=', 'sales_summary.customer_id')
            ->leftJoin(DB::raw('(
                SELECT customer_id,
                       SUM(current_due_amount) AS current_due
                FROM customer_accounts
                GROUP BY customer_id
            ) AS account_summary'), 'customers.id', '=', 'account_summary.customer_id')
            ->leftJoin(DB::raw('(
                SELECT customer_id,
                       SUM(back_forward_amount) AS total_back_forward_amount
                FROM customer_accounts
                GROUP BY customer_id
            ) AS back_forward_summary'), 'customers.id', '=', 'back_forward_summary.customer_id')
            ->select(
                'customers.id as customer_id',
                'customers.name',
                'customers.email',
                'customers.business_name',
                'customers.type',
                DB::raw('COALESCE(sales_summary.invoice_count, 0) as invoice_count'),
                DB::raw('COALESCE(sales_summary.total_sales, 0) as total_sales'),
                DB::raw('COALESCE(account_summary.current_due, 0) + COALESCE(back_forward_summary.total_back_forward_amount, 0) as total_due'),
                DB::raw('COALESCE(sales_summary.total_sales, 0) - (COALESCE(account_summary.current_due, 0) + COALESCE(back_forward_summary.total_back_forward_amount, 0)) as total_paid')
            )
            ->orderByDesc('total_sales')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer_sales_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($customerSales) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['#', 'Customer Name', 'Email', 'Business Name', 'Type', 'Invoices', 'Total Sales', 'Total Paid', 'Total Due', 'Collection %']);

            // Add data rows
            foreach ($customerSales as $index => $customer) {
                $percentage = $customer->total_sales > 0 ? round(($customer->total_paid / $customer->total_sales) * 100) : 100;

                fputcsv($file, [
                    $index + 1,
                    $customer->name,
                    $customer->email,
                    $customer->business_name ?? 'N/A',
                    ucfirst($customer->type),
                    $customer->invoice_count,
                    'Rs.' . number_format($customer->total_sales, 2),
                    'Rs.' . number_format($customer->total_paid, 2),
                    'Rs.' . number_format($customer->total_due, 2),
                    $percentage . '%'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $customerSales = DB::table('customers')
            ->leftJoin(DB::raw('(SELECT customer_id, SUM(current_due_amount) as current_due FROM customer_accounts GROUP BY customer_id) as account_summary'), 'customers.id', '=', 'account_summary.customer_id')
            ->leftJoin(DB::raw('(SELECT customer_id, COUNT(DISTINCT invoice_number) as invoice_count, SUM(total_amount) as total_sales, SUM(due_amount) as total_due, MAX(created_at) as last_sale_date FROM sales GROUP BY customer_id) as sales_summary'), 'customers.id', '=', 'sales_summary.customer_id')
            ->leftJoin(DB::raw('(SELECT customer_id, SUM(back_forward_amount) as total_back_forward_amount FROM customer_accounts GROUP BY customer_id) as back_forward_summary'), 'customers.id', '=', 'back_forward_summary.customer_id')
            ->select(
                'customers.id as customer_id',
                'customers.name',
                'customers.email',
                'customers.business_name',
                'customers.type',
                'sales_summary.total_sales',
                'account_summary.current_due as total_due',
                'back_forward_summary.total_back_forward_amount',
                'sales_summary.last_sale_date',
                DB::raw('COALESCE(sales_summary.total_sales, 0) - (COALESCE(account_summary.current_due, 0)) as total_paid')
            )
            ->orderByDesc('last_sale_date');

        // Apply search filter if search term exists
        if (!empty($this->search)) {
            $customerSales = $customerSales->where(function ($query) {
                $query->where('customers.name', 'like', '%' . $this->search . '%')
                    ->orWhere('customers.email', 'like', '%' . $this->search . '%')
                    ->orWhere('customers.business_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customers.type', 'like', '%' . $this->search . '%');
            });
        }

        // Paginate normally (show only the current page in the UI and print)
        $customerSales = $customerSales->paginate(10);

        return view('livewire.admin.customer-sale-details', [
            'customerSales' => $customerSales
        ]);
    }
}