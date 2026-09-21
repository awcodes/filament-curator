<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

use Closure;
use Filament\Actions\Action;

trait HasDeleteActions
{
    protected ?Closure $configureDeleteActionsUsing = null;

    /**
     * Customise every action that deletes media: the resource table's row and
     * bulk actions, the edit page's header action, and the picker panel's
     * destroyItem action. The callback receives the action as `$action`.
     */
    public function configureDeleteActionsUsing(?Closure $callback): static
    {
        $this->configureDeleteActionsUsing = $callback;

        return $this;
    }

    /**
     * @template TAction of Action
     *
     * @param  TAction  $action
     * @return TAction
     */
    public function configureDeleteAction(Action $action): Action
    {
        if ($this->configureDeleteActionsUsing instanceof Closure) {
            $this->evaluate(
                $this->configureDeleteActionsUsing,
                namedInjections: ['action' => $action],
                typedInjections: [Action::class => $action, $action::class => $action],
            );
        }

        return $action;
    }
}
