<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // default reporting
        });
    }

    public function render($request, Throwable $e)
    {
        // Handle POST too large gracefully and show upload limits
        if ($e instanceof PostTooLargeException || (method_exists($e, 'getStatusCode') && $e->getStatusCode() === 413)) {
            try {
                $postMax = ini_get('post_max_size') ?: 'unknown';
                $uploadMax = ini_get('upload_max_filesize') ?: 'unknown';
                $msg = "Upload gagal: ukuran request melebihi batas. php.ini limits: post_max_size={$postMax}, upload_max_filesize={$uploadMax}.";
            } catch (\Throwable $xx) {
                $msg = 'Upload gagal: ukuran request melebihi batas.';
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 413);
            }
            // flash and redirect back
            return redirect()->back()->withInput()->with('error', $msg);
        }

        return parent::render($request, $e);
    }
}
