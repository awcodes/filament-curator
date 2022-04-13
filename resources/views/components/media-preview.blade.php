<x-forms::field-wrapper :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()">
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
        <img x-bind:src="state?.large_url"
            x-bind:alt="state?.alt"
            x-bind:width="state?.width"
            x-bind:height="state?.height"
            x-bind:srcset="`${state?.large_url} 1024w, ${state?.medium_url} 640w`"
            sizes="(max-width: 1200px) 100vw, 1024px"
            loading="lazy"
            class="overflow-hidden border border-gray-300 rounded dark:border-black" />
    </div>
</x-forms::field-wrapper>
