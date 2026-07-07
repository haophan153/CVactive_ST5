<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::latest();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                  ->orWhere('email', 'like', "%$term%")
                  ->orWhere('subject', 'like', "%$term%");
            });
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read === 'read');
        }

        $contacts = $query->paginate(20)->withQueryString();
        $selected = $request->filled('selected') ? Contact::find($request->selected) : $contacts->first();

        $stats = [
            'total'  => Contact::count(),
            'unread' => Contact::where('is_read', false)->count(),
            'today'  => Contact::whereDate('created_at', today())->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'selected', 'stats'));
    }

    public function show(Contact $contact)
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        $contacts = Contact::latest()->limit(20)->get();
        $stats = [
            'total'  => Contact::count(),
            'unread' => Contact::where('is_read', false)->count(),
            'today'  => Contact::whereDate('created_at', today())->count(),
        ];
        return view('admin.contacts.show', compact('contact', 'contacts', 'stats'));
    }

    public function toggleRead(Contact $contact)
    {
        $contact->update(['is_read' => !$contact->is_read]);
        return back()->with('success', 'Đã cập nhật.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Đã xóa liên hệ.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,mark_unread,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:contacts,id',
        ]);

        $ids = $request->ids;

        if ($request->action === 'delete') {
            Contact::whereIn('id', $ids)->delete();
            return back()->with('success', 'Đã xóa ' . count($ids) . ' liên hệ.');
        }
        if ($request->action === 'mark_read') {
            Contact::whereIn('id', $ids)->update(['is_read' => true]);
            return back()->with('success', 'Đã đánh dấu đã đọc.');
        }
        if ($request->action === 'mark_unread') {
            Contact::whereIn('id', $ids)->update(['is_read' => false]);
            return back()->with('success', 'Đã đánh dấu chưa đọc.');
        }
    }
}
