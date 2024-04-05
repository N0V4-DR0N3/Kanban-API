<?php

return [
    'authorization_url' => 'https://discordapp.com/api/oauth2/authorize',
    'token_url' => 'https://discordapp.com/api/oauth2/token',
    'resource_owner_url' => 'https://discordapp.com/api/users/@me',

    'redirect_uri' => env('DISCORD_REDIRECT_URI'),

    'client_id' => env('DISCORD_CLIENT_ID'),
    'client_secret' => env('DISCORD_CLIENT_SECRET'),

    'scopes' => env('DISCORD_SCOPES', 'identify email'),
];
