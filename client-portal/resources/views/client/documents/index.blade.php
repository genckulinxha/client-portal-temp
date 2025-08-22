@extends('client.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Documents</h1>
        <p class="mt-2 text-gray-600">Upload and manage your case documents.</p>
    </div>

    <!-- Upload Section -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upload Documents</h3>
            
            <form action="{{ route('client.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label for="files" class="block text-sm font-medium text-gray-700">Select Files</label>
                    <input type="file" 
                           id="files" 
                           name="files[]" 
                           multiple 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT. Max size: 10MB per file.</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <textarea id="description" 
                              name="description" 
                              rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Brief description of the documents..."></textarea>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Upload Documents
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($documents->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($documents as $document)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <!-- File Icon -->
                                <div class="flex-shrink-0 mr-4">
                                    @if(str_contains($document->mime_type, 'pdf'))
                                        <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif(str_contains($document->mime_type, 'image'))
                                        <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Document Info -->
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $document->title }}</h4>
                                    <div class="mt-1 flex items-center text-xs text-gray-500 space-x-4">
                                        <span>{{ $document->file_size_human }}</span>
                                        <span>{{ $document->created_at->format('M j, Y g:i A') }}</span>
                                        @if($document->case)
                                            <span>Case: {{ $document->case->case_number }}</span>
                                        @endif
                                        @if($document->task)
                                            <span>Task: {{ $document->task->title }}</span>
                                        @endif
                                    </div>
                                    @if($document->description)
                                        <p class="mt-1 text-sm text-gray-600">{{ $document->description }}</p>
                                    @endif
                                    @if($document->uploadedByUser)
                                        <p class="mt-1 text-xs text-gray-500">Uploaded by: {{ $document->uploadedByUser->name }}</p>
                                    @elseif($document->uploaded_by_client_id === $client->id)
                                        <p class="mt-1 text-xs text-gray-500">Uploaded by: You</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('client.documents.download', $document) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                    Download
                                </a>
                                @if($document->uploaded_by_client_id === $client->id)
                                    <button onclick="deleteDocument({{ $document->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $documents->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No documents</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't uploaded any documents yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Document</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this document? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function deleteDocument(documentId) {
    document.getElementById('deleteForm').action = '/client/documents/' + documentId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// File upload validation
document.getElementById('files').addEventListener('change', function(e) {
    const files = e.target.files;
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    for (let file of files) {
        if (file.size > maxSize) {
            alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
            e.target.value = '';
            return;
        }
    }
});
</script>
@endsection