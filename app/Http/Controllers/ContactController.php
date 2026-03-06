<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        Contact::create($request->only('name', 'email', 'subject', 'message'));

        return back()->with('success', 'Cảm ơn bạn! Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }
}
