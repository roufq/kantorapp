<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Mark all received messages as read when visiting the inbox
        Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) {
                $otherUser = $message->sender_id == auth()->id() ? $message->receiver : $message->sender;
                return $otherUser->id;
            });

        // Restrict recipient list by role/location
        if ($user->hasRole('Super Admin')) {
            $users = User::where('id', '!=', $user->id)->get();
        } elseif ($user->hasRole('Admin Lokasi')) {
            $users = User::where('id', '!=', $user->id)
                ->where(function ($q) use ($user) {
                    $q->where('location_id', $user->location_id)
                      ->orWhereHas('roles', function ($r) {
                          $r->where('name', 'Super Admin');
                      });
                })
                ->get();
        } else {
            // Karyawan: same location users and any admins (Admin Lokasi/Super Admin)
            $users = User::where('id', '!=', $user->id)
                ->where(function ($q) use ($user) {
                    $q->where('location_id', $user->location_id)
                      ->orWhereHas('roles', function ($r) {
                          $r->whereIn('name', ['Admin Lokasi', 'Super Admin']);
                      });
                })
                ->get();
        }

        // Build contact list (allowed users + ones you've chatted with) with last message info
        $contacts = $users->mapWithKeys(function ($u) {
            return [
                $u->id => [
                    'user' => $u,
                    'last_message' => null,
                    'last_message_at' => null,
                ],
            ];
        });

        foreach ($conversations as $otherId => $msgs) {
            $last = $msgs->sortByDesc('created_at')->first();
            // Ensure contact exists even if not in allowed users list (fallback)
            if (!$contacts->has($otherId)) {
                $otherUser = $last->sender_id == $user->id ? $last->receiver : $last->sender;
                $contacts->put($otherId, [
                    'user' => $otherUser,
                    'last_message' => null,
                    'last_message_at' => null,
                ]);
            }
            $contact = $contacts->get($otherId);
            $contact['last_message'] = $last;
            $contact['last_message_at'] = $last->created_at;
            $contacts->put($otherId, $contact);
        }

        // Sort by last message desc, then name
        $contacts = $contacts->sortBy([
            ['last_message_at', 'desc'],
            ['user.name', 'asc'],
        ]);

        return view('messages.index', compact('conversations', 'users', 'contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message));

        return redirect()->route('messages.index')->with('success', 'Message sent!');
    }

    public function show(User $user)
    {
        // Mark thread messages addressed to the current user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->where('receiver_id', Auth::id());
        })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();

        return view('messages.show', compact('messages', 'user'));
    }

    public function markAsRead($id)
    {
        $message = Message::where('id', $id)->where('receiver_id', Auth::id())->firstOrFail();
        $message->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted!');
    }
}
