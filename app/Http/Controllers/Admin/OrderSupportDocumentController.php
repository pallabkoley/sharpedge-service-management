<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderSupportDocument;
use Illuminate\Support\Facades\Storage;

class OrderSupportDocumentController extends Controller
{

    // list documents of an order
    public function index($orderId)
    {
        $documents = OrderSupportDocument::where('order_id',$orderId)->latest()->get();

        return response()->json([
            'status' => true,
            'documents' => $documents
        ]);
    }


    // upload multiple documents
    public function store(Request $request)
    {

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'documents.*' => 'required|file|max:10240'
        ]);

        $uploadedDocs = [];

        if($request->hasFile('documents')){

            foreach($request->file('documents') as $file){

                $path = $file->store(
                    'Order_support_documents/'.$request->order_id,
                    'public'
                );

                $doc = OrderSupportDocument::create([
                    'order_id' => $request->order_id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'uploaded_by' => auth()->id()
                ]);

                $uploadedDocs[] = $doc;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Documents uploaded successfully',
            'data' => $uploadedDocs
        ]);
    }


    // delete document
    public function destroy($id)
    {
        $document = OrderSupportDocument::findOrFail($id);

        if(Storage::disk('public')->exists($document->file_path)){
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'status' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

}