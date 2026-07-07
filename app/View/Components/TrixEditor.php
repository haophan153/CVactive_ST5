<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TrixEditor extends Component
{
    public string $name;
    public ?string $value;
    public ?string $placeholder;

    public function __construct(string $name, ?string $value = null, ?string $placeholder = null)
    {
        $this->name        = $name;
        $this->value       = $value;
        $this->placeholder = $placeholder;
    }

    public function render(): View
    {
        return view('components.trix-editor');
    }
}
