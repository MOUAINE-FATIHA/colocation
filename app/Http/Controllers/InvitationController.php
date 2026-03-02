<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    // Afficher le formulaire d'invitation (owner seulement)
    public function create(Colocation $colocation)
    {
        $this->authorizeOwner($colocation);
        return view('invitations.create', compact('colocation'));
    }

    // Envoyer l'invitation
    public function store(Request $request, Colocation $colocation)
    {
        $this->authorizeOwner($colocation);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Vérifier qu'une invitation pending n'existe pas déjà
        $exists = Invitation::where('colocation_id', $colocation->id)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Une invitation est déjà en attente pour cet email.']);
        }

        // Créer l'invitation avec token unique
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email'         => $request->email,
            'token'         => Str::uuid(),
            'status'        => 'pending',
        ]);

        // Envoyer l'email
        Mail::to($request->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation envoyée à ' . $request->email);
    }

    // Afficher la page d'acceptation/refus
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        return view('invitations.show', compact('invitation'));
    }

    // Accepter l'invitation
    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        // Vérifier que l'email correspond à l'user connecté
        if (auth()->user()->email !== $invitation->email) {
            abort(403, 'Cette invitation ne vous est pas destinée.');
        }

        // Vérifier que l'user n'a pas déjà une colocation active
        if (auth()->user()->activeMembership) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Vous avez déjà une colocation active.']);
        }

        // Créer le membership
        Membership::create([
            'user_id'       => auth()->id(),
            'colocation_id' => $invitation->colocation_id,
            'role'          => 'member',
            'joined_at'     => now(),
        ]);

        // Mettre à jour le statut de l'invitation
        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $invitation->colocation)
            ->with('success', 'Vous avez rejoint la colocation !');
    }

    // Refuser l'invitation
    public function refuse(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if (auth()->user()->email !== $invitation->email) {
            abort(403, 'Cette invitation ne vous est pas destinée.');
        }

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')
            ->with('success', 'Invitation refusée.');
    }

    // Helper privé : vérifier que l'user est owner
    private function authorizeOwner(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->where('role', 'owner')
            ->first();

        if (!$membership) {
            abort(403, 'Seul le propriétaire peut inviter des membres.');
        }
    }
}