<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreCvRequest;
use App\Http\Requests\Api\UpdateCvRequest;
use App\Http\Resources\CvResource;
use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CvController extends ApiController
{
    /**
     * List all CVs of current user.
     *
     * GET /api/cvs
     */
    public function index(Request $request): JsonResponse
    {
        $cvs = $request->user()
            ->cvs()
            ->with(['template'])
            ->orderByDesc('updated_at')
            ->paginate(10);

        return $this->paginated($cvs, 'Danh sách CV.');
    }

    /**
     * Create a new CV.
     *
     * POST /api/cvs
     */
    public function store(StoreCvRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);

        $cv = Cv::create($data);

        return $this->success(
            new CvResource($cv->load(['template', 'sections'])),
            'Tạo CV thành công!',
            201
        );
    }

    /**
     * Get a single CV by ID.
     *
     * GET /api/cvs/{cv}
     */
    public function show(Request $request, Cv $cv): JsonResponse
    {
        if ($cv->user_id !== $request->user()->id && $cv->visibility !== 'public') {
            return $this->error('Bạn không có quyền xem CV này.', 403);
        }

        $cv->load(['template', 'sections.items', 'user']);

        return $this->success(new CvResource($cv), 'Chi tiết CV.');
    }

    /**
     * Update a CV.
     *
     * PUT /api/cvs/{cv}
     */
    public function update(UpdateCvRequest $request, Cv $cv): JsonResponse
    {
        if ($cv->user_id !== $request->user()->id) {
            return $this->error('Bạn không có quyền chỉnh sửa CV này.', 403);
        }

        $data = $request->validated();
        $data['last_saved_at'] = now();

        $cv->update($data);

        return $this->success(
            new CvResource($cv->fresh()->load(['template', 'sections.items'])),
            'Cập nhật CV thành công!'
        );
    }

    /**
     * Delete a CV.
     *
     * DELETE /api/cvs/{cv}
     */
    public function destroy(Request $request, Cv $cv): JsonResponse
    {
        if ($cv->user_id !== $request->user()->id) {
            return $this->error('Bạn không có quyền xóa CV này.', 403);
        }

        $cv->delete();

        return $this->success(null, 'Xóa CV thành công!');
    }
}
