# Messenger

Trainer messaging for 1:1 client and group conversations.

## Architecture

```
conversations (trainer_id, type, target_id)
    └── conversation_messages (sender_type, sender_id, body, payload_type, payload)
            └── message_reads (reader_id, read_at)
```

- **Conversations**: One per trainer-client or trainer-group pair. `type` is `client` or `group`; `target_id` is the client or group ID.
- **Messages**: `sender_type` is `trainer` or `client`; `sender_id` is the user ID (trainer) or client ID (client). `payload_type` can be `text`, `workout`, `image`, `file`, `schedule`, `audio`.
- **Read receipts**: `message_reads` stores who read each message.

## Routes

| Method | Path | Description |
|--------|------|-------------|
| GET | `/trainer/messages` | List conversations + clients/groups without conversations |
| GET | `/trainer/messages/{conversation}` | Show conversation and messages |
| POST | `/trainer/messages` | Send message (creates conversation if needed) |
| PATCH | `/trainer/messages/{conversation}` | Update (e.g. pin) |

## Message Payloads

- **text**: `body` is the message text.
- **audio**: `payload` `{ duration: "0:32" }`.
- **workout**: `payload` `{ title, details, note }`.

## Real-time (Echo)

- **Channel**: `private-conversation.{id}`
- **Event**: `message.sent` with `{ message, conversation_id }`

Set `VITE_PUSHER_APP_KEY` (or `VITE_REVERB_APP_KEY`) and related env vars to enable. If Echo is unavailable, the UI degrades to page refresh on send.

## Components

- `ConversationList` – Sidebar with search and conversation items
- `ConversationItem` – Single row (client/group, last message, unread)
- `ChatHeader` – Header with avatar and actions
- `MessageThread` – Scrollable message list with auto-scroll
- `MessageBubble` – Renders text, workout, or audio messages
- `MessageComposer` – Text input + emoji/attach/audio/send
- `AudioMessagePlayer` – Playback for audio messages
- `AudioRecorder` – Record and send voice notes
