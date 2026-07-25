<?php

namespace Awcodes\Curator\Tests\Fixtures\Policies;

use Awcodes\Curator\Models\Media;

/**
 * Allows the empty guard instance the panel passes to CuratorPlugin::authorize()
 * (a fresh, non-persisted Media), but denies every persisted record.
 *
 * This isolates the per-record enforcement added to the panel's item actions:
 * the pre-existing global ->visible() guard still passes (so the action is
 * reachable), yet the action must refuse to touch the concrete target record.
 *
 * The $user parameter defaults to null so the policy is evaluated even for the
 * guest user the test suite runs as (Laravel only calls a policy method for a
 * guest when its first parameter is nullable or defaults to null).
 */
class MediaRecordDeniedPolicy
{
    public function view($user = null, ?Media $media = null): bool
    {
        return ! $media?->exists;
    }

    public function update($user = null, ?Media $media = null): bool
    {
        return ! $media?->exists;
    }

    public function delete($user = null, ?Media $media = null): bool
    {
        return ! $media?->exists;
    }

    public function download($user = null, ?Media $media = null): bool
    {
        // Keep the download action's global 'download' guard passing so the
        // action mounts; the per-record 'view' check is what must block it.
        return true;
    }
}
