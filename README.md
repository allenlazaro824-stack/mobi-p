# Stream Hub

A full-stack sample website using **HTML/CSS/JS + PHP + SQLite**:

- Create account and sign in
- Save profile name and profile photo in database
- Show 100 Action + 100 Anime + 100 Comedy YouTube videos
- Play videos in-page using embedded YouTube player
- Automatically download the user's catalog JSON file after account creation
- Store full catalog entries in database for each created account

## Run locally

```bash
php -S 0.0.0.0:8000
```

Open: <http://localhost:8000>

## Database

SQLite database file: `data/app.db`

Tables:
- `users`
- `downloads`
