<?php

declare(strict_types=1);

namespace Awcodes\Curator\Support;

use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * The media a picker field may list, select and keep: the records on its disk, of a type it accepts, inside its
 * directory when it is limited to one, and belonging to the current tenant when tenancy applies. The picker, its
 * panel and the rich editor's panel read media only through this, so a record that can't be listed can't be
 * selected, inserted, downloaded or loaded back from a saved value either.
 */
final readonly class MediaScope
{
    private ?string $directory;

    /**
     * @param  array<int, mixed>  $acceptedFileTypes  types as a field accepts them, `image/*` wildcards included; none means any type
     * @param  string|null  $directory  the directory the field is limited to, which includes its subdirectories
     * @param  bool  $isLimitedToDirectory  whether the field is limited to its directory; when it is but names none, nothing is in the scope
     */
    public function __construct(
        private string $disk,
        private array $acceptedFileTypes = [],
        ?string $directory = null,
        private bool $isLimitedToDirectory = false,
        private bool $isTenantAware = false,
        private ?string $tenantOwnershipRelationshipName = null,
    ) {
        $this->directory = $isLimitedToDirectory ? self::normalizeDirectory($directory) : null;
    }

    /**
     * The ids in a picker's state, in order: a single id, a record or its array, or a list of them.
     *
     * @return array<int, string>
     */
    public static function extractIds(mixed $state): array
    {
        if ($state instanceof Model || (is_array($state) && array_key_exists('id', $state))) {
            $state = [$state];
        }

        if (is_iterable($state)) {
            $state = is_array($state) ? $state : iterator_to_array($state, false);
        } else {
            $state = [$state];
        }

        $ids = [];

        foreach ($state as $item) {
            $id = match (true) {
                $item instanceof Model => $item->getKey(),
                is_array($item) => $item['id'] ?? null,
                default => $item,
            };

            if (is_int($id) || (is_string($id) && $id !== '')) {
                $ids[] = (string) $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Constrain a query to a directory and the directories below it.
     *
     * @param  Builder<Media>  $query
     * @return Builder<Media>
     */
    public static function whereWithinDirectory(Builder $query, string $directory): Builder
    {
        $column = $query->qualifyColumn('directory');
        $wrapped = $query->getGrammar()->wrap($column);
        $prefix = $directory . '/';

        // LIKE, and `=` under MySQL's usual collations, ignore case, while directories are case-sensitive on disk and
        // in navigation, so the match is also compared as bytes. CAST(... AS BINARY) rather than the BINARY operator,
        // which MySQL 8.0.27 deprecates; MariaDB reads both the same way.
        return match ($query->getModel()->getConnection()->getDriverName()) {
            'mysql', 'mariadb' => $query
                ->whereRaw("cast({$wrapped} as binary) = cast(? as binary)", [$directory])
                ->orWhereRaw("{$wrapped} like ? escape '~' and cast(substring({$wrapped}, 1, ?) as binary) = cast(? as binary)", [self::escapeLike($directory) . '/%', mb_strlen($prefix), $prefix]),
            'sqlite', 'pgsql' => $query
                ->where($column, $directory)
                ->orWhereRaw("{$wrapped} like ? escape '~' and substr({$wrapped}, 1, ?) = ?", [self::escapeLike($directory) . '/%', mb_strlen($prefix), $prefix]),
            default => $query
                ->where($column, $directory)
                ->orWhereRaw("{$wrapped} like ? escape '~'", [self::escapeLike($directory) . '/%']),
        };
    }

    /**
     * Trim the slashes around a directory, which isn't stored with them; an empty directory is the disk root, null.
     */
    public static function normalizeDirectory(?string $directory): ?string
    {
        $directory = trim((string) $directory, '/');

        return $directory === '' ? null : $directory;
    }

    /**
     * Escape the LIKE wildcards in a value, with `~` as the escape character. Backslash is avoided because MySQL and
     * Postgres disagree on how it is read inside a string literal.
     */
    public static function escapeLike(string $value): string
    {
        return str_replace(['~', '%', '_'], ['~~', '~%', '~_'], $value);
    }

    /**
     * @return Builder<Media>
     */
    public function query(): Builder
    {
        return $this->apply(App::make(Media::class)::query());
    }

    /**
     * @param  Builder<Media>  $query
     * @return Builder<Media>
     */
    public function apply(Builder $query): Builder
    {
        $query->where($query->qualifyColumn('disk'), $this->disk);

        $this->applyTenant($query);
        $this->applyTypes($query);

        if ($this->isLimitedToDirectory && $this->directory === null) {
            $query->whereRaw('1 = 0');
        } elseif ($this->directory !== null) {
            $query->where(fn (Builder $query): Builder => self::whereWithinDirectory($query, $this->directory));
        }

        return $query;
    }

    /**
     * The records for some ids, in the order the ids were given. Ids outside the scope, or without a record, are
     * left out.
     *
     * Ids already saved on the record being edited only have to exist and belong to the current tenant: they were
     * stored by the server, and may predate a change to the field's disk, types or directory, which shouldn't make
     * them disappear from the record.
     *
     * @param  array<int, int|string>  $persistedIds
     * @return Collection<int, Media>
     */
    public function resolve(mixed $ids, array $persistedIds = []): Collection
    {
        $ids = self::extractIds($ids);

        if ($ids === []) {
            return new Collection;
        }

        $records = $this->query()
            ->whereKey($ids)
            ->get()
            ->keyBy(fn (Model $media): string => (string) $media->getKey());

        $persisted = array_values(array_diff(
            array_intersect($ids, self::extractIds($persistedIds)),
            array_map(strval(...), $records->keys()->all()),
        ));

        if ($persisted !== []) {
            $records = $records->union(
                $this->tenantQuery()
                    ->whereKey($persisted)
                    ->get()
                    ->keyBy(fn (Model $media): string => (string) $media->getKey()),
            );
        }

        return new Collection(array_values(array_filter(
            array_map(fn (string $id): ?Model => $records->get($id), $ids),
        )));
    }

    /**
     * Whether every id names a record within the scope, or, for an id already saved on the record, a record of the
     * current tenant.
     *
     * @param  array<int, int|string>  $persistedIds
     */
    public function contains(mixed $ids, array $persistedIds = []): bool
    {
        $ids = self::extractIds($ids);

        return $this->resolve($ids, $persistedIds)->count() === count($ids);
    }

    /**
     * The distinct directories that hold media within the scope, told apart by case as navigation does, which a
     * plain DISTINCT under MySQL's usual collations wouldn't.
     *
     * @return array<int, string>
     */
    public function directories(): array
    {
        $query = $this->query()->whereNotNull('directory')->distinct();

        if (in_array($query->getModel()->getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $wrapped = $query->getGrammar()->wrap($query->qualifyColumn('directory'));

            return $query->toBase()->selectRaw("cast({$wrapped} as binary) as directory")->pluck('directory')->map(strval(...))->all();
        }

        return $query->pluck('directory')->map(strval(...))->all();
    }

    /**
     * Media of the current tenant, whatever its disk, type or directory.
     *
     * @return Builder<Media>
     */
    public function tenantQuery(): Builder
    {
        $query = App::make(Media::class)::query();

        $this->applyTenant($query);

        return $query;
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    public function isLimitedToDirectory(): bool
    {
        return $this->isLimitedToDirectory;
    }

    /**
     * Whether a directory may be browsed: any directory when the scope isn't limited to one, otherwise the limited
     * directory and those below it, and none when it's limited without naming a directory. Null is the disk root.
     */
    public function allowsDirectory(?string $directory): bool
    {
        if (! $this->isLimitedToDirectory) {
            return true;
        }

        if ($this->directory === null) {
            return false;
        }

        $directory = self::normalizeDirectory($directory);

        return $directory !== null
            && ($directory === $this->directory || str_starts_with($directory, $this->directory . '/'));
    }

    /**
     * @param  Builder<Media>  $query
     */
    private function applyTenant(Builder $query): void
    {
        if (! $this->isTenantAware || ! filament()->hasTenancy()) {
            return;
        }

        $tenant = filament()->getTenant();

        if (! $tenant instanceof Model) {
            $query->whereRaw('1 = 0');

            return;
        }

        $relationship = $this->tenantOwnershipRelationshipName ?? filament()->getTenantOwnershipRelationshipName();

        $query->where($query->qualifyColumn($relationship . '_id'), $tenant->getKey());
    }

    /**
     * Matches the types MimeType::isAccepted() accepts: an exact type, or a `type/*` wildcard, which leaves out the
     * types that can run script unless they're listed exactly.
     *
     * @param  Builder<Media>  $query
     */
    private function applyTypes(Builder $query): void
    {
        $types = collect($this->acceptedFileTypes)
            ->filter(fn (mixed $type): bool => is_string($type))
            ->map(fn (string $type): string => mb_strtolower(trim($type)))
            ->filter(fn (string $type): bool => $type !== '')
            ->unique();

        if ($types->isEmpty()) {
            return;
        }

        $exact = $types->reject(fn (string $type): bool => str_ends_with($type, '/*'))->values()->all();
        $wildcards = $types->filter(fn (string $type): bool => str_ends_with($type, '/*'))
            ->map(fn (string $type): string => substr($type, 0, -2))
            ->values()
            ->all();

        $column = $query->qualifyColumn('type');
        $wrapped = $query->getGrammar()->wrap($column);

        $query->where(function (Builder $query) use ($column, $exact, $wildcards, $wrapped): void {
            if ($exact !== []) {
                $query->whereIn($column, $exact);
            }

            foreach ($wildcards as $group) {
                $query->orWhere(fn (Builder $query): Builder => $query
                    ->whereRaw("{$wrapped} like ? escape '~'", [self::escapeLike($group) . '/%'])
                    ->whereNotIn($column, MimeType::scriptableTypes())
                    ->where(fn (Builder $query): Builder => $query
                        ->whereRaw("{$wrapped} not like ? escape '~'", ['%+xml'])
                        ->orWhere($column, MimeType::ImageSvgXml->value)));
            }
        });
    }
}
