<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Payment;

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
                DB::raw('COUNT(DISTINCT invoice_number) as invoice_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(due_amount) as total_due')
            )
            ->first();

        // Payment sums by logical status for this customer's sales and Brought-forward payments
        $paymentsBase = Payment::where(function ($query) use ($customerId) {
            $query->whereHas('sale', function($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->orWhere('customer_id', $customerId);
        });

        $paidSum = (clone $paymentsBase)
            ->where(function($q){
                $q->where('is_completed', true)
                  ->orWhereIn('status', ['Paid','paid']);
            })
            ->sum('amount');
        $paidForwardSum = (clone $paymentsBase)
            ->where(function($q){
                $q->where('applied_to', 'back_forward')
                  ->orWhere('status', 'forward');
            })
            ->sum('amount');

        $currentSum = (clone $paymentsBase)
            ->where(function($q){
                $q->where('applied_to', 'current')
                  ->orWhere('status', 'current');
            })
            ->sum('amount');

        $forwardSum = (clone $paymentsBase)
            ->where(function($q){
                $q->where('applied_to', 'back_forward')
                  ->orWhere('status', 'forward');
            })
            ->sum('amount');

        // Attach computed paid sum to sales summary
        $salesSummary->total_paid = $paidSum;

        // Get individual invoices
        $invoices = Sale::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get due totals from customer_accounts (authoritative due balances)
        $accountTotals = DB::table('customer_accounts')
            ->where('customer_id', $customerId)
            ->select(
                DB::raw('SUM(total_due) as total_due'),
                DB::raw('SUM(current_due_amount) as current_due'),
                DB::raw('SUM(back_forward_amount) as back_forward_due')
            )
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
                'sales.notes',
                'sales.created_at as sale_date',
                DB::raw('SUM(sales_items.price * sales_items.quantity - sales_items.discount) as total_invoice_amount'),
                'customers.name as customer_name'
            )
            ->join('sales_items', 'sales.id', '=', 'sales_items.sale_id')
            ->groupBy('sales.id', 'sales.invoice_number', 'sales.notes', 'sales.created_at', 'customers.name')
            ->orderBy('sales.created_at', 'desc')
            ->get();

        // Get paid records related to this customer's sales and Brought-forward payments
        $payments = DB::table('payments')
            ->leftJoin('sales', 'payments.sale_id', '=', 'sales.id')
            ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
            ->where(function ($query) use ($customerId) {
                $query->where('sales.customer_id', $customerId)
                      ->orWhere('payments.customer_id', $customerId);
            })
            ->select(
                'payments.id',
                'payments.amount',
                'payments.due_payment_method',
                'payments.payment_reference',
                'payments.payment_date',
                'payments.created_at',
                'payments.is_completed',
                'payments.status',
                'payments.applied_to',
                'payments.sale_id',
                'sales.invoice_number',
                'customers.name as customer_name'
            )
            ->orderBy('payments.created_at', 'desc')
            ->get();

        // Build unified invoice summary rows timeline: Brought-Forward first, then Invoices and Paid ordered by date
        $invoiceSummaryRows = [];

        $bfAmount = ($accountTotals->back_forward_due + $paidForwardSum ) ?? 0;

        // Collect invoice and payment events with comparable dates
        $events = [];
        foreach ($invoiceSales as $inv) {
            $events[] = [
                'type' => 'invoice',
                'description' => 'Invoice ' . $inv->invoice_number.'(' . ($inv->notes ? $inv->notes : 'No notes.') . ')',
                'date' => $inv->sale_date,
                'amount' => floatval($inv->total_invoice_amount ?? 0),
            ];
        }
        foreach ($payments as $p) {
            $isPaid = ($p->is_completed === 1) || (strtolower((string)$p->status) === 'paid');
            if (!$isPaid) continue;

            // Build label: Paid (Current/Forward) - Method
            $target = ($p->applied_to === 'back_forward' || strtolower((string)$p->status) === 'forward') ? 'Forward' : 'Current';
            $label = 'Paid (' . $target . ')';

            // Add invoice number if available (for sale-related payments)
            if (!empty($p->invoice_number)) {
                $label .= ' - Invoice ' . $p->invoice_number;
            }

            if (!empty($p->due_payment_method)) {
                $label .= ' - ' . ucfirst(str_replace('_',' ', $p->due_payment_method));
            }
            if (!empty($p->payment_reference)) {
                $label .= ' (' . $p->payment_reference . ')';
            }

            // For Brought-forward payments without sale_id, show customer context
            if (empty($p->sale_id) && !empty($p->customer_name)) {
                $label .= ' - ' . $p->customer_name;
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

        // Start with Brought-Forward due (if any), then the ordered timeline
        if ($bfAmount && floatval($bfAmount) != 0.0) {
            $invoiceSummaryRows[] = [
                'type' => 'broughtforward',
                'description' => 'Brought-Forward Due',
                'date' => null,
                'amount' => floatval($bfAmount),
            ];
        }
        $invoiceSummaryRows = array_merge($invoiceSummaryRows, $events);

        $this->modalData = [
            'customer' => $customer,
            'salesSummary' => $salesSummary,
            'paymentSums' => [
                'paid' => $paidSum,
                'current' => $currentSum,
                'forward' => $forwardSum,
            ],
            'invoices' => $invoices,
            'productSales' => $productSales,
            'invoiceSales' => $invoiceSales,
            'accountTotals' => [
                'total_due' => $accountTotals->total_due ?? 0,
                'current_due' => $accountTotals->current_due ?? 0,
                'back_forward_due' => $accountTotals->back_forward_due ?? 0,
            ],
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
        // Build a comprehensive summary per customer including payment status splits and due breakdown
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
                       SUM(current_due_amount) AS current_due,
                       SUM(back_forward_amount) AS back_forward_due,
                       SUM(total_due) AS total_due
                FROM customer_accounts
                GROUP BY customer_id
            ) AS account_totals'), 'customers.id', '=', 'account_totals.customer_id')
            ->leftJoin(DB::raw('(
                SELECT s.customer_id,
                       SUM(CASE WHEN p.is_completed = 1 OR LOWER(p.status) IN ("paid") THEN p.amount ELSE 0 END) AS paid_all,
                       SUM(CASE WHEN p.applied_to = "current" OR LOWER(p.status) = "current" THEN p.amount ELSE 0 END) AS paid_current,
                       SUM(CASE WHEN p.applied_to = "back_forward" OR LOWER(p.status) = "forward" THEN p.amount ELSE 0 END) AS paid_forward
                FROM payments p
                LEFT JOIN sales s ON p.sale_id = s.id
                WHERE p.customer_id IS NOT NULL OR s.customer_id IS NOT NULL
                GROUP BY COALESCE(s.customer_id, p.customer_id)
            ) AS pay_summaries'), 'customers.id', '=', 'pay_summaries.customer_id')
            ->select(
                'customers.id as customer_id',
                'customers.name',
                DB::raw('COALESCE(sales_summary.invoice_count, 0) as invoice_count'),
                DB::raw('COALESCE(sales_summary.total_sales, 0) as total_sales'),
                DB::raw('COALESCE(pay_summaries.paid_all, 0) as paid_all'),
                DB::raw('COALESCE(pay_summaries.paid_current, 0) as paid_current'),
                DB::raw('COALESCE(pay_summaries.paid_forward, 0) as paid_forward'),
                DB::raw('COALESCE(account_totals.current_due, 0) as current_due'),
                DB::raw('COALESCE(account_totals.back_forward_due, 0) as back_forward_due'),
                DB::raw('COALESCE(account_totals.total_due, 0) as total_due')
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
            fputcsv($file, ['#', 'Customer Name', 'Invoices', 'Total Sales', 'Paid (All)', 'Paid - Current', 'Paid - Brought-Forward', 'Current Due', 'Brought-Forward Due', 'Total Due', 'Collection %']);

            // Add data rows
            foreach ($customerSales as $index => $customer) {
                $percentage = $customer->total_sales > 0 ? round(((float)$customer->paid_all / (float)$customer->total_sales) * 100) : 100;

                fputcsv($file, [
                    $index + 1,
                    $customer->name,
                    $customer->invoice_count,
                    'Rs.' . number_format($customer->total_sales, 2),
                    'Rs.' . number_format($customer->paid_all, 2),
                    'Rs.' . number_format($customer->paid_current, 2),
                    'Rs.' . number_format($customer->paid_forward, 2),
                    'Rs.' . number_format($customer->current_due, 2),
                    'Rs.' . number_format($customer->back_forward_due, 2),
                    'Rs.' . number_format($customer->total_due, 2),
                    $percentage . '%'
                ]);
            }

            fclose($file);
        };  
    }

    // For modal CSV export
    public function exportModalToCSV()
    {
        if (!$this->modalData) {
            return;
        }

        $customer = $this->modalData['customer'];
        $salesSummary = $this->modalData['salesSummary'];
        $paymentSums = $this->modalData['paymentSums'];
        $invoiceSummaryRows = $this->modalData['invoiceSummaryRows'];
        $accountTotals = $this->modalData['accountTotals'];

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $customer->name . '_sales_summary_' . $customer->id . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($customer, $salesSummary, $paymentSums, $invoiceSummaryRows, $accountTotals) {
            $file = fopen('php://output', 'w');

            // Customer Information Header
            fputcsv($file, ['Customer Information']);
            fputcsv($file, ['Name', $customer->name]);
            fputcsv($file, ['Email', $customer->email ?? 'N/A']);
            fputcsv($file, ['Phone', $customer->phone ?? 'N/A']);
            fputcsv($file, ['Business Name', $customer->business_name ?? 'N/A']);
            fputcsv($file, ['Type', ucfirst($customer->type ?? 'N/A')]);
            fputcsv($file, ['']);

            // Sales Summary Header
            fputcsv($file, ['Sales Summary']);
            fputcsv($file, ['Total Sales Amount', 'Rs.' . number_format($salesSummary->total_amount ?? 0, 2)]);
            fputcsv($file, ['Amount Paid', 'Rs.' . number_format($paymentSums['paid'] ?? 0, 2)]);
            fputcsv($file, ['Current Paid', 'Rs.' . number_format($paymentSums['current'] ?? 0, 2)]);
            fputcsv($file, ['Brought-Forward Paid', 'Rs.' . number_format($paymentSums['forward'] ?? 0, 2)]);
            fputcsv($file, ['Total Due Amount', 'Rs.' . number_format($salesSummary->total_due ?? 0, 2)]);
            fputcsv($file, ['Brought-Forward Due', 'Rs.' . number_format($accountTotals['back_forward_due'] ?? 0, 2)]);
            fputcsv($file, ['Current Due', 'Rs.' . number_format($accountTotals['current_due'] ?? 0, 2)]);
            fputcsv($file, ['']);

            // Sales Timeline Header
            fputcsv($file, ['Sales Timeline']);
            fputcsv($file, ['No', 'Description', 'Date', 'Amount']);

            // Sales Timeline Data
            foreach ($invoiceSummaryRows as $index => $row) {
                $date = !empty($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('d M Y') : '—';
                $amount = ($row['type'] ?? '') === 'paid' ?
                    '(Rs.' . number_format($row['amount'] ?? 0, 2) . ')' :
                    'Rs.' . number_format($row['amount'] ?? 0, 2);

                fputcsv($file, [
                    $index + 1,
                    $row['description'] ?? '',
                    $date,
                    $amount
                ]);
            }

            // Balance Total
            if (isset($accountTotals['total_due'])) {
                fputcsv($file, ['']);
                fputcsv($file, ['Balance Total Due Amount', 'Rs.' . number_format($accountTotals['total_due'], 2)]);
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