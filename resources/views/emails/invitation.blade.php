<!DOCTYPE html>
<html>
<body>
    <h2>Vous avez été invité à rejoindre la colocation :
        <strong>{{ $invitation->colocation->name }}</strong>
    </h2>

    <p>Cliquez sur le lien ci-dessous pour accepter ou refuser l'invitation :</p>

    <a href="{{ route('invitations.show', $invitation->token) }}">
        Répondre à l'invitation
    </a>

    <p>Ce lien est valable uniquement pour l'adresse : <strong>{{ $invitation->email }}</strong></p>
</body>
</html>