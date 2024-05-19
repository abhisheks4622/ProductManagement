<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }
    
    public function headings(): array
    {
        return [
            'Product ID',
            'Name',
            'Price',
            'Description',
            'Variants',
        ];
    }

    public function map($product): array
    {
        $variantNames = $product->variants->pluck('name')->implode(', ');

        return [
            $product->id,
            $product->name,
            $product->price,
            $product->description,
            $variantNames,
        ];
    }
}
