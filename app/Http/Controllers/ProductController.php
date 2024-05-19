<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use TCPDF;

class ProductController extends Controller
{
      public function index()
      {
          return view('products.index');
      }
  
      public function fetchProducts()
      {
          $products = Product::with('variants')->get();
          return response()->json($products);
      }
  
      public function store(Request $request)
      {
          $product = new Product;
          $product->name = $request->name;
          $product->description = $request->description;
          $product->price = $request->price;
          $product->save();
  
          $variants = explode(',', $request->variants);
          foreach ($variants as $variant) {
              $product->variants()->create(['name' => trim($variant)]);
          }
  
          return response()->json(['message' => 'Product added successfully!']);
      }
  
      public function edit($id)
      {
          $product = Product::with('variants')->findOrFail($id);
          return response()->json($product);
      }
  
      public function update(Request $request, $id)
      {
          $product = Product::findOrFail($id);
          $product->name = $request->name;
          $product->description = $request->description;
          $product->price = $request->price;
          $product->save();
  
          $product->variants()->delete();
          $variants = explode(',', $request->variants);
          foreach ($variants as $variant) {
              $product->variants()->create(['name' => trim($variant)]);
          }
  
          return response()->json(['message' => 'Product updated successfully!']);
      }
  
      public function destroy($id)
      {
          $product = Product::findOrFail($id);
          $product->variants()->delete();
          $product->delete();
          return response()->json(['message' => 'Product deleted successfully!']);
      }
  
    //   public function export($format)
    //   {
    //       $products = Product::with('variants')->get();
  
    //       if ($format === 'excel') {
    //           return Excel::download(new ProductsExport($products), 'products.xlsx');
    //       } elseif ($format === 'pdf') {
    //           $pdf = PDF::loadView('products.pdf', compact('products'));
    //           return $pdf->download('products.pdf');
    //       }
  
    //       return redirect()->route('products.index');
    //   }
    public function export($format)
    {
        $products = Product::with('variants')->get();

        if ($format === 'excel') {
            return Excel::download(new ProductsExport($products), 'products.xlsx');
        } elseif ($format === 'pdf') {
            // Create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Your Name');
            $pdf->SetTitle('Products');
            $pdf->SetSubject('Product List');
            $pdf->SetKeywords('TCPDF, PDF, products, export');

            // Set default header data
            $pdf->SetHeaderData('', 0, 'Products', 'Product List');

            // Set header and footer fonts
            $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
            $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

            // Set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // Set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // Set auto page breaks
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

            // Set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // Add a page
            $pdf->AddPage();

            // Generate HTML view
            $html = view('products.pdf', compact('products'))->render();

            // Output HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // Close and output PDF document
            return response()->streamDownload(function() use ($pdf) {
                $pdf->Output('products.pdf', 'I');
            }, 'products.pdf');
        }

        return redirect()->route('products.index');
    }
}
