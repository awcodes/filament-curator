---
title: Glide token
description: What CURATOR_GLIDE_TOKEN does, why every environment needs its own, and what happens when you rotate it.
---

# Glide token

Curator signs the image URLs it renders, and the media route rejects any request whose signature does not match. The signing key is `CURATOR_GLIDE_TOKEN`.

## Generating one

The installer runs this for you, but you can run it directly:

```bash
php artisan curator:token
```

It writes a freshly generated value into the `.env` of the machine you run it on.

## Every environment needs its own

Because `.env` is normally gitignored, the token exists only where you installed. Every other environment — a teammate's checkout, your other machine, CI, staging, production — needs its own value. Add the key to `.env.example` and set it wherever you deploy:

```dotenv
CURATOR_GLIDE_TOKEN=
```

The value does **not** have to match across environments. URLs are signed when rendered and validated by the same environment that served them, so each can hold its own token.

> [!IMPORTANT]
> It does have to be present. With the variable missing, generating a media URL and serving the media route both fail.

## Rotating it

Re-running `php artisan curator:token` overwrites the existing value rather than adding a second one. That invalidates every signed URL that has outlived the request it was rendered in:

- cached HTML
- a CDN copy
- an already-sent email
- a URL pasted into stored content

All of those start returning 403. If your config is cached, run `php artisan config:clear` after changing the token.
