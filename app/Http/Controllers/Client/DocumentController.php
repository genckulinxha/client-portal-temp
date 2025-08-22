<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->client;

        $documents = Document::where('client_id', $client->id)
            ->where('client_viewable', true)
            ->with(['case', 'task', 'uploadedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('client.documents.index', compact('client', 'documents'));
    }

    public function upload(Request $request)
    {
        $client = $request->client;

        $request->validate([
            'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,txt',
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'nullable|string|max:500',
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = "client-documents/{$client->id}/" . $filename;

            // Store file
            Storage::put($filePath, file_get_contents($file));

            // Create document record
            $document = Document::create([
                'title' => $originalName,
                'filename' => $filename,
                'original_filename' => $originalName,
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_hash' => hash_file('md5', $file->getRealPath()),
                'type' => 'client_document',
                'client_viewable' => true,
                'client_id' => $client->id,
                'uploaded_by_client_id' => $client->id,
                'task_id' => $request->task_id,
                'description' => $request->description,
            ]);

            $uploadedFiles[] = $document;
        }

        return redirect()->back()->with('success', 'Files uploaded successfully!');
    }

    public function download(Request $request, Document $document)
    {
        $client = $request->client;

        // Verify client can access this document
        if ($document->client_id !== $client->id || !$document->client_viewable) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($document->file_path, $document->original_filename);
    }

    public function destroy(Request $request, Document $document)
    {
        $client = $request->client;

        // Only allow clients to delete their own uploaded documents
        if ($document->uploaded_by_client_id !== $client->id) {
            abort(403, 'You can only delete documents you uploaded');
        }

        // Delete file from storage
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully!');
    }
}