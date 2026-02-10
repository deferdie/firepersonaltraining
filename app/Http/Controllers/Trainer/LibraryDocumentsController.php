<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\LibraryDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LibraryDocumentsController extends Controller
{
    private const DISK = 'public';

    private const ALLOWED_MIMES = [
        'jpeg', 'jpg', 'png', 'gif', 'webp',
        'pdf',
        'mp4', 'webm', 'mov',
        'wav', 'mp3', 'm4a',
        'doc', 'docx', 'xls', 'xlsx',
    ];

    private const MAX_FILE_SIZE_KB = 1048576; // 1GB

    public function list(Request $request): JsonResponse
    {
        $trainerId = auth()->id();
        $query = LibraryDocument::forTrainer($trainerId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('title')->limit(50)->get()->map(function (LibraryDocument $doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'description' => $doc->description,
                'file_type' => $doc->file_type,
                'file_url' => $doc->file_url,
            ];
        });

        return response()->json(['documents' => $documents]);
    }

    public function index(Request $request): Response
    {
        $trainerId = auth()->id();

        $query = LibraryDocument::forTrainer($trainerId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('title')->paginate(12);

        $documents->getCollection()->transform(function (LibraryDocument $doc) {
            $previewUrl = null;

            if ($doc->file_url) {
                $disk = Storage::disk(self::DISK);

                // Prefer temporary URLs when supported (e.g. S3) so previews work with external viewers.
                if (method_exists($disk, 'temporaryUrl')) {
                    try {
                        $previewUrl = $disk->temporaryUrl($doc->file_url, now()->addMinutes(10));
                    } catch (\Throwable $e) {
                        // Fallback to regular URL if temporary URLs are not available
                        $previewUrl = method_exists($disk, 'url') ? $disk->url($doc->file_url) : null;
                    }
                } elseif (method_exists($disk, 'url')) {
                    $previewUrl = $disk->url($doc->file_url);
                }
            }

            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'description' => $doc->description,
                'file_url' => $doc->file_url,
                'file_type' => $doc->file_type,
                'file_size' => $doc->file_size,
                'category' => $doc->category,
                'created_at' => $doc->created_at->format('Y-m-d'),
                'preview_url' => $previewUrl,
            ];
        });

        return Inertia::render('Trainer/Library/Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:255',
            'file' => [
                'required',
                'file',
                'mimes:' . implode(',', self::ALLOWED_MIMES),
                'max:' . self::MAX_FILE_SIZE_KB,
            ],
        ]);

        $file = $request->file('file');
        $path = $file->store(
            'library-documents/' . auth()->id(),
            self::DISK
        );

        LibraryDocument::create([
            'trainer_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'file_url' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()
            ->route('trainer.library.documents.index')
            ->with('success', 'Document uploaded successfully!');
    }

    public function update(Request $request, LibraryDocument $document): RedirectResponse
    {
        if ($document->trainer_id !== auth()->id()) {
            abort(403);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:255',
            'file' => [
                'nullable',
                'file',
                'mimes:' . implode(',', self::ALLOWED_MIMES),
                'max:' . self::MAX_FILE_SIZE_KB,
            ],
        ];

        $validated = $request->validate($rules);

        $updates = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store(
                'library-documents/' . auth()->id(),
                self::DISK
            );
            $updates['file_url'] = $path;
            $updates['file_type'] = $file->getClientOriginalExtension();
            $updates['file_size'] = $file->getSize();

            if ($document->file_url && Storage::disk(self::DISK)->exists($document->file_url)) {
                Storage::disk(self::DISK)->delete($document->file_url);
            }
        }

        $document->update($updates);

        return redirect()
            ->route('trainer.library.documents.index')
            ->with('success', 'Document updated successfully!');
    }

    public function destroy(LibraryDocument $document): RedirectResponse
    {
        if ($document->trainer_id !== auth()->id()) {
            abort(403);
        }

        if ($document->file_url && Storage::disk(self::DISK)->exists($document->file_url)) {
            Storage::disk(self::DISK)->delete($document->file_url);
        }

        $document->delete();

        return redirect()
            ->route('trainer.library.documents.index')
            ->with('success', 'Document deleted successfully!');
    }

    public function download(LibraryDocument $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if ($document->trainer_id !== auth()->id()) {
            abort(403);
        }

        if (! $document->file_url || ! Storage::disk(self::DISK)->exists($document->file_url)) {
            abort(404);
        }

        $filename = $document->title . '.' . ($document->file_type ?? 'bin');
        $filename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

        $disk = Storage::disk(self::DISK);
        $contentType = 'application/octet-stream';
        if (method_exists($disk, 'mimeType')) {
            $contentType = $disk->mimeType($document->file_url) ?: $contentType;
        }

        return response()->streamDownload(
            function () use ($document) {
                $stream = Storage::disk(self::DISK)->readStream($document->file_url);
                fpassthru($stream);
                fclose($stream);
            },
            $filename,
            [
                'Content-Type' => $contentType,
            ],
            'attachment'
        );
    }
}
