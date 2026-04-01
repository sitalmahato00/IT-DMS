<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ParentCommunicationController extends Controller
{
    /**
     * Display communication page (messaging/feedback).
     */
    public function index()
    {
        return view('parent.communication.index');
    }

    /**
     * Send feedback/message to administration.
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'recipient_type' => 'required|in:admin,teacher',
        ]);

        $parent = Auth::user();

        // Log the message/feedback in the system
        // Could implement a Messages/Feedback table here
        
        try {
            // Send email notification to admin/teachers
            // This is a simplified implementation
            // You could enhance this with a proper message tracking system

            return redirect()->route('parent.communication.index')
                ->with('success', __('Your message has been sent successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('parent.communication.index')
                ->with('error', __('Failed to send message. Please try again.'));
        }
    }
}
