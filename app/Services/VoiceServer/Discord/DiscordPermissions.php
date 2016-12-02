<?php

namespace App\Services\VoiceServer\Discord;

class DiscordPermissions
{
    const CREATE_INSTANT_INVITE = 0x00000001; // Allows creation of instant invites
    const KICK_MEMBERS          = 0x00000002; // Allows kicking members
    const BAN_MEMBERS           = 0x00000004; // Allows banning members
    const ADMINISTRATOR         = 0x00000008; // Allows all permissions and bypasses channel permission overwrites
    const MANAGE_CHANNELS       = 0x00000010; // Allows management and editing of channels
    const MANAGE_GUILD          = 0x00000020; // Allows management and editing of the guild
    const ADD_REACTIONS         = 0x00000040; // Allows for the addition of reactions to messages
    const READ_MESSAGES	        = 0x00000400; // Allows reading messages in a channel. The channel will not appear for users without this permission
    const SEND_MESSAGES	        = 0x00000800; // Allows for sending messages in a channel.
    const SEND_TTS_MESSAGES     = 0x00001000; // Allows for sending of /tts messages
    const MANAGE_MESSAGES       = 0x00002000; // Allows for deletion of other users messages
    const EMBED_LINKS           = 0x00004000; // Links sent by this user will be auto-embedded
    const ATTACH_FILES          = 0x00008000; // Allows for uploading images and files
    const READ_MESSAGE_HISTORY  = 0x00010000; // Allows for reading of message history
    const MENTION_EVERYONE      = 0x00020000; // Allows for using the @everyone tag to notify all users in a channel, and the @here tag to notify all online users in a channel
    const USE_EXTERNAL_EMOJIS	= 0x00040000; // Allows the usage of custom emojis from other servers
    const CONNECT	            = 0x00100000; // Allows for joining of a voice channel
    const SPEAK                 = 0x00200000; // Allows for speaking in a voice channel
    const MUTE_MEMBERS	        = 0x00400000; // Allows for muting members in a voice channel
    const DEAFEN_MEMBERS	    = 0x00800000; // Allows for deafening of members in a voice channel
    const MOVE_MEMBERS	        = 0x01000000; // Allows for moving of members between voice channels
    const USE_VAD	            = 0x02000000; // Allows for using voice-activity-detection in a voice channel
    const CHANGE_NICKNAME       = 0x04000000; // Allows for modification of own nickname
    const MANAGE_NICKNAMES      = 0x08000000; // Allows for modification of other users nicknames
    const MANAGE_ROLES          = 0x10000000; // Allows management and editing of roles
    const MANAGE_WEBHOOKS       = 0x20000000; // Allows management and editing of webhooks
    const MANAGE_EMOJIS         = 0x40000000; // Allows management and editing of emojis
}