@extends('layouts.admin')
@section('title', 'Sửa – ' . $blog->title)
@section('page-title', 'Chỉnh sửa bài viết')

@section('content')

<form action="{{ route('admin.blog.update', $blog) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="grid lg:grid-cols-3 gap-5">

    {{-- Editor --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $blog->title) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg font-semibold focus:ring-indigo-500 focus:border-indigo-500">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                    <textarea name="excerpt" rows="2" maxlength="500"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('excerpt', $blog->excerpt) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung <span class="text-red-500">*</span></label>
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <div class="flex flex-wrap gap-1 p-2 border-b border-gray-200 bg-gray-50">
                            @foreach([
                                ['cmd'=>'bold','icon'=>'<strong>B</strong>'],['cmd'=>'italic','icon'=>'<em>I</em>'],
                                ['cmd'=>'underline','icon'=>'<u>U</u>'],['cmd'=>'h2','icon'=>'H2'],['cmd'=>'h3','icon'=>'H3'],
                                ['cmd'=>'insertUnorderedList','icon'=>'• List'],['cmd'=>'insertOrderedList','icon'=>'1. List'],
                                ['cmd'=>'link','icon'=>'🔗'],
                            ] as $btn)
                            <button type="button" onclick="execCmd('{{ $btn['cmd'] }}')"
                                class="px-2.5 py-1 text-xs font-medium text-gray-600 hover:bg-gray-200 rounded transition">{!! $btn['icon'] !!}</button>
                            @endforeach
                        </div>
                        <div id="editor" contenteditable="true"
                            class="min-h-64 p-4 text-sm leading-7 focus:outline-none prose prose-sm max-w-none"
                            oninput="document.getElementById('content-input').value = this.innerHTML"
                            style="min-height: 300px;"
                        >{!! old('content', $blog->content) !!}</div>
                    </div>
                    <input type="hidden" name="content" id="content-input" value="{{ old('content', $blog->content) }}">
                    @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Xuất bản</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="draft" {{ old('status', $blog->status) === 'draft' ? 'selected' : '' }}>Nháp</option>
                        <option value="published" {{ old('status', $blog->status) === 'published' ? 'selected' : '' }}>Đã đăng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $blog->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($blog->published_at)
                <p class="text-xs text-gray-400">Đăng lúc: {{ $blog->published_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">Lưu</button>
                <a href="{{ route('admin.blog.index') }}" class="flex-1 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition text-center">Hủy</a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Ảnh đại diện</h3>
            <div x-data="{ preview: '{{ $blog->featured_image ? asset('storage/'.$blog->featured_image) : '' }}' }">
                <div x-show="preview" class="relative mb-3">
                    <img :src="preview" class="w-full rounded-lg object-cover aspect-video">
                    <button type="button" @click="preview = ''; $refs.imgInput.value = ''"
                        class="absolute top-2 right-2 bg-white rounded-full p-1 shadow text-gray-600 hover:text-red-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div x-show="!preview" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-indigo-400 transition"
                    @click="$refs.imgInput.click()">
                    <p class="text-xs text-gray-400">Click để thay ảnh</p>
                </div>
                <input type="file" name="featured_image" accept="image/*" x-ref="imgInput" class="hidden"
                    @change="preview = URL.createObjectURL($event.target.files[0])">
            </div>
        </div>
    </div>
</div>

</form>

<script>
function execCmd(cmd) {
    const editor = document.getElementById('editor');
    editor.focus();
    if (cmd === 'h2') document.execCommand('formatBlock', false, 'h2');
    else if (cmd === 'h3') document.execCommand('formatBlock', false, 'h3');
    else if (cmd === 'link') { const url = prompt('Nhập URL:'); if (url) document.execCommand('createLink', false, url); }
    else document.execCommand(cmd, false, null);
    document.getElementById('content-input').value = editor.innerHTML;
}
</script>

@endsection
