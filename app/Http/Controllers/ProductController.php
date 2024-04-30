<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{

    public function showData()
    {
        try {
            $response = Http::get('https://api.gsutil.xyz/product');
            $products = $response->json();

            // Check if the response was successful (status code 2xx)
            if ($response->successful()) {
                $products = $response->json();
                return view('pages.product', compact('products'));
            } else {
                // Log the error
                \Log::error('API Error: ' . $response->status());

                // Return the error view with status code and message
                return view('pages.error')->with([
                    'errorCode' => $response->status(),
                ]);
            }
        } catch (RequestException $e) {
            // Log the error
            \Log::error('Request Exception: ' . $e->getMessage());

            // Get the HTTP status code from the response headers
            $statusCode = $e->response->status();

            // Return the error view with status code and message
            return view('pages.error')->with([
                'errorCode' => $statusCode,
                'errorMessage' => $e->response->json() // Assuming the API returns error messages in JSON format
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('General Exception: ' . $e->getMessage());

            // Return the error view with error code and description
            return view('pages.error')->with([
                'errorCode' => $e->getCode(),
                'errorMessage' => 'General Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteProduct($id)
    {
        try {
            $response = Http::delete("https://api.gsutil.xyz/product/$id");

            if ($response->successful()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete product']);
            }
        } catch (\Exception $e) {
            \Log::error('General Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to delete product']);
        }
    }

    public function addProductForm()
    {
        return view('pages.product.add-product');
    }

    public function submitProduct(Request $request)
    {
        \Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
            'product_code' => 'required|string',
            'measurement_unit' => 'required|in:pcs,kg,lb,g',
            'description' => 'nullable|string',
            'product_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Assuming a max size of 2MB
            'added_by_admin_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $added_by_admin_id = (int) $request->input('added_by_admin_id');

            $product_image = 'Default image URL if upload fails'; // Default image URL
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $response = Http::attach(
                    'image',
                    $image->get(),
                    $image->getClientOriginalName()
                )->post('http://api.gsutil.xyz/images/upload');

                if ($response->successful()) {
                    $image_path = $response->json()['url'];
                    $product_image = 'http://api.gsutil.xyz/uploads/' . $image_path;
                } else {
                    // If image upload fails, handle with default or log error message from API
                    return response()->json(['success' => false, 'message' => $response->json()['message'] ?? 'Failed to upload image']);
                }
            }

            $productData = [
                'name' => $request->name,
                'product_code' => $request->product_code,
                'measurement_unit' => $request->measurement_unit,
                'description' => $request->description,
                'product_image' => $product_image,
                'added_by_admin_id' => $added_by_admin_id,
            ];

            $productResponse = Http::post('https://api.gsutil.xyz/product', $productData);

            if ($productResponse->successful()) {
                return response()->json(['success' => true, 'message' => 'Product added successfully']);
            } else {
                // Capture and forward error message from the API
                return response()->json(['success' => false, 'message' => $productResponse->json()['message'] ?? 'Failed to add product']);
            }
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add product', 'errorDetail' => $e->getMessage()]);
        }
    }


    public function editProductForm($id)
    {
        // Replace 'https://api.gsutil.xyz/product/{id}' with your actual API URL
        $response = Http::get("https://api.gsutil.xyz/product/{$id}");

        if ($response->successful()) {
            $product = $response->json();
            return view('pages.product.edit-product', ['product' => (object) $product]);
        } else {
            // Handle errors or redirect if the product is not found
            return redirect()->route('product.manage')->withErrors('Product not found.');
        }
    }


    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
            'measurement_unit' => 'required|in:kg,pcs,g',
            'description' => 'nullable|string',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $data = $request->except(['_token', '_method', 'product_image']);
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $response = Http::attach('image', $image->get(), $image->getClientOriginalName())->post('http://api.gsutil.xyz/images/upload');
            if ($response->successful()) {
                $image_path = $response->json()['url']; // Retrieve the image URL from the response
                $data['product_image'] = 'http://api.gsutil.xyz/uploads/' . $image_path; // Assign the full image path to data array
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to upload image']);
            }
        }

        $response = Http::put("https://api.gsutil.xyz/product/{$id}", $data);
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Product successfully updated']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update product']);
        }
    }

}
