@props(['name', 'value' => null, 'placeholder' => null])

<div
    x-data="{
        value: @js($value ?? ''),
        setHtml(html) { this.value = html; this.$refs.editor.innerHTML = html; },
        sync() { this.value = this.$refs.editor.innerHTML; },
        fileAdded(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (evt) => {
                const b64 = evt.target.result;
                // Basic storage backend via global function if provided
                if (window.uploadTrixAttachment) {
                    window.uploadTrixAttachment(b64, file).then(url => {
                        this.insertImage(url);
                    }).catch(() => this.insertImage(b64));
                } else {
                    this.insertImage(b64);
                }
            };
            reader.readAsDataURL(file);
            e.target.value = '';
        },
        insertImage(url) {
            document.execCommand('insertImage', false, url);
            this.sync();
        },
    }"
    wire:ignore
    class="trix-wrapper border border-gray-300 rounded-lg overflow-hidden bg-white"
>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.umd.min.js" defer></script>
    <style>
        trix-editor { background: white; min-height: 320px; font-size: 14px; line-height: 1.7; padding: 16px; }
        trix-editor:focus { outline: none; }
        .trix-wrapper .trix-toolbar { background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 8px 10px; }
        .trix-wrapper .trix-button-row { gap: 4px; }
        .trix-wrapper .trix-button-group { background: white; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; }
        .trix-wrapper .trix-button { border-color: transparent; padding: 4px 8px; border-radius: 4px; }
        .trix-wrapper .trix-button.trix-active { background: #eef2ff; color: #4338ca; }
        .trix-content { padding: 0 !important; }
    </style>

    <input
        id="trix-input-{{ $name }}"
        type="hidden"
        name="{{ $name }}"
        :value="value"
    />

    {{-- Toolbar from Trix web component --}}
    <trix-toolbar id="trix-toolbar-{{ $name }}" class="trix-toolbar"></trix-toolbar>

    {{-- Editor --}}
    <trix-editor
        x-ref="editor"
        input="trix-input-{{ $name }}"
        placeholder="{{ $placeholder ?? 'Nhập nội dung...' }}"
        @input.debounce.150ms="sync()"
        class="trix-content"
    >{!! $value !!}</trix-editor>
</div>
