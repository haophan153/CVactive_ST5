<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chỉnh sửa CV – {{ $cv->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-panel { transition: all .3s ease; }
        .cv-preview { font-family: v-bind(cvFont); }
        #cv-preview-frame { zoom: 0.7; transform-origin: top left; }
        @media (max-width: 1024px) { #cv-preview-frame { zoom: 0.5; } }
        .sortable-ghost { opacity: 0.4; background: #e0e7ff !important; border-radius: 8px; }
        .sortable-drag { box-shadow: 0 8px 24px rgba(0,0,0,0.15); cursor: grabbing !important; }
    </style>
</head>
<body class="h-full bg-gray-100" x-data="cvEditor()" x-init="init()">

{{-- Top Toolbar --}}
<header class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-4 fixed top-0 left-0 right-0 z-50 shadow-sm">
    <div class="flex items-center space-x-3">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 p-1.5 rounded-md hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="w-px h-6 bg-gray-200"></div>
        <input type="text" x-model="cvTitle"
            @blur="autoSave()"
            class="text-gray-900 font-semibold text-sm bg-transparent border-0 focus:ring-0 focus:outline-none px-1 w-56"
            placeholder="Tiêu đề CV...">
    </div>

    <div class="flex items-center space-x-2">
        {{-- Trạng thái lưu --}}
        <span x-show="saving" class="text-xs text-blue-500 flex items-center space-x-1">
            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span>Đang lưu...</span>
        </span>
        <span x-show="!saving && savedAt" x-text="'Đã lưu lúc ' + savedAt" class="text-xs text-gray-400"></span>

        {{-- Share --}}
        <button @click="getShareLink()" class="flex items-center space-x-1.5 px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
            <span>Chia sẻ</span>
        </button>

        {{-- Download PDF --}}
        <a href="{{ route('cv.pdf', $cv) }}" target="_blank"
            class="flex items-center space-x-1.5 px-3 py-1.5 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>PDF</span>
        </a>

        {{-- Export PNG --}}
        <button @click="exportPng()"
            :disabled="exportingPng"
            class="flex items-center space-x-1.5 px-3 py-1.5 text-sm text-white bg-emerald-600 rounded-md hover:bg-emerald-700 transition disabled:opacity-60">
            <svg x-show="!exportingPng" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <svg x-show="exportingPng" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span x-text="exportingPng ? 'Đang xuất...' : 'PNG'"></span>
        </button>
    </div>
</header>

<div class="flex h-full pt-14">

    {{-- LEFT: Section List --}}
    <aside class="w-72 bg-white border-r border-gray-200 flex flex-col fixed left-0 top-14 bottom-0 overflow-y-auto z-30">

        {{-- Tabs --}}
        <div class="flex border-b border-gray-200">
            <button @click="activeTab = 'sections'"
                :class="activeTab === 'sections' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-3 text-xs font-semibold uppercase tracking-wide transition">
                Nội dung
            </button>
            <button @click="activeTab = 'design'"
                :class="activeTab === 'design' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-3 text-xs font-semibold uppercase tracking-wide transition">
                Thiết kế
            </button>
        </div>

        {{-- Content Tab --}}
        <div x-show="activeTab === 'sections'" class="flex-1 overflow-y-auto p-3">
            <p class="text-xs text-gray-400 uppercase font-semibold mb-3 px-1">Các mục CV</p>

            <div class="space-y-1" id="sections-list">
                <template x-for="(section, idx) in sections" :key="section.id">
                    <div class="group flex items-center space-x-2 px-2 py-2 rounded-lg cursor-pointer transition section-list-item"
                        :class="activeSection === section.id ? 'bg-indigo-50 border border-indigo-200' : 'hover:bg-gray-50'"
                        :data-id="section.id"
                        @click="activeSection = section.id; activeTab = 'sections'">

                        <span class="text-gray-300 cursor-grab group-hover:text-gray-400 flex-shrink-0 drag-handle">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/></svg>
                        </span>

                        <span class="text-sm flex-1 truncate" :class="activeSection === section.id ? 'text-indigo-700 font-medium' : 'text-gray-700'" x-text="section.title"></span>

                        <button @click.stop="toggleSection(section)"
                            :class="section.is_visible ? 'text-gray-400 hover:text-gray-600' : 'text-gray-200 hover:text-gray-400'"
                            class="opacity-0 group-hover:opacity-100 p-0.5 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <template x-if="section.is_visible">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </template>
                                <template x-if="!section.is_visible">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </template>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Add Custom Section --}}
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div x-show="!showAddSection">
                    <button @click="showAddSection = true"
                        class="w-full flex items-center justify-center space-x-2 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Thêm mục tùy chỉnh</span>
                    </button>
                </div>
                <div x-show="showAddSection" x-cloak class="space-y-2">
                    <input type="text" x-model="newSectionTitle" placeholder="Tên mục mới..."
                        @keyup.enter="addCustomSection()"
                        class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="flex space-x-2">
                        <button @click="addCustomSection()" class="flex-1 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">Thêm</button>
                        <button @click="showAddSection = false; newSectionTitle = ''" class="flex-1 py-1.5 bg-gray-100 text-gray-600 text-sm rounded-md hover:bg-gray-200 transition">Huỷ</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Design Tab --}}
        <div x-show="activeTab === 'design'" x-cloak class="flex-1 overflow-y-auto p-4 space-y-6">

            {{-- Theme Color --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Màu chủ đề</label>
                <div class="grid grid-cols-6 gap-2 mb-2">
                    <template x-for="color in themeColors" :key="color">
                        <button @click="cvThemeColor = color; autoSave()"
                            :style="`background-color: ${color}`"
                            :class="cvThemeColor === color ? 'ring-2 ring-offset-1 ring-gray-400 scale-110' : ''"
                            class="w-8 h-8 rounded-full transition transform hover:scale-110 shadow-sm">
                        </button>
                    </template>
                </div>
                <div class="flex items-center space-x-2 mt-2">
                    <input type="color" x-model="cvThemeColor" @change="autoSave()"
                        class="w-8 h-8 rounded cursor-pointer border-0 p-0">
                    <span class="text-xs text-gray-500" x-text="cvThemeColor"></span>
                </div>
            </div>

            {{-- Font Family --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Font chữ</label>
                <select x-model="cvFontFamily" @change="autoSave()"
                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Inter">Inter (Mặc định)</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Lato">Lato</option>
                    <option value="Montserrat">Montserrat</option>
                </select>
            </div>

            {{-- Template --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Mẫu CV</label>
                <div class="space-y-2">
                    @foreach($templates as $tpl)
                    <button @click="changeTemplate({{ $tpl->id }})"
                        :class="currentTemplateId === {{ $tpl->id }} ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                        class="w-full flex items-center space-x-3 p-2 border-2 rounded-lg transition text-left">
                        <div class="w-10 h-14 bg-gray-100 rounded flex-shrink-0 overflow-hidden">
                            @if($tpl->thumbnail)
                                <img src="{{ $tpl->thumbnail }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $tpl->name }}</p>
                            @if($tpl->is_premium)
                                <span class="text-xs text-amber-600 font-medium">PRO</span>
                            @else
                                <span class="text-xs text-green-600">Miễn phí</span>
                            @endif
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Visibility --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Quyền riêng tư</label>
                <select x-model="cvVisibility" @change="autoSave()"
                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="private">Riêng tư</option>
                    <option value="public">Công khai</option>
                </select>
            </div>
        </div>
    </aside>

    {{-- MIDDLE: Editor Panel --}}
    <main class="flex-1 ml-72 mr-[45%] min-h-screen pt-4 pb-16 overflow-y-auto px-6">

        <template x-for="section in sections" :key="section.id">
            <div x-show="activeSection === section.id || !activeSection"
                class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 overflow-hidden">

                {{-- Section header --}}
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full" :style="`background-color: ${cvThemeColor}`"></span>
                        <h3 class="font-semibold text-gray-800 text-sm" x-text="section.title"></h3>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button @click="toggleSection(section)" class="p-1.5 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="section.is_visible" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path x-show="!section.is_visible" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                        <button x-show="section.is_custom" @click="deleteSection(section)"
                            class="p-1.5 rounded text-red-400 hover:text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Section body --}}
                <div class="p-5" :class="!section.is_visible ? 'opacity-50' : ''">

                    {{-- Personal Info --}}
                    <template x-if="section.type === 'personal'">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" x-model="personal.full_name" @input="debouncedSave()"
                                    class="form-input" placeholder="Nguyễn Văn A">
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" x-model="personal.email" @input="debouncedSave()"
                                    class="form-input" placeholder="email@example.com">
                            </div>
                            <div>
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" x-model="personal.phone" @input="debouncedSave()"
                                    class="form-input" placeholder="0901 234 567">
                            </div>
                            <div>
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" x-model="personal.address" @input="debouncedSave()"
                                    class="form-input" placeholder="Hà Nội, Việt Nam">
                            </div>
                            <div>
                                <label class="form-label">Website</label>
                                <input type="text" x-model="personal.website" @input="debouncedSave()"
                                    class="form-input" placeholder="https://...">
                            </div>
                            <div>
                                <label class="form-label">LinkedIn</label>
                                <input type="text" x-model="personal.linkedin" @input="debouncedSave()"
                                    class="form-input" placeholder="linkedin.com/in/...">
                            </div>
                            <div>
                                <label class="form-label">GitHub</label>
                                <input type="text" x-model="personal.github" @input="debouncedSave()"
                                    class="form-input" placeholder="github.com/...">
                            </div>
                        </div>
                    </template>

                    {{-- Objective --}}
                    <template x-if="section.type === 'objective'">
                        <div>
                            <label class="form-label">Mục tiêu nghề nghiệp</label>
                            <textarea x-model="objective" @input="debouncedSave()" rows="5"
                                class="form-input resize-none"
                                placeholder="Mô tả ngắn về bản thân, mục tiêu và định hướng nghề nghiệp của bạn..."></textarea>
                        </div>
                    </template>

                    {{-- Experience --}}
                    <template x-if="section.type === 'experience'">
                        <div>
                            <div class="space-y-4">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="border border-gray-200 rounded-lg p-4 relative group">
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="form-label">Chức vụ / Vị trí</label>
                                                <input type="text" x-model="item.content.position" @input="debouncedSave()"
                                                    class="form-input" placeholder="Senior Developer">
                                            </div>
                                            <div>
                                                <label class="form-label">Công ty</label>
                                                <input type="text" x-model="item.content.company" @input="debouncedSave()"
                                                    class="form-input" placeholder="Tên công ty">
                                            </div>
                                            <div>
                                                <label class="form-label">Địa điểm</label>
                                                <input type="text" x-model="item.content.location" @input="debouncedSave()"
                                                    class="form-input" placeholder="Hà Nội">
                                            </div>
                                            <div>
                                                <label class="form-label">Từ tháng/năm</label>
                                                <input type="text" x-model="item.content.start_date" @input="debouncedSave()"
                                                    class="form-input" placeholder="01/2022">
                                            </div>
                                            <div>
                                                <label class="form-label">Đến tháng/năm</label>
                                                <input type="text" x-model="item.content.end_date" @input="debouncedSave()"
                                                    class="form-input" placeholder="Hiện tại">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="form-label">Mô tả công việc</label>
                                                <textarea x-model="item.content.description" @input="debouncedSave()" rows="3"
                                                    class="form-input resize-none"
                                                    placeholder="Mô tả các trách nhiệm và thành tích..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {position:'', company:'', location:'', start_date:'', end_date:'', description:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm kinh nghiệm
                            </button>
                        </div>
                    </template>

                    {{-- Education --}}
                    <template x-if="section.type === 'education'">
                        <div>
                            <div class="space-y-4">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="border border-gray-200 rounded-lg p-4 relative group">
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="form-label">Trường / Cơ sở đào tạo</label>
                                                <input type="text" x-model="item.content.school" @input="debouncedSave()"
                                                    class="form-input" placeholder="Đại học Bách Khoa Hà Nội">
                                            </div>
                                            <div>
                                                <label class="form-label">Bằng cấp / Chuyên ngành</label>
                                                <input type="text" x-model="item.content.degree" @input="debouncedSave()"
                                                    class="form-input" placeholder="Cử nhân Công nghệ Thông tin">
                                            </div>
                                            <div>
                                                <label class="form-label">GPA (nếu có)</label>
                                                <input type="text" x-model="item.content.gpa" @input="debouncedSave()"
                                                    class="form-input" placeholder="3.5/4.0">
                                            </div>
                                            <div>
                                                <label class="form-label">Năm bắt đầu</label>
                                                <input type="text" x-model="item.content.start_date" @input="debouncedSave()"
                                                    class="form-input" placeholder="2018">
                                            </div>
                                            <div>
                                                <label class="form-label">Năm tốt nghiệp</label>
                                                <input type="text" x-model="item.content.end_date" @input="debouncedSave()"
                                                    class="form-input" placeholder="2022">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {school:'', degree:'', gpa:'', start_date:'', end_date:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm học vấn
                            </button>
                        </div>
                    </template>

                    {{-- Skills --}}
                    <template x-if="section.type === 'skills'">
                        <div>
                            <div class="space-y-3">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="flex items-center space-x-3 group">
                                        <input type="text" x-model="item.content.name" @input="debouncedSave()"
                                            class="form-input flex-1" placeholder="Tên kỹ năng (VD: JavaScript)">
                                        <select x-model="item.content.level" @change="debouncedSave()"
                                            class="form-input w-36">
                                            <option value="">-- Mức độ --</option>
                                            <option value="beginner">Cơ bản</option>
                                            <option value="intermediate">Trung bình</option>
                                            <option value="advanced">Nâng cao</option>
                                            <option value="expert">Chuyên gia</option>
                                        </select>
                                        <button @click="removeItem(section, idx)"
                                            class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition p-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {name:'', level:'intermediate'})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm kỹ năng
                            </button>
                        </div>
                    </template>

                    {{-- Certifications --}}
                    <template x-if="section.type === 'certifications'">
                        <div>
                            <div class="space-y-3">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="border border-gray-200 rounded-lg p-4 relative group">
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="form-label">Tên chứng chỉ</label>
                                                <input type="text" x-model="item.content.name" @input="debouncedSave()"
                                                    class="form-input" placeholder="AWS Certified Developer">
                                            </div>
                                            <div>
                                                <label class="form-label">Tổ chức cấp</label>
                                                <input type="text" x-model="item.content.issuer" @input="debouncedSave()"
                                                    class="form-input" placeholder="Amazon Web Services">
                                            </div>
                                            <div>
                                                <label class="form-label">Năm cấp</label>
                                                <input type="text" x-model="item.content.date" @input="debouncedSave()"
                                                    class="form-input" placeholder="2023">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {name:'', issuer:'', date:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm chứng chỉ
                            </button>
                        </div>
                    </template>

                    {{-- Projects --}}
                    <template x-if="section.type === 'projects'">
                        <div>
                            <div class="space-y-4">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="border border-gray-200 rounded-lg p-4 relative group">
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="form-label">Tên dự án</label>
                                                <input type="text" x-model="item.content.name" @input="debouncedSave()"
                                                    class="form-input" placeholder="CVactive Platform">
                                            </div>
                                            <div>
                                                <label class="form-label">Công nghệ</label>
                                                <input type="text" x-model="item.content.tech" @input="debouncedSave()"
                                                    class="form-input" placeholder="Laravel, Vue.js, MySQL">
                                            </div>
                                            <div>
                                                <label class="form-label">Link dự án</label>
                                                <input type="text" x-model="item.content.url" @input="debouncedSave()"
                                                    class="form-input" placeholder="https://github.com/...">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="form-label">Mô tả</label>
                                                <textarea x-model="item.content.description" @input="debouncedSave()" rows="2"
                                                    class="form-input resize-none" placeholder="Mô tả dự án..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {name:'', tech:'', url:'', description:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm dự án
                            </button>
                        </div>
                    </template>

                    {{-- Activities --}}
                    <template x-if="section.type === 'activities'">
                        <div>
                            <div class="space-y-3">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="border border-gray-200 rounded-lg p-4 relative group">
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="form-label">Hoạt động</label>
                                                <input type="text" x-model="item.content.name" @input="debouncedSave()"
                                                    class="form-input" placeholder="Tình nguyện viên...">
                                            </div>
                                            <div>
                                                <label class="form-label">Tổ chức</label>
                                                <input type="text" x-model="item.content.organization" @input="debouncedSave()"
                                                    class="form-input" placeholder="Tên tổ chức">
                                            </div>
                                            <div>
                                                <label class="form-label">Thời gian</label>
                                                <input type="text" x-model="item.content.period" @input="debouncedSave()"
                                                    class="form-input" placeholder="2021 - 2022">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {name:'', organization:'', period:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm hoạt động
                            </button>
                        </div>
                    </template>

                    {{-- References --}}
                    <template x-if="section.type === 'references'">
                        <div>
                            <div class="space-y-3">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="border border-gray-200 rounded-lg p-4 relative group">
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="form-label">Họ tên</label>
                                                <input type="text" x-model="item.content.name" @input="debouncedSave()"
                                                    class="form-input" placeholder="Nguyễn Văn B">
                                            </div>
                                            <div>
                                                <label class="form-label">Chức vụ</label>
                                                <input type="text" x-model="item.content.title" @input="debouncedSave()"
                                                    class="form-input" placeholder="CTO tại Công ty ABC">
                                            </div>
                                            <div>
                                                <label class="form-label">Email</label>
                                                <input type="text" x-model="item.content.email" @input="debouncedSave()"
                                                    class="form-input" placeholder="email@company.com">
                                            </div>
                                            <div>
                                                <label class="form-label">Điện thoại</label>
                                                <input type="text" x-model="item.content.phone" @input="debouncedSave()"
                                                    class="form-input" placeholder="0901 000 000">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {name:'', title:'', email:'', phone:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm người tham chiếu
                            </button>
                        </div>
                    </template>

                    {{-- Custom Section --}}
                    <template x-if="section.type === 'custom'">
                        <div>
                            <div class="space-y-3">
                                <template x-for="(item, idx) in section.items" :key="item.id || idx">
                                    <div class="relative group">
                                        <textarea x-model="item.content.text" @input="debouncedSave()" rows="2"
                                            class="form-input resize-none w-full pr-8"
                                            placeholder="Nội dung..."></textarea>
                                        <button @click="removeItem(section, idx)"
                                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button @click="addItem(section, {text:''})"
                                class="mt-3 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                                + Thêm nội dung
                            </button>
                        </div>
                    </template>

                </div>
            </div>
        </template>
    </main>

    {{-- RIGHT: Live Preview --}}
    <aside class="w-[45%] fixed right-0 top-14 bottom-0 bg-gray-200 overflow-auto flex flex-col">
        <div class="flex items-center justify-between px-4 py-2 bg-white border-b border-gray-200">
            <span class="text-xs font-semibold text-gray-500 uppercase">Xem trước</span>
            <div class="flex items-center space-x-2 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Live preview</span>
            </div>
        </div>

        <div class="flex-1 overflow-auto p-4 flex justify-center">
            <div id="cv-preview-frame" class="w-[210mm] min-h-[297mm] bg-white shadow-xl" :style="`font-family: '${cvFontFamily}', sans-serif`">
                @include($cv->template->blade_view ?? 'cv-templates.classic-blue', ['cv' => $cv, 'preview' => true])
            </div>
        </div>
    </aside>
</div>

{{-- Share Modal --}}
<div x-show="shareModal" x-cloak
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    @click.self="shareModal = false">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Chia sẻ CV</h3>
        <div class="flex items-center space-x-2 mb-4">
            <input type="text" :value="shareUrl" readonly
                class="flex-1 text-sm border border-gray-300 rounded-md px-3 py-2 bg-gray-50 focus:outline-none">
            <button @click="copyShareLink()"
                class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                <span x-text="copied ? 'Đã copy!' : 'Copy'"></span>
            </button>
        </div>
        <p class="text-xs text-gray-500">Link này cho phép ai cũng có thể xem CV của bạn.</p>
        <button @click="shareModal = false" class="mt-4 w-full py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Đóng</button>
    </div>
</div>

<style>
.form-label { @apply block text-xs font-medium text-gray-600 mb-1; }
.form-input { @apply w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition; }
</style>

<script>
function cvEditor() {
    return {
        activeTab: 'sections',
        activeSection: null,
        showAddSection: false,
        newSectionTitle: '',
        saving: false,
        savedAt: null,
        saveTimeout: null,
        shareModal: false,
        shareUrl: '',
        copied: false,
        exportingPng: false,
        currentTemplateId: @json($cv->template_id),

        cvTitle: @json($cv->title),
        cvThemeColor: @json($cv->theme_color),
        cvFontFamily: @json($cv->font_family),
        cvVisibility: @json($cv->visibility),
        objective: @json($cv->objective ?? ''),
        personal: @json($cv->personal_info ?? []),
        sections: @json($cv->sections->map(fn($s) => [
            'id' => $s->id,
            'type' => $s->type,
            'title' => $s->title,
            'sort_order' => $s->sort_order,
            'is_visible' => $s->is_visible,
            'is_custom' => $s->is_custom,
            'items' => $s->items->map(fn($i) => [
                'id' => $i->id,
                'content' => $i->content,
                'sort_order' => $i->sort_order,
            ])->values(),
        ])->values()),

        themeColors: ['#4F46E5', '#2563EB', '#0891B2', '#059669', '#D97706', '#DC2626', '#7C3AED', '#DB2777', '#374151', '#1F2937'],

        init() {
            setInterval(() => this.autoSave(), 30000);
            this.$nextTick(() => this.initSortable());
        },

        initSortable() {
            const el = document.getElementById('sections-list');
            if (!el || typeof Sortable === 'undefined') return;
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: (evt) => {
                    const moved = this.sections.splice(evt.oldIndex, 1)[0];
                    this.sections.splice(evt.newIndex, 0, moved);
                    this.sections.forEach((s, i) => { s.sort_order = i; });
                    this.debouncedSave();
                },
            });
        },

        debouncedSave() {
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => this.autoSave(), 1500);
        },

        async autoSave() {
            this.saving = true;
            try {
                // Save CV base info
                const res = await fetch('{{ route("cv.update", $cv) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title: this.cvTitle,
                        theme_color: this.cvThemeColor,
                        font_family: this.cvFontFamily,
                        visibility: this.cvVisibility,
                        objective: this.objective,
                        personal_info: this.personal,
                    }),
                });

                // Save sections
                await fetch('{{ route("cv.sections.save", $cv) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ sections: this.sections }),
                });

                const data = await res.json();
                this.savedAt = data.saved_at;
            } catch (e) {
                console.error('Save error:', e);
            } finally {
                this.saving = false;
            }
        },

        toggleSection(section) {
            section.is_visible = !section.is_visible;
            this.debouncedSave();
        },

        addItem(section, defaultContent) {
            if (!section.items) section.items = [];
            section.items.push({
                id: null,
                content: { ...defaultContent },
                sort_order: section.items.length,
            });
            this.debouncedSave();
        },

        removeItem(section, idx) {
            section.items.splice(idx, 1);
            this.debouncedSave();
        },

        async addCustomSection() {
            if (!this.newSectionTitle.trim()) return;
            const res = await fetch('{{ route("cv.sections.add", $cv) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ title: this.newSectionTitle }),
            });
            const data = await res.json();
            if (data.success) {
                this.sections.push({ ...data.section, items: [] });
                this.newSectionTitle = '';
                this.showAddSection = false;
            }
        },

        async deleteSection(section) {
            if (!confirm(`Xoá mục "${section.title}"?`)) return;
            const res = await fetch(`/cv/{{ $cv->id }}/sections/${section.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            if ((await res.json()).success) {
                this.sections = this.sections.filter(s => s.id !== section.id);
            }
        },

        async getShareLink() {
            const res = await fetch('{{ route("cv.share", $cv) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.success) {
                this.shareUrl = data.url;
                this.shareModal = true;
            }
        },

        async copyShareLink() {
            await navigator.clipboard.writeText(this.shareUrl);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        async changeTemplate(id) {
            if (id === this.currentTemplateId) return;
            try {
                const res = await fetch(`/cv/{{ $cv->id }}/template`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ template_id: id }),
                });
                const data = await res.json();
                if (data.success) {
                    this.currentTemplateId = id;
                    window.location.reload();
                }
            } catch (e) {
                console.error('Template change error:', e);
            }
        },

        async exportPng() {
            this.exportingPng = true;
            try {
                const frame = document.getElementById('cv-preview-frame');
                if (!frame || typeof html2canvas === 'undefined') return;

                // Temporarily reset zoom for accurate capture
                const originalZoom = frame.style.zoom;
                frame.style.zoom = '1';

                const canvas = await html2canvas(frame, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: '#ffffff',
                    logging: false,
                });

                frame.style.zoom = originalZoom;

                const link = document.createElement('a');
                link.download = '{{ Str::slug($cv->title) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            } catch (e) {
                console.error('PNG export error:', e);
                alert('Xuất PNG thất bại. Vui lòng thử lại.');
            } finally {
                this.exportingPng = false;
            }
        },
    };
}
</script>

</body>
</html>
